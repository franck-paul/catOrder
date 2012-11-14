<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

$core->blog->settings->addNamespace('catOrder');
$co_active = (boolean) $core->blog->settings->catorder->active;
$co_orders = @unserialize($core->blog->settings->catorder->orders);
if (!is_array($co_orders)) {
	$co_orders = array();
}

if (!empty($_POST))
{
	try
	{
		$co_active = (boolean) $_POST['co_active'];
		$co_orders = array();
		if (!empty($_POST['co_order'])) {
			for ($i = 0; $i < count($_POST['co_order']); $i++) {
				$co_orders[$_POST['co_catid'][$i]] = $_POST['co_order'][$i];
			}
		}
		
		# Everything's fine, save options
		$core->blog->settings->addNamespace('catorder');
		$core->blog->settings->catorder->put('active',$co_active);
		$core->blog->settings->catorder->put('orders',serialize($co_orders));
		
		//$core->emptyTemplatesCache();
		$core->blog->triggerBlog();
		
		http::redirect($p_url.'&upd=1');
	}
	catch (Exception $e)
	{
		$core->error->add($e->getMessage());
	}
}

$co_combo = array(
	__('Default') => '',
	__('By date descending') => 'desc',
	__('By date ascending') => 'asc'
);

?>
<html>
<head>
	<title><?php echo __('Categories entry orders'); ?></title>
</head>

<body>
<?php
echo '<h2>'.html::escapeHTML($core->blog->name).' &rsaquo; '.__('Categories entry orders').'</h2>';

if (!empty($_GET['upd'])) {
	dcPage::message(__('Settings have been successfully updated.'));
}

echo
'<form action="'.$p_url.'" method="post">'.
'<fieldset><legend>'.__('Activation').'</legend>'.
'<p class="field"><label for="co_active">'.__('Active:').'</label> '.
form::checkbox('co_active',1,$co_active).'</p>'.
'</fieldset>';

echo
'<fieldset><legend>'.__('Orders').'</legend>';

$rs = $core->blog->getCategories(array('post_type'=>'post'));
if ($rs->isEmpty()) {
	echo '<p>'.__('No category yet.').'</p>';
} else {
	echo '<ul>';
	while ($rs->fetch()) {
		$order = (array_key_exists($rs->cat_id,$co_orders) ? $co_orders[$rs->cat_id] : '');
		echo '<li id="cat-"'.$rs->cat_id.'>'.
			form::hidden(array('co_catid[]'),$rs->cat_id).
			'<p class="field">'.
			'<label>'.html::escapeHTML($rs->cat_title).'</label>'.
			form::combo(array('co_order[]'),$co_combo,$order).
			'</p>'.
			'</li>';
	}
	echo '</ul>';
}

echo 
'</fieldset>'.
'<p>'.$core->formNonce().'<input type="submit" value="'.__('Save').'" /></p>'.
'</form>';

?>
</body>
</html>