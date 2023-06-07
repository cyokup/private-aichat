<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="never">
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>正在与[{{$item->title}}]沟通</title>
    <style type="text/css">
        * {
            padding: 0;
            margin: 0;
        }

        body {
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            height: 100%;
            width: 900px;
            border-radius: 4px;
            border: 0.5px solid #e0e0e0;
            background-color: #f5f5f5;
            display: flex;
            flex-flow: column;
            overflow: hidden;
        }

        .content {
            /*width: calc(100% - 40px);*/
            padding: 10px;
            overflow-y: scroll;
            flex: 1;
            margin-top: 20px;
        }

        .content:hover::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
        }

        .title {
            width: 100%;
            font: 18px/34px '宋体';
            position: fixed;
            top: 0;
            left: 0;
            background: #393D49;
            border-bottom: 1px solid #393D49;
            text-align: center;
            z-index: 100;
            color: #fff;
        }

        .bubble {
            max-width: 400px;
            padding: 10px;
            border-radius: 5px;
            position: relative;
            color: #000;
            word-wrap: break-word;
            word-break: normal;
        }

        .item-left .bubble {
            margin-left: 15px;
            background-color: #fff;
        }

        .item-left .bubble:before {
            content: "";
            position: absolute;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-top: 10px solid transparent;
            border-right: 10px solid #fff;
            border-bottom: 10px solid transparent;
            left: -20px;
        }

        .item-right .bubble {
            margin-right: 15px;
            background-color: #9eea6a;
        }

        .item-right .bubble:before {
            content: "";
            position: absolute;
            width: 0;
            height: 0;
            border-left: 10px solid #9eea6a;
            border-top: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid transparent;
            right: -20px;
        }

        .item {
            margin-top: 15px;
            display: flex;
            width: 100%;
        }

        .item.item-right {
            justify-content: flex-end;
        }

        .item.item-center {
            justify-content: center;
        }

        .item.item-center span {
            font-size: 12px;
            padding: 2px 4px;
            color: #fff;
            background-color: #dadada;
            border-radius: 3px;
            -moz-user-select: none; /*火狐*/
            -webkit-user-select: none; /*webkit浏览器*/
            -ms-user-select: none; /*IE10*/
            -khtml-user-select: none; /*早期浏览器*/
            user-select: none;
        }

        .avatar img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
        }

        .input-area {
            border-top: 0.5px solid #e0e0e0;
            /*height: 150px;*/
            display: flex;
            /*flex-flow: column;*/
            background-color: #fff;
        }

        textarea {
            flex: 1;
            padding: 5px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            overflow-y: auto;
            overflow-x: hidden;
            outline: none;
            resize: none;
        }

        #textarea {
            padding: 5px;
            float: left;
            width: 80%;
            height: 30px;
            border: none;
            font: 18px/30px '宋体';
            color: #999;
        }

        #send-btn {
            float: left;
            width: 20%;
            padding: 10px;
            background: #5FB878;
            color: #fff;
            border: 2px solid #5fb878;
            border-radius: 3px;
        }

        .button-area {
            display: flex;
            height: 40px;
            margin-right: 10px;
            line-height: 40px;
            padding: 5px;
            justify-content: flex-end;
        }

        .button-area button {
            width: 80px;
            border: none;
            outline: none;
            border-radius: 4px;
            float: right;
            cursor: pointer;
        }

        /* 设置滚动条的样式 */
        ::-webkit-scrollbar {
            width: 10px;
        }

        /* 滚动槽 */
        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset006pxrgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }

        /* 滚动条滑块 */
        ::-webkit-scrollbar-thumb {
            border-radius: 10px;
            background: rgba(0, 0, 0, 0);
            -webkit-box-shadow: inset006pxrgba(0, 0, 0, 0.5);
        }

        .spic {
            max-width: 100px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="title">正在与[{{$item->title}}]沟通</div>
    <div class="content">
    </div>
    <div class="input-area">
        <input type="text" id="textarea"/>
        <button id="send-btn" onclick="send()">发 送</button>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="/static/layer/layer.js"></script>

<script type="text/javascript">
    var _index;
    var _lastnum = 0;

    function send() {
        let text = $('#textarea').val();
        if (!text) {
            alert('请输入内容');
            return;
        }
        var index = layer.load(0, {shade: false});

        $.ajax({
            url: '/api/send_msg',
            data: {chat_no: '{{$chat_no}}', content: text, code: '{{$item->code}}'},
            dataType: 'json',
            type: 'post',
            success: function (res) {
                if (res.code * 1 == 1000) {
                    clearInterval(_index);
                    startList();
                    $('#textarea').val('');
                    layer.close(index); // 关闭 loading
                } else {
                    layer.close(index); // 关闭 loading
                    alert(res.info);
                }
            }, error: function (e) {
                alert('异常');
            }
        })
    }

    startList();

    function startList() {
        getlist();
        _index = setInterval(function () {
            getlist();
        }, 5000);
    }


    function getlist() {
        $.ajax({
            url: "/api/msg_list?chat_no={{$chat_no}}&code={{$item->code}}",
            dataType: 'json',
            type: 'get',
            success: function (res) {
                if (res.code == 1000) {
                    var _list = res.data;
                    var _len = _list.length;
                    if (_len > _lastnum) {
                        _lastnum = _len;
                    } else {
                        return true;
                    }
                    var _str = '';
                    if (_len > 0) {
                        for (var i = 0; i < _len; i++) {
                            var _d = _list[i];
                            var _content = _d['content'];
                            if (_d['is_reply'] == 1) {
                                _str += '<div class="item item-left">\n' +
                                    '            <div class="avatar">\n' +
                                    '                <img src="{!! $item->headimgurl !!}" />\n' +
                                    '            </div>\n' +
                                    '            <div class="bubble bubble-left">' + _content + '<br/>' + _d['created_at'] + '</div>\n' +
                                    '        </div>';
                            } else {
                                _str += '<div class="item item-right">\n' +
                                    '            <div class="bubble bubble-right">' + _content + '<br/>' + _d['created_at'] + '</div>\n' +
                                    '            <div class="avatar">\n' +
                                    '                <img src="{!!$user_info->headimgurl!!}" />\n' +
                                    '            </div>\n' +
                                    '        </div>'
                            }
                        }
                    }
                    $('.content').html(_str);
                    //滚动条置底
                    let height = document.querySelector('.content').scrollHeight;
                    document.querySelector(".content").scrollTop = height;
                }
            }
        })
    }

</script>
</body>
</html>
