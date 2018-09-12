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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "Ordering category entries",  // Name
    "Set category entries order", // Description
    "Franck Paul",                // Author
    '0.7',                        // Version
    [
        'requires'    => [['core', '2.14']],
        'permissions' => 'admin',
        'support'     => 'https://open-time.net/?q=catorder', // Support URL
        'type'        => 'plugin'
    ]
);
