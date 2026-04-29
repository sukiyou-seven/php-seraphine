单纯的不想再用 node flask gin 了
也不想学 php框架 自己撸一个自己用


## 生产模式请 务必关闭 debug开关 

```yaml
# 在 config/app.yml 
debug: false
```
请访问路由 [GET]
```http
/help
```
### Api
自动生成接口文档 在 /help 中可以查看看
```php
/**
 * 创建新用户
 * @param string $username 用户名  
 * @param string $email 邮箱
 * @return string 是否成功
 * @example
 * {
 *   "username": "zhangsan",
 *   "email": "zhangsan@example.com"
 * }
 */
# 自动解析 param ,return ,example 内容 并生成文档 
# 目前也仅仅只是 生成一份普普通通的文档 能看 但没有 Apifox 那么多功能
```

### controllers 内不支持驼峰命名法

### 编写代码

- 在application/controllers 下创建文件/文件夹即可
- controllers/ 下面的文件 需使用 class , 类名 ***必须*** 是文件名第一个字母大写
- 文件名 ***不支持*** ***驼峰命名法***  但可使用 ***下划线命名法***
- 例如: controllers/phone_number.php 类名就为 Phone_number
- 或: controllers/phone_number/phone_number.php 类名就为 Phone_number


### 伪静态

``` apacheconf
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

error_page 404 /404.html;
error_page 403 /403.html;
error_page 500 /500.php;
error_page 502 /502.html;
error_page 320 /320.php;
```

### 已装修页面
请在 seraphine/error_page 下修改

### 配置文件   
配置文件全部放在 config/ 

该模式仅支持 yaml 配置文件

使用其他配置文件 请自行编写文件读取函数

### 数据库
- 默认使用 mongodb 数据库 
- config/db.yml 至少需要 [775] 权限
- 0.0.2 增加了mysql支持

