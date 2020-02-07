jQuery(function($) {/**声明加载jQuery */

	

/**
 * 展示被隐藏的内容,适用于密码可见,付费可见,积分可见
 * @param {string} a 内容div的 id或者class
 * @param {string} b 内容数据
 */
function show_hide_content(a,b){
	$(a).hide();
    $(a).after("<fieldset class=\"fieldset\"><legend class=\"legend\">隐藏内容</legend><p>" + b + "</p></fieldset>");
}


/**
 * 点击开启密码可见
 */
$("#submit_pass_view").click(function() {
    var ajax_data = {
    	action: $('#submit_pass_view').data('action'),
    	pass_nonce: ajax.pass_nonce,
    	id: $('#submit_pass_view').data('id'),
    	pass: $('#pass_view').val()
    };
    $.post(ajax.url, ajax_data, function(c) {
    	c = $.trim(c);/**Ajax返回有空行,这里处理一下.妈的花了老子3个小时 */
    	if (c !== '400') {
			show_hide_content('.pass_viewbox',c);
			localStorage.setItem('gdk_pass_' + ajax_data['id'], c);
			/**隐藏内容直接存入浏览器缓存,下次直接读取,ps.有个问题,内容更新会略坑,不管了 */
    	} else {
    		swal("查看失败", "您的密码错误，请重试", "error");
    	}
    });
});


/**
 * 已经密码可见的自动从浏览器读取内容
 * 并显示,这里加个延时处理
 */
if ( $("#submit_pass_view").length > 0 ) {/**如果网站有密码可见,就执行 */
    setTimeout(function() {
        var id = 'gdk_pass_' + $('#submit_pass_view').data('id'),length = localStorage.length;
        for (var i = 0; i < length; i++) {
            var key = localStorage.key(i),value = localStorage.getItem(key);
            if (key.indexOf(id) >= 0) {/**发现目标 */
				show_hide_content('.pass_viewbox',value);
                break;
            }
        }
    
    }, 900);
}
/**密码可见end */


/**
 * 数据验证
 * @param {string} a 数据
 * @param {init} b 验证模式,1=数字,2=邮箱,3=网址,4=IP 剩下..到时候再写吧
 */
function check_data(a,b){
	a = $.trim(a);
	if(a==null || a=="" || a=='undefined') return false;
	var numRegex = /^[1-9][0-9]*$/i,
    	emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
		urlRegex = /^((http|https):\/\/(\w+:{0,1}\w*@)?(\S+)|)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/,
		ipRegex = /^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})$/i,
		chineseReg = /^[\u4e00-\u9fa5]{0,}$/;
	switch(b) {
		case 1:
			return numRegex.test(a);
		   break;
		case 2:
			return emailRegex.test(a);
		   break;
		case 3:
			return urlRegex.test(a);
			break;
		case 4:
			return ipRegex.test(a);
			break;
		default:
			return false;
   }

}



/**
 * 检查服务器是否有积分充值订单
 * @param {init} a 用户ID
 * @param {string} b 订单号
 */
function check_pay_points(a, b) {
    var ajax_data = {
        check_pay_points: ajax.check_pay_points,
        action: 'check_pay_points',
        id: a,
        orderid: b
    };
    $.post(ajax.url, ajax_data,function(c) {
        c = $.trim(c);
        if (c == '200') {
            swal("支付成功!", "为支付详情已发送到您的邮箱", "success"); //ok
        } else {
            swal("支付失败", "您的支付没有成功，请重试", "error");
        }
    },
    'json');
}


/**
 * 获取隐藏内容
 * @param {init} a 文章ID
 * @param {string} b 提取码
 */
function getcontent(a,b) {
    var ajax_data = {
        action: 'getcontent',
        id: a
    };
    $.post(ajax.url, ajax_data,
        function(c) {
            if (c) {
                show_hide_content('#hide_notice',c);
            }
        });
}

/** */
/**
 * 检查服务器是否有付费可见订单
 * @param {init} a 文章ID
 * @param {string} b 订单号
 */
function check_pay_view(a, b) { 
    var ajax_data = {
        check_pay_view: ajax.check_pay_view,
        action: 'check_pay_view',
        id: a,
        orderid: b
    };
    $.post(ajax.url, ajax_data,function(c) {
        c = $.trim(c);
        if (c == '200') {
			swal("支付成功!", "为了方便您后续再次查看，建议您输入您的常用邮箱作为提取码", "info", {
					dangerMode: true,
					closeOnClickOutside: false,
					content: "input",
				})
				.then((d) => {/**提取码 */
					getcontent(a, `${d}`);
					addcode(a, `${d}`);
				}); //ok
		} else {
            swal("支付失败", "您的支付没有成功，请重试", "error");
        }
    },
    'json');
}


