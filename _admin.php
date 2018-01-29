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

// dead but useful code, in order to have translations
__('Ordering category entries') . __('Set category entries order');

$_menu['Blog']->addItem(__('Categories orders'), 'plugin.php?p=catOrder',
    urldecode(dcPage::getPF('catOrder/icon.png')),
    preg_match('/plugin.php\?p=catOrder(&.*)?$/', $_SERVER['REQUEST_URI']),
    $core->auth->check('admin', $core->blog->id));

/* Register favorite */
$core->addBehavior('adminDashboardFavorites', array('adminCatOrder', 'adminDashboardFavorites'));

class adminCatOrder
{
    public static function adminDashboardFavorites($core, $favs)
    {
        $favs->register('catOrder', array(
            'title'       => __('Categories orders'),
            'url'         => 'plugin.php?p=catOrder',
            'small-icon'  => urldecode(dcPage::getPF('catOrder/icon.png')),
            'large-icon'  => urldecode(dcPage::getPF('catOrder/icon-big.png')),
            'permissions' => 'admin'
        ));
    }
}
