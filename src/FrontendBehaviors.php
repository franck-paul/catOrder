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

use ArrayObject;
use Dotclear\App;

class FrontendBehaviors
{
    /**
     * @param      ArrayObject<string, mixed>  $params  The parameters
     */
    public static function coreBlogBeforeGetPosts(ArrayObject $params): string
    {
        if (App::url()->getType() === 'category') {
            $settings = My::settings();
            if ($settings->active) {
                $cat_id = App::frontend()->context()->categories->cat_id;
                $orders = $settings->orders;
                if (is_array($orders) && array_key_exists($cat_id, $orders) && $orders[$cat_id] !== '') {
                    // Do not sort other entry types
                    if ($params->offsetExists('post_type') && $params['post_type'] !== 'post') {
                        return '';
                    }
                    // Do not sort if order not present in parameters
                    if ($params->offsetExists('order')) {
                        // Specific order set for the category
                        switch ($orders[$cat_id]) {
                            case 'asc':
                            case 'desc':
                                $params->offsetSet('order', 'post_dt');

                                break;
                            case 'title-asc':
                            case 'title-desc':
                                $params->offsetSet('order', App::db()->con()->lexFields('post_title'));

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

                $numbers = $settings->numbers;
                if (is_array($numbers) && array_key_exists($cat_id, $numbers) && (int) $numbers[$cat_id] > 0) {
                    // Specific number of entry per page set for the category
                    App::frontend()->context()->nb_entry_per_page = (int) $numbers[$cat_id];
                }
            }
        }

        return '';
    }
}
