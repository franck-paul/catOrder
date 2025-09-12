<?php

/**
 * @brief catOrder, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'Ordering category entries',
    'Set category entries order',
    'Franck Paul',
    '5.0.2',
    [
        'date'        => '2025-08-31T11:36:12+0200',
        'requires'    => [['core', '2.34']],
        'permissions' => '',
        'type'        => 'plugin',
        'settings'    => [
        ],

        'details'    => 'https://open-time.net/?q=catOrder',
        'support'    => 'https://github.com/franck-paul/catOrder',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/catOrder/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
