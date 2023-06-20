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
use dcNamespace;
use dcNsProcess;
use dcPage;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Select;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Html;
use Exception;

class Manage extends dcNsProcess
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::MANAGE);

        return static::$init;
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        if (!empty($_POST)) {
            try {
                $co_active = (bool) $_POST['co_active'];
                $co_orders = [];
                if (!empty($_POST['co_order'])) {
                    for ($i = 0; $i < (is_countable($_POST['co_order']) ? count($_POST['co_order']) : 0); $i++) {
                        $co_orders[$_POST['co_catid'][$i]] = $_POST['co_order'][$i];
                    }
                }
                $co_numbers = [];
                if (!empty($_POST['co_number'])) {
                    for ($i = 0; $i < (is_countable($_POST['co_number']) ? count($_POST['co_number']) : 0); $i++) {
                        $co_numbers[$_POST['co_catid'][$i]] = $_POST['co_number'][$i];
                    }
                }

                # Everything's fine, save options
                $settings = dcCore::app()->blog->settings->get(My::id());
                $settings->put('active', $co_active, dcNamespace::NS_BOOL);
                $settings->put('orders', $co_orders, dcNamespace::NS_ARRAY);
                $settings->put('numbers', $co_numbers, dcNamespace::NS_ARRAY);

                dcCore::app()->blog->triggerBlog();

                dcPage::addSuccessNotice(__('Settings have been successfully updated.'));
                dcCore::app()->adminurl->redirect('admin.plugin.' . My::id());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!static::$init) {
            return;
        }

        $settings   = dcCore::app()->blog->settings->get(My::id());
        $co_active  = (bool) $settings->active;
        $co_orders  = $settings->orders;
        $co_numbers = $settings->numbers;
        if (!is_array($co_orders)) {
            $co_orders = [];
        }
        if (!is_array($co_numbers)) {
            $co_numbers = [];
        }

        $co_combo = [
            __('Default')             => '',
            __('By date descending')  => 'desc',
            __('By date ascending')   => 'asc',
            __('By title ascending')  => 'title-asc',
            __('By title descending') => 'title-desc',
        ];

        // Prepare lines
        $rs = dcCore::app()->blog->getCategories(['post_type' => 'post']);
        if ($rs->isEmpty()) {
            $block = (new Para())->items([
                (new Text(null, __('No category yet.'))),
            ]);
        } else {
            $raws = [];
            while ($rs->fetch()) {
                $order  = (array_key_exists($rs->cat_id, $co_orders) ? $co_orders[$rs->cat_id] : '');
                $number = (array_key_exists($rs->cat_id, $co_numbers) ? $co_numbers[$rs->cat_id] : '');

                $raws[] = (new Para('cat-' . $rs->cat_id, 'tr'))
                    ->items([
                        (new Para(null, 'td'))->items([
                            (new Text(null, str_repeat('&nbsp;&nbsp;', $rs->level - 1) . Html::escapeHTML($rs->cat_title))),
                            (new Hidden(['co_catid[]'], $rs->cat_id)),
                        ]),
                        (new Para(null, 'td'))->items([
                            (new Select(['co_order[]', 'cat-' . $rs->cat_id]))
                                ->items($co_combo)
                                ->default($order),
                        ]),
                        (new Para(null, 'td'))->items([
                            (new Number(['co_number[]'], 0, 99_999, (int) $number)),
                        ]),
                    ]);
            }
            $block = (new Para(null, 'table'))
                ->items([
                    (new Para(null, 'thead'))->items([
                        (new Para(null, 'th'))->items([
                            (new Text(null, __('Category'))),
                        ]),
                        (new Para(null, 'th'))->items([
                            (new Text(null, __('Order'))),
                        ]),
                        (new Para(null, 'th'))->items([
                            (new Text(null, __('Number of items per page'))),
                        ]),
                    ]),
                    (new Para(null, 'tbody'))->items($raws),
                ]);
        }

        dcPage::openModule(__('Categories entry orders'));

        echo dcPage::breadcrumb(
            [
                Html::escapeHTML(dcCore::app()->blog->name) => '',
                __('Categories entry orders')               => '',
            ]
        );
        echo dcPage::notices();

        // Form

        echo (new Form('catorder_settings'))
            ->action(dcCore::app()->admin->getPageURL())
            ->method('post')
            ->fields([
                (new Checkbox('co_active', $co_active))
                    ->value(1)
                    ->label((new Label(__('Activate user-defined orders for this blog\'s categories'), Label::INSIDE_TEXT_AFTER))),
                (new Text('h3', __('Order and number of entries per page'))),
                (new Para())->class('form-note')->items([
                    (new Text(null, __('Set order to Default to use the order set by the theme.') . '<br />' . sprintf(__('Leave number blank to use the default blog <a href="%s">parameter</a>.'), dcCore::app()->adminurl->get('admin.blog.pref') . '#params.nb_post_per_page'))),
                ]),
                $block,
                // Submit
                (new Para())->items([
                    (new Submit(['frmsubmit']))
                        ->value(__('Save')),
                    dcCore::app()->formNonce(false),
                ]),
            ])
        ->render();

        dcPage::closeModule();
    }
}
