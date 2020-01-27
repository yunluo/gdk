<?php
/**
 * 主题选项配置文件
 */

$gdk_options = [
	'优化选项' => [
		[
			'name'  => '新版编辑器开关',
			'desc'  => '新版编辑器尚不成熟，很多主题不兼容，建议禁用',
			'id'    => 'gdk_diasble_gutenberg',
			'type'  => 'radio',
			'options' => [
				'1' => '禁用',
				'0' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用头部冗余代码',
			'desc'  => 'WordPress头部自带很多无用代码，不安全且浪费，建议禁用',
			'id'    => 'gdk_diasble_head_useless',
			'type'  => 'radio',
			'options' => [
				'1' => '禁用',
				'0' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用WordPress更新',
			'desc'  => 'WordPress更新会不时发送请求数据，所以可以关闭WordPress更新，包括主题，插件和内核更新',
			'id'    => 'gdk_diasble_wp_update',
			'type'  => 'radio',
			'options' => [
				'1' => '禁用',
				'0' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用Emojis功能',
			'desc'  => 'WordPress的Emojis功能会加载国外资源，那是网站速度，所以建议禁用',
			'id'    => 'gdk_disable_emojis',
			'type'  => 'radio',
			'options' => [
				'1' => '禁用',
				'0' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用XML-RPC功能',
			'desc'  => '该功能有安全风险，如果不使用WordPress的手机客户端或者第三方编辑器软件，那么建议禁用',
			'id'    => 'gdk_disable_xmlrpc',
			'type'  => 'radio',
			'options' => [
				'1' => '禁用',
				'0' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用文章版本功能',
			'desc'  => '该功能有会造成数据库体量暴增为了你的数据库考虑，建议禁用',
			'id'    => 'gdk_disable_revision',
			'type'  => 'radio',
			'options' => [
				'1' => '禁用',
				'0' => '开启'
			],
			'std'   => '1'
		],
		[
			'name'  => '禁用pingback功能',
			'desc'  => '该功能会增加垃圾评论的几率，建议禁用',
			'id'    => 'gdk_disable_trackbacks',
			'type'  => 'radio',
			'options' => [
				'1' => '禁用',
				'0' => '开启'
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
			'std'   => '1'
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
			'name'  => '图片自动添加alt以及title',
			'desc'  => '启用',
			'id'    => 'git_imgalt_b',
			'type'  => 'checkbox'
		],
		[
			'name'  => '外链自动GO跳转',
			'desc'  => '启用 【启用之后需要新建页面，模板选择Go跳转页面，别名为go】',
			'id'    => 'git_go',
			'type'  => 'checkbox'
		],
		[
			'name'  => '外链自动添加nofollow',
			'desc'  => '启用',
			'id'    => 'git_nofollow',
			'type'  => 'checkbox'
		],
		[
			'name'  => 'Robot.txt优化',
			'desc'  => '启用 【开启本项之后，将只对搜索引擎开放首页，页面，文章页，其他一律屏蔽】',
			'id'    => 'git_robot_b',
			'type'  => 'checkbox'
		],
		[
			'title' => '百度主动推送 <a href="http://zhanzhang.baidu.com/linksubmit/index" target="_blank">查看主动推送效果</a>',//标题文字
			'type'  => 'title'//title 是标签下的标题
		],
		[
			'name'  => '主动推送接口地址，填写本项即开启推送',
			'desc'  => '在百度站长平台获取主动推送接口地址，比如：http://data.zz.baidu.com/urls?site=域名&token=一组字符, <a class="button-primary" rel="nofollow" href="http://zhanzhang.baidu.com/linksubmit/index" target="_blank">主动推送接口地址</a>',
			'id'    => 'git_sitemap_api',
			'type'  => 'text'
		]
	],
	'安全设置' => [
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
			'desc'  => '开启 【启用后，在WordPress-设置-讨论-黑名单中添加想要屏蔽的关键词，邮箱，网址，IP地址，每行一个】<a class="button-primary" target="_blank" href="https://img.alicdn.com/imgextra/i4/1597576229/TB2FnxnlpXXXXcDXXXXXXXXXXXX_!!1597576229.png">如图设置</a>',
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
			'title' => '评论设置属性',
			'type'  => 'title'
		],
		[
			'name'  => '贴图',
			'desc'  => '不显示',
			'id'    => 'git_tietu',
			'type'  => 'checkbox'
		],
		[
			'name'  => '加粗',
			'desc'  => '不显示',
			'id'    => 'git_jiacu',
			'type'  => 'checkbox'
		],
		[
			'name'  => '删除线',
			'desc'  => '不显示',
			'id'    => 'git_shanchu',
			'type'  => 'checkbox'
		],
		[
			'name'  => '居中',
			'desc'  => '不显示',
			'id'    => 'git_juzhong',
			'type'  => 'checkbox'
		],
		[
			'name'  => '斜体',
			'desc'  => '不显示',
			'id'    => 'git_xieti',
			'type'  => 'checkbox'
		],
		[
			'name'  => '签到',
			'desc'  => '不显示',
			'id'    => 'git_qiandao',
			'type'  => 'checkbox'
		],
		[
			'title' => '评论VIP设置',
			'type'  => 'title'
		],
		[
			'name'  => '启用',
			'desc'  => ' 【启用之后，您需要在下面设置用户的评论数字区间】',
			'id'    => 'git_vip',
			'type'  => 'checkbox'
		],
		[
			'name'  => 'VIP 1',
			'desc'  => '输入的数字减一就是VIP 1的所要求的评论数字区间，默认是5',
			'id'    => 'git_vip1',
			'type'  => 'number',
			'std'   => 5
		],
		[
			'name'  => 'VIP 2',
			'desc'  => '输入的数字减去上面的数字就是VIP 2的所要求的评论数字区间,默认是10',
			'id'    => 'git_vip2',
			'type'  => 'number',
			'std'   => 10
		],
		[
			'name'  => 'VIP 3',
			'desc'  => '输入的数字减去上面的数字就是VIP 3的所要求的评论数字区间，默认是20',
			'id'    => 'git_vip3',
			'type'  => 'number',
			'std'   => 20
		],
		[
			'name'  => 'VIP 4',
			'desc'  => '输入的数字减去上面的数字就是VIP 4的所要求的评论数字区间，默认是40',
			'id'    => 'git_vip4',
			'type'  => 'number',
			'std'   => 30
		],
		[
			'name'  => 'VIP 5',
			'desc'  => '输入的数字减去上面的数字就是VIP 5的所要求的评论数字区间，默认是70',
			'id'    => 'git_vip5',
			'type'  => 'number',
			'std'   => 40
		],
		[
			'name'  => 'VIP 6',
			'desc'  => '输入的数字减去上面的数字就是VIP 6的所要求的评论数字区间，默认是110',
			'id'    => 'git_vip6',
			'type'  => 'number',
			'std'   => 50
		],
		[
			'name'  => '文章摘要',
			'desc'  => '个字',
			'id'    => 'git_excerpt_length',
			'type'  => 'number',
			'std'   => 180
		],
		[
			'name'  => '文章二维码',
			'desc'  => '启用',
			'id'    => 'git_qr_b',
			'type'  => 'checkbox'
		],
		[
			'name'  => '作者模块',
			'desc'  => '启用',
			'id'    => 'git_auther_b',
			'type'  => 'checkbox'
		],
		[
			'name'  => '文章目录索引',
			'desc'  => '启用  【开启之后，默认索引文章H2标题】',
			'id'    => 'git_article_list',
			'type'  => 'checkbox'
		],
		[
			'name'  => '相关文章显示条数',
			'desc'  => '条&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 这是是显示文章下面的相关文章数目的',
			'id'    => 'git_related_count',
			'type'  => 'number',
			'std'   => 8
		],
		[
			'name'  => '禁止站内文章Pingback',
			'desc'  => '开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 开启后，不会发送站内Pingback，建议开启',
			'id'    => 'git_pingback_b',
			'type'  => 'checkbox'
		],
		[
			'name'  => '禁止后台编辑时自动保存',
			'desc'  => '开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 开启后，后台编辑文章时候不会定时保存，有效缩减数据库存储量；但是，一般不建议开启，除非你的数据库容量很小',
			'id'    => 'git_autosave_b',
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
			'id'    => 'git_chongzhi_dh',
			'type'  => 'number',
			'std'   => 10
		],
		[
			'name'  => '选择一个支付方式',
			'desc'  => '两种方案选择其中一种，必须选择一个哦',
			'id'    => 'git_pay_way',
			'type'  => 'radio',
			'options' => [
				'git_payjs_ok' => '调用Payjs支付',
				'git_eapay_ok' => '调用简付支付'
			],
			'std'   => 'git_payjs_ok'
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
		],
		[
			'title' => '简付支付设置&nbsp;&nbsp;&nbsp;<a href="https://b.eapay.cc" target="_blank" >注册简付</a>',
			'type'  => 'title'
		],
		[
			'name'  => '简付App ID',
			'desc'  => '',
			'id'    => 'git_eapay_id',
			'type'  => 'text',
			'std'   => 2333333333
		],
		[
			'name'  => '简付App Key',
			'desc'  => '',
			'id'    => 'git_eapay_secret',
			'type'  => 'text',
			'std'   => 444444444
		]
	],
	'高级设置' => [
			[
				'name'  => 'jQuery来源设置',
				'desc'  => '选择一个适合自己的jQuery公共库来源',
				'id'    => 'git_jqcdn',
				'type'  => 'radio',
				'options' => [
					'git_jqcdn_upai' => '远程jQuery库【底部加载，速度快，兼容差】',
					'git_jqcdn_bendi' => '本地jQuery库【头部加载，速度慢，兼容好】'
				],
				'std'   => 'git_jqcdn_upai'
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
			'desc'  => '输入您的CDN域名，一般需要到cdn后台获取，必须带 <font color="#cc0000"><strong>http(s):// ，且结尾不能带/ </strong></font>',
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
			'desc'  => '启用【如果启用，请在七牛，又拍，OSS等CDN中设置自定义样式，名字为：<font color="#cc0000"><strong>water.jpg</strong></font>，分隔符为 ! 】',
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
			'std'   => '请关注极客公园官方微信公众号，关注并订阅<span style="color:#E96463;font-weight:bold;">云落极客公园</span>获取验证码。在微信里搜索<span style="color:#E96463;font-weight:bold;">云落极客公园</span>或者微信扫描二维码都可以关注极客公园官方微信公众号。'
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
			'name'  => '发件人地址',
			'desc'  => '请输入您的邮箱地址',
			'id'    => 'git_maildizhi_b',
			'type'  => 'text',
			'std'   => ''
		],
		[
			'name'  => '发件人昵称',
			'desc'  => '请输入您的网站名称',
			'id'    => 'git_mailnichen_b',
			'type'  => 'text',
			'std'   => ''
		],
		[
			'name'  => 'SMTP服务器地址',
			'desc'  => '请输入您的邮箱的SMTP服务器，查看<a class="button-primary" target="_blank" href="http://wenku.baidu.com/link?url=Xc_mRFw2K-dimKX845QalqLpZzly07mC4a_t_QjOSPov0uFx3MWTl3wgw4tOAyTbDlS7lT8TOAj8VOxDYU186wQLKPt1fKncz7k_jbP_RQi">查看常用SMTP地址</a>',
			'id'    => 'git_mailsmtp_b',
			'type'  => 'text',
			'std'   => 'smtp.qq.com'
		],
		[
			'name'  => 'SSL安全连接',
			'desc'  => '启用【如果你布吉岛这个是什么东东，那么请不要启用】',
			'id'    => 'git_smtpssl_b',
			'type'  => 'checkbox'
		],
		[
			'name'  => 'SMTP服务器端口',
			'desc'  => '请输入您的smtp端口，一般QQ邮箱25就可以了,如果选择了上面的SSL，推荐使用465端口',
			'id'    => 'git_mailport_b',
			'type'  => 'number',
			'std'   => 465
		],
		[
			'name'  => '邮箱账号',
			'desc'  => '请输入您的邮箱地址，比如云落的sp91@qq.com',
			'id'    => 'git_mailuser_b',
			'type'  => 'text',
			'std'   => ''
		],
		[
			'name'  => '邮箱密码',
			'desc'  => '请输入您的邮箱授权码',
			'id'    => 'git_mailpass_b',
			'type'  => 'password',
			'std'   => ''
		],
		[
			'title' => '站内搜索设置',
			'type'  => 'title'
		],
		[
			'name'  => '百度站内搜索',
			'desc'  => '开启 【开启百度站内搜索将关闭自带搜索】',
			'id'    => 'git_search_baidu',
			'type'  => 'checkbox'
		],
		[
			'name'  => '百度站内搜索代码',
			'desc'  => '将从百度搜索获取的代码添加到本输入框',
			'id'    => 'git_search_code',
			'type'  => 'textarea',
			'std'   => ''
		],
		[
			'title' => '下载设置',
			'type'  => 'title'
		],
		[
			'name'  => '弹窗下载备注',
			'desc'  => '开启【主要填写一句对文件的普遍性备注，一般是下载方式，加密密码，解压方式等等】',
			'id'    => 'git_fancydlad',
			'type'  => 'text',
			'std'   => '本站文件全部采用7Z压缩，请使用7-Zip解压文件'
		],
		[
			'name'  => '弹窗下载版权声明',
			'desc'  => '开启【就是那种本文件收集自网络，有问题联系站长那些装X的文字】',
			'id'    => 'git_fancydlcp',
			'type'  => 'textarea',
			'std'   => '本站所有软件和资料均为软件作者提供或网友推荐发布而来，仅供学习和研究使用，不得用于任何商业用途。如本站不慎侵犯你的版权请联系我，我将及时处理，并撤下相关内容！ '
		],
		[
			'name'  => '下载面板下载声明',
			'desc'  => '这里的文字在下载面板中粗线，建议文字不要太多，防止错位',
			'id'    => 'git_dltable_b',
			'type'  => 'textarea',
			'std'   => '本站文件大多来自于网络，仅供学习和研究使用，不得用于商业用途，如有版权问题，请联系博猪！'
		],
		[
			'name'  => '下载单页下载声明',
			'desc'  => '这里的文字在下载单页中粗线，采用<code>&lt;ol&gt;&lt;li&gt;文字&lt;/li&gt;&lt;/ol&gt;</code>的形式',
			'id'    => 'git_dlpage_dl',
			'type'  => 'textarea',
			'std'   => '<p>下载文件若出现其中一个渠道链接失效，可切换其其他渠道下载，若下载地址全部失效，请回复文章，博猪会第一时间更新！</p>
					<p>下载文件若为压缩包，亲留意文章中的解压密码，并尽量使用最新版压缩软件解压</p>
					<p>下载压缩包文件损坏，请切换其他渠道下载损坏部分</p>
					<p>以上如有疑问，请在文章中留言给博猪</p>'
		],
		[
			'name'  => '下载单页免责声明',
			'desc'  => '这里的文字在下载单页中粗线，纯文字即可',
			'id'    => 'git_dlpage_mz',
			'type'  => 'textarea',
			'std'   => '本站大部分下载资源收集于网络，只做学习和交流使用，版权归原作者所有，若为付费内容，请在下载后24小时之内自觉删除，若作商业用途请购买正版，由于未及时购买和付费发生的侵权行为，与本站无关。本站发布的内容若侵犯到您的权益，请联系站长删除，我们将及时处理！'
		]
	]
];