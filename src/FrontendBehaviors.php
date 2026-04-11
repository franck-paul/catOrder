<?php

/**
 * @brief catOrder, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\catOrder;

use ArrayObject;
use Dotclear\App;
use Dotclear\Database\MetaRecord;

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
                $cat_id = App::frontend()->context()->categories instanceof MetaRecord && is_numeric($cat_id = App::frontend()->context()->categories->cat_id) ? (int) $cat_id : 0;
                if ($cat_id > 0) {
                    /**
                     * @var array<int, string> key may be numeric string with old registered values, but array_key_exists() is ok with that
                     */
                    $orders = is_array($orders = $settings->orders) ? $orders : [];

                    /**
                     * @var array<int, string> key may be numeric string with old registered values, but array_key_exists() is ok with that
                     */
                    $numbers = is_array($numbers = $settings->numbers) ? $numbers : [];

                    $order  = array_key_exists($cat_id, $orders) ? $orders[$cat_id] : '';
                    $number = array_key_exists($cat_id, $numbers) && is_numeric($number = $numbers[$cat_id]) ? (int) $number : 0;

                    if ($order !== '') {
                        // Do not sort other entry types
                        if ($params->offsetExists('post_type') && $params['post_type'] !== 'post') {
                            return '';
                        }
                        // Do not sort if order not present in parameters
                        if ($params->offsetExists('order')) {
                            $field     = 'post_dt';
                            $direction = 'desc';

                            // Specific order set for the category
                            switch ($order) {
                                case 'asc':
                                case 'desc':
                                    $field = 'post_dt';

                                    break;
                                case 'title-asc':
                                case 'title-desc':
                                    $field = App::db()->con()->lexFields('post_title');

                                    break;
                            }

                            switch ($order) {
                                case 'asc':
                                case 'title-asc':
                                    $direction = 'asc';

                                    break;
                                case 'desc':
                                case 'title-desc':
                                    $direction = 'desc';

                                    break;
                            }
                            $params->offsetSet('order', $field . ' ' . $direction);
                        }
                    }

                    if ($number > 0) {
                        // Specific number of entry per page set for the category
                        App::frontend()->context()->nb_entry_per_page = $number;
                    }
                }
            }
        }

        return '';
    }
}
