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
use Dotclear\Core\Process;

class Backend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        dcCore::app()->addBehaviors([
            'adminDashboardHeaders' => function (): string {
                return My::jsLoad('service', dcCore::app()->getVersion(My::id()));
            },
            'adminPageFooterV2' => [Utils::class, 'addMark'],
        ]);

        dcCore::app()->rest->addFunction(
            'adminDotclearWatchSendReport',
            function (): array {
                Utils::sendReport();

                return [
                    'ret' => true,
                    'msg' => 'report sent',
                ];
            },
        );

        return true;
    }
}
