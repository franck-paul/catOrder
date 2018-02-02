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

$core->blog->settings->addNamespace('catorder');
$co_active  = (boolean) $core->blog->settings->catorder->active;
$co_orders  = $core->blog->settings->catorder->orders;
$co_numbers = $core->blog->settings->catorder->numbers;
if (!is_array($co_orders)) {
    $co_orders = array();
}
if (!is_array($co_numbers)) {
    $co_numbers = array();
}

if (!empty($_POST)) {
    try
    {
        $co_active = (boolean) $_POST['co_active'];
        $co_orders = array();
        if (!empty($_POST['co_order'])) {
            for ($i = 0; $i < count($_POST['co_order']); $i++) {
                $co_orders[$_POST['co_catid'][$i]] = $_POST['co_order'][$i];
            }
        }
        $co_numbers = array();
        if (!empty($_POST['co_number'])) {
            for ($i = 0; $i < count($_POST['co_number']); $i++) {
                $co_numbers[$_POST['co_catid'][$i]] = $_POST['co_number'][$i];
            }
        }

        # Everything's fine, save options
        $core->blog->settings->addNamespace('catorder');
        $core->blog->settings->catorder->put('active', $co_active);
        $core->blog->settings->catorder->put('orders', $co_orders);
        $core->blog->settings->catorder->put('numbers', $co_numbers);

        //$core->emptyTemplatesCache();
        $core->blog->triggerBlog();

        dcPage::addSuccessNotice(__('Settings have been successfully updated.'));
        http::redirect($p_url);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

$co_combo = array(
    __('Default')             => '',
    __('By date descending')  => 'desc',
    __('By date ascending')   => 'asc',
    __('By title ascending')  => 'title-asc',
    __('By title descending') => 'title-desc'
);

?>
<html>
<head>
	<title><?php echo __('Categories entry orders'); ?></title>
</head>

<body>
<?php
echo dcPage::breadcrumb(
    array(
        html::escapeHTML($core->blog->name) => '',
        __('Categories entry orders')       => ''
    ));
echo dcPage::notices();

echo
'<form action="' . $p_url . '" method="post">' .
'<p>' . form::checkbox('co_active', 1, $co_active) . ' ' .
'<label for="co_active" class="classic">' . __('Activate user-defined orders for this blog\'s categories') . '</label>' .
    '</p>';

echo
'<h3>' . __('Order and number of entries per page') . '</h3>' .
'<p class="form-note">' . __('Set order to Default to use the order set by the theme.') . '</p>' .
'<p class="form-note">' . sprintf(__('Leave number blank to use the default blog <a href="%s">parameter</a>.'),
    $core->adminurl->get('admin.blog.pref') . '#params.nb_post_per_page') . '</p>';

$rs = $core->blog->getCategories(array('post_type' => 'post'));
if ($rs->isEmpty()) {
    echo '<p>' . __('No category yet.') . '</p>';
} else {
    echo '<ul>';
    while ($rs->fetch()) {
        $order  = (array_key_exists($rs->cat_id, $co_orders) ? $co_orders[$rs->cat_id] : '');
        $number = (array_key_exists($rs->cat_id, $co_numbers) ? $co_numbers[$rs->cat_id] : '');
        echo '<li id="cat-' . $rs->cat_id . '">' .
        form::hidden(array('co_catid[]'), $rs->cat_id) .
        '<p class="field">' .
        '<label>' . html::escapeHTML($rs->cat_title) . '</label>' .
        form::combo(array('co_order[]'), $co_combo, $order) . ' ' .
        form::field(array('co_number[]'), 2, 5, (string) $number) .
            '</p>' .
            '</li>';
    }
    echo '</ul>';
}

echo
'<p>' . $core->formNonce() . '<input type="submit" value="' . __('Save') . '" /></p>' .
    '</form>';

?>
</body>
</html>
