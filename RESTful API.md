# REST API
## Schema
没有特别说明的情况下，API采用`HTTPS`协议，返回数据格式为`JSON`。

* API中国大陆域名为 `https://effectapi.camera360.com`
* API海外域名为 `https://effectapi.360in.com`
* 图片资源下载域名为 `https://effect.c360dn.com`

## 安全机制
### <a name="security-idtoken"></a>身份凭证
身份凭证是Camera360云特效用于验证请求是否合法的机制。不带身份凭证或带非法凭证的管理请求将返回HTTP错误码`401`，代表认证失败。上传资源是例外，需要[上传凭证](#security-uploadtoken)，而不是身份凭证。身份凭证的作用是：

* 识别请求发起者为合法授权客户
* 保证服务端收到的请求内容未经中途篡改，具体包括代表请求的`URI`和该请求的参数信息均应未受到篡改。

每一个请求均需在HTTP请求头部增加一个`Authorization`字段，其值为合法的身份凭证的字符串，示例如下：

	GET /<URI> HTTP/1.1
	Host: effectapi.camera360.com
	Authorization: Camera360 <身份凭证>

#### 算法

1. 生成待签名的原始字符串：

	抽取请求 `URL` 中 `<path>` 或 `<path>?<query>` 的部分与请求内容部分即 `HTTP Body`，用 `\n` 连接起来。如无请求内容，`HTTP Body`部分必须为空字符串。
	
		signingStr = "<path>?<query>\n"
		或
		signingStr = "<path>?<query>\n<body>"

2. 使用 `SecertKey` 对上一步生成的原始字符串计算 `HMAC-SHA1` 签名：

		sign = hmac_sha1(signingStr, "<SecretKey>")

3. 对签名进行 `URL安全的Base64` 编码：

		encodedSign = urlsafe_base64_encode(sign)

4. 将 `AccessKey` 和 `encodedSign` 用英文符号 `:` 连接起来：

		accessToken = "<AccessKey>:<encodedSign>"
		
#### 示例
	# 假设有如下的请求：
	AccessKey = "MY_ACCESS_KEY"
	SecretKey = "MY_SECRET_KEY"
	url = "https://effectapi.camera360.com/uploadtoken"
	
	# 则待签名的原始字符串是：
	signingStr = "/uploadtoken\n"
	
	# 签名字符串是：
	sign = "eeecc1fa40922be5e46d6e455d093101dd03c88e"
	# 注意：签名结果是二进制数据，此处输出的是每个字节的十六进制表示，以便核对检查。
	
	# 编码后的签名字符串是：
	encodedSign = "7uzB-kCSK-XkbW5FXQkxAd0DyI4="
	
	# 最终的身份凭证是：
	accessToken = "MY_ACCESS_KEY:7uzB-kCSK-XkbW5FXQkxAd0DyI4="

### <a name="security-uploadtoken"></a>上传凭证
上传资源前需要先调用`/uploadtoken`获取上传凭证，并在上传资源时将上传凭证作为请求内容的一部分。不带上传凭证或带非法凭证的请求将返回HTTP错误码`401`，代表认证失败。上传凭证是用于验证上传请求合法性的机制，通过上传凭证授权客户端，使其具备访问指定资源的能力。

## 通用错误码
* 参数错误

		HTTP/1.1 400 Bad Request

* 身份凭证无效

		HTTP/1.1 401 Unauthorized
		
* 服务器错误

		HTTP/1.1 500 Internal Server Error

## API Reference
### <a name="api-uploadtoken"></a>获取上传凭证
上传凭证有效期为 1 小时，失效后会返回`401`表示权限认证失败。

	GET /uploadtoken

**授权方式**

[身份凭证](#security-idtoken)

**参数**

名称 | 类型 | 是否必须 | 描述
:----------|:---:|:--:|:--
uploadOnly | int | 否 | 是否只是上传。取值范围 0 - 1，默认值为 1。若为 0，表示上传后自动触发特效处理，同时上传时必须设置特效处理相关的参数，例如`x:filter`等。

**返回结果**

`uphost`是上传资源的URL地址，`key`是上传资源时的资源名，`token`是上传凭证。

	Status: 200 OK
	{
	    "uphost": "http://upload.qiniu.com/",
	    "key": "origin_595f2d7e826b3a4be511a91f",
	    "token": "5taQL1r-Ldksq6PxHJA68mjtuHROSIKZSwjgr76x:NP_i1DkVyPQmnqu6sN63t0nPSe4=:eyJpbnNlcnRPbmx5IjoxLCJlbmRVc2VyIjoiMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwIiwiY2FsbGJhY2tVcmwiOiJodHRwczpcL1wvZWZmZWN0YXBpdGVzdC5jYW1lcmEzNjAuY29tXC9jYWxsYmFjayIsImNhbGxiYWNrSG9zdCI6Imh0dHBzOlwvXC9lZmZlY3RhcGl0ZXN0LmNhbWVyYTM2MC5jb20iLCJjYWxsYmFja0JvZHkiOiJjaWQ9JHtlbmRVc2VyfSZrZXk9JHtrZXl9JmV0YWc9JHtldGFnfSZleHQ9JHtleHR9JmZpbHRlcj0ke3g6ZmlsdGVyfSZzdHJlbmd0aD0ke3g6c3RyZW5ndGh9JnJvdGF0ZUFuZ2xlPSR7eDpyb3RhdGVBbmdsZX0mbWlycm9yWD0ke3g6bWlycm9yWH0mbWlycm9yWT0ke3g6bWlycm9yWX0iLCJmc2l6ZUxpbWl0Ijo1MjQyODgwMCwiZGV0ZWN0TWltZSI6MSwibWltZUxpbWl0IjoiaW1hZ2VcLyoiLCJkZWxldGVBZnRlckRheXMiOjEsInNjb3BlIjoiZWZmZWN0Om9yaWdpbl81OTVmMmQ3ZTgyNmIzYTRiZTUxMWE5MWYiLCJkZWFkbGluZSI6MTQ5OTQxMzM5MCwidXBIb3N0cyI6WyJodHRwOlwvXC91cC5xaW5pdS5jb20iLCJodHRwOlwvXC91cGxvYWQucWluaXUuY29tIiwiLUggdXAucWluaXUuY29tIGh0dHA6XC9cLzE4My4xMzYuMTM5LjE2Il19"
	}

**错误码**

* 上传未授权

		HTTP/1.1 401 Unauthorized

* 请求超过频次限制

		HTTP/1.1 429 Too Many Requests

### 上传图片
上传图片到服务器，原图大小不超过 5MB，在服务器保存 1 天后随即删除。生成的特效图，同样只保存 1 天。

	POST :uphost
	
`:uphost`是[获取上传凭证](#api-uploadtoken)接口返回结果中的`uphost`字段。
	
**授权方式**

[上传凭证](#security-uploadtoken)

**参数**

名称 | 类型 | 是否必须 | 描述
:-------------|:------:|:--:|:--
key           | string | 是 | 资源名，[获取上传凭证](#api-uploadtoken)接口返回的`key`
token         | string | 是 | 必须是一个符合相应规格的[上传凭证](#security-uploadtoken)，否则会返回 401 表示权限认证失败。
file          | file   | 是 | 图片文件。
x:filter      | string | 是 | 滤镜名称，取值范围参考[滤镜列表][filters]。
x:strength    | int    | 否 | 滤镜强度，取值范围 0 - 100，默认值为 100。
x:rotateAngle | int    | 否 | 旋转角度，取值范围 0 - 360，默认值为 0。
x:mirrorX     | int    | 否 | 水平翻转。取值范围 0 - 1，默认值为 0。
x:mirrorY     | int    | 否 | 垂直翻转。取值范围 0 - 1，默认值为 0。

**返回结果**

`url`是特效图的下载地址。

	Status: 200 OK
	{
	    "url": "https://effect.c360dn.com/Fh1fsQStU39gimfqH1K6xg_pZzld?e=1499420774&token=5taQL1r-Ldksq6PxHJA68mjtuHROSIKZSwjgr76x:lJj9f5tfdaItupWrxd8cX_nuy7U="
	}

**错误码**

* 滤镜无效

		HTTP/1.1 400 Bad Request
		{
		    "message": "Invalid filter"
		}

* 滤镜未授权

		HTTP/1.1 401 Unauthorized
		{
		    "message": "Unauthorized filter"
		}

* 图片不存在

		HTTP/1.1 404 Not Found
		{
		    "message": "Key not exists"
		}

### 特效处理
生成的特效图只保存 1 天。切记只能在上传图片之后才能调用特效处理接口，否则会返回HTTP错误码`404`。

	POST /pics/:key/effects
	
`:key`是[获取上传凭证](#api-uploadtoken)接口返回结果中的`key`字段。
	
**授权方式**

[身份凭证](#security-idtoken)
	
**参数**

名称 | 类型 | 是否必须 | 描述
:-------------|:------:|:--:|:--
x:filter      | string | 是 | 滤镜名称，取值范围参考[滤镜列表][filters]。
x:strength    | int    | 否 | 滤镜强度，取值范围 0 - 100，默认值为 100。
x:rotateAngle | int    | 否 | 旋转角度，取值范围 0 - 360，默认值为 0。
x:mirrorX     | int    | 否 | 水平翻转。取值范围 0 - 1，默认值为 0。
x:mirrorY     | int    | 否 | 垂直翻转。取值范围 0 - 1，默认值为 0。
	
**返回结果**

`url`是特效图的下载地址。

	Status: 200 OK
	{
	    "url": "https://effect.c360dn.com/Fh1fsQStU39gimfqH1K6xg_pZzld?e=1499420774&token=5taQL1r-Ldksq6PxHJA68mjtuHROSIKZSwjgr76x:lJj9f5tfdaItupWrxd8cX_nuy7U="
	}

**错误码**

* 滤镜无效

		HTTP/1.1 400 Bad Request
		{
		    "message": "Invalid filter"
		}

* 滤镜未授权

		HTTP/1.1 401 Unauthorized
		{
		    "message": "Unauthorized filter"
		}

* 图片不存在

		HTTP/1.1 404 Not Found
		{
		    "message": "Key not exists"
		}

[filters]: https://github.com/pinguo/effectapi-php-sdk/blob/master/滤镜列表.md