<?php

namespace modules\special_blocks\admin;

use m\module;
use m\core;
use modules\special_blocks\models\special_blocks;

class delete extends module {

    public function _init()
    {
        $item = new special_blocks(!empty($this->get->delete) ? $this->get->delete : null);

        if (!empty($item->id) && !empty($this->user->profile) && $this->user->is_admin() && $item->destroy()) {
            core::redirect('/' . $this->conf->admin_panel_alias . '/special-blocks');
        }
    }
}
