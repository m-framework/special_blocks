<?php

namespace modules\special_blocks\admin;

use m\module;
use m\view;
use m\config;
use modules\pages\models\pages;
use modules\special_blocks\models\special_blocks;

class blocks_on_page extends module {

    protected static $module = 'special_blocks';

    public function _init()
    {
        config::set('per_page', 1000);

        $conditions = [
            'site' => $this->site->id,
            [['language' => $this->language_id], ['language' => null]],
            'module' => static::$module,
        ];

        if (!empty($this->get->page) && (int)$this->get->page > 0) {
            $conditions['page'] = (int)$this->get->page;
        }

        $items = special_blocks::call_static()->s([], $conditions, [1000])->all();

        $arr = [];

        if (!empty($items)) {
            foreach ($items as $item) {

                $page = empty($item['page']) ? '' : new pages($item['page']);

                $arr[] = $this->view->{'overview_blocks_on_page_item'}->prepare([
                    'id' => $item['id'],
                    'title' => !empty($item['title']) ? $item['title'] : '',
                    'link' => !empty($item['link']) ? $item['link'] : '#',
                    'module' => static::$module,
                    'page' => $item['page'],
                    'page_name' => empty($page) ? '*' : $page->name,
                ]);
            }
        }

        view::set('content', $this->view->overview_blocks_on_page->prepare([
                'items' => implode("\n", $arr),
            ]));
    }
}