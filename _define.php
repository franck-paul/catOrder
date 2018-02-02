<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of catOrder, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "Ordering category entries",  // Name
    "Set category entries order", // Description
    "Franck Paul",                // Author
    '0.6',                        // Version
    array(
        'requires'    => array(array('core', '2.11')),
        'permissions' => 'admin',
        'support'     => 'https://open-time.net/?q=catorder', // Support URL
        'type'        => 'plugin'
    )
);
