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
    '0.1',
    [
        'requires' => [
            ['php', '7.4'],
            ['core', '2.27'],
        ],
        'type'       => 'plugin',
        'support'    => 'https://github.com/JcDenis/DotclearWatch/issues',
        'details'    => 'http://plugins.dotaddict.org/dc2/details/DotclearWatch',
        'repository' => 'https://raw.githubusercontent.com/JcDenis/DotclearWatch/master/dcstore.xml',
    ]
);
