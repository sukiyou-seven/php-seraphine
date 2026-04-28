<?php


namespace error_code {
# /**
#  * 阿里巴巴Java开发手册-崇山版-2020.08.03 错误码整理
#  * 错误码：
#  * 1. 五位组成
#  * 2. A代表用户端错误
#  * 3. B代表当前系统异常
#  * 4. C代表第三方服务异常
#  * 4. 若无法确定具体错误，选择宏观错误
#  * 6. 大的错误类间的步长间距预留100
#  */
# /**
#  * 成功
#  */
    const SUCCESS = array("code" => "00000", "message" => "success", "data" => "", "num" => "", "status" => "");

    const SUCCESS_kong = array("code" => "00000", "message" => "NO Data", "data" => array(), "num" => false, "status" => false);

# /**
#  * 一级宏观错误码
#  */
    const USER_ERROR_0001 = array("code" => "A0001", "message" => "用户端错误", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0100 = array("code" => "A0100", "message" => "用户注册错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0101 = array("code" => "A0101", "message" => "用户未同意隐私协议", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0102 = array("code" => "A0102", "message" => "注册国家或地区受限", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0110 = array("code" => "A0110", "message" => "用户名校验失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0110_1 = array("code" => "A0110_1", "message" => "用户信息缺失，请联系物业管理人员", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0111 = array("code" => "A0111", "message" => "用户名已存在", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0112 = array("code" => "A0112", "message" => "用户名包含敏感词", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0113 = array("code" => "A0113", "message" => "用户名包含特殊字符", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0120 = array("code" => "A0120", "message" => "密码校验失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0121 = array("code" => "A0121", "message" => "密码长度不够", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0122 = array("code" => "A0122", "message" => "密码强度不够", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0130 = array("code" => "A0130", "message" => "校验码输入错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0131 = array("code" => "A0131", "message" => "短信校验码输入错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0132 = array("code" => "A0132", "message" => "邮件校验码输入错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0133 = array("code" => "A0133", "message" => "语音校验码输入错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0140 = array("code" => "A0140", "message" => "用户证件异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0141 = array("code" => "A0141", "message" => "用户证件类型未选择", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0142 = array("code" => "A0142", "message" => "大陆身份证编号校验非法", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0143 = array("code" => "A0143", "message" => "护照编号校验非法", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0144 = array("code" => "A0144", "message" => "军官证编号校验非法", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0150 = array("code" => "A0150", "message" => "用户基本信息校验失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0151 = array("code" => "A0151", "message" => "手机格式校验失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0152 = array("code" => "A0152", "message" => "地址格式校验失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0153 = array("code" => "A0153", "message" => "邮箱格式校验失败", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0200 = array("code" => "A0200", "message" => "用户登录异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0200_1 = array("code" => "A0200_1", "message" => "用户未登录", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0201 = array("code" => "A0201", "message" => "用户账户不存在", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0202 = array("code" => "A0202", "message" => "用户账户被冻结", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0203 = array("code" => "A0203", "message" => "用户账户已作废", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0210 = array("code" => "A0210", "message" => "用户密码错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0211 = array("code" => "A0211", "message" => "用户输入密码错误次数超限", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0212 = array("code" => "A0210", "message" => "用户账户或密码错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0220 = array("code" => "A0220", "message" => "用户身份校验失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0221 = array("code" => "A0221", "message" => "用户指纹识别失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0222 = array("code" => "A0222", "message" => "用户面容识别失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0223 = array("code" => "A0223", "message" => "用户未获得第三方登录授权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0230 = array("code" => "A0230", "message" => "用户登录已过期", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0240 = array("code" => "A0240", "message" => "用户验证码错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0241 = array("code" => "A0241", "message" => "用户验证码尝试次数超限", "data" => "", "num" => "", "status" => "");


    const USER_ERROR_A0290 = array("code" => "A0290", "message" => "该房间不存在", "data" => "", "num" => "", "status" => "");


# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0300 = array("code" => "A0300", "message" => "访问权限异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0301 = array("code" => "A0301", "message" => "访问未授权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0302 = array("code" => "A0302", "message" => "正在授权中", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0303 = array("code" => "A0303", "message" => "用户授权申请被拒绝", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0310 = array("code" => "A0310", "message" => "因访问对象隐私设置被拦截", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0311 = array("code" => "A0311", "message" => "授权已过期", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0312 = array("code" => "A0312", "message" => "无权限使用 API", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0320 = array("code" => "A0320", "message" => "用户访问被拦截", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0321 = array("code" => "A0321", "message" => "黑名单用户", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0322 = array("code" => "A0322", "message" => "账号被冻结", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0323 = array("code" => "A0323", "message" => "非法 IP 地址", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0324 = array("code" => "A0324", "message" => "网关访问受限", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0325 = array("code" => "A0325", "message" => "地域黑名单", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0330 = array("code" => "A0330", "message" => "服务已欠费", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0340 = array("code" => "A0340", "message" => "用户签名异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0341 = array("code" => "A0341", "message" => "RSA 签名错误", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0400 = array("code" => "A0400", "message" => "用户请求参数错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_18 = array("code" => "A040018", "message" => "未查询到支付结果", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_500 = array("code" => "A0400_500", "message" => "用户请求数据错误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_1 = array("code" => "A0400_1", "message" => "报价金额必须大于1", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_2 = array("code" => "A0400_2", "message" => "至少上传一张图片", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_3 = array("code" => "A0400_3", "message" => "工程师未确认完成", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_4 = array("code" => "A0400_4", "message" => "您还未支付尾款", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_5 = array("code" => "A0400_5", "message" => "输入的邀请码有误", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_6 = array("code" => "A0400_6", "message" => "不能邀请自己哦", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_7 = array("code" => "A0400_7", "message" => "余额不足,至少提现200元", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_8 = array("code" => "A0400_8", "message" => "记录已经存在", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_9 = array("code" => "A0400_9", "message" => "不可以互相邀请", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_10 = array("code" => "A0400_10", "message" => "选项不可以小于一个", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_11 = array("code" => "A0400_11", "message" => "装饰装修类别仅可认证一项", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_12 = array("code" => "A0400_12", "message" => "该项分类已在认证中，请勿重复认证", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0400_13 = array("code" => "A0400_13", "message" => "该项分类已被添加，请勿重复添加", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0401 = array("code" => "A0401", "message" => "包含非法恶意跳转链接", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0402 = array("code" => "A0402", "message" => "无效的用户输入", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0410 = array("code" => "A0410", "message" => "请求必填参数为空", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0411 = array("code" => "A0411", "message" => "用户订单号为空", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0412 = array("code" => "A0412", "message" => "订购数量为空", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0413 = array("code" => "A0413", "message" => "缺少时间戳参数", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0414 = array("code" => "A0414", "message" => "非法的时间戳参数", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0420 = array("code" => "A0420", "message" => "请求参数值超出允许的范围", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0421 = array("code" => "A0421", "message" => "参数格式不匹配", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0422 = array("code" => "A0422", "message" => "地址不在服务范围", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0422_1 = array("code" => "A0422_1", "message" => "经纬度缺失", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0423 = array("code" => "A0423", "message" => "时间不在服务范围", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0424 = array("code" => "A0424", "message" => "金额超出限制", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0425 = array("code" => "A0425", "message" => "商品数量不足", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0426 = array("code" => "A0426", "message" => "请求批量处理总个数超出限制", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0427 = array("code" => "A0427", "message" => "请求 JSON 解析失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0430 = array("code" => "A0430", "message" => "用户输入内容非法", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0431 = array("code" => "A0431", "message" => "包含违禁敏感词", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0432 = array("code" => "A0432", "message" => "图片包含违禁信息", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0433 = array("code" => "A0433", "message" => "文件侵犯版权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0440 = array("code" => "A0440", "message" => "用户操作异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0441 = array("code" => "A0441", "message" => "用户支付超时", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0442 = array("code" => "A0442", "message" => "确认订单超时", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0443 = array("code" => "A0443", "message" => "订单已关闭", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0444 = array("code" => "A0444", "message" => "用户支付失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0445 = array("code" => "A0445", "message" => "订单创建失败", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0446 = array("code" => "A0446", "message" => "设置已存在", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0446_1 = array("code" => "A0446_1", "message" => "技能已存在", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0446_2 = array("code" => "A0446_2", "message" => "暂无题库", "data" => "", "num" => "", "status" => "");
    const USER_ERROR_A0499 = array("code" => "A0499", "message" => "安全密码错误", "data" => "", "num" => "", "status" => "");
    const USER_ERROR_A0480 = array("code" => "A0480", "message" => "该小区存在楼栋,无法删除", "data" => "", "num" => "", "status" => "");
    const USER_ERROR_A0481 = array("code" => "A0481", "message" => "该楼栋存在房间,无法删除", "data" => "", "num" => "", "status" => "");
    const USER_ERROR_A0482 = array("code" => "A0482", "message" => "该房间存在人员,无法删除", "data" => "", "num" => "", "status" => "");
    const USER_ERROR_A0483 = array("code" => "A0483", "message" => "重复录入", "data" => "", "num" => "", "status" => "");
    const USER_ERROR_A0484 = array("code" => "A0484", "message" => "该,无法删除", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0500 = array("code" => "A0500", "message" => "用户请求服务异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0501 = array("code" => "A0501", "message" => "请求次数超出限制", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0502 = array("code" => "A0502", "message" => "请求并发数超出限制", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0503 = array("code" => "A0503", "message" => "用户操作请等待", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0504 = array("code" => "A0504", "message" => "WebSocket 连接异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0505 = array("code" => "A0505", "message" => "WebSocket 连接断开", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0506 = array("code" => "A0506", "message" => "用户重复请求", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0600 = array("code" => "A0600", "message" => "用户资源异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0601 = array("code" => "A0601", "message" => "账户余额不足", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0602 = array("code" => "A0602", "message" => "用户磁盘空间不足", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0603 = array("code" => "A0603", "message" => "用户内存空间不足", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0604 = array("code" => "A0604", "message" => "用户 OSS 容量不足", "data" => "", "num" => "", "status" => "");

# /**
#  * 例如：每天抽奖数
#  */
    const USER_ERROR_A0605 = array("code" => "A0605", "message" => "用户配额已用光", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0700 = array("code" => "A0700", "message" => "用户上传文件异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0701 = array("code" => "A0701", "message" => "用户上传文件类型不匹配", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0702 = array("code" => "A0702", "message" => "用户上传文件太大", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0703 = array("code" => "A0703", "message" => "用户上传图片太大", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0704 = array("code" => "A0704", "message" => "用户上传视频太大", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0705 = array("code" => "A0705", "message" => "用户上传压缩文件太大", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0800 = array("code" => "A0800", "message" => "用户当前版本异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0801 = array("code" => "A0801", "message" => "用户安装版本与系统不匹配", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0802 = array("code" => "A0802", "message" => "用户安装版本过低", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0803 = array("code" => "A0803", "message" => "用户安装版本过高", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0804 = array("code" => "A0804", "message" => "用户安装版本已过期", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0805 = array("code" => "A0805", "message" => "用户 API 请求版本不匹配", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0806 = array("code" => "A0806", "message" => "用户 API 请求版本过高", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0807 = array("code" => "A0807", "message" => "用户 API 请求版本过低", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A0900 = array("code" => "A0900", "message" => "用户隐私未授权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0901 = array("code" => "A0901", "message" => "用户隐私未签署", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0902 = array("code" => "A0902", "message" => "用户摄像头未授权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0903 = array("code" => "A0903", "message" => "用户相机未授权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0904 = array("code" => "A0904", "message" => "用户图片库未授权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0905 = array("code" => "A0905", "message" => "用户文件未授权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0906 = array("code" => "A0906", "message" => "用户位置信息未授权", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A0907 = array("code" => "A0907", "message" => "用户通讯录未授权", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const USER_ERROR_A1000 = array("code" => "A1000", "message" => "用户设备异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A1001 = array("code" => "A1001", "message" => "用户相机异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A1002 = array("code" => "A1002", "message" => "用户麦克风异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A1003 = array("code" => "A1003", "message" => "用户听筒异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A1004 = array("code" => "A1004", "message" => "用户扬声器异常", "data" => "", "num" => "", "status" => "");

    const USER_ERROR_A1005 = array("code" => "A1005", "message" => "用户 GPS 定位异常", "data" => "", "num" => "", "status" => "");


# /**
#  * 系统异常
#  * 一级宏观错误码
#  */
    const SYSTEM_ERROR_B0001 = array("code" => "B0001", "message" => "系统执行出错", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const SYSTEM_ERROR_B0100 = array("code" => "B0100", "message" => "系统执行超时", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0101 = array("code" => "B0101", "message" => "系统订单处理超时", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const SYSTEM_ERROR_B0200 = array("code" => "B0200", "message" => "系统容灾功能被触发", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0210 = array("code" => "B0210", "message" => "系统限流", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0220 = array("code" => "B0220", "message" => "系统功能降级", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const SYSTEM_ERROR_B0300 = array("code" => "B0300", "message" => "系统资源异常", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0310 = array("code" => "B0310", "message" => "系统资源耗尽", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0311 = array("code" => "B0311", "message" => "系统磁盘空间耗尽", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0312 = array("code" => "B0312", "message" => "系统内存耗尽", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0313 = array("code" => "B0313", "message" => "文件句柄耗尽", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0314 = array("code" => "B0314", "message" => "系统连接池耗尽", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0315 = array("code" => "B0315", "message" => "系统线程池耗尽", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0320 = array("code" => "B0320", "message" => "系统资源访问异常", "data" => "", "num" => "", "status" => "");

    const SYSTEM_ERROR_B0321 = array("code" => "B0321", "message" => "系统读取磁盘文件失败", "data" => "", "num" => "", "status" => "");


#
# /**
#  * 调用第三方服务
#  * 一级宏观错误码
#  */
    const SERVICE_ERROR_C0001 = array("code" => "C0001", "message" => "调用第三方服务出错", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const SERVICE_ERROR_C0100 = array("code" => "C0100", "message" => "中间件服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0110 = array("code" => "C0110", "message" => "RPC 服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0111 = array("code" => "C0111", "message" => "RPC 服务未找到", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0112 = array("code" => "C0112", "message" => "RPC 服务未注册", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0113 = array("code" => "C0113", "message" => "接口不存在", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0120 = array("code" => "C0120", "message" => "消息服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0121 = array("code" => "C0121", "message" => "消息投递出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0122 = array("code" => "C0122", "message" => "消息消费出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0123 = array("code" => "C0123", "message" => "消息订阅出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0124 = array("code" => "C0124", "message" => "消息分组未查到", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0130 = array("code" => "C0130", "message" => "缓存服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0131 = array("code" => "C0131", "message" => "key 长度超过限制", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0132 = array("code" => "C0132", "message" => "value 长度超过限制", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0133 = array("code" => "C0133", "message" => "存储容量已满", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0134 = array("code" => "C0134", "message" => "不支持的数据格式", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0140 = array("code" => "C0140", "message" => "配置服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0150 = array("code" => "C0150", "message" => "网络资源服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0151 = array("code" => "C0151", "message" => "VPN 服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0152 = array("code" => "C0152", "message" => "CDN 服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0153 = array("code" => "C0153", "message" => "域名解析服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0154 = array("code" => "C0154", "message" => "网关服务出错", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const SERVICE_ERROR_C0200 = array("code" => "C0200", "message" => "第三方系统执行超时", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0210 = array("code" => "C0210", "message" => "RPC 执行超时", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0220 = array("code" => "C0220", "message" => "消息投递超时", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0230 = array("code" => "C0230", "message" => "缓存服务超时", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0240 = array("code" => "C0240", "message" => "配置服务超时", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0250 = array("code" => "C0250", "message" => "数据库服务超时", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const SERVICE_ERROR_C0300 = array("code" => "C0300", "message" => "数据库服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0311 = array("code" => "C0311", "message" => "表不存在", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0312 = array("code" => "C0312", "message" => "列不存在", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0321 = array("code" => "C0321", "message" => "多表关联中存在多个相同名称的列", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0331 = array("code" => "C0331", "message" => "数据库死锁", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0341 = array("code" => "C0341", "message" => "主键冲突", "data" => "", "num" => "", "status" => "");
    const SERVICE_ERROR_C0342 = array("code" => "C0342", "message" => "数据更新异常", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const SERVICE_ERROR_C0400 = array("code" => "C0400", "message" => "第三方容灾系统被触发", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0401 = array("code" => "C0401", "message" => "第三方系统限流", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0402 = array("code" => "C0402", "message" => "第三方功能降级", "data" => "", "num" => "", "status" => "");

# /**
#  * 二级宏观错误码
#  */
    const SERVICE_ERROR_C0500 = array("code" => "C0500", "message" => "通知服务出错", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0501 = array("code" => "C0501", "message" => "短信提醒服务失败", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0502 = array("code" => "C0502", "message" => "语音提醒服务失败", "data" => "", "num" => "", "status" => "");

    const SERVICE_ERROR_C0503 = array("code" => "C0503", "message" => "邮件提醒服务失败", "data" => "", "num" => "", "status" => "");


}