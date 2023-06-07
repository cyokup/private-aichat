<?php

namespace App\Models;


use Illuminate\Support\Str;

class Item extends SoftDeletesModel
{
    protected $guarded = ['id'];

    protected $appends = ['chat_url'];

    public function contents()
    {
        return $this->hasMany(ItemContent::class, 'item_id', 'id');
    }

    protected function getChatUrlAttribute()
    {
        return config('app.url') . '/chat?code=' . $this->code;
    }

    public function getHeadimgurlAttribute($value)
    {
        // 如果 thub_image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['headimgurl'], ['http://', 'https://'])) {
            return $this->attributes['headimgurl'];
        }
        $baseUrl = config('filesystems.disks.admin.url');
        return $baseUrl . '/' . $this->attributes['headimgurl'];
    }

}
