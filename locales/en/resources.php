<?php

/**
 * @file
 * @brief       The plugin DotclearWatch help resource
 * @ingroup     DotclearWatch
 *
 * @author      Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

\Dotclear\App::backend()->resources()
    ->set('help', 'DotclearWatch', __DIR__ . '/help/help.html');
