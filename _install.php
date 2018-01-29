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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$new_version = $core->plugins->moduleInfo('catOrder', 'version');
$old_version = $core->getVersion('catOrder');

if (version_compare($old_version, $new_version, '>=')) {return;}

try
{
    if (version_compare($old_version, '0.4') < 0) {
        // Convert oldschool settings
        dcUpgrade::settings2array('catorder', 'orders');
    }

    // Create namespace if necessary
    $core->blog->settings->addNamespace('catorder');

    // Chech if settings exist, create them if not
    if (!$core->blog->settings->catorder->getGlobal('active')) {
        $core->blog->settings->catorder->put('active', false, 'boolean', 'Active', false, true);
    }
    if (!$core->blog->settings->catorder->getGlobal('orders')) {
        $core->blog->settings->catorder->put('orders', array(), 'array', 'Categories order', false, true);
    }
    if (!$core->blog->settings->catorder->getGlobal('numbers')) {
        $core->blog->settings->catorder->put('numbers', array(), 'array', 'Categories nb of entries per page', false, true);
    }

    $core->setVersion('catOrder', $new_version);

    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}
return false;
