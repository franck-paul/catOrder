<?php
# -- BEGIN LICENSE BLOCK ---------------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2011 Franck Paul
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK -----------------------------------------
if (!defined('DC_RC_PATH')) { return; }

$core->addBehavior('templateBeforeBlock',array('behaviorCatOrder','templateBeforeBlock'));

class behaviorCatOrder
{
	public static function templateBeforeBlock($core,$b,$attr)
	{
		if ($core->url->type == 'category') {
			if ($b == 'Entries' && !isset($attr['no_context'])) {
				return '<?php echo behaviorCatOrder::catOrderHelp(@$params); ?>';
			}
		}
	}

	public static function catOrderHelp($params)
	{
		global $core, $_ctx;
		
		if ($core->blog->settings->catorder->active && ($core->blog->settings->catorder->orders != '')) {
			$orders = unserialize($core->blog->settings->catorder->orders);
			if (is_array($orders)) {
				$cat_id = $_ctx->categories->cat_id;
				if (array_key_exists($cat_id,$orders)) {
					if ($orders[$cat_id] != '') {
						if (is_array($params))
							$params['order'] = 'post_dt '.$orders[$cat_id];
					}
				}
			}
		}
	}
}
?>