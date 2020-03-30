<?php

namespace modules\special_blocks\admin;

use m\module;
use m\view;
use m\i18n;
use m\config;
use modules\admin\admin\overview_data;

class overview extends module {

    public function _init()
    {
        $arr = [];

        foreach (['catalog','footer','home_big','home','side'] as $type) {

            $arr[] = $this->view->overview_item->prepare([
                'name' => i18n::get(ucfirst($type) . ' blocks'),
                'link' => '~language_prefix~/' . config::get('admin_panel_alias') . '/special_blocks/' . $type,
            ]);
        }

        view::set('content', $this->view->overview->prepare([
            'items' => implode('', $arr)
        ]));
    }
}
