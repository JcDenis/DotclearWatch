<?php

declare(strict_types=1);

namespace Dotclear\Plugin\DotclearWatch;

use Dotclear\App;
use Dotclear\Module\MyPlugin;

/**
 * @brief       DotclearWatch My helper.
 * @ingroup     DotclearWatch
 *
 * @author      Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class My extends MyPlugin
{
    public static function checkCustomContext(int $context): ?bool
    {
        return match ($context) {
            // The compatibility audit checks the modules of the whole installation: super admin only
            self::MENU, self::MANAGE => App::task()->checkContext('BACKEND')
                && App::blog()->isDefined()
                && App::auth()->isSuperAdmin(),

            default => null,
        };
    }
}
