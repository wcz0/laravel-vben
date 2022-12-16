# Laravel vben

一个基础laravel框架组件, 配合vue-vben-admin

**本仓库为后端部分**

使用包

- jwt-auth 作为用户验证
- casbin(laravel-authz) 作为权限管理
- nestedset 无限级分类
- 

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

### BUGs:

* ~~权限不能排序~~
* xdebug 失效
* 添加权限不用指定实体_test
