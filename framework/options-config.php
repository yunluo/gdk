<?php
/**
 * 插件选项配置文件
 */

$gdk_options = [
    '优化选项'  => [
        [
            'name'    => '禁用新版编辑器',
            'desc'    => '新版编辑器尚不成熟，很多主题不兼容，建议开启',
            'id'      => 'gdk_diasble_gutenberg',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '禁用新版小工具',
            'desc'    => '新版小工具尚不成熟，很多主题不兼容，建议开启',
            'id'      => 'gdk_diasble_widgets_block',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '禁用头部冗余代码',
            'desc'    => 'WordPress头部自带很多无用代码，不安全且浪费，建议开启',
            'id'      => 'gdk_diasble_head_useless',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '禁用WordPress更新',
            'desc'    => 'WordPress更新会不时发送请求数据，所以可以关闭WordPress更新，包括主题，插件和内核更新,默认禁用',
            'id'      => 'gdk_diasble_wp_update',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '0',
        ],
        [
            'name'    => '禁用Emojis功能',
            'desc'    => 'WordPress的Emojis功能会加载国外资源，那是网站速度，所以建议开启',
            'id'      => 'gdk_disable_emojis',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '前台禁用dashicons字体和编辑器资源',
            'desc'    => '一般前台不需要加载dashicons字体，默认开启',
            'id'      => 'gdk_disable_dashicons',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name' => '中英文自动空格',
            'desc' => '启用 【开启后，中文和英文之前将自动增加一个空格,比如:WordPress插件=>WordPress 插件】',
            'id'   => 'gdk_auto_space',
            'type' => 'checkbox',
        ],
        [
            'name'    => '禁用XML-RPC功能',
            'desc'    => '该功能有安全风险，如果不使用WordPress的手机客户端或者第三方编辑器软件，那么建议开启',
            'id'      => 'gdk_disable_xmlrpc',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '禁用文章版本功能',
            'desc'    => '该功能有会造成数据库体量暴增为了你的数据库考虑，建议开启',
            'id'      => 'gdk_disable_revision',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '禁用pingback功能',
            'desc'    => '该功能会增加垃圾评论的几率，建议开启',
            'id'      => 'gdk_disable_trackbacks',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '文件上传重命名',
            'desc'    => '该功能会将zip,rar,7z格式以外的所有全部数字重命名，服务器文件不建议使用中文,默认开启',
            'id'      => 'gdk_upload_rename',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => 'WordPress更新中国加速',
            'desc'    => '该功能会帮助顺利更新WordPress，突破429，注意：如果上面的WordPress禁用更新了，此处设置无效，建议开启',
            'id'      => 'gdk_porxy_update',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '头像加速功能',
            'desc'    => '该功能会增加头像加载速度，有随机头像，V2EX头像镜像和七牛头像镜像，默认选择随机头像',
            'id'      => 'gdk_switch_get_avatar',
            'type'    => 'radio',
            'options' => [
                '1' => '随机头像',
                '2' => 'V2EX头像镜像',
                '3' => '七牛头像镜像',
                '4' => 'Cravatar',
            ],
            'std'     => '3',
        ],
    ],
    'SEO设置' => [
        [
            'name' => '网站关键字', //选项显示的文字，选填
             'desc' => '各关键字间用半角逗号', '分割，数量在6个以内最佳。', //选项显示的一段描述文字，选填
             'id'   => 'gdk_keywords', //选项的id，必须是唯一，后面根据这个获取值，必填
             'type' => 'text', //种类，这个是普通的文字输入，必填
             'std'  => '', //选项的默认值，选填
        ],
        [
            'name' => '网站描述',
            'desc' => '用简洁的文字描述本站点，字数建议在120个字以内。',
            'id'   => 'gdk_description',
            'type' => 'text',
        ],
        [
            'name' => 'title分隔符',
            'desc' => '显示在浏览器标题栏的一个用来风格网站名字的',
            'id'   => 'gdk_delimiter',
            'type' => 'text',
            'std'  => '|',
        ],
        [
            'name'    => '自动添加nofollow',
            'desc'    => '该功能会给外链自动添加nofollow,默认开启',
            'id'      => 'gdk_nofollow',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name' => '分类去Category优化',
            'desc' => '该功能会给外链自动添加nofollow,默认开启',
            'id'   => 'gdk_no_category',
            'type' => 'checkbox',
        ],
        [
            'name'    => '文章自动内链',
            'desc'    => '该功能会将文章中与标签匹配的文字自动添加标签链接,如果是纯文字的内容建议打开,默认禁用',
            'id'      => 'gdk_tag_link',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '0',
        ],
        [
            'name' => '关键词链接次数',
            'desc' => '文章中最多链接的次数，默认是5',
            'id'   => 'gdk_tag_num',
            'type' => 'number',
            'std'  => 5,
        ],
        [
            'name'    => 'Robots.txt 优化',
            'desc'    => '该功能会自动生成一个虚拟Robots.txt文件,和真实文件效果一样的,默认开启',
            'id'      => 'gdk_robots',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '禁止蜘蛛爬取作者页面',
            'desc'    => '该功能会屏蔽蜘蛛访问作者页面,如果是单作者网站没必要展示作者页,根据自己网站实际情况选择是否开启,默认禁用',
            'id'      => 'gdk_no_author_page',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '0',
        ],
        [
            'name'    => '网站地图 sitemap',
            'desc'    => '该功能会自动生成网站地图[xml版和html版],链接:域名/sitemap.xml,域名/sitemap.html,开启后建议更新固定链接一次,默认开启',
            'id'      => 'gdk_sitemap_xml',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => '文章图片自动添加alt以及title',
            'desc'    => '该功能会自动给文章中图片添加alt和title,并且是按照文章标题进行命名,默认开启',
            'id'      => 'gdk_seo_img',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name' => '外链GO跳转',
            'desc' => '启用',
            'id'   => 'gdk_link_go',
            'type' => 'checkbox',
        ],
        [
            'name'    => '百度自动&主动推送',
            'desc'    => '该功能会自动给文章中图片添加alt和title,并且是按照文章标题进行命名,默认开启',
            'id'      => 'gdk_baidu_push',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '0',
        ],
        [
            'name' => '主动推送接口token',
            'desc' => '在百度站长平台获取主动推送token，比如：http://data.zz.baidu.com/urls?site=xxoo&token=<span class="key_word">一组字符</span>, 需要填写的是红色部分,<a class="key_word" rel="nofollow" href="http://zhanzhang.baidu.com/linksubmit/index" target="_blank">主动推送接口地址</a>',
            'id'   => 'gdk_baidu_token',
            'type' => 'text',
        ],
    ],
    '安全设置'  => [
        [
            'name'    => '屏蔽各种不正常的请求',
            'desc'    => '该功能会将各种不正常的请求比如破解,注入类的屏蔽掉，默认开启',
            'id'      => 'gdk_block_requst',
            'type'    => 'radio',
            'options' => [
                '1' => '开启',
                '0' => '禁用',
            ],
            'std'     => '1',
        ],
        [
            'name' => '网站维护模式',
            'desc' => '开启 【启用后，未登录用户将看到一个简陋的维护页面】',
            'id'   => 'gdk_maintenance_mode',
            'type' => 'checkbox',
        ],
        [
            'name' => '禁用REST API功能',
            'desc' => '禁用 【启用后，REST API功能将关闭,如果没不使用该功能的话,建议关闭】',
            'id'   => 'gdk_disable_restapi',
            'type' => 'checkbox',
        ],
        [
            'name' => '保护用户暴露用户名',
            'desc' => '开启 【启用后，将隐藏掉用户的登录名,起到保护作用】',
            'id'   => 'gdk_hide_user_name',
            'type' => 'checkbox',
        ],
        [
            'title' => '登陆安全防御',
            'type'  => 'title',
        ],
        [
            'name'    => '登陆安全保护',
            'desc'    => '该功能会将连续多次登陆错误的用户暂时锁定，待解锁后方可重新登陆，默认开启',
            'id'      => 'gdk_lock_login',
            'type'    => 'radio',
            'options' => [
                '1' => '开启',
                '0' => '禁用',
            ],
            'std'     => '1',
        ],
        [
            'name' => '登录失败最大次数',
            'desc' => '默认：5',
            'id'   => 'gdk_failed_login_limit',
            'type' => 'number',
            'std'  => 5,
        ],
        [
            'name' => '登录失败锁定时间',
            'desc' => '单位秒，默认：60',
            'id'   => 'gdk_lockout_duration',
            'type' => 'number',
            'std'  => 60,
        ],
        [
            'name' => '登录失败邮件通知',
            'desc' => '启用 【开启后，将所有登陆失败信息发邮件通知管理员】',
            'id'   => 'gdk_login_email',
            'type' => 'checkbox',
        ],
        [
            'name' => '登陆数学验证',
            'desc' => '启用 【开启后，将会登陆页面增加数学验证码】',
            'id'   => 'gdk_login_verify',
            'type' => 'checkbox',
        ],
        [
            'title' => '垃圾评论屏蔽',
            'type'  => 'title',
        ],
        [
            'name'    => '垃圾评论拦截',
            'desc'    => '该功能会默认屏蔽垃圾评论,支持纯外语拦截,日语拦截[外贸站慎用],关键词黑名单拦截,请务必选择对应主题的评论方式,关键词黑名单如图设置<a class="key_word" target="_blank" href="https://ae03.alicdn.com/kf/U146356e193b14a6da3f7cbb9cf507ea3D.png">点击查看如图设置</a>',
            'id'      => 'gdk_fuck_spam',
            'type'    => 'radio',
            'options' => [
                '1' => '开启',
                '0' => '禁用',
            ],
            'std'     => '1',
        ],
    ],
    '高级设置'  => [
        [
            'name'    => '图片懒加载',
            'desc'    => '该功能会降低因为图片而导致的打开速度慢问题，建议开启',
            'id'      => 'gdk_lazyload',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '1',
        ],
        [
            'name'    => 'jQuery加载位置设置',
            'desc'    => '选择一个适合自己网站的jQuery加载位置,默认是底部加载',
            'id'      => 'gdk_jq',
            'type'    => 'radio',
            'options' => [
                '1' => '底部加载,速度快',
                '0' => '头部加载,兼容好',
            ],
            'std'     => '1',
        ],
        [
            'name' => '友情链接分类ID',
            'desc' => '请选择专门用来存放友链的链接分类ID',
            'id'   => 'gdk_link_id',
            'type' => 'number',
        ],
        [
            'name' => 'HTML代码压缩',
            'desc' => '启用 【开启后，将压缩网页HTML代码，可读性会降低，但是性能略有提升】',
            'id'   => 'gdk_compress',
            'type' => 'checkbox',
        ],
        [
            'name' => '文章目录',
            'desc' => '启用 【开启后，将在文章页显示一个文章目录】',
            'id'   => 'gdk_article_list',
            'type' => 'checkbox',
        ],
        [
            'name' => '侧边栏缓存',
            'desc' => '启用 【开启后，将会自动缓存小工具，如果想禁止缓存某个小工具，可以去小工具页面排除】',
            'id'   => 'gdk_sidebar_cache',
            'type' => 'checkbox',
        ],
        [
            'name' => '新窗口打开',
            'desc' => '禁用 【禁用后，所有文章链接将当前窗口打开】',
            'id'   => 'gdk_target_blank',
            'type' => 'checkbox',
        ],
        [
            'title' => '统一支付设置',
            'type'  => 'title',
        ],
        [
            'name'    => '网站支付功能',
            'desc'    => '开启网站支付功能需要HTTPS支持,需要开通Payjs使用,<a class="key_word" href="https://payjs.cn/ref/ZVEMKD" target="_blank" >点击注册Payjs</a>',
            'id'      => 'gdk_payjs',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '0',
        ],
        [
            'name' => '支付宝支付通道',
            'desc' => '启用 【开启后，将增加支付宝支付方式,支付宝通道需要到payjs申请开通,如果开通的话,建议使用支付宝,因为费率便宜】',
            'id'   => 'gdk_payjs_alipay',
            'type' => 'checkbox',
        ],
        [
            'name' => '金币和RMB兑换关系',
            'desc' => '请输入兑换关系，默认1RMB=10金币，请慎重选择，一旦设置好后面不能修改的,本选项仅对会员金币支付生效,游客免登陆支持不受影响',
            'id'   => 'gdk_rate',
            'type' => 'number',
            'std'  => 10,
        ],
        [
            'name' => 'PayJs商户号',
            'desc' => '',
            'id'   => 'gdk_payjs_id',
            'type' => 'text',
            'std'  => 2333333333,
        ],
        [
            'name' => 'PayJs密钥',
            'desc' => '',
            'id'   => 'gdk_payjs_key',
            'type' => 'text',
            'std'  => 444444444,
        ],
        [
            'title' => 'CDN镜像加速',
            'type'  => 'title',
        ],
        [
            'name'    => 'CDN镜像加速',
            'desc'    => '开启本功能可以将本站静态资源同步到远程CDN服务器，减轻本站的流量压力，提高网站整体速度，国内主流CDN服务商[七牛，又拍，阿里OSS，腾讯COS，华为BOS等等]均支持，默认关闭，配置好服务器端后开启',
            'id'      => 'gdk_cdn',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '0',
        ],
        [
            'name' => 'CDN域名',
            'desc' => '输入您的CDN域名，一般需要到cdn后台获取，必须带 <span class="key_word">http(s):// ，且结尾不能带/ </span>',
            'id'   => 'gdk_cdn_host',
            'type' => 'text',
            'std'  => '',
        ],
        [
            'name' => 'CDN镜像文件格式',
            'desc' => '在输入框内添加准备镜像的文件格式，比如png|jpg|jpeg|gif|ico（使用|分隔）',
            'id'   => 'gdk_cdn_ext',
            'type' => 'text',
            'std'  => 'png|jpg|jpeg|gif|ico|html|7z|zip|rar|pdf|ppt|wmv|mp4|avi|mp3|txt',
        ],
        [
            'name' => 'CDN镜像目录',
            'desc' => '在输入框内添加准备镜像的文件夹，默认为wp-content|wp-includes（使用|分隔）',
            'id'   => 'gdk_cdn_dir',
            'type' => 'text',
            'std'  => 'wp-content|wp-includes',
        ],
        [
            'name'    => 'CDN服务商',
            'desc'    => '选择使用国内CDN服务商,此项主要影响CDN缩略图,请选择对应服务商',
            'id'      => 'gdk_cdn_serves',
            'type'    => 'radio',
            'options' => [
                '1' => '七牛云',
                '2' => '又拍云',
                '3' => '腾讯云',
                '4' => '阿里云',
                '5' => '华为云',
            ],
            'std'     => '4',
        ],
        [
            'name' => 'CDN水印',
            'desc' => '启用【如果启用，请在七牛，又拍，OSS等CDN中设置自定义样式，名字为：<span class="key_word">water.jpg</span>，分隔符为<span class="key_word">!</span> 】',
            'id'   => 'gdk_cdn_water',
            'type' => 'checkbox',
        ],
        [
            'title' => '微信登录设置',
            'type'  => 'title',
        ],
        [
            'name' => '是否启用微信扫码登录',
            'desc' => '启用 【开启后，新建微信登录页面即可，另外需要HTTPS】',
            'id'   => 'gdk_weauth_oauth',
            'type' => 'checkbox',
        ],
        [
            'name' => '是否启用强制微信登录',
            'desc' => '启用 【开启后，将禁用WordPress自带的登录，所有登录地址都跳转到微信的登录，如需临时使用自带登录，可以使用这个链接：你的域名/wp-login.php?loggedout=true】',
            'id'   => 'gdk_weauth_force',
            'type' => 'checkbox',
        ],
        [
            'title' => '微信推送设置',
            'type'  => 'title',
        ],
        [
            'name' => '评论微信推送提醒',
            'desc' => '启用【开启后，如果网站有新的评论，可以给您的微信推送提醒，这个只是给网站管理员提醒，不涉及访客】',
            'id'   => 'gdk_Server',
            'type' => 'checkbox',
        ],
        [
            'name' => '微信公众号TOKEN',
            'desc' => '请输入您的微信公众号TOKEN,微信公众号后台获取',
            'id'   => 'gdk_wxmp_token',
            'type' => 'text',
            'std'  => '',
        ],
        [
            'name' => '微信推送KEY',
            'desc' => '请输入您的微信推送KEY',
            'id'   => 'gdk_Server_key',
            'type' => 'text',
            'std'  => '',
        ],
        [
            'name' => '微信订阅号/公众号二维码',
            'desc' => '请输入您的微信订阅号/公众号二维码图片链接，不要想得太多，只是给主题调用的。',
            'id'   => 'gdk_mp_qr',
            'type' => 'text',
            'std'  => '',
        ],
        [
            'title' => 'HTML5 桌面推送',
            'type'  => 'title',
        ],
        [
            'name'    => 'HTML5 桌面推送',
            'desc'    => '该功能开启后会在浏览器进行信息推送,需要高级浏览器支持,默认关闭',
            'id'      => 'gdk_h5notice',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '0',
        ],
        [
            'name' => 'HTML5推送标题【必选】',
            'desc' => '显示在弹窗顶部',
            'id'   => 'gdk_notification_title',
            'type' => 'text',
            'std'  => 'Hi，你好',
        ],
        [
            'name' => 'HTML5推送间隔【必选】',
            'desc' => '输入数字，当自动关闭或者用户关闭之后多久再次弹窗，默认10天',
            'id'   => 'gdk_notification_days',
            'type' => 'number',
            'std'  => 10,
        ],
        [
            'name' => 'HTML5推送COOKIE【必选】',
            'desc' => '修改COOKIE值可以强制向访客推送新的信息，无视时间间隔，不能使用中文，默认233',
            'id'   => 'gdk_notification_cookie',
            'type' => 'text',
            'std'  => '233',
        ],
        [
            'name' => 'HTML5推送图片【必选】',
            'desc' => '填写一个正方形的图片，显示在推送信息左侧，默认为默认头像',
            'id'   => 'gdk_notification_icon',
            'type' => 'text',
            'std'  => '',
        ],
        [
            'name' => 'HTML5推送链接【可选】',
            'desc' => '当用户点击弹窗的时候说点击的链接，默认为极客公园',
            'id'   => 'gdk_notification_link',
            'type' => 'text',
            'std'  => 'https://gitcafe.net',
        ],
        [
            'name' => 'HTML5推送内容',
            'desc' => '在这里输入推送主体内容，字数合适就行，不能太多【必选】',
            'id'   => 'gdk_notification_body',
            'type' => 'textarea',
            'std'  => '极客公园，一个分享有趣的安卓APP和实用的WordPress技术以及Windows使用技巧的网站',
        ],
        [
            'title' => 'SMTP邮箱设置',
            'type'  => 'title',
        ],
        [
            'name'    => 'SMTP邮箱发送',
            'desc'    => '该功能利用第三方SMTP邮箱服务发送邮件,比如评论邮件,需要配置好再开启',
            'id'      => 'gdk_smtp',
            'type'    => 'radio',
            'options' => [
                '0' => '禁用',
                '1' => '开启',
            ],
            'std'     => '0',
        ],
        [
            'name' => '发件人昵称',
            'desc' => '请输入您的网站名称,比如:云落',
            'id'   => 'gdk_smtp_username',
            'type' => 'text',
            'std'  => '',
        ],
        [
            'name' => 'SMTP服务器地址',
            'desc' => '请输入您的邮箱的SMTP服务器，<a class="key_word" target="_blank" href="https://blog.csdn.net/whyhonest/article/details/7289420">点击查看常用SMTP地址</a>',
            'id'   => 'gdk_smtp_host',
            'type' => 'text',
            'std'  => 'smtp.qq.com',
        ],
        [
            'name' => 'SMTP服务器端口',
            'desc' => '请输入您的smtp端口，一般QQ邮箱推荐使用465端口',
            'id'   => 'gdk_smtp_port',
            'type' => 'number',
            'std'  => 465,
        ],
        [
            'name' => '邮箱账号',
            'desc' => '请输入您的邮箱地址，比如云落的sp91@qq.com',
            'id'   => 'gdk_smtp_mail',
            'type' => 'text',
            'std'  => '',
        ],
        [
            'name' => '邮箱密码',
            'desc' => '请输入您的邮箱授权码,<span class="key_word">注意不同邮箱服务器的密码不一样,有的是密码,有的比如QQ邮箱就是授权码</span>',
            'id'   => 'gdk_smtp_password',
            'type' => 'password',
            'std'  => '',
        ],
        [
            'title' => '自定义代码',
            'type'  => 'title',
        ],
        [
            'name' => '网站头部自定义代码',
            'desc' => '代码将插入到head区域',
            'id'   => 'gdk_custom_head_code',
            'type' => 'textarea',
            'std'  => '',
        ],
        [
            'name' => '网站底部自定义代码',
            'desc' => '代码将插入到foot区域',
            'id'   => 'gdk_custom_foot_code',
            'type' => 'textarea',
            'std'  => '',
        ],
        [
            'name' => '文章顶部自定义内容',
            'desc' => '在文章顶部插入一个内容',
            'id'   => 'gdk_artical_top',
            'type' => 'textarea',
            'std'  => '',
        ],
        [
            'name' => '文章底部自定义内容',
            'desc' => '在文章底部插入一个内容',
            'id'   => 'gdk_artical_bottom',
            'type' => 'textarea',
            'std'  => '',
        ],
    ],
];

//载入主题配置,默认路径在主题根目录options.php
if (file_exists(get_template_directory() . '/options.php')) {
    include get_template_directory() . '/options.php';
}

if (!empty($gdk_theme_options)) {
    $gdk_options = array_merge($gdk_theme_options, $gdk_options);
}

//var_dump($gdk_options);
