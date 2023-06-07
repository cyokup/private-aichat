## 私有文档智能对话工具，基于国内AI语言大模型MiniMax版本。
#### 可对word，excel，pdf等文档进行解析。并基于这些文档内容智能回答用户提问。无须科学上网，响应速度更快，更安全。

## 安装须知：

- 运行环境：php7.4，数据库mysql5.7。当前框架：Laravel8.83.27
- 申请MiniMax接口权限：https://api.minimax.chat/
- 将.env.example改成.env文件
- 执行命令生成APP_KEY：php artisan key:generate
- 配置好.env数据库信息和MiniMax的请求key参数：MINIMAX_GROUPID，MINIMAX_SECRET
- 执行命令迁移必要数据表：php artisan migrate
- 执行后台数据表安装：php artisan admin:install
- 增加软连接命令：php artisan storage:link
- 后台地址：域名/admin，默认账号密码都是amdin

### 预览图
![输入图片说明](https://gitee.com/cyokup/private-aichat/raw/main/public/static/images/page_1.png "首页.png")
![输入图片说明](https://gitee.com/cyokup/private-aichat/raw/main/public/static/images/page_2.png "文档上传.png")
![输入图片说明](https://gitee.com/cyokup/private-aichat/raw/main/public/static/images/page_3.png "对话.png")

### 其他
- 欢迎提PR一起完善项目
- 作者微信：cyokup

