<?php
/**
 * 主题选项配置文件
 */

$gdk_options = [
	'优化选项' => [
		[
			'name'  => '禁用新版编辑器',
			'desc'  => '新版编辑器尚不成熟，很多主题不兼容，建议开启',
			'id'    => 'gdk_diasble_gutenberg',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用头部冗余代码',
			'desc'  => 'WordPress头部自带很多无用代码，不安全且浪费，建议开启',
			'id'    => 'gdk_diasble_head_useless',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用WordPress更新',
			'desc'  => 'WordPress更新会不时发送请求数据，所以可以关闭WordPress更新，包括主题，插件和内核更新,默认禁用',
			'id'    => 'gdk_diasble_wp_update',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '0'
		],
		[
			'name'  => '禁用Emojis功能',
			'desc'  => 'WordPress的Emojis功能会加载国外资源，那是网站速度，所以建议开启',
			'id'    => 'gdk_disable_emojis',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用XML-RPC功能',
			'desc'  => '该功能有安全风险，如果不使用WordPress的手机客户端或者第三方编辑器软件，那么建议开启',
			'id'    => 'gdk_disable_xmlrpc',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用文章版本功能',
			'desc'  => '该功能有会造成数据库体量暴增为了你的数据库考虑，建议开启',
			'id'    => 'gdk_disable_revision',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用pingback功能',
			'desc'  => '该功能会增加垃圾评论的几率，建议开启',
			'id'    => 'gdk_disable_trackbacks',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '文件上传重命名',
			'desc'  => '该功能会将zip,rar,7z格式以外的所有全部数字重命名，服务器文件不建议使用中文,默认开启',
			'id'    => 'gdk_upload_rename',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => 'WordPress更新中国加速',
			'desc'  => '该功能会帮助顺利更新WordPress，突破429，注意：如果上面的WordPress禁用更新了，此处设置无效，建议开启',
			'id'    => 'gdk_porxy_update',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '头像加速功能',
			'desc'  => '该功能会增加头像加载速度，有随机头像，V2EX头像镜像和七牛头像镜像，默认选择随机头像',
			'id'    => 'gdk_switch_get_avatar',
			'type'  => 'radio',
			'options' => [
				'1' => '随机头像',
				'2' => 'V2EX头像镜像',
				'3' => '七牛头像镜像',
			],
			'std'   => '3'
		]
	],
	'SEO设置' => [
		[
			'name'  => '网站关键字',//选项显示的文字，选填
			'desc'  => '各关键字间用半角逗号','分割，数量在6个以内最佳。',//选项显示的一段描述文字，选填
			'id'    => 'git_keywords',//选项的id，必须是唯一，后面根据这个获取值，必填
			'type'  => 'text',//种类，这个是普通的文字输入，必填
			'std'   => ''//选项的默认值，选填
		],
		[
			'name'  => '网站描述',
			'desc'  => '用简洁的文字描述本站点，字数建议在120个字以内。',
			'id'    => 'git_description',
			'type'  => 'text'
		],
		[
			'name'  => 'title分隔符',
			'desc'  => '显示在浏览器标题栏的一个用来风格网站名字的',
			'id'    => 'git_delimiter',
			'type'  => 'text',
			'std'   => '|'
		],
		[
			'name'  => '自动添加nofollow',
			'desc'  => '该功能会给外链自动添加nofollow,默认开启',
			'id'    => 'gdk_nofollow',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '分类去Category优化',
			'desc'  => '该功能会给外链自动添加nofollow,默认开启',
			'id'    => 'gdk_no_category',
			'type'  => 'checkbox'
		],
		[
			'name'  => '文章自动内链',
			'desc'  => '该功能会将文章中与标签匹配的文字自动添加标签链接,如果是纯文字的内容建议打开,默认禁用',
			'id'    => 'gdk_tag_link',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '0'
		],
		[
			'name'  => '关键词链接次数',
			'desc'  => '文章中最多链接的次数，默认是5',
			'id'    => 'gdk_tag_num',
			'type'  => 'number',
			'std'   => 5
		],
		[
			'name'  => 'Robots.txt 优化',
			'desc'  => '该功能会自动生成一个虚拟Robots.txt文件,和真实文件效果一样的,默认开启',
			'id'    => 'gdk_robots',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁止蜘蛛爬取作者页面',
			'desc'  => '该功能会屏蔽蜘蛛访问作者页面,如果是单作者网站没必要展示作者页,根据自己网站实际情况选择是否开启,默认禁用',
			'id'    => 'gdk_no_author_page',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '0'
		],
		[
			'name'  => '网站地图 sitemap_xml',
			'desc'  => '该功能会自动生成网站地图链接:域名/sitemap_xml,开启后建议更新固定链接一次,默认开启',
			'id'    => 'gdk_sitemap_xml',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '文章图片自动添加alt以及title',
			'desc'  => '该功能会自动给文章中图片添加alt和title,并且是按照文章标题进行命名,默认开启',
			'id'    => 'gdk_seo_img',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '外链自动GO跳转',
			'desc'  => '启用 【启用之后需要新建页面，模板选择Go跳转页面，别名为go】',
			'id'    => 'git_go',
			'type'  => 'checkbox'
		],
		[
			'name'  => '百度自动&主动推送',
			'desc'  => '该功能会自动给文章中图片添加alt和title,并且是按照文章标题进行命名,默认开启',
			'id'    => 'gdk_baidu_push',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '主动推送接口地址，填写本项即开启推送',
			'desc'  => '在百度站长平台获取主动推送接口地址，比如：http://data.zz.baidu.com/urls?site=域名&token=一组字符, <a class="key_word" rel="nofollow" href="http://zhanzhang.baidu.com/linksubmit/index" target="_blank">主动推送接口地址</a>',
			'id'    => 'gdk_baidu_api',
			'type'  => 'text'
		]
	],
	'安全设置' => [
		[
			'name'  => '屏蔽各种不正常的请求',
			'desc'  => '该功能会将各种不正常的请求比如破解,注入类的屏蔽掉，默认开启',
			'id'    => 'gdk_block_requst',
			'type'  => 'radio',
			'options' => [
				'1' => '开启',
				'0' => '禁用'
			],
			'std'   => '1'
		],
		[
			'name'  => '保护用户暴露用户名',
			'desc'  => '开启 【启用后，将隐藏掉用户的登录名,起到保护作用】',
			'id'    => 'gdk_hide_user_name',
			'type'  => 'checkbox'
		],
		[
			'title' => '登陆安全防御',
			'type'  => 'title'
		],
		[
			'name'  => '登陆安全保护',
			'desc'  => '该功能会将连续多次登陆错误的用户暂时锁定，待解锁后方可重新登陆，默认开启',
			'id'    => 'gdk_lock_login',
			'type'  => 'radio',
			'options' => [
				'1' => '开启',
				'0' => '禁用'
			],
			'std'   => '1'
		],
		[
			'name'  => '登录失败最大次数',
			'desc'  => '默认：5',
			'id'    => 'gdk_failed_login_limit',
			'type'  => 'number',
			'std'   => 5
		],
		[
			'name'  => '登录失败锁定时间',
			'desc'  => '单位秒，默认：60',
			'id'    => 'gdk_lockout_duration',
			'type'  => 'number',
			'std'   => 60
		],
		[
			'title' => '垃圾评论屏蔽',
			'type'  => 'title'
		],
		[
			'name'  => '过滤外语评论',
			'desc'  => '开启 【启用后，将屏蔽所有含有日文以及英语的评论，外贸站慎用】',
			'id'    => 'git_spam_lang',
			'type'  => 'checkbox'
		],
		[
			'name'  => '关键词，IP，邮箱屏蔽',
			'desc'  => '开启 【启用后，在WordPress-设置-讨论-黑名单中添加想要屏蔽的关键词，邮箱，网址，IP地址，每行一个】<a class="key_word" target="_blank" href="https://img.alicdn.com/imgextra/i4/1597576229/TB2FnxnlpXXXXcDXXXXXXXXXXXX_!!1597576229.png">如图设置</a>',
			'id'    => 'git_spam_keywords',
			'type'  => 'checkbox'
		],
		[
			'name'  => '屏蔽含有链接的评论',
			'desc'  => '开启 【启用后，屏蔽内容或者评论昵称含有链接的评论，如果您的评论需要输入链接或者图片的话，请慎选！！！】',
			'id'    => 'git_spam_url',
			'type'  => 'checkbox'
		],
		[
			'name'  => '屏蔽长链接评论',
			'desc'  => '开启 【启用后，屏蔽含有过长网址(超过50个字符)的评论，当然如果你已经选择了上面的选项的话，就不用选择了】',
			'id'    => 'git_spam_long',
			'type'  => 'checkbox'
		],
		[
			'name'  => '文章版权声明',
			'desc'  => '此处输入的文字将出现在每篇文章最底部，你可以使用：{{title}}表示文章标题，{{link}}表示文章链接',
			'id'    => 'git_copyright_b',
			'type'  => 'textarea',
			'std'   => '极客公园 , 版权所有丨如未注明 , 均为原创丨本网站采用<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" rel="nofollow" target="_blank" title="BY-NC-SA授权协议">BY-NC-SA</a>协议进行授权 <br >转载请注明原文链接：<a href="{{link}}" target="_blank" title="{{title}}">{{title}}</a>'
		]
	],
	'支付设置' => [
		[
			'title' => '统一支付设置',
			'type'  => 'title'
		],
		[
			'name'  => '金币和RMB兑换关系',
			'desc'  => '请输入兑换关系，比如1RMB=10金币，请慎重选择，一旦设置好后面不能修改的',
			'id'    => 'git_payjs_rate',
			'type'  => 'number',
			'std'   => 10
		],
		[
			'name'  => '选择一个支付方式',
			'desc'  => '两种方案选择其中一种，必须选择一个哦',
			'id'    => 'git_payjs',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'title' => 'PayJs支付设置&nbsp;&nbsp;&nbsp;<a href="https://payjs.cn/ref/ZVEMKD" target="_blank" >注册PayJs</a>&nbsp;&nbsp;&nbsp;【微信官方，微信正规渠道，强烈推荐】',
			'type'  => 'title'
		],
		[
			'name'  => 'PayJs商户号',
			'desc'  => '',
			'id'    => 'git_payjs_id',
			'type'  => 'text',
			'std'   => 2333333333
		],
		[
			'name'  => 'PayJs密钥',
			'desc'  => '',
			'id'    => 'git_payjs_secret',
			'type'  => 'text',
			'std'   => 444444444
		]
	],
	'高级设置' => [
			[
				'name'  => 'jQuery来源设置',
				'desc'  => '选择一个适合自己的jQuery公共库来源',
				'id'    => 'git_jq',
				'type'  => 'radio',
				'options' => [
					'1' => '远程jQuery库【底部加载，速度快，兼容差】',
					'0' => '本地jQuery库【头部加载，速度慢，兼容好】'
				],
				'std'   => '1'
			],
			[
			'name'  => 'HTML代码压缩',
			'desc'  => '启用 【开启后，将压缩网页HTML代码，可读性会降低，但是性能略有提升】',
			'id'    => 'git_compress',
			'type'  => 'checkbox'
		],
			[
			'name'  => '图片懒加载',
			'desc'  => '启用 【开启后，网站图片将进行懒加载】',
			'id'    => 'git_lazyload',
			'type'  => 'checkbox'
		],
		[
			'name'  => '侧边栏缓存',
			'desc'  => '启用 【开启后，将会自动缓存小工具，如果想禁止缓存某个小工具，可以去小工具页面排除】',
			'id'    => 'git_sidebar_cache',
			'type'  => 'checkbox'
		],
		[
			'name'  => '开启前台弹窗登录',
			'desc'  => '如果启用UM插件,最好开启',
			'id'    => 'git_fancylogin',
			'type'  => 'checkbox'
		],
		[
			'title' => 'CDN镜像加速',
			'type'  => 'title'
		],
		[
			'name'  => 'CDN镜像加速',
			'desc'  => '开启本功能可以将本站静态资源同步到远程CDN服务器，减轻本站的流量压力，提高网站整体速度，国内主流CDN服务商[七牛，又拍，阿里OSS，腾讯COS，华为BOS等等]均支持，默认关闭，配置好服务器端后开启',
			'id'    => 'gdk_cdn',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '0'
		],
		[
			'name'  => 'CDN域名',
			'desc'  => '输入您的CDN域名，一般需要到cdn后台获取，必须带 <span class="key_word">http(s):// ，且结尾不能带/ </span>',
			'id'    => 'gdk_cdn_host',
			'type'  => 'text',
			'std'   => ''
		],
		[
			'name'  => 'CDN镜像文件格式',
			'desc'  => '在输入框内添加准备镜像的文件格式，比如png|jpg|jpeg|gif|ico（使用|分隔）',
			'id'    => 'gdk_cdn_ext',
			'type'  => 'text',
			'std'   => 'png|jpg|jpeg|gif|ico|html|7z|zip|rar|pdf|ppt|wmv|mp4|avi|mp3|txt'
		],
		[
			'name'  => 'CDN镜像目录',
			'desc'  => '在输入框内添加准备镜像的文件夹，默认为wp-content|wp-includes（使用|分隔）',
			'id'    => 'gdk_cdn_dir',
			'type'  => 'text',
			'std'   => 'wp-content|wp-includes'
		],
		[
			'name'  => 'CDN自定义缩略图样式',
			'desc'  => '国内CDN服务商均支持的功能，使用自定义图片样式功能进行裁剪缩略图，默认使用的分隔符为【!】,默认开启',
			'id'    => 'gdk_cdn_style',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => 'CDN水印',
			'desc'  => '启用【如果启用，请在七牛，又拍，OSS等CDN中设置自定义样式，名字为：<span class="key_word">water.jpg</span>，分隔符为<span class="key_word">!</span> 】',
			'id'    => 'git_cdn_water',
			'type'  => 'checkbox'
		],
		[
			'name'  => 'CDN镜像后台化',
			'desc'  => '启用【一般可不启用，如果您启用CDN镜像之后并在FTP删除了本地文件，则必须开启】',
			'id'    => 'git_adminqn_b',
			'type'  => 'checkbox'
		],
		[
			'title' => '微信登录设置',
			'type'  => 'title'
		],
		[
			'name'  => '是否启用微信扫码登录',
			'desc'  => '启用 【开启后，新建微信登录页面即可，另外需要HTTPS】',
			'id'    => 'git_weauth_oauth',
			'type'  => 'checkbox'
		],
		[
			'name'  => '是否启用强制微信登录',
			'desc'  => '启用 【开启后，将禁用WordPress自带的登录，所有登录地址都跳转到微信的登录，如需临时使用自带登录，可以使用这个链接：你的域名/wp-login.php?loggedout=true】',
			'id'    => 'git_weauth_oauth_force',
			'type'  => 'checkbox'
		],
		[
			'title' => '微信推送设置',
			'type'  => 'title'
		],
		[
			'name'  => '评论微信推送提醒',
			'desc'  => '启用【开启后，如果网站有新的评论，可以给您的微信推送提醒，这个只是给网站管理员提醒，不涉及访客】',
			'id'    => 'git_Server',
			'type'  => 'checkbox'
		],
		[
			'name'  => '微信推送KEY',
			'desc'  => '请输入您的微信推送KEY',
			'id'    => 'git_Server_key',
			'type'  => 'text',
			'std'   => ''
		],
		[
			'name'  => '微信订阅号/公众号二维码',
			'desc'  => '请输入您的微信订阅号/公众号二维码图片链接，不要想得太多，只是给主题调用的。',
			'id'    => 'git_mp_qr',
			'type'  => 'text',
			'std'   => ''
		],
		[
			'name'  => '微信验证码',
			'desc'  => '请输入您的微信验证码，这里的必须要要和微信里面回复的保持一致。',
			'id'    => 'git_mp_code',
			'type'  => 'text',
			'std'   => '2233'
		],
		[
			'name'  => '微信可见提示信息，可用html代码',
			'desc'  => '在本输入框内输入您的微信公众号描述信息，支持html代码，字数合适就行，不能太多',
			'id'    => 'git_mp_tips',
			'type'  => 'textarea',
			'std'   => '请关注极客公园官方微信公众号，关注并订阅<span class="key_word">云落极客公园</span>获取验证码。在微信里搜索<span class="key_word">云落极客公园</span>或者微信扫描二维码都可以关注极客公园官方微信公众号。'
		],
		[
			'title' => 'HTML5 桌面推送',
			'type'  => 'title'
		],
		[
			'name'  => 'HTML5推送标题【必选】',
			'desc'  => '显示在弹窗顶部',
			'id'    => 'git_notification_title',
			'type'  => 'text',
			'std'   => 'Hi，你好'
		],
		[
			'name'  => 'HTML5推送间隔【必选】',
			'desc'  => '输入数字，当自动关闭或者用户关闭之后多久再次弹窗，默认10天',
			'id'    => 'git_notification_days',
			'type'  => 'number',
			'std'   => 10
		],
		[
			'name'  => 'HTML5推送COOKIE【必选】',
			'desc'  => '修改COOKIE值可以强制向访客推送新的信息，无视时间间隔，不能使用中文，默认233',
			'id'    => 'git_notification_cookie',
			'type'  => 'text',
			'std'   => '233'
		],
		[
			'name'  => 'HTML5推送图片【必选】',
			'desc'  => '填写一个正方形的图片，显示在推送信息左侧，默认为默认头像',
			'id'    => 'git_notification_icon',
			'type'  => 'text',
			'std'   => '',
		],
		[
			'name'  => 'HTML5推送链接【可选】',
			'desc'  => '当用户点击弹窗的时候说点击的链接，默认为极客公园',
			'id'    => 'git_notification_link',
			'type'  => 'text',
			'std'   => 'https://gitcafe.net'
		],
		[
			'name'  => 'HTML5推送内容',
			'desc'  => '在这里输入推送主体内容，字数合适就行，不能太多【必选】',
			'id'    => 'git_notification_body',
			'type'  => 'textarea',
			'std'   => '极客公园，一个分享有趣的安卓APP和实用的WordPress技术以及Windows使用技巧的网站'
		],
		[
			'title' => 'SMTP邮箱设置',
			'type'  => 'title'
		],
		[
			'name'  => 'SMTP邮箱发送',
			'desc'  => '该功能利用第三方SMTP邮箱服务发送邮件,比如评论邮件,需要配置好再开启',
			'id'    => 'gdk_smtp',
			'type'  => 'radio',
			'options' => [
				'0' => '禁用',
				'1' => '开启'
			],
			'std'   => '0'
		],
		[
			'name'  => '发件人昵称',
			'desc'  => '请输入您的网站名称,比如:云落',
			'id'    => 'gdk_smtp_username',
			'type'  => 'text',
			'std'   => ''
		],
		[
			'name'  => 'SMTP服务器地址',
			'desc'  => '请输入您的邮箱的SMTP服务器，<a class="key_word" target="_blank" href="https://blog.csdn.net/whyhonest/article/details/7289420">点击查看常用SMTP地址</a>',
			'id'    => 'gdk_smtp_host',
			'type'  => 'text',
			'std'   => 'smtp.qq.com'
		],
		[
			'name'  => 'SMTP服务器端口',
			'desc'  => '请输入您的smtp端口，一般QQ邮箱推荐使用465端口',
			'id'    => 'gdk_smtp_port',
			'type'  => 'number',
			'std'   => 465
		],
		[
			'name'  => '邮箱账号',
			'desc'  => '请输入您的邮箱地址，比如云落的sp91@qq.com',
			'id'    => 'gdk_smtp_mail',
			'type'  => 'text',
			'std'   => ''
		],
		[
			'name'  => '邮箱密码',
			'desc'  => '请输入您的邮箱授权码,<span class="key_word">注意不同邮箱服务器的密码不一样,有的是密码,有的比如QQ邮箱就是授权码</span>',
			'id'    => 'gdk_smtp_password',
			'type'  => 'password',
			'std'   => ''
		],
		[
			'title' => '自定义代码',
			'type'  => 'title'
		],
		[
			'name'  => '头部自定义代码',
			'desc'  => '代码将插入到head区域',
			'id'    => 'gdk_custom_head_code',
			'type'  => 'textarea',
			'std'   => ''
		],
		[
			'name'  => '底部自定义代码',
			'desc'  => '代码将插入到foot区域',
			'id'    => 'gdk_custom_foot_code',
			'type'  => 'textarea',
			'std'   => ''
		]
	]
];