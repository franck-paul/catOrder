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

$new_version = dcCore::app()->plugins->moduleInfo('catOrder', 'version');
$old_version = dcCore::app()->getVersion('catOrder');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try {
    if (version_compare($old_version, '0.4') < 0) {
        // Convert oldschool settings
        dcUpgrade::settings2array('catorder', 'orders');
    }

    // Create namespace if necessary
    dcCore::app()->blog->settings->addNamespace('catorder');

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

    dcCore::app()->setVersion('catOrder', $new_version);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
