## 介绍
- 私有文档智能对话工具，基于国内AI语言大模型MiniMax版本。
- 可对word，excel，pdf等文档进行解析。并基于这些文档内容智能回答用户提问。无须科学上网，响应速度更快，更安全。

### 安装准备
- 运行环境：php7.4，数据库mysql5.7。当前框架：Laravel8.83.27
- 申请MiniMax接口权限：https://api.minimax.chat/
### 安装步骤
- clone当前仓库到本地
- 将运行目录放在public下，并给予public，storage目录777权限
- nginx添加配置
````
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
````
- 安装第三方包：composer install
- 将.env.example改成.env文件，配置域名APP_URL参数值，执行命令生成APP_KEY：php artisan key:generate
- 配置好.env数据库信息和MiniMax的请求key参数：MINIMAX_GROUPID，MINIMAX_SECRET
- 执行后台数据表安装：php artisan admin:install
- 增加软连接命令：php artisan storage:link
- 后台地址：域名/admin，默认账号密码都是admin 。添加导航栏，路径：item，名字：项目管理
- 文档分析和聊天采用了队列方式运行。默认不配置队列的情况下，是同步请求。配置了队列，会异步请求数据，后者体验更好。如果采用异步队列，安装supervisor并配置（命令：php artisan queue:work --sleep=3 --daemon ）启动守护进程。.env文件修改配置：QUEUE_CONNECTION=database，具体详情可见Laravel队列文档。

### 预览图
![输入图片说明](https://gitee.com/cyokup/private-aichat/raw/main/public/static/images/page_1.png "首页.png")
![输入图片说明](https://gitee.com/cyokup/private-aichat/raw/main/public/static/images/page_2.png "文档上传.png")
![输入图片说明](https://gitee.com/cyokup/private-aichat/raw/main/public/static/images/page_3.png "对话.png")

### 其他
- 欢迎提PR一起完善项目
- 作者微信：cyokup
- 承接网站开发，小程序开发，微信公众号开发，APP开发等定制开发项目

