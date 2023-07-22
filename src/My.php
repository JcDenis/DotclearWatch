<?php

declare(strict_types=1);

namespace Dotclear\Plugin\DotclearWatch;

use dcCore;
use Dotclear\Module\MyPlugin;

class My extends MyPlugin
{
    protected static function checkCustomContext(int $context): ?bool
    {
        return $context === My::INSTALL ? null :
            defined('DC_CONTEXT_ADMIN') && dcCore::app()->auth->isSuperAdmin();
    }
}
