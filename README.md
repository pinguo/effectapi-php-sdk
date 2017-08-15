# Camera360 Effect API SDK for PHP
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

## SDK介绍
PHP版本：cURL extension, 5.3.2+

利用Camera360最先进的图像处理技术，给图片添加上百款滤镜，支持的滤镜参考[滤镜列表][filters]。

特别声明：所有上传的图片和特效图都只会在服务器保存 **1天**，**1天** 后自动删除没有备份。

## 安装
通过 [Composer][]，这是推荐的方式。SDK 包已经放到这里 [`pinguo/effectapi-php-sdk`](https://packagist.org/packages/pinguo/effectapi-php-sdk)。

[Composer][] 是PHP的依赖管理工具，你在项目中声明所依赖的东西，[Composer][] 会找出哪个版本的包需要安装，并安装它们（将它们下载到你的项目中）。

* Composer官网：[https://getcomposer.org/](Composer)
* 中文网址：[https://docs.phpcomposer.com/](Composer_cn)

1. 安装Composer

    局部安装，在项目根目录下执行命令

    ```bash
    curl -sS https://getcomposer.org/installer | php
    ```
    `composer.phar` 将下载到你的项目中。
    
    全局安装，继续执行命令
    
    ```bash
    mv composer.phar /usr/local/bin/composer
    ```

2. 安装最新稳定版本SDK

    ```bash
    composer require pinguo/effectapi-php-sdk
    ```
    
3. 自动加载

    [Composer][] 准备了一个自动加载文件，可以加载 [Composer][] 下载的库中所有的类文件。只需将下面的代码添加到项目引导文件中。
    
    ```php
    require 'vendor/autoload.php';
    ```

## 快速接入
在开始使用 SDK 之前，首先需要[联系商务](#商务合作)注册一个账号，并获得一对有效的密钥对 `AccessKey` 和 `SecretKey`，请妥善保管密钥对，**切勿泄露**。

示例：

```php
use Camera360\Authorization;
use Camera360\EffectManager;

// 用于签名的公钥和私钥
$accessKey = 'Access_Key';
$secretKey = 'Secret_Key';

// 1.构造授权类
$authorization = new Authorization($accessKey, $secretKey);
// 2.构造特效处理类
$effectManager = new EffectManager($authorization);
    
// 3.上传图片二进制流
$uploadRet = $effectManager->upload($image);
    
/**
 * 调用上传接口的其他方式
 * 参数 $filter 是滤镜对应的枚举值
 */
// 上传图片二进制流，并自动触发特效处理流程
// $uploadRet = $effectManager->upload($image, $filter);
// 上传图片文件
// $uploadRet = $effectManager->uploadFile($filePath);
// 上传图片文件，并自动触发特效处理流程
// $uploadRet = $effectManager->uploadFile($filePath, $filter);
    
// 4.调用特效处理类的增加特效滤镜接口
$effectPicUrl = $effectManager->addFilter($uploadRet['key'], $filter);
```

说明：字段 `$filter` 参考[滤镜列表][filters]。

## 常见问题
* 内部发生错误，都将抛出异常，请根据实际处理场景来捕获异常。
* API 的使用 demo 可以参考 [单元测试](https://github.com/pinguo/effectapi-php-sdk/blob/master/tests)。

## 联系我们

* 如果需要帮助，请直接向 <zhanglu@camera360.com> 发送邮件
* 更详细的文档，见[官方文档站](https://developer.camera360.com/)
* 如果发现了bug， 欢迎提交 [issue](https://github.com/pinguo/effectapi-php-sdk/issues)
* 如果有功能需求，欢迎提交 [issue](https://github.com/pinguo/effectapi-php-sdk/issues)
* 如果要提交代码，欢迎提交 pull request

## <a name="商务合作"></a>商务合作

* [申请地址](https://sdk.camera360.com/apply.html)
* QQ: 2851258253
* 在线技术支持  
    周一到周五：北京时间 9:00 - 18:00
    
## 代码许可

The MIT License (MIT).详情见 [License文件](https://github.com/pinguo/effectapi-php-sdk/blob/master/LICENSE).

[Composer]: https://getcomposer.org/
[Composer_cn]: https://docs.phpcomposer.com/
[filters]: https://github.com/pinguo/effectapi-php-sdk/blob/master/滤镜列表.md
