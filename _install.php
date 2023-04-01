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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    $old_version = dcCore::app()->getVersion(basename(__DIR__));

    if (version_compare((string) $old_version, '0.4') < 0) {
        // Convert oldschool settings
        dcUpgrade::settings2array('catorder', 'orders');
    }

    // Chech if settings exist, create them if not
    if (!dcCore::app()->blog->settings->catorder->getGlobal('active')) {
        dcCore::app()->blog->settings->catorder->put('active', false, 'boolean', 'Active', false, true);
    }
    if (!dcCore::app()->blog->settings->catorder->getGlobal('orders')) {
        dcCore::app()->blog->settings->catorder->put('orders', [], 'array', 'Categories order', false, true);
    }
    if (!dcCore::app()->blog->settings->catorder->getGlobal('numbers')) {
        dcCore::app()->blog->settings->catorder->put('numbers', [], 'array', 'Categories nb of entries per page', false, true);
    }

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
