<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ItemContentAction extends RowAction
{
    public $name = '资料管理';

    /**
     * @return  string
     */
    public function href()
    {
        return "/admin/item-content?item_id=".$this->getKey();
    }

}
