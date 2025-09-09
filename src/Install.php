<?php

declare(strict_types=1);

namespace Dotclear\Plugin\DotclearWatch;

use Dotclear\Helper\Process\TraitProcess;

/**
 * @brief       DotclearWatch install class.
 * @ingroup     DotclearWatch
 *
 * @author      Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Install
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (self::status()) {
            My::settings()->put(
                'hidden_modules',
                'DotclearWatch',
                'string',
                'Hidden modules from report',
                false,
                true
            );
            My::settings()->put(
                'distant_api_url',
                'https://dotclear.watch/api',
                'string',
                'Distant API URL',
                false,
                true
            );
        }

        return self::status();
    }
}
