<?php

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

        //My::addBackendMenuItem();

        dcCore::app()->addBehaviors([
            'adminDashboardHeaders' => [Utils::class, 'sendReport'],
            'adminPageFooterV2'     => [Utils::class, 'addMark'],
        ]);

        return true;
    }
}
