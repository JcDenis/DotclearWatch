<?php

declare(strict_types=1);

namespace Dotclear\Plugin\DotclearWatch;

use Dotclear\App;
use Dotclear\Helper\Crypt;
use Dotclear\Helper\Network\HttpClient;
use Dotclear\Module\ModuleDefine;
use Throwable;

/**
 * @brief       DotclearWatch utils class.
 * @ingroup     DotclearWatch
 *
 * @author      Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Utils
{
    /**
     * The expiration delay before resend report (one week).
     *
     * @var     int     EXPIRED_DELAY
     */
    public const EXPIRED_DELAY = 604800;

    /**
     * The default distant blog API URL.
     *
     * @var     string  DISTANT_API_URL
     */
    public const DISTANT_API_URL = 'https://dotclear.watch/';

    /**
     * The default distant API URI.
     *
     * @var     string  DISTANT_API_URL
     */
    public const DISTANT_API_URI = 'api/';

    /**
     * The distant API version.
     *
     * @var     string  DISTANT_API_VERSION
     */
    public const DISTANT_API_VERSION = 'v1';

    /**
     * The hiddens modules IDs.
     *
     * @var     array<int,string>   $hiddens
     */
    private static array $hiddens = [];

    /**
     * Multiblog unique identifiant.
     *
     * @var     string  $uid
     */
    private static string $uid = '';

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
        $defines = App::plugins()->getDefines($strict ? ['state' => ModuleDefine::STATE_ENABLED] : []);
        foreach ($defines as $define) {
            if ($strict && in_array($define->getId(), $hiddens)) {
                continue;
            }
            $modules[(string) $define->getId()] = (string) $define->get('version');
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
        if (App::themes()->isEmpty()) {
            App::themes()->loadModules(App::blog()->themesPath());
        }

        $modules = [];
        $hiddens = self::getHiddens();
        $defines = App::themes()->getDefines($strict ? ['state' => ModuleDefine::STATE_ENABLED] : []);
        foreach ($defines as $define) {
            if ($strict && in_array($define->getId(), $hiddens)) {
                continue;
            }
            $modules[(string) $define->getId()] = (string) $define->get('version');
        }

        return $modules;
    }

    /**
     * Get server software and version.
     *
     * @return  array<string,string> The server info
     */
    public static function getServer(): array
    {
        $res = [
            'name'    => 'undefined',
            'version' => 'undefined',
        ];

        if (!empty($_SERVER['SERVER_SOFTWARE'])) {
            $exp = explode('/', $_SERVER['SERVER_SOFTWARE']);
            if (count($exp) == 2) {
                $res = [
                    'name'    => $exp[0],
                    'version' => $exp[1],
                ];
            }
        }

        return $res;
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
        $rs = App::log()->getLogs([
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
        if (!self::check() || !$force && !self::expired()) {
            return;
        }

        // Build and write content
        self::write($contents = self::contents());

        // Prepare API request
        $response = '';
        $url      = sprintf(self::url(), 'report');
        $path     = '';
        $agent    = My::id() . '/' . (string) App::plugins()->getDefine(My::id())->get('version');
        $header   = 'X-API-Version: ' . static::DISTANT_API_VERSION;
        $params   = [
            'uid'    => self::uid(),
            'key'    => self::key(),
            'report' => $contents
        ];

        try {
            if (false !== ($client = HttpClient::initClient($url, $path))) {
                // Try using Dotcler HTTP client
                $client->setUserAgent($agent);
                $client->useGzip(false);
                $client->setPersistReferers(false);
                $client->setMoreHeader($header);
                $client->post($path, $params);

                $response = $client->getContent();
            } elseif (function_exists('curl_init')) {
                // Try using CURL
                if (false !== ($client = curl_init($url))) {
                    curl_setopt($client, CURLOPT_USERAGENT, $agent);
                    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($client, CURLOPT_POST, true);
                    curl_setopt($client, CURLOPT_HTTPHEADER, [$header]);
                    curl_setopt($client, CURLOPT_POSTFIELDS, $params);

                    $response = curl_exec($client);
                }
            }

            unset($client);
        } catch (Throwable) {
            unset($client);
        }

        // Parse reponse
        $rsp = json_decode((string) $response, true) ?? [];

        if (!isset($rsp['code']) || !isset($rsp['message'])) {
            self::error('Dotclear.watch report failed');
        } elseif ($rsp['code'] != 200) {
            self::error('(' . $rsp['code'] . ') ' . $rsp['message']);
        }
    }

    /**
     * Check if report can be done.
     *
     * @return  bool    True if it can
     */
    private static function check(): bool
    {
        return true; // not yet
    }

    /**
     * Get report key.
     *
     * @return  string  The report key
     */
    private static function key(): string
    {
        return Crypt::hmac(self::uid() . My::id(), App::config()->cryptAlgo());
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

    /**
     * Get blog report uid.
     *
     * @return  string  The blog report uid
     */
    private static function buid(): string
    {
        return md5(self::uid() . App::blog()->uid());
    }

    /**
     * Get query URL.
     *
     * ex: https://blog.url/api/DotclearWatch/report/
     *
     * @return  string  The URL
     */
    private static function url(): string
    {
        $api_url = My::settings()->getGlobal('distant_api_url') ?? self::DISTANT_API_URL;
        if (!str_ends_with($api_url, '/')) {
            $api_url .= '/';
        }
        // Remove old style (< 1.0) API URL
        if (str_ends_with($api_url, 'api/')) {
            $api_url = substr($api_url, 0, -4);
        }

        return $api_url . self::DISTANT_API_URI . My::id() . '/%s/';
    }

    /**
     * Clear report logs.
     */
    private static function clear(): void
    {
        $rs = App::log()->getLogs([
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
        App::log()->delLogs($logs);
    }

    /**
     * Log error.
     */
    private static function error(string $message): void
    {
        self::clear();

        $cur = App::log()->openLogCursor();
        $cur->setField('log_table', My::id() . '_error');
        $cur->setField('log_msg', $message);

        App::log()->addLog($cur);
    }

    /**
     * Write report.
     */
    private static function write(string $contents): void
    {
        self::clear();

        $cur = App::log()->openLogCursor();
        $cur->setField('log_table', My::id() . '_report');
        $cur->setField('log_msg', $contents);

        App::log()->addLog($cur);
    }

    /**
     * Check if report is expired.
     *
     * @return  bool True if expired
     */
    private static function expired(): bool
    {
        $rs = App::log()->getLogs([
            'log_table' => My::id() . '_report',
        ]);

        return $rs->isEmpty() || !is_string($rs->f('log_dt')) || (int) strtotime($rs->f('log_dt')) + self::EXPIRED_DELAY < time();
    }

    /**
     * Get report content.
     *
     * @return  string Teh report content
     */
    private static function contents(): string
    {
        // Build json response
        return (string) json_encode([
            'uid'     => self::uid(),
            'buid'    => self::buid(),
            'plugins' => self::getPlugins(), // enabled plugins
            'themes'  => self::getThemes(), // enabled themes
            'blog'    => [
                'lang'  => (string) App::blog()->settings()->get('system')->get('lang'),
                'theme' => (string) App::blog()->settings()->get('system')->get('theme'),
            ],
            'blogs' => [
                'count' => (int) App::blogs()->getBlogs([], true)->f(0),
            ],
            'core' => [
                'version' => App::config()->dotclearVersion(),
            ],
            'server' => self::getServer(),
            'php'    => [
                'sapi'    => php_sapi_name() ?: 'php',
                'version' => PHP_VERSION,
                'minor'   => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
            ],
            'system' => [
                'name'    => php_uname('s'),
                'version' => php_uname('r'),
            ],
            'database' => [
                'driver'  => App::con()->driver(),
                'version' => App::con()->version(),
            ],
        ], JSON_PRETTY_PRINT);
    }
}
