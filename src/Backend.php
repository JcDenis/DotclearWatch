<?php

declare(strict_types=1);

namespace Dotclear\Plugin\DotclearWatch;

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;
use Dotclear\Helper\Html\Form\Img;
use Dotclear\Helper\Html\Form\Li;
use Dotclear\Helper\Html\Form\Link;
use Dotclear\Helper\Html\Form\Ul;

/**
 * @brief   DotclearWatch backend class.
 * @ingroup DotclearWatch
 *
 * @author      Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Backend
{
    use TraitProcess;

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
            // Add JS for asynchronous report sending
            'adminDashboardHeaders' => function (): string {
                return My::jsLoad('service', App::version()->getVersion(My::id()));
            },
            // Add icon on bottom of dashboard sidebar menu
            'adminPageFooterV2' => function (): void {
                if (My::settings()->getGlobal('distant_api_url')) {
                    echo (new Ul())->items([
                        (new li())->items([
                            (new Link())
                                ->class('outgoing')
                                ->href('https://stat.dotclear.watch')
                                ->title(__('Uses DotclearWatch plugin statistics'))
                                ->text(__('Shared statistics'))
                                ->items([
                                    (new Img(My::fileURL('icon.svg'))),
                                ]),
                        ]),
                    ])->render();
                }
            },
        ]);

        App::rest()->addFunction(
            // Add REST service for asynchronous report sending
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
