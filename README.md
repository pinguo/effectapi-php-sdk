# Camera360 Effect API SDK for PHP
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

[install-packagist]: https://packagist.org/packages/pinguo/effectapi-php-sdk

## 安装
通过composer，这是推荐的方式，在 `composer.json` 里声明依赖，或者运行下面的命令。SDK 包已经放到这里 [`pinguo/effectapi-php-sdk`][install-packagist] 。

```bash
$ composer require pinguo/effectapi-php-sdk
```

## 运行环境
PHP版本：cURL extension, 5.3.3+

## 使用方法
```php
use Camera360\Authorization;
use Camera360\EffectManager;
...
    // 构造授权类
    $authorization = new Authorization($accessKey, $secretKey);
    // 构造特效处理类
    $effectManager = new EffectManager($authorization);
    
    // 上传图片二进制流
    $uploadRet = $effectManager->upload($image);
    
    /**
     * 调用上传接口的其他方式
     */
    // 上传图片二进制流，并自动触发特效处理流程
    // $uploadRet = $effectManager->upload($image, filter);
    // 上传图片文件
    // $uploadRet = $effectManager->uploadFile($filePath);
    // 上传图片文件，并自动触发特效处理流程
    // $uploadRet = $effectManager->uploadFile($filePath, filter);
    
    // 调用特效处理类的增加特效滤镜接口
    $effectPicUrl = $effectManager->addFilter($uploadRet['key'], $filter);
...
```

## FAQ
* 内部发生错误，都将抛出异常，请根据实际处理场景来捕获异常。
* API 的使用 demo 可以参考 [单元测试](https://github.com/pinguo/effectapi-php-sdk/blob/master/tests)。

## 联系我们

- 如果需要帮助，请直接向 <zhanglu@camera360.com> 发送邮件
- 更详细的文档，见[官方文档站](https://sdk.camera360.com/views/index.html)
- 如果发现了bug， 欢迎提交 [issue](https://github.com/pinguo/effectapi-php-sdk/issues)
- 如果有功能需求，欢迎提交 [issue](https://github.com/pinguo/effectapi-php-sdk/issues)
- 如果要提交代码，欢迎提交 pull request

## 代码许可

The MIT License (MIT).详情见 [License文件](https://github.com/pinguo/effectapi-php-sdk/blob/master/LICENSE).
