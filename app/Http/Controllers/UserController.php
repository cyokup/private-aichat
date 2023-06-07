<?php
/*
 * 用户类
 */

namespace App\Http\Controllers;

use App\Helpers\ResponseCode;
use App\Jobs\ToChat;
use App\Models\Item;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    // 聊天界面
    public function chat(Request $request)
    {
        $code = request('code');
        if (empty($code)) {
            return '编号不存在';
        }
        $chat_no = session('chat_no');
        if (empty($chat_no)) {
            $chat_no = randStr(32);
            session('chat_no', $chat_no);
        }
        // 项目配置
        $item = Item::where('code', $code)->firstOrFail();
        // 用户信息
        $user_info = getDefualtUser();
        return view('chat', compact('item', 'chat_no', 'user_info'));
    }

    /**
     * @api {GET} /msg_list 消息列表
     * @apiGroup 聊天
     * @apiParamExample {json} 参数
     * number   会话编号
     * @apiSuccessExample {json} 返回
     * 自行调用
     */
    public function msgList()
    {
        $all = \request()->all();
        $messages = [
            'chat_no.required' => '聊天编号必填',
            'code.required' => '项目code必填',
        ];
        $validator = Validator::make($all, [
            'chat_no' => 'required',
            'code' => 'required',
        ], $messages);
        if ($validator->fails()) {
            return $this->fail([ResponseCode::ERROR_PARAMETER, implode(',', $validator->errors()->all())]);
        }
        // 平台项目配置
        $item = Item::where('code', $all['code'])->first();
        if (empty($item)) {
            return $this->errorjson(ResponseCode::ERROR_BUSINESS, '项目不存在');
        }
        if (empty($item->status)) {
            return $this->errorjson(ResponseCode::ERROR_BUSINESS, '项目禁用');
        }
        // 消息列表
        $messageList = Message::where('chat_no', $all['chat_no'])
            ->orderBy('created_at', 'asc')
            ->get();
        return $this->successJson($messageList);
    }

    /**
     * @api {GET} /send_msg 发送消息
     * @apiGroup 聊天
     * @apiParamExample {json} 参数
     * chat_no   会话编号
     * item_id  项目id
     * content  内容
     * @apiSuccessExample {json} 返回
     * 自行调用
     */
    public function sendMsg()
    {
        $all = \request()->all();
        $messages = [
            'chat_no.required' => '聊天编号必填',
            'code.required' => '项目code必填',
            'content.required' => '内容必填'
        ];
        $validator = Validator::make($all, [
            'chat_no' => 'required',
            'code' => 'required',
            'content' => 'required'
        ], $messages);
        if ($validator->fails()) {
            return $this->fail([ResponseCode::ERROR_PARAMETER, implode(',', $validator->errors()->all())]);
        }
        $item = Item::where('code', $all['code'])->first();
        if (empty($item)) {
            return $this->errorjson(ResponseCode::ERROR_BUSINESS, '项目不存在');
        }
        if (empty($item->status)) {
            return $this->errorjson(ResponseCode::ERROR_BUSINESS, '项目禁用');
        }
        // 写入聊天数据
        $data['item_id'] = $item->id;
        $data['content'] = $all['content'];
        $data['chat_no'] = $all['chat_no'];
        $data['is_reply'] = 0;
        $messageModel = Message::create($data);
        // 异步发送请求
        dispatch((new ToChat($messageModel)));
        return $this->successJson('发送成功');
    }

}
