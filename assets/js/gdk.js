jQuery(function($) {/**声明加载jQuery */

	
/**点击开启密码可见 */
    $("#start_view").click(function() {
    	var ajax_data = {
    		action: $('#start_view').data('action'),
    		pass_nonce: ajax.pass_nonce,
    		id: $('#start_view').data('id'),
    		pass: $('#pass_view').val()
    	};
    	$.post(ajax.url, ajax_data, function(c) {
    		c = $.trim(c);/**Ajax返回有空行,这里处理一下.妈的花了老子3个小时 */
    		if (c != '400') {
    			$(".pass_viewbox").hide();
    			$(".pass_viewbox").after("<div class='content-hide-tips'><span class='rate label label-warning'>隐藏内容：</span><p>" + c + "</p></div>");
    			localStorage.setItem('gdk_pass_' + ajax_data['id'], c);/**隐藏内容直接存入浏览器缓存,下次直接读取 */
    		} else {
    			swal("查看失败", "您的密码错误，请重试", "error");
    		}
    	});
    });

/**已经密码可见的自动从浏览器读取内容并显示,这里加个延时处理 */
if ( $("#start_view").length > 0 ) {/**如果网站有密码可见,就执行 */
    setTimeout(function() {
        var id = 'gdk_pass_' + $('#start_view').data('id'),length = localStorage.length;
        for (var i = 0; i < length; i++) {
            var key = localStorage.key(i),value = localStorage.getItem(key);
            if (key.indexOf(id) >= 0) {/**发现目标 */
                $(".pass_viewbox").hide();
                $(".pass_viewbox").after("<div class='content-hide-tips'><span class='rate label label-warning'>隐藏内容：</span><p>" + value + "</p></div>");
                break;
            }
        }
    
    }, 900);
}
/**密码可见end */

/**数据验证 数据,验证模式*/
function check_data(a,b){/**1=数字,2=邮箱,3=网址,4= 到时候再写 */
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
		default:
			return false;
   } 

}

$("#submit_pay").click(function() {

var money = $("#money").val(),
	pay_way = $("input[name='pay_way']:checked").val(),
	user_id = $('#submit_pay').data('id'),
	action = $('#submit_pay').data('action');


	/**检查服务器是否有订单 */
	function checkpayjs(a, b) { //ID，订单号
		var ajax_data = {
			check_pay_points: ajax.check_pay_points,
			action: 'check_pay_points',
			id: a,
			orderid: b
		};
		$.post(ajax.url, ajax_data,
			function(c) {
				if (c == '200') {
					swal("支付成功!", "为了方便您后续再次查看，建议您输入您的常用邮箱作为提取码", "info", {
							dangerMode: true,
							closeOnClickOutside: false,
							content: "input",
						})
						.then((d) => {
							gdk_getcontent(a);
							addcode(a, `${d}`);
						}); //ok
				} else {
					swal("查看失败", "您的支付没有成功，请重试", "error");
				}
			});
	}


/** 生成支付二维码money , pay_way ,user_id*/
	function get_payjs_qr(a , b ,c){
		var ajax_data = {
			pay_points: ajax.pay_points,
			action: action,
			money: a,
			way: b,
			id: c
		};
		$.post(ajax.url, ajax_data,
			function(d) {
				if (d !== '400') {
					console.log(d);
					var f = document.createElement("img"),
					e = d.split('|');
					f.id = 'pqrious';
					
					swal("支付金额：" + e[0] + "元", {
							content: f,
							closeOnClickOutside: false,
							button: ""+e[2]+"支付已完成",
						})
						.then((value) => {
							checkpayjs(c, e[1]);/**用户id,订单号 */
						});
					new QRious({
						element: document.getElementById("pqrious"),
						size: 300,
						value: e[3]
					});
				}else{
					swal("充值发生错误", "哦嚯,好像发生了什么错误,二维码加载失败", "error");
				}
			});
	}
	/**生成二维码结束 */








if(check_data(money,1)){
	get_payjs_qr(money , pay_way ,user_id);
}else{
	swal("充值金额不正确", "请输入正确的充值金额", "error");

}




});




























});