/**
 * 生成支付二维码,公共调用 
 * @param {init} a 金额
 * @param {string} b 支付通道,可选参数alipay,wechat
 * @param {init} c user_id/post_id,用户id或者文章id,取决于是积分充值还是付费可见
 * @param {string} d action操作
 */
function get_payjs_qr(a, b, c, d) {
	var ajax_data = {
		action: d,
		money: a,
		way: b,
		id: c	
	};
	$.post(ajax.url, ajax_data, function(e) {
		e = $.trim(e);
		if (e !== '400') {
			var g = document.createElement("img"),
				f = e.split('|');/**使用|风格e,分为数组f */
				g.id = 'pqrious';
			swal("支付金额：" + f[0] + "元", {
				content: g,
				closeOnClickOutside: false,
				button: "" + f[2] + "支付已完成",
			}).then(() => {
				if(f[4]){
					check_pay_points(c, f[1]); /**用户id,订单号,积分充值检测 */
				}else{
					check_pay_view(c, f[1]); /**文章id,订单号,付费可见检测 */
				}
			});
			new QRious({
				element: document.getElementById("pqrious"),
				size: 300,
				value: f[3]
			});
		} else {
			swal("支付发生错误", "哦嚯,好像发生了什么错误,支付二维码加载失败", "error");
		}
	});
}
/**生成二维码结束 */


/**
 * 点击积分充值按钮
 */
$("#submit_pay").click(function() {
	var money = $("#money").val(),
		pay_way = $("input[name='pay_way']:checked").val(),
		user_id = $('#submit_pay').data('id'),
		action = $('#submit_pay').data('action');
	if (check_data(money, 1)) {
		get_payjs_qr(money, pay_way, user_id, action);
	} else {
		swal("充值金额不正确", "请输入正确的充值金额", "error");
	}
});

////end

/**
 * 检验提取码
 * @param {init} a 文章ID
 * @param {string} b 提取码
 */
function checkcode(a, b) {
    var ajax_data = {
        check_code: ajax.check_code,
        action: 'check_code',
        id: a,
        code: b
    };
    $.post(ajax.url, ajax_data,
        function(c) {
            if (c == '1') {
                getcontent(a);
				localStorage.setItem('payjs_view_id:'+a,b);
            } else {
                swal("查看失败", "服务器不存在此提取码，请重新输入", "error");
            }
        });

}


/**
 * 选择哪个支付
 * @param {init} a 文章ID
 * @param {init} b 金额
 * @param {string} c action操作
 */
function pay_way(a,b,c) {
    swal("点此开始扫码", "支持支付宝、微信，支付过程中请勿刷新页面！", "warning", {
            buttons: ["支付宝", "微信"],
            dangerMode: true,
            closeOnClickOutside: false,
        })
        .then((way) => {
            if (way) { //微信
                get_payjs_qr(b, 'wechat',a,c);
            } else { //支付宝
                get_payjs_qr(b, 'alipay',a,c);
            }
        });
}


/**
 * 开始付费可见代码
 * @param {init} a 文章ID
 * @param {init} b 金额
 * @param {string} c action操作
 */
function pay_view(a,b,c){
    swal("查看付费内容", "如未支付，请先支付，如已支付，请点击已支付", "warning", {
            buttons: ["扫码支付", "我已支付"],
            dangerMode: true,
            closeOnClickOutside: false,
        })
        .then((pay) => {
            if (pay) {/* 我已支付*/
                swal("请输入您的支付提取码:", {
                        content: "input",
                        button: "验证提取码"
                    })
                    .then((code) => {
                        checkcode(a, `${code}`);/**文章id, 提取码 */
                    });
            } else {/* 未支付,选择支付方式*/
                pay_way(a,b,c);
            }
        });
}


/**
 * 发现有付费可见内容就自动提取
 */
if ( $("#pay_view").length > 0 ) {/**如果网站有付费可见,就执行 */
    setTimeout(function() {
        var id = $("#pay_view").data("id"),keys = 'payjs_view_id:' +id,length = localStorage.length;
        for (var i = 0; i < length; i++) {
            var key = localStorage.key(i),value = localStorage.getItem(key);
            if (key.indexOf(keys) >= 0) {/**发现目标 */
				checkcode(id, value);
                break;
            }
        }

    }, 1000);
}

/**
 * 点击开启是执行付费可见
 */
$("#pay_view").click(function() {
    var id = $("#pay_view").data("id"),
		money = $("#pay_view").data("money"),
		action = $("#pay_view").data("action");
		pay_view(id,money,action);
});





















});