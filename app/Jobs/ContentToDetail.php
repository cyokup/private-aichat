<?php

namespace App\Jobs;

use App\Models\Item;
use App\Models\ItemContent;
use App\Models\ItemContentDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpWord\IOFactory;
use Rap2hpoutre\FastExcel\FastExcel;
use Smalot\PdfParser\Parser;

class ContentToDetail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 850;
    public $itemContent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($itemContent)
    {
        $this->itemContent = $itemContent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 修改状态处理中
        ItemContent::where('id', $this->itemContent->id)->update(['status' => 1]);
        // 获取文件内容字符串
        $content = $this->getContent();
        if (empty($content)) {
            ItemContent::where('id', $this->itemContent->id)->update(['status' => 3, 'remark' => '内容不存在']);
            return false;
        }
        $ItemContentModel = new ItemContent();
        // 去除多余空格
        $itemContent = preg_replace("/\s/", "", $content);
        $contentArray = contentSlice($itemContent, 500);
        $item = Item::find($this->itemContent->item_id);
        foreach ($contentArray as $v) {
            list($embedding, $tokens) = $ItemContentModel->createEmbeddings($v);
            ItemContentDetail::create(['item_content_id' => $this->itemContent->id, 'item_id' => $this->itemContent->item_id, 'admin_id' => $this->itemContent->admin_id, 'content' => $v, 'tokens' => $tokens, 'embedding' => $embedding]);
        }
        ItemContent::where('id', $this->itemContent->id)->update(['status' => 2, 'content' => $content]);
        return true;
    }

    /**
     * 获取文件内容并切分
     */
    public function getContent()
    {
        $content = '';
        $basePath = public_path() . '/storage/';
        $allPath = $basePath . $this->itemContent->path;
        // 文本
        if (in_array($this->itemContent->ext, ['txt'])) {
            $content = file_get_contents($allPath);
        }
        // excel
        if (in_array($this->itemContent->ext, ['csv', 'xls', 'xlsx'])) {
            $dataArray = (new FastExcel)->import($allPath)->toArray();
            $content = json_encode($dataArray, JSON_UNESCAPED_UNICODE);
        }
        // word
        if (in_array($this->itemContent->ext, ['doc', 'docx'])) {
            $phpWord = IOFactory::load($allPath);
            $content = $this->getNodeContent($phpWord);
        }
        if (in_array($this->itemContent->ext, ['pdf'])) {
            $parser = new Parser();
            $pdf = $parser->parseFile($allPath);
            $content = $pdf->getText();
        }
        return $content;
    }

    /**
     * word处理
     * 根据word主节点获取分节点内容
     * @param $word
     * @return array
     */
    public function getNodeContent($word)
    {
        $return = [];
        //分解部分
        foreach ($word->getSections() as $section) {
            if ($section instanceof \PhpOffice\PhpWord\Element\Section) {
                //分解元素
                foreach ($section->getElements() as $element) {
                    //文本元素
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        $text = '';
                        foreach ($element->getElements() as $ele) {
                            $text .= $this->getTextNode($ele);
                        }
                        $return[] = $text;
                    } else if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                        //表格元素
                        foreach ($element->getRows() as $ele) {
                            $return[] = $this->getTableNode($ele);
                        }
                    }
                }
            }
        }
        return implode('。', $return);
    }

    /**
     * word处理
     * 获取文档节点内容
     * @param $node
     * @return string
     */
    public function getTextNode($node)
    {
        $return = '';
        //处理文本
        if ($node instanceof \PhpOffice\PhpWord\Element\Text) {
            $return .= $node->getText();
        } //处理文本元素
        else if ($node instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($node->getElements() as $ele) {
                $return .= $this->getTextNode($ele);
            }
        }
        return $return;
    }

    /**
     * word处理
     * 获取表格节点内容
     * @param $node
     * @return string
     */
    public function getTableNode($node)
    {
        $return = '';
        //处理行
        if ($node instanceof \PhpOffice\PhpWord\Element\Row) {
            foreach ($node->getCells() as $ele) {
                $return .= $this->getTableNode($ele);
            }
        } //处理列
        else if ($node instanceof \PhpOffice\PhpWord\Element\Cell) {
            foreach ($node->getElements() as $ele) {
                $return .= $this->getTextNode($ele);
            }
        }
        return $return;
    }

}
