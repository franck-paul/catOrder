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

// dead but useful code, in order to have translations
__('Ordering category entries') . __('Set category entries order');

$_menu['Blog']->addItem(
    __('Categories orders'),
    'plugin.php?p=catOrder',
    [urldecode(dcPage::getPF('catOrder/icon.svg')), urldecode(dcPage::getPF('catOrder/icon-dark.svg'))],
    preg_match('/plugin.php\?p=catOrder(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check('admin', dcCore::app()->blog->id)
);

/* Register favorite */
dcCore::app()->addBehavior('adminDashboardFavorites', ['adminCatOrder', 'adminDashboardFavorites']);

class adminCatOrder
{
    public static function adminDashboardFavorites($core, $favs)
    {
        $favs->register('catOrder', [
            'title'      => __('Categories orders'),
            'url'        => 'plugin.php?p=catOrder',
            'small-icon' => [
                urldecode(dcPage::getPF('catOrder/icon.svg')),
                urldecode(dcPage::getPF('catOrder/icon-dark.svg')), ],
            'large-icon' => [
                urldecode(dcPage::getPF('catOrder/icon.svg')),
                urldecode(dcPage::getPF('catOrder/icon-dark.svg')), ],
            'permissions' => 'admin',
        ]);
    }
}
