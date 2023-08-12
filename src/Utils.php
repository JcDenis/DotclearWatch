<?php
/**
 * @brief DotclearWatch, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Jean-Christian Denis and contributors
 *
 * @copyright Jean-Christain Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\DotclearWatch;

use dcCore;
use dcLog;
use dcModuleDefine;
use dcThemes;
use Dotclear\Helper\Crypt;
use Dotclear\Helper\Date;
use Dotclear\Helper\Network\HttpClient;
use Exception;

class Utils
{
    /** @var    int     The expiration delay before resend report (one week) */
    public const EXPIRED_DELAY = 604800;

    /** @var    string  The default distant API URL */
    public const DISTANT_API_URL = 'https://dotclear.watch/api';

    /** @var    string  The distant API version */
    public const DISTANT_API_VERSION = '1.1';

    /** @var    array<int,string>   The hiddens modules IDs */
    private static array $hiddens = [];

    /** @var    string  Multiblog unique identifiant */
    private static string $uid = '';

    /**
     * Add mark to backend menu footer.
     */
    public static function addMark(): void
    {
        if (My::settings()->getGlobal('distant_api_url')) {
            echo sprintf(
                '<ul><li><a href="%s" title="%s" class="outgoing">%s<img src="%s" /></a></ul></li>',
                'https://statistics.dotclear.watch',
                __('DotclearWatch plugin statistics'),
                __('Tracked by dotclear.watch'),
                My::fileURL('icon.svg')
            );
        }
    }

    /**
     * Get hidden modules.
     *
     * This does not check if module exists.
     *
     * @return  array<int,string>   The hiddens modules
     */
    public static function getHiddens(): array
    {
        if (empty(self::$hiddens)) {
            foreach (explode(',', (string) My::settings()->getGlobal('hidden_modules')) as $hidden) {
                $hidden = trim($hidden);
                if (!empty($hidden)) {
                    self::$hiddens[] = trim($hidden);
                }
            }
        }

        return self::$hiddens;
    }

    /**
     * Get plugins list.
     *
     * @param   bool    $strict     take only enabled and not hidden plugins
     *
     * @return  array<string,string>    The plugins list.
     */
    public static function getPlugins(bool $strict = true): array
    {
        $modules = [];
        $hiddens = self::getHiddens();
        $defines = dcCore::app()->plugins->getDefines($strict ? ['state' => dcModuleDefine::STATE_ENABLED] : []);
        foreach ($defines as $define) {
            if ($strict && in_array($define->getId(), $hiddens)) {
                continue;
            }
            $modules[$define->getId()] = $define->get('version');
        }

        return $modules;
    }

    /**
     * Get themes list.
     *
     * @param   bool    $strict     take only enabled and not hidden themes
     *
     * @return  array<string,string>    The themes list.
     */
    public static function getThemes(bool $strict = true): array
    {
        if (!(dcCore::app()->themes instanceof dcThemes)) {
            dcCore::app()->themes = new dcThemes();
            dcCore::app()->themes->loadModules(dcCore::app()->blog->themes_path);
        }

        $modules = [];
        $hiddens = self::getHiddens();
        $defines = dcCore::app()->themes->getDefines($strict ? ['state' => dcModuleDefine::STATE_ENABLED] : []);
        foreach ($defines as $define) {
            if ($strict && in_array($define->getId(), $hiddens)) {
                continue;
            }
            $modules[$define->getId()] = $define->get('version');
        }

        return $modules;
    }

    /**
     * Get report contents.
     *
     * @return  string  The report contents as it will be sent
     */
    public static function getReport(): string
    {
        return self::check() ? self::contents() : '';
    }

    /**
     * Get client uid.
     */
    public static function getClient(): string
    {
        return self::check() ? self::uid() : '';
    }

    /**
     * Get request error.
     */
    public static function getError(): string
    {
        $rs = dcCore::app()->log->getLogs([
            'log_table' => My::id() . '_error',
        ]);

        return $rs->isEmpty() || !is_string($rs->f('log_msg')) ? '' : $rs->f('log_msg');
    }

    /**
     * Clear cache directory.
     */
    public static function clearCache(): void
    {
        if (self::check()) {
            self::clear();
        }
    }

    /**
     * Send report.
     *
     * Report will be by blog to keep track of all used themes.
     * Plugins stats are by multiblogs and themes stats by blog.
     *
     * @param   bool    $force  Send report even if delay not expired
     */
    public static function sendReport(bool $force = false): void
    {
        if (!self::check()) {
            return;
        }

        if (!$force && !self::expired()) {
            return;
        }

        $contents = self::contents();

        self::write($contents);

        $client   = false;
        $status   = 500;
        $response = '';
        $url      = sprintf(self::url(), 'report');
        $path     = '';

        try {
            if (false !== ($client = HttpClient::initClient($url, $path))) {
                $client->setUserAgent('Dotclear.watch ' . My::id() . '/' . self::DISTANT_API_VERSION);
                $client->useGzip(false);
                $client->setPersistReferers(false);
                $client->post($path, ['key' => self::key(), 'report' => $contents]);

                $status   = (int) $client->getStatus();
                $response = $client->getContent();
            } elseif (function_exists('curl_init')) {
                if (false !== ($client = curl_init($url))) {
                    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($client, CURLOPT_POST, true);
                    curl_setopt($client, CURLOPT_POSTFIELDS, ['key' => self::key(), 'report' => $contents]);

                    if (false !== ($response = curl_exec($client))) {
                        $status = (int) curl_getinfo($client, CURLINFO_HTTP_CODE);
                    }
                }
            }

            unset($client);
        } catch (Exception $e) {
            unset($client);
        }

        if ($status == 202) {
            return;
        }

        if ($status !== false) {
            self::error((string) '(' . $status . ') ' . $response);
        }

        if ($force) {
            self::error('Dotclear.watch report failed');
        }
    }

    private static function check(): bool
    {
        return defined('DC_CRYPT_ALGO');
    }

    private static function key(): string
    {
        return Crypt::hmac(self::uid() . My::id(), DC_CRYPT_ALGO);
    }

    private static function uid(): string
    {
        if (empty(self::$uid)) {
            self::$uid = (string) My::settings()->getGlobal('client_uid');
            if (empty(self::$uid) || strlen(self::$uid) != 32) {
                self::$uid = md5(uniqid() . My::id() . time());
                My::settings()->put('client_uid', self::$uid, 'string', 'Client UID', false, true);
            }
        }

        return self::$uid;
    }

    private static function buid(): string
    {
        return md5(self::uid() . dcCore::app()->blog->uid);
    }

    private static function url(): string
    {
        $api_url = My::settings()->getGlobal('distant_api_url');

        return (is_string($api_url) ? $api_url : self::DISTANT_API_URL) . '/' . self::DISTANT_API_VERSION . '/%s/' . self::uid();
    }

    private static function clear(): void
    {
        $rs = dcCore::app()->log->getLogs([
            'log_table' => [
                My::id() . '_report',
                My::id() . '_error',
            ],
        ]);

        if ($rs->isEmpty()) {
            return;
        }

        $logs = [];
        while ($rs->fetch()) {
            $logs[] = (int) $rs->f('log_id');
        }
        dcCore::app()->log->delLogs($logs);
    }

    private static function error(string $message): void
    {
        self::clear();

        $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . dcLog::LOG_TABLE_NAME);
        $cur->setField('log_table', My::id() . '_error');
        $cur->setField('log_msg', $message);

        dcCore::app()->log->addLog($cur);
    }

    private static function write(string $contents): void
    {
        self::clear();

        $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . dcLog::LOG_TABLE_NAME);
        $cur->setField('log_table', My::id() . '_report');
        $cur->setField('log_msg', $contents);

        dcCore::app()->log->addLog($cur);
    }

    private static function read(): string
    {
        $rs = dcCore::app()->log->getLogs([
            'log_table' => My::id() . '_report',
        ]);

        return $rs->isEmpty() || !is_string($rs->f('log_msg')) ? '' : $rs->f('log_msg');
    }

    private static function expired(): bool
    {
        $rs = dcCore::app()->log->getLogs([
            'log_table' => My::id() . '_report',
        ]);

        return $rs->isEmpty() || !is_string($rs->f('log_dt')) || (int) Date::str('%s', $rs->f('log_dt')) + self::EXPIRED_DELAY < time();
    }

    private static function contents(): string
    {
        // Build json response
        return (string) json_encode([
            'uid'     => self::uid(),
            'buid'    => self::buid(),
            'plugins' => self::getPlugins(), // enabled plugins
            'themes'  => self::getThemes(), // enabled themes
            'blog'    => [
                'lang'  => (string) dcCore::app()->blog->settings->get('system')->get('lang'),
                'theme' => (string) dcCore::app()->blog->settings->get('system')->get('theme'),
            ],
            'blogs' => [
                'count' => (int) dcCore::app()->getBlogs([], true)->f(0),
            ],
            'core' => [
                'version' => DC_VERSION,
            ],
            'php' => [
                'sapi'    => php_sapi_name() ?: 'php',
                'version' => phpversion(),
            ],
            'system' => [
                'name'    => php_uname('s'),
                'version' => php_uname('r'),
            ],
            'database' => [
                'driver'  => dcCore::app()->con->driver(),
                'version' => dcCore::app()->con->version(),
            ],
        ], JSON_PRETTY_PRINT);
    }
}
