<?php

namespace App\Models;


use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\JiebaAnalyse;
use Illuminate\Support\Facades\Log;
use Orhanerday\OpenAi\OpenAi;

class ItemContent extends SoftDeletesModel
{
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(ItemContentDetail::class, 'item_content_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        // 监听模型删除事件，删除关联的评论数据
        static::deleting(function ($itemContent) {
            $itemContent->details()->delete();
        });
    }

    /**
     * 生成向量
     * 返回向量数组和使用的token数量
     */
    public function createEmbeddings($content)
    {
        // minimax
        $baseUrl = 'https://api.minimax.chat/v1/embeddings?GroupId=' . config('app.minimax_groupid');
        $header[] = "Authorization:Bearer " . config('app.minimax_secret');
        $header[] = "Content-Type:application/json";
        $params = [
            'model' => 'embo-01',
            'type' => 'db',
            'texts' => [$content]
        ];
        $params = json_encode($params, JSON_UNESCAPED_UNICODE);
        $result = curl($baseUrl, 'POST', $params, $header);
        $result = json_decode($result, true);
        if (isset($result['vectors']) && !empty($result['vectors'][0])) {
            return [$result['vectors'][0], 0];
        }
        return [[], 0];
    }


    /**
     * 回答提问
     * 返回向量数组和使用的token数量
     */
    public function askChat($fromContent, $userInput)
    {
        return $this->chatByMiniMax($fromContent, $userInput);
    }

    /**
     * minimax应事聊天
     */
    public function chatByMiniMax($fromContent, $userInput)
    {
        // minimax
        $baseUrl = 'https://api.minimax.chat/v1/text/chatcompletion?GroupId=' . config('app.minimax_groupid');
        $header[] = "Authorization:Bearer " . config('app.minimax_secret');
        $header[] = "Content-Type:application/json";
        $params = [
            'model' => 'abab5-chat',
            'tokens_to_generate' => 512,
            'role_meta' => [
                'user_name' => '用户',
                'bot_name' => '智能助理'
            ],
            'prompt' => '使用根据以下内容来回答问题。 如果你不知道答案，就说你不知道，不要试图编造答案。' . $fromContent,
            'messages' => [[
                'sender_type' => 'USER',
                'text' => $userInput
            ]]
        ];
        $params = json_encode($params, JSON_UNESCAPED_UNICODE);
        try {
            $result = curl($baseUrl, 'POST', $params, $header);
            $result = json_decode($result, true);
            return $result['reply'];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
