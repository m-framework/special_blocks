<?php

namespace modules\special_blocks\client;

use m\config;
use m\core;
use m\module;
use m\registry;
use m\view;
use modules\special_blocks\models;

class special_blocks extends module
{
    public function _init()
    {
        if (!isset($this->view->{$this->module_name}) || !isset($this->view->{$this->module_name . '_item'})) {
            return false;
        }

        $items = models\special_blocks::call_static()
            ->s(
                [],
                [
                    'site' => (int)$this->site->id,
                    'module' => $this->module_name,
                    [
                        ['language' => (int)$this->language_id],
                        ['language' => null]
                    ],
                    [
                        ['page' => null],
                        ['page' => $this->page->id],
                    ],
                ],
                [1000]
            )
            ->all('object');

        $arr = [];

        if (!empty($items)) {
            foreach ($items as $item) {

                $item->content = htmlspecialchars_decode(htmlspecialchars_decode($item->content));
                $item->link = empty($item->link) ? '#' : $item->link;

                if ($this->user->has_permission($this->name, $this->page->id) && isset($this->view->edit_bar)) {
                    $item->edit_bar = $this->view->edit_bar->prepare([
                        'model' => 'special_blocks',
                        'module' => $this->module_name,
                        'id' => $item->id,
                        'edit_link' => '/' . config::get('admin_panel_alias') . '/special-blocks/edit/' . $item->id,
                    ]);
                }

                $arr[] = $this->view->{$this->module_name . '_item'}->prepare($item);
            }
        }

        if ($this->user->has_permission($this->name, $this->page->id) && isset($this->view->{$this->module_name . '_add_link'})) {
            $arr[] = $this->view->{$this->module_name . '_add_link'}->prepare([
                'model' => 'special_blocks',
                'module' => $this->module_name,
                'edit_link' => '/' . config::get('admin_panel_alias') . '/special-blocks/add/',
            ]);
        }

        if (empty($arr)) {
            return false;
        }

        return view::set($this->module_name, $this->view->{$this->module_name}->prepare([
            'items' => implode('', $arr)
        ]));
    }

    public function _ajax_delete_special_blocks()
    {
        if (!empty($this->post->module) && $this->post->module !== $this->module_name) {
            return false;
        }

        if (!$this->user->has_permission($this->module_name, $this->page->id)) {

            return $this->ajax_arr = [
                'error' => 'No permissions',
//                'db_logs' => $this->db_logs,
            ];
        }

        if (!$this->user->has_permission($this->module_name, $this->page->id) || empty($this->post->id)
            || empty($this->post->model) || $this->post->model !== 'special_blocks') {

            return $this->ajax_arr = ['error' => 'Not fully data'];
        }

        $item = new models\special_blocks($this->post->id);

        if ($item->destroy()) {
            return $this->ajax_arr = ['result' => 'success'];
        }
        else {
            return $this->ajax_arr = ['error' => 'Can\'t delete this special block'];
        }
    }

    public function _ajax_update_special_blocks()
    {
        if (!empty($this->post->module) && $this->post->module !== $this->module_name) {
            return false;
        }

        if (!$this->user->has_permission($this->module_name, $this->page->id) || empty($this->post->id)
            || empty($this->post->model) || $this->post->model !== 'special_blocks') {
            return $this->ajax_arr = ['error' => 'Not fully data'];
        }

        $item = new models\special_blocks($this->post->id);
        $item->import($this->post);

        if ($item->save()) {
            $this->ajax_arr = ['result' => 'success'];
        }
        else {
            $this->ajax_arr = ['error' => 'Can\'t update this special block'];
        }

        return true;
    }

    public function _ajax_add_special_blocks()
    {
        if (!empty($this->post->module) && $this->post->module !== $this->module_name) {
            return false;
        }

        if (!$this->user->has_permission($this->name, $this->page->id) || empty($this->post->model)
            || $this->post->model !== 'special_blocks') {
            return $this->ajax_arr = ['error' => 'Not fully data'];
        }

        $data = [
            'site' => $this->site->id,
            'module' => $this->module_name,
            'page' => $this->page->id,
            'language' => (int)$this->language_id,
            'title' => 'Lorem ipsum dolor sit amet',
            'content' => 'Lorem ipsum dolor sit amet',
            'link' => '#',
        ];

        $last_sequence = models\special_blocks::call_static()->s(['sequence'], $data, [1])->one();

        $data['sequence'] = empty($last_sequence) ? 1 : (int)$last_sequence + 1;

        $item = new models\special_blocks();
        $item->import($data);
        $item->save();

        $item->edit_bar = $this->view->edit_bar->prepare([
            'model' => 'special_blocks',
            'module' => $this->module_name,
            'id' => $item->id,
            'edit_link' => '/' . config::get('admin_panel_alias') . '/special-blocks/edit/' . $item->id,
        ]);

        $this->ajax_arr = ['item' => $this->view->{$this->module_name . '_item'}->prepare($item)];

        core::out($this->ajax_arr);

        return true;
    }
}