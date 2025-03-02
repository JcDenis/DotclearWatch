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
    '0.9.4',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://github.com/JcDenis/' . $this->id . '/issues',
        'details'     => 'https://github.com/JcDenis/' . $this->id . '/',
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/' . $this->id . '/master/dcstore.xml',
        'date'        => '2025-02-24T23:31:12+00:00',
    ]
);
