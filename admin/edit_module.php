<?php

namespace modules\special_blocks\admin;

use m\config;
use m\core;
use m\module;
use m\i18n;
use m\registry;
use m\view;
use m\form;
use modules\special_blocks\models\special_blocks;
use modules\pages\models\pages;
use modules\pages\models\pages_types;
use modules\pages\models\pages_types_modules;
use modules\sites\models\sites;

class edit_module extends module {

    protected $type_related = [
        ['value' => 'catalog_blocks', 'name' => '*Catalog block*'],
        ['value' => 'footer_blocks', 'name' => '*Footer block*'],
        ['value' => 'home_big_block', 'name' => '*Home big block*'],
        ['value' => 'home_blocks', 'name' => '*Home block*'],
        ['value' => 'side_blocks', 'name' => '*Side block*'],
        ['value' => 'top_blocks', 'name' => '*Top block*'],
        ['value' => 'bottom_blocks', 'name' => '*Bottom block*'],
    ];

    public function _init()
    {
        if (!isset($this->view->{'special_block_' . $this->name . '_form'})) {
            return false;
        }

        $item = new special_blocks(!empty($this->get->edit) ? $this->get->edit : null);

        if (!empty($item->id)) {
            view::set('page_title', '<h1><i class="fa fa-list-alt"></i> *Edit a special block* ' . (empty($item->title) ? '' : '`' . $item->title . '`') . '</h1>');
            registry::set('title', i18n::get('Edit a special block'));

            registry::set('breadcrumbs', [
                '/' . config::get('admin_panel_alias') . '/special-blocks' => '*Special blocks*',
                '/' . config::get('admin_panel_alias') . '/special-blocks/' . str_replace('_blocks', '', $item->module) => $item->get_type_name(),
                '/' . config::get('admin_panel_alias') . '/special-blocks/edit/' . $item->id => '*Edit a special block*',
            ]);
        }
        else {
            view::set('page_title', '<h1><i class="fa fa-list-alt"></i> *Add new special block*</h1>');
            registry::set('title', i18n::get('Add new special block'));
        }

        if (empty($item->site)) {
            $item->site = $this->site->id;
        }
        if (empty($item->language)) {
            $item->language = (string)$this->language_id;
        }

        //$pages_tree = $this->page->get_pages_tree();
        $this->page->prepare_page([]);
        $pages_tree = $this->page->get_pages_tree();

//        if (empty($pages_tree)) {
//        }

        $pages_arr = empty($pages_tree) ? [] : pages::options_arr_recursively($pages_tree, '');


        new form(
            $item,
            [
                'module' => [
                    'field_name' => i18n::get('Block type'),
                    'related' => $this->type_related,
                    'required' => 1,
                ],
                'page' => [
                    'field_name' => i18n::get('Page'),
                    'related' => $pages_arr,
                ],
                'title' => [
                    'type' => 'varchar',
                    'field_name' => i18n::get('Title'),
                ],
                'content' => [
                    'type' => 'text',
                    'field_name' => i18n::get('Content'),
                ],
                'link' => [
                    'type' => 'varchar',
                    'field_name' => i18n::get('Link'),
                ],
                'image' => [
                    'type' => 'file_path',
                    'field_name' => i18n::get('Image'),
                ],
                'language' => [
                    'type' => 'hidden',
                    'field_name' => '',
                ],
            ],
            [
                'form' => $this->view->{'special_block_' . $this->name . '_form'},
                'varchar' => $this->view->edit_row_varchar,
                'text' => $this->view->edit_row_text,
                'related' => $this->view->edit_row_related,
                'file_path' => $this->view->edit_row_file_path,
                'hidden' => $this->view->edit_row_hidden,
                'saved' => $this->view->edit_row_saved,
                'error' => $this->view->edit_row_error,
            ]
        );
    }
}