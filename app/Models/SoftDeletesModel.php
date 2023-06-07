<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeletesModel extends CommonModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
