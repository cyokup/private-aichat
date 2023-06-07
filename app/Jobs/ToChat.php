<?php
/**
 * 发送聊天信息
 */

namespace App\Jobs;

use App\Models\Item;
use App\Models\ItemContent;
use App\Models\ItemContentDetail;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Orhanerday\OpenAi\OpenAi;

class ToChat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 850;
    public $messageModel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($messageModel)
    {
        $this->messageModel = $messageModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ItemContentModel = new ItemContent();
        $item = Item::find($this->messageModel->item_id);
        // 获取输入内容的向量
        list($embedding, $tokens) = $ItemContentModel->createEmbeddings($this->messageModel->content);
        // 寻找最相似的2段文本
        $itemContentDetail = ItemContentDetail::where('item_id', $this->messageModel->item_id)->get();
        $embeddingArray = $itemContentDetail->pluck('embedding')->toArray();
        list($indexs, $result) = findSimilarVectors($embedding, $embeddingArray);
        // 发送给chatAPI接口
        $fromContent = '';
        foreach ($indexs as $v) {
            $fromContent .= ($itemContentDetail[$v]->content) ?? '';
        }
        $content = $ItemContentModel->askChat($fromContent, $this->messageModel->content);
        // 写入数据库
        Message::create([
            'item_id' => $this->messageModel->item_id,
            'chat_no' => $this->messageModel->chat_no,
            'content' => $content,
            'is_reply' => 1
        ]);
        return true;
    }

}
