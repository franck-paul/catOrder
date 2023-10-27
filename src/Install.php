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

use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Core\Upgrade\Upgrade;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            // Update
            $old_version = App::version()->getVersion(My::id());
            if (version_compare((string) $old_version, '0.4') < 0) {
                // Convert oldschool settings
                Upgrade::settings2array('catorder', 'orders');
            }

            // Rename settings namespace
            if (version_compare((string) $old_version, '2.0', '<') && App::blog()->settings()->exists('catorder')) {
                App::blog()->settings()->delWorkspace(My::id());
                App::blog()->settings()->renWorkspace('catorder', My::id());
            }

            // Chech if settings exist, create them if not
            $settings = My::settings();
            if (!$settings->getGlobal('active')) {
                $settings->put('active', false, App::blogWorkspace()::NS_BOOL, 'Active', false, true);
            }

            if (!$settings->getGlobal('orders')) {
                $settings->put('orders', [], App::blogWorkspace()::NS_ARRAY, 'Categories order', false, true);
            }

            if (!$settings->getGlobal('numbers')) {
                $settings->put('numbers', [], App::blogWorkspace()::NS_ARRAY, 'Categories nb of entries per page', false, true);
            }
        } catch (Exception $exception) {
            App::error()->add($exception->getMessage());
        }

        return true;
    }
}
