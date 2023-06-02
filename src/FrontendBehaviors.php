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

class FrontendBehaviors
{
    public static function coreBlogBeforeGetPosts($params)
    {
        if (dcCore::app()->url->type == 'category') {
            $settings = dcCore::app()->blog->settings->get(My::id());
            if ($settings->active) {
                $cat_id = dcCore::app()->ctx->categories->cat_id;
                $orders = $settings->orders;
                if (is_array($orders) && array_key_exists($cat_id, $orders) && $orders[$cat_id] != '') {
                    // Specific order set for the category
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
                $numbers = $settings->numbers;
                if (is_array($numbers) && array_key_exists($cat_id, $numbers) && $numbers[$cat_id] != '') {
                    // Specific number of entry per page set for the category
                    dcCore::app()->ctx->nb_entry_per_page = (int) $numbers[$cat_id];
                }
            }
        }
    }
}
