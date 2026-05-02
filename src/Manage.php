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

use Dotclear\App;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Select;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Table;
use Dotclear\Helper\Html\Form\Tbody;
use Dotclear\Helper\Html\Form\Td;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Th;
use Dotclear\Helper\Html\Form\Thead;
use Dotclear\Helper\Html\Form\Tr;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Process\TraitProcess;
use Exception;

class Manage
{
    use TraitProcess;

    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if ($_POST !== []) {
            try {
                $active = (bool) $_POST['co_active'];

                /**
                 * @var array<int, string>
                 */
                $orders = [];

                /**
                 * @var array<int, string>
                 */
                $numbers = [];

                /**
                 * @var array<string>
                 */
                $catids = is_array($catids = $_POST['co_catid']) ? $catids : [];

                if ($catids !== []) {
                    if (is_array($_POST['co_order']) && $_POST['co_order'] !== []) {
                        $counter = count($_POST['co_order']);
                        for ($i = 0; $i < $counter; ++$i) {
                            $cat_id = is_numeric($cat_id = $catids[$i]) ? (int) $cat_id : 0;
                            if ($cat_id > 0) {
                                $orders[$cat_id] = $_POST['co_order'][$i];
                            }
                        }
                    }

                    if (is_array($_POST['co_number']) && $_POST['co_number'] !== []) {
                        $counter = count($_POST['co_number']);
                        for ($i = 0; $i < $counter; ++$i) {
                            $cat_id = is_numeric($cat_id = $catids[$i]) ? (int) $cat_id : 0;
                            if ($cat_id > 0) {
                                $numbers[$cat_id] = $_POST['co_number'][$i];
                            }
                        }
                    }
                }

                # Everything's fine, save options
                $settings = My::settings();
                $settings->put('active', $active, App::blogWorkspace()::NS_BOOL);
                $settings->put('orders', $orders, App::blogWorkspace()::NS_ARRAY);
                $settings->put('numbers', $numbers, App::blogWorkspace()::NS_ARRAY);

                App::blog()->triggerBlog();

                App::backend()->notices()->addSuccessNotice(__('Settings have been successfully updated.'));
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        $settings = My::settings();

        $active = (bool) $settings->active;

        /**
         * @var array<int, string> key may be numeric string with old registered values, but array_key_exists() is ok with that
         */
        $orders = is_array($orders = $settings->orders) ? $orders : [];

        /**
         * @var array<int, string> key may be numeric string with old registered values, but array_key_exists() is ok with that
         */
        $numbers = is_array($numbers = $settings->numbers) ? $numbers : [];

        $combo = [
            __('Default')             => '',
            __('By date descending')  => 'desc',
            __('By date ascending')   => 'asc',
            __('By title ascending')  => 'title-asc',
            __('By title descending') => 'title-desc',
        ];

        // Prepare lines
        $rs = App::blog()->getCategories(['post_type' => 'post']);
        if ($rs->isEmpty()) {
            $block = (new Note())
                ->text(__('No category yet.'));
        } else {
            $raws = [];
            while ($rs->fetch()) {
                $cat_id = is_numeric($cat_id = $rs->cat_id) ? (int) $cat_id : 0;
                if ($cat_id > 0) {
                    $cat_level = is_numeric($cat_level = $rs->level) ? (int) $cat_level : 1;
                    $cat_title = is_string($cat_title = $rs->cat_title) ? $cat_title : '';

                    $order  = array_key_exists($cat_id, $orders) ? $orders[$cat_id] : '';
                    $number = array_key_exists($cat_id, $numbers) && is_numeric($number = $numbers[$cat_id]) ? (int) $number : 0;

                    $raws[] = (new Tr('cat-' . $cat_id))
                        ->items([
                            (new Td())
                                ->items([
                                    (new Text(null, str_repeat('&nbsp;&nbsp;', $cat_level - 1) . Html::escapeHTML($cat_title))),
                                    (new Hidden(['co_catid[]'], (string) $cat_id)),
                                ]),
                            (new Td())
                                ->items([
                                    (new Select(['co_order[]', 'cat-' . $cat_id]))
                                        ->items($combo)
                                        ->default($order),
                                ]),
                            (new Td())
                                ->items([
                                    (new Number(['co_number[]'], 0, 99_999, $number)),
                                ]),
                        ]);
                }
            }

            $block = (new Table())
                ->thead((new Thead())
                    ->items([
                        (new Th())
                            ->items([
                                (new Text(null, __('Category'))),
                            ]),
                        (new Th())
                            ->items([
                                (new Text(null, __('Order'))),
                            ]),
                        (new Th())
                            ->items([
                                (new Text(null, __('Number of items per page'))),
                            ]),
                    ]))
                ->tbody((new Tbody())
                    ->items($raws));
        }

        App::backend()->page()->openModule(My::name());

        echo App::backend()->page()->breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('Categories entry orders')         => '',
            ]
        );
        echo App::backend()->notices()->getNotices();

        // Form

        echo (new Form('catorder_settings'))
            ->action(App::backend()->getPageURL())
            ->method('post')
            ->fields([
                (new Checkbox('co_active', $active))
                    ->value(1)
                    ->label((new Label(__('Activate user-defined orders for this blog\'s categories'), Label::INSIDE_TEXT_AFTER))),
                (new Text('h3', __('Order and number of entries per page'))),
                (new Note())
                    ->class('form-note')
                    ->text(__('Set order to Default to use the order set by the theme.') . '<br>' . sprintf(__('Leave number blank to use the default blog <a href="%s">parameter</a>.'), App::backend()->url()->get('admin.blog.pref') . '#params.nb_post_per_page')),
                $block,
                // Submit
                (new Para())->items([
                    (new Submit(['frmsubmit']))
                        ->value(__('Save')),
                    ...My::hiddenFields(),
                ]),
            ])
        ->render();

        App::backend()->page()->closeModule();
    }
}
