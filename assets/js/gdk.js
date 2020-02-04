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
    		c = c.replace(/\s/g, '');/**Ajax返回有空行,这里处理一下.妈的花了老子3个小时 */
    		if (c != '0') {
    			$(".pass_viewbox").hide();
    			$(".pass_viewbox").after("<div class='content-hide-tips'><span class='rate label label-warning'>隐藏内容：</span><p>" + c + "</p></div>");
    			localStorage.setItem('gdk_pass_' + ajax_data['id'], c);/**隐藏内容直接存入浏览器缓存,下次直接读取 */
    		} else {
    			swal("查看失败", "您的密码错误，请重试", "error");
    		}
    	});gdk_
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






























});