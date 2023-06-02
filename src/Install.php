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
declare(strict_types=1);

namespace Dotclear\Plugin\catOrder;

use dcCore;
use dcNamespace;
use dcNsProcess;
use dcUpgrade;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::INSTALL);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            // Update
            $old_version = dcCore::app()->getVersion(My::id());
            if (version_compare((string) $old_version, '0.4') < 0) {
                // Convert oldschool settings
                dcUpgrade::settings2array('catorder', 'orders');
            }
            if (version_compare((string) $old_version, '2.0', '<')) {
                // Rename settings namespace
                if (dcCore::app()->blog->settings->exists('catorder')) {
                    dcCore::app()->blog->settings->delNamespace(My::id());
                    dcCore::app()->blog->settings->renNamespace('catorder', My::id());
                }
            }

            // Chech if settings exist, create them if not
            $settings = dcCore::app()->blog->settings->get(My::id());
            if (!$settings->getGlobal('active')) {
                $settings->put('active', false, dcNamespace::NS_BOOL, 'Active', false, true);
            }
            if (!$settings->getGlobal('orders')) {
                $settings->put('orders', [], dcNamespace::NS_ARRAY, 'Categories order', false, true);
            }
            if (!$settings->getGlobal('numbers')) {
                $settings->put('numbers', [], dcNamespace::NS_ARRAY, 'Categories nb of entries per page', false, true);
            }
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
