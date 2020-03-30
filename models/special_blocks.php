<?php

namespace modules\special_blocks\models;

use m\model;
use m\cache;
use m\registry;
use modules\files\models\files;

class special_blocks extends model
{
    public $_table = 'special_blocks';
    protected $_sort = ['page' => 'ASC', 'sequence' => 'ASC'];

    protected $fields = [
        'id' => 'int',
        'site' => 'int',
        'module' => 'varchar',
        'author' => 'int',
        'page' => 'int',
        'title' => 'varchar',
        'content' => 'text',
        'link' => 'varchar',
        'image' => 'varchar',
        'language' => 'int',
        'sequence' => 'int',
    ];

    public function _before_save()
    {
        return true;
    }

    public function get_type_name()
    {
        $_n = 'modules\\special_blocks\\client\\' . $this->module;

        if (class_exists($_n)) {
            $vars = get_class_vars($_n);
            return empty($vars['_name']) ? $this->module : $vars['_name'];
        }

        return '*' . $this->module . '*';
    }

    public function _autoload_title()
    {
        return $this->title = '';
    }

    public function _autoload_related_image()
    {
        $this->related_image = '';

        if (empty($this->id)) {
            return $this->related_image;
        }

        $file = files::call_static()
            ->s([],
                [
                    [['site' => null], ['site' => (int)registry::get('site')->id]],
                    'related_model' => 'special_blocks',
                    'related_id' => $this->id
                ]
            )->obj();

        if (!empty($file->id)) {
            $this->related_image = $file->get_path();
        }

        return $this->related_image;
    }
}
