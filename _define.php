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
    '2.0',
    [
        'requires'    => [['core', '2.26']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type'     => 'plugin',
        'settings' => [
        ],

        'details'    => 'https://open-time.net/?q=catOrder',
        'support'    => 'https://github.com/franck-paul/catOrder',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/catOrder/master/dcstore.xml',
    ]
);
