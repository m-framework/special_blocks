<?php

namespace modules\special_blocks\admin;

class edit extends edit_module {

    protected $type_related = [
        ['value' => 'catalog_blocks', 'name' => '*Catalog block*'],
        ['value' => 'footer_blocks', 'name' => '*Footer block*'],
        ['value' => 'home_big_block', 'name' => '*Home big block*'],
        ['value' => 'home_blocks', 'name' => '*Home block*'],
        ['value' => 'home_text', 'name' => '*Home long text*'],
        ['value' => 'side_blocks', 'name' => '*Side block*'],
        ['value' => 'top_blocks', 'name' => '*Top block*'],
        ['value' => 'bottom_blocks', 'name' => '*Bottom block*'],
    ];
}