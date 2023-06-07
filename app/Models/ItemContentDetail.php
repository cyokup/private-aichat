<?php

namespace App\Models;

class ItemContentDetail extends CommonModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'embedding'=>'json'
    ];


}
