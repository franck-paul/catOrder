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

if (!defined('DC_RC_PATH')) {return;}

$core->addBehavior('coreBlogBeforeGetPosts', array('behaviorCatOrder', 'coreBlogBeforeGetPosts'));

class behaviorCatOrder
{
    public static function coreBlogBeforeGetPosts($params)
    {
        global $core, $_ctx;

        if ($core->url->type == 'category') {

            $core->blog->settings->addNamespace('catorder');
            if ($core->blog->settings->catorder->active) {
                $cat_id = $_ctx->categories->cat_id;
                $orders = $core->blog->settings->catorder->orders;
                if (is_array($orders)) {
                	// Specific order set for the category
                    if (array_key_exists($cat_id, $orders)) {
                        if ($orders[$cat_id] != '') {
                        	switch ($orders[$cat_id]) {
                        		case 'asc':
                        		case 'desc';
		                            $params['order'] = 'post_dt';
		                            break;
		                        case 'title-asc':
		                        case 'title-desc':
		                        	$params['order'] = $core->con->lexFields('post_title');
		                        	break;
                        	}
                        	switch ($orders[$cat_id]) {
                        		case 'asc':
                        		case 'title-asc':
                        			$params['order'] .= ' asc';
                        			break;
                        		case 'desc':
                        		case 'title-desc':
                        			$params['order'] .= ' desc';
                        			break;
                        	}
                        }
                    }
                }
                $numbers = $core->blog->settings->catorder->numbers;
                if (is_array($numbers)) {
                    if (array_key_exists($cat_id, $numbers)) {
                    	// Specific number of entry per page set for the category
                        if ($numbers[$cat_id] != '') {
                            $_ctx->nb_entry_per_page = (integer) $numbers[$cat_id];
                        }
                    }
                }
            }
        }
    }
}
