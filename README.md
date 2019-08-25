# GravatarServer Typecho 插件
提供替换Gravatar服务器，**支持QQ头像加密地址**。  原作者 [LT21][1] [GravatarServer][2]

由于原作者LT21最后更新于2015年3月，原有的Gravatar镜像已多数失效。
因此我收集了一些Gravatar镜像

```php
'https://gravatar.loli.net/avatar' => 'Gravatar loli 镜像 ( https://gravatar.loli.net )',
'https://gravatar.cat.net/avatar' => 'Gravatar cat 镜像 ( https://gravatar.cat.net )',
'https://cdn.v2ex.com/gravatar' => 'Gravatar v2ex 镜像 ( https://cdn.v2ex.com )',
'https://dn-qiniu-avatar.qbox.me/avatar/' => 'Gravatar qiniu 镜像 ( https://dn-qiniu-avatar.qbox.me )',
'https://sdn.geekzu.org/avatar/' => 'Gravatar 极客 镜像 ( https://sdn.geekzu.org )',
'http://cn.gravatar.com/avatar' => 'Gravatar CN ( http://cn.gravatar.com )',
'https://secure.gravatar.com/avatar' => 'Gravatar Secure ( https://secure.gravatar.com )'
```

考虑
====

考虑到有些朋友没有gravatar头像，所以引入了**使用QQ头像**。
不久前一个朋友提到直接用头像会暴露qq，所以我找到了，腾讯qq头像**加密地址**。
比如`https://thirdqq.qlogo.cn/g?b=sdk&k=s7FaiaNibSwRuBKft2wGnMzw&s=100&t=1552706192`目前解析不出QQ号。

Github
====
下载:[https://github.com/kraity/GravatarServer][3]


  [1]: http://lt21.me
  [2]: https://github.com/LT21/GravatarServer
  [3]: https://github.com/kraity/GravatarServer
