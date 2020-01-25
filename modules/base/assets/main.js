jQuery(document).on('ready', function() {
	var ajax_url = nicetheme.ajax_url
	retrieve_database_items_count()

	function acf_site_tips(type, msg) {
		var c = type ? 'updated' : 'error';
		var html = '<div class="nicetheme-notice notice '+ c +'">'
					+ '<p>' + msg + '</p>'
					+ '</div>'
					jQuery('.acf-settings-wrap h1').append(html);
	
		var tipsDOM = jQuery('.nicetheme-notice');
	
		window.setTimeout(function(){
			tipsDOM.remove();
		}, 3000);
	}
	
	function acf_fire(data, successCallback = function() {}, failCallback = function() {}) {
		jQuery.ajax({
			url: ajax_url,
			type: 'POST',
			dataType: 'html',
			data: data,
		})
		.done(function(data) {
			data = JSON.parse(data)
			if (data.s == 200) {
				acf_site_tips(1, data.m)
				successCallback()
			} else {
				acf_site_tips(0, data.m)
				failCallback()
			}
		})
		.fail(function() {
			acf_site_tips(0, '网络错误，请稍后再试！')
			failCallback()
		});
	}

	// 显示数据库优化内容
	function retrieve_database_items_count() {
		jQuery.ajax({
			url: ajax_url,
			type: 'POST',
			dataType: 'html',
			data: { action: 'nc_database_clean_up_count' },
		})
		.done(function(data) {
			counts = JSON.parse(data).counts
			var cleanupForm = jQuery('.acf-database-cleanup-form')
			var cleanupFormKeys = Object.keys(counts)
			cleanupFormKeys.forEach(function (item) {
				cleanupForm.find('.' + item).text(counts[item])
				counts[item] == 0 && cleanupForm.find('input[data-action_type="' + item + '"]').prop('disabled', 'disabled')
			})
		})
		.fail(function() {
			acf_site_tips(0, '网络错误，请刷新页面重试！')
		});
	}

	jQuery(document).on('click', 'input[data-action=nc_test_email]', function(event) {
		event.preventDefault();
		var data = jQuery(this).data();
		acf_fire(data)
	});

	jQuery(document).on('click', 'input[data-action=nc_database_optimize]', function(event) {
		event.preventDefault();
		var data = jQuery(this).data();
		acf_fire(data)
	});

	jQuery(document).on('click', 'input[data-action_type]', function(event) {
		event.preventDefault();
		var data = jQuery(this).data();
		acf_fire(data, function() {
			retrieve_database_items_count()
		}, function() {
			retrieve_database_items_count()
		})
	});

	

	jQuery(document).on('keydown', '.wp-block-code textarea, .mce-highlight textarea', function(e){
		
		if(e.keyCode == 9 && !e.shiftKey){
			e.preventDefault();
			var indent = '    ';
			var start = this.selectionStart;
			var end = this.selectionEnd;
			var selected = window.getSelection().toString();
			selected = indent + selected.replace(/\n/g,'\n'+indent);
			this.value = this.value.substring(0,start) + selected + this.value.substring(end);
			this.setSelectionRange(start+indent.length,start+selected.length);
		}else if( e.keyCode == 9 && e.shiftKey ){

			e.preventDefault();
			var indent = '    ';
			var start = this.selectionStart;
			var end = this.selectionEnd;
			var selected = window.getSelection().toString();
			this.value = this.value.substring(0,start).replace(indent,'') + selected + this.value.substring(end);
			this.setSelectionRange(start-indent.length,start-selected.length-indent.length);

		}

	});

	
})

