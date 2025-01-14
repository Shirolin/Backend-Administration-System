# 项目概述

- 项目中文名：后台管理系统
- 项目英文名：Backend Administration System
- 项目代号：BAS

BAS 是一个基于 Laravel-Admin 的后台管理系统，用于管理用户、角色、权限、菜单、日志等信息。
管理员可管理教师、学生用户。教师可管理学生用户。

# 功能如下

- 用户认证——登录、退出
- 用户管理——用户列表、用户添加、用户编辑、用户删除
- 角色管理——角色列表、角色添加、角色编辑、角色删除
- 权限管理——权限列表、权限添加、权限编辑、权限删除
- 菜单管理——菜单列表、菜单添加、菜单编辑、菜单删除
- 日志管理——日志列表、日志删除
- 教师管理——教师列表、教师添加、教师编辑、教师删除
- 学生管理——学生列表、学生添加、学生编辑、学生删除

# 运行环境要求

- PHP = 7.3.33
- PostgreSQL = 13.0 +

# 开发环境部署/安装

本项目代码使用 PHP 框架 Laravel-Admin 1.8 开发，基于 Laravel 5.5 。请确保安装了对应的 PHP 版本。

### 基础安装

1. 克隆源代码到本地：

```shell
git clone https://github.com/Shirolin/Backend-Administration-System
```

2. 进入项目目录安装依赖：

```shell
composer install
```

3. 复制 `.env.example` 为 `.env`：

```shell
cp .env.example .env
```

4. 生成应用秘钥：

```shell
php artisan key:generate
```

5. 创建数据库，数据库配置如下：

```shell
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

6. 数据库迁移：

```shell
php artisan migrate
```

7. 数据填充：

```shell
php artisan db:seed
```

8. 本地开发运行：

```shell
php artisan serve
```

9. 浏览器访问 `http://localhost:8000/admin` 即可看到项目运行效果。

管理员账号密码如下：

```shell
username: admin
password: admin
```

至此，安装完成。

# 扩展包使用情况

| 扩展包名称 | 一句话描述 | 本项目应用场景 |
| --- | --- | --- |
| encore/laravel-admin | Laravel 的后台管理扩展 | 后台管理系统基础扩展包 |
| predis/predis | Redis 官方首推的 PHP 客户端开发包 | 缓存驱动 Redis 基础扩展包 |
| manzhouya/auth-attempts | Laravel-Admin 验证码扩展 | 登录验证码扩展包 |

# 自定义 Artisan 命令

- 无

# 队列清单

- 无