# Laravel vben

一个基础laravel框架组件, 配合vue-vben-admin

**本仓库为后端部分**

使用包

- casbin 鉴权 [https://github.com/php-casbin/laravel-authz](https://github.com/php-casbin/laravel-authz)
- jwt 用户验证 [https://github.com/tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
- 雪花id [https://github.com/godruoyi/php-snowflake](https://github.com/godruoyi/php-snowflake)
- 无限级分类 [https://github.com/lazychaser/laravel-nestedset](https://github.com/lazychaser/laravel-nestedset)

ER 图查看 ./laravel-vben ER.drawio

Apifox 在线地址 [https://www.apifox.cn/apidoc/shared-39d721f7-b20b-4b27-8f1e-7666469da059](https://www.apifox.cn/apidoc/shared-39d721f7-b20b-4b27-8f1e-7666469da059)

## 运行项目

使用 sail 作为开发环境, 必须装有docker

```shell
sail up -d
```

初始化数据库

```shell
sail php artisan migrate --seed
```

浏览 http://localhost/admin

## 运行前端后台项目

```shell
cd ./laravel-vben-admin
pnpm install
pnpm dev
```

### Todo:

结构分层 Service层


er实体关系图 转移到 Confluence 上


### BUGs:
