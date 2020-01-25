/*
            /$$            
    /$$    /$$$$            
   | $$   |_  $$    /$$$$$$$
 /$$$$$$$$  | $$   /$$_____/
|__  $$__/  | $$  |  $$$$$$ 
   | $$     | $$   \____  $$
   |__/    /$$$$$$ /$$$$$$$/
          |______/|_______/ 
================================
        Keep calm and get rich.
                    Is the best.

  	@Author: Dami
  	@Date:   2018-10-14 14:35:10
 * @Last Modified by: suxing
 * @Last Modified time: 2019-07-22 17:58:22
*/

// 登录
jQuery(document).on('click', '.nicetheme-sso-login', function(event) {
	event.preventDefault();
	layer.open({
		type: 2,
		title: '登录',
		shadeClose: true,
		shade: 0.8,
		area: ['800px', '70%'],
		content: jQuery(this).data('login_url')
	}); 
});

jQuery(document).on('click', '.nicetheme-store-logout', function (event){ 
	event.preventDefault();

	var index = layer.load(1, {
		shade: [0.1,'#000'] 
	});

	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: {action: 'nicetheme-store-logout' },
		success: function(data, textStatus, xhr) {
			layer.msg('退出成功！');
			window.location.href = window.location.href;
			layer.close(index);
		},
		error: function (xhr, textStatus, errorThrown) {
			layer.close(index);
			layer.msg('网络错误，请稍后再试！');
		}
	});

});


jQuery(document).on('click', '.delete-nc-module', function(event) {
	event.preventDefault();
	
	var that = jQuery(this);

	var index = layer.confirm('您确定要删除此积木吗？', {
		title: '重要提示',
		btn: ['取消','删除'] 
	}, function(){
		layer.close(index);
	}, function(){
		window.location.href = that.attr('href');
	});

});

jQuery(document).on('click', '.nc-install', function(event) {

	event.preventDefault();
	var that = jQuery(this);
	var text = that.text();

	if( that.hasClass('disabled') ){
		return false;
	}

	that.addClass('updating-message disabled').text('正在安装');

	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: {action: 'nc-store-plugin-install', module: that.data('id') },
		success: function(data, textStatus, xhr) {
			if( data.status == 200 ){

				layer.confirm('安装成功，是否启用？', {
					title: '安装信息',
					btn: ['启用','不用'] 
				}, function(){
					
					window.location.href = data.url;

				}, function(){
					
				});

				that.removeClass('updating-message').addClass('updated-message').text('安装成功');
			}else{
				that.removeClass('updating-message disabled').text(text);
				layer.msg(data.msg);
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			that.removeClass('updating-message disabled').text(text);
			layer.msg('网络错误，请稍后再试！');
		}
	});
	

});

jQuery(document).on('click', '.nc-updata-module', function(event) {
	event.preventDefault();
	
	var that = jQuery(this);
	var text = that.text();

	if( that.hasClass('disabled') ){
		return false;
	}

	var index = layer.load(1, {
		shade: [0.1,'#000'] 
	});
	that.addClass('disabled').text('正在升级...');

	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: {action: 'nc-store-plugin-updata', module: that.data('key') },
		success: function(data, textStatus, xhr) {
			if( data.status == 200 ){
				that.text('升级成功');
				layer.msg(data.msg);
			}else{
				that.removeClass('disabled').text(text);
				layer.msg(data.msg);
			}
			layer.close(index);
		},
		error: function(xhr, textStatus, errorThrown) {
			that.removeClass('disabled').text(text);
			layer.close(index);
			layer.msg('网络错误，请稍后再试！');
		}
	});

});

jQuery(document).on('click', '.nc-store-check-update', function(event) {
	event.preventDefault();
	
	var that = jQuery(this);

	if( that.hasClass('disabled') ){
		return false;
	}

	var index = layer.load(1, {
		shade: [0.1,'#000'] 
	});

	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: {action: 'nc-store-check-update' },
		success: function(data, textStatus, xhr) {
			if( data.status == 200 ){
				
				var confirm = layer.confirm(data.msg, {
					btn: ['升级','取消'] //按钮
				}, function(){
					
					layer.close(confirm);

					var index = layer.load(1, {
						shade: [0.1,'#000'] 
					});

					jQuery.ajax({
						url: ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {plugin: data.plugin, slug: data.slug, _ajax_nonce: data._ajax_nonce, action: data.action },
						success: function(data, textStatus, xhr) {
							
							if( data.success == true ){
								layer.msg('升级成功');
								setTimeout(function(){
									window.location.href = window.location.href;
								},1500);
							}else{
								layer.msg('升级失败');
							}

							layer.close(index);
						},
						error: function(xhr, textStatus, errorThrown) {
							layer.close(index);
							layer.msg('网络错误，请稍后再试！');
						}
					});

				}, function(){
						
				});

			}else{
				that.removeClass('disabled');
				layer.msg(data.msg);
			}
			layer.close(index);
		},
		error: function(xhr, textStatus, errorThrown) {
			that.removeClass('disabled');
			layer.close(index);
			layer.msg('网络错误，请稍后再试！');
		}
	});

});












