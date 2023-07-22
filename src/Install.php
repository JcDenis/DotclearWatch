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

use Dotclear\Core\Process;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        $s = My::settings();
        if (self::status() && $s !== null) {
            $s->put(
                'hidden_modules',
                'DotclearWatch',
                'string',
                'Hidden modules from report',
                false,
                true
            );
            $s->put(
                'distant_api_url',
                'https://dotclear.watch/report',
                'string',
                'Distant API report URL',
                false,
                true
            );
        }

        return self::status();
    }
}
