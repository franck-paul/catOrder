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
if (!defined('DC_RC_PATH')) {
    return;
}

dcCore::app()->addBehavior('coreBlogBeforeGetPosts', ['behaviorCatOrder', 'coreBlogBeforeGetPosts']);

class behaviorCatOrder
{
    public static function coreBlogBeforeGetPosts($params)
    {
        if (dcCore::app()->url->type == 'category') {
            dcCore::app()->blog->settings->addNamespace('catorder');
            if (dcCore::app()->blog->settings->catorder->active) {
                $cat_id = dcCore::app()->ctx->categories->cat_id;
                $orders = dcCore::app()->blog->settings->catorder->orders;
                if (is_array($orders)) {
                    // Specific order set for the category
                    if (array_key_exists($cat_id, $orders)) {
                        if ($orders[$cat_id] != '') {
                            switch ($orders[$cat_id]) {
                                case 'asc':
                                case 'desc':
                                    $params->offsetSet('order', 'post_dt');

                                    break;
                                case 'title-asc':
                                case 'title-desc':
                                    $params->offsetSet('order', dcCore::app()->con->lexFields('post_title'));

                                    break;
                            }
                            switch ($orders[$cat_id]) {
                                case 'asc':
                                case 'title-asc':
                                    $params->offsetSet('order', $params['order'] . ' asc');

                                    break;
                                case 'desc':
                                case 'title-desc':
                                    $params->offsetSet('order', $params['order'] . ' desc');

                                    break;
                            }
                        }
                    }
                }
                $numbers = dcCore::app()->blog->settings->catorder->numbers;
                if (is_array($numbers)) {
                    if (array_key_exists($cat_id, $numbers)) {
                        // Specific number of entry per page set for the category
                        if ($numbers[$cat_id] != '') {
                            dcCore::app()->ctx->nb_entry_per_page = (int) $numbers[$cat_id];
                        }
                    }
                }
            }
        }
    }
}
