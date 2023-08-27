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
$this->registerModule(
    'Dotclear Watch',
    'Send report about your Dotclear',
    'Jean-Christian Denis and contributors',
    '0.8',
    [
        'requires' => [
            ['php', '7.4'],
            ['core', '2.27'],
        ],
        'type'       => 'plugin',
        'support'    => 'https://git.dotclear.watch/dw/' . basename(__DIR__) . '/issues',
        'details'    => 'https://git.dotclear.watch/dw/' . basename(__DIR__) . '/src/branch/master/README.md',
        'repository' => 'https://git.dotclear.watch/dw/' . basename(__DIR__) . '/raw/branch/master/dcstore.xml',
    ]
);
