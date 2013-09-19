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

if (!defined('DC_RC_PATH')) { return; }

$core->addBehavior('coreBlogBeforeGetPosts',array('behaviorCatOrder','coreBlogBeforeGetPosts'));

class behaviorCatOrder
{
	public static function coreBlogBeforeGetPosts($params)
	{
		global $core, $_ctx;
		
		if ($core->url->type == 'category') {

			$core->blog->settings->addNamespace('catorder');
			if ($core->blog->settings->catorder->active && ($core->blog->settings->catorder->orders != '')) {

				$orders = unserialize($core->blog->settings->catorder->orders);
				if (is_array($orders)) {
					$cat_id = $_ctx->categories->cat_id;
					if (array_key_exists($cat_id,$orders)) {
						if ($orders[$cat_id] != '') {
							$params['order'] = 'post_dt '.$orders[$cat_id];
						}
					}
				}

			}
		}
	}
}
?>