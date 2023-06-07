# Laravel-UEditor

UEditor integration for Laravel 5.

## 安装

```shell
$ composer require "codingyu/laravel-ueditor:~2.0"
```

## 配置

1. 发布配置文件与资源

    ```php
    $ php artisan vendor:publish --provider='Codingyu\LaravelUEditor\UEditorServiceProvider'
    ```

2. 模板引入编辑器

    这行的作用是引入编辑器需要的 css,js 等文件，所以你不需要再手动去引入它们。

    ```php
    @include('vendor.ueditor.assets')
    ```

3. 编辑器的初始化

    ```html
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container');
        ue.ready(function() {
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
        });
    </script>

    <!-- 编辑器容器 -->
    <script id="container" name="content" type="text/plain"></script>
    ```

# 说明

- 5.4+ 请不要忘记 `php artisan storage:link`
- 在 `config/ueditor.php` 配置 `disk` 为 `'public'` 情况下，上传路径在：`public/uploads/` 下，确认该目录存在并可写。
- 如果要修改上传路径，请在 `config/ueditor.php` 里各种类型的上传路径，但是都在 public 下。
- 请在 `.env` 中正确配置 `APP_URL` 为你的当前域名，否则可能上传成功了，但是无法正确显示。

## 七牛支持

如果你想使用七牛云储存，需要进行下面几个简单的操作：

1.安装和配置 [laravel-filesystem-qiniu](https://github.com/overtrue/laravel-filesystem-qiniu)

2.配置 `config/ueditor.php` 的 `disk` 为 `qiniu`:

```php
'disk' => 'qiniu'
```

3.剩下时间打局 LOL，已经完事了。

> 七牛的 `access_key` 和 `secret_key` 可以在这里找到：https://portal.qiniu.com/user/key ,在创建 `bucket` （空间）的时候，推荐大家都使用公开的空间。

## 事件

你肯定有一些朋友肯定会有一些比较特殊的场景，那么你可以使用本插件提供的事件来支持：

> 请按照 Laravel 事件的文档来使用：
> https://laravel.com/docs/5.4/events#registering-events-and-listeners

### 上传中事件

> Codingyu\LaravelUEditor\Events\Uploading

在保存文件之前，你可以拿到一些信息：

- `$event->file` 这是请求的已经上传的文件对象，`Symfony\Component\HttpFoundation\File\UploadedFile` 实例。
- `$event->filename` 这是即将存储时用的新文件名
- `$event->config` 上传配置，数组。

你可以在本事件监听器返回值，返回值将替换 `$filename` 作为存储文件名。

### 上传完成事件

> Codingyu\LaravelUEditor\Events\Uploaded

它有两个属性：

- `$event->file` 与 Uploading 一样，上传的文件
- `$event->result` 上传结构，数组，包含以下信息：

   ```php
   'state' => 'SUCCESS',
   'url' => 'http://xxxxxx.qiniucdn.com/xxx/xxx.jpg',
   'title' => '文件名.jpg',
   'original' => '上传时的源文件名.jpg',
   'type' => 'jpg',
   'size' => 17283,
   ```

你可以监听此事件用于一些后续处理任务，比如记录到数据库。

# License

MIT
