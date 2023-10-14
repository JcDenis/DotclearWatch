<?php
/**
 * @file
 * @brief       The plugin DotclearWatch definition
 * @ingroup     DotclearWatch
 *
 * @defgroup    DotclearWatch Plugin DotclearWatch.
 *
 * QSend report about your Dotclear.
 *
 * @author      Jean-Christian Denis
 * @copyright   Jean-Christian Denis
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

$this->registerModule(
    'Dotclear Watch',
    'Send report about your Dotclear',
    'Jean-Christian Denis and contributors',
    '0.9.2',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://git.dotclear.watch/dw/' . basename(__DIR__) . '/issues',
        'details'     => 'https://git.dotclear.watch/dw/' . basename(__DIR__) . '/src/branch/master/README.md',
        'repository'  => 'https://git.dotclear.watch/dw/' . basename(__DIR__) . '/raw/branch/master/dcstore.xml',
    ]
);
