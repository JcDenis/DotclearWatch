<?php

declare(strict_types=1);

namespace Dotclear\Plugin\DotclearWatch;

use Dotclear\App;
use Dotclear\Core\Process;

/**
 * @brief   DotclearWatch backend class.
 * @ingroup DotclearWatch
 *
 * @author      Jean-Christian Denis
 * @copyright   Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
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

        App::behavior()->addBehaviors([
            'adminDashboardHeaders' => function (): string {
                return My::jsLoad('service', App::version()->getVersion(My::id()));
            },
            'adminPageFooterV2' => function (): void {
                if (My::settings()->getGlobal('distant_api_url')) {
                    echo sprintf(
                        '<ul><li><a href="%s" title="%s" class="outgoing">%s<img src="%s" /></a></ul></li>',
                        'https://stat.dotclear.watch',
                        __('Uses DotclearWatch plugin statistics'),
                        __('Shared statistics'),
                        My::fileURL('icon.svg')
                    );
                }
            },
        ]);

        App::rest()->addFunction(
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
