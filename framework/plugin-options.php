<?php
/**
 * Git 插件后台选项
 */

 if ( ! defined( 'WPINC' ) ) {
	 die;
 }

$current_theme = wp_get_theme();
$gdk_default_options = [];
$gdk_options = [];
include('options-config.php');
$gdk_current_options = get_option('gdk_options_setup');

function gdk_update_options() {
	global $gdk_default_options, $gdk_options, $gdk_current_options;
	foreach ($gdk_options as $panel) {
		foreach ($panel as $option) {
			$id = $option['id'] ?? '';
			$type = $option['type'] ?? '';
			$std = $option['std'] ?? '';
			if ( !$id ) continue;
			$gdk_default_options[$id] = $std;
			if ( isset($gdk_current_options[$id]) ) continue;
			$gdk_current_options[$id] = $std;
		}
	}
}
gdk_update_options();

//获取设置选项
function gdk_option($id, $returnDefault = false) {
	global $gdk_default_options, $gdk_current_options;
	return stripslashes( $returnDefault ? $gdk_default_options[$id] : $gdk_current_options[$id] );
}

//设置页面模板
function gdk_options_page() {
	global $gdk_options;
?>

<div class="wrap">
	<h2>GDK选项</h2>
	<hr/>
<?php
	if (isset($_GET['update'])) echo '<div class="updated"><p><strong>设置已保存。</strong></p></div>';
	if (isset($_GET['reset'])) echo '<div class="updated"><p><strong>设置已重置。</strong></p></div>';
?>

	<div class="wp-filter">
		<ul class="filter-links">
<?php
$activePanelIdx = empty($_GET['panel']) ? 0 : $_GET['panel'];
foreach ( array_keys($gdk_options) as $i => $name ) {
	echo '<li><a href="#panel_' . $i . '" data-panel="' . $i . '" ' . ( $i == $activePanelIdx ? 'class="current"' : '' ) . '>' . $name . '</a></li>';
}
?>
			<li><a href="#panel_data" data-panel="data">数据清理</a></li>
			<li><a href="#panel_about" data-panel="about">关于插件</a></li>
		</ul>
<div class="search-form"><label class="screen-reader-text" for="wp-filter-search-input">筛选插件选项…</label><input placeholder="筛选插件选项…" type="search" id="wp-filter-search-input" class="wp-filter-search"></div>
</div>

<form method="post">
<?php
$index = 0;
foreach ( $gdk_options as $panel ) {
	echo '<div class="panel gdk_option" id="panel_' . $index . '" ' . ( $index == $activePanelIdx ? ' style="display:block"' : '' ) . '><table class="form-table">';
	foreach ( $panel as $option ) {
		$type = $option['type'];
		if ( $type == 'title' ) {
?>
<tr class="title">
	<th colspan="2">
		<h3><?php echo $option['title']; ?></h3>
		<?php if ( isset( $option['desc'] ) ) echo '<p>' . $option['desc'] . '</p>'; ?>
	</th>
</tr>
<?php
			continue;
		}
		$id = $option['id'];
?>
<tr id="row-<?php echo $id; ?>">
	<th><label for="<?php echo $id; ?>"><?php echo $option['name']; ?></label></th>
	<td>
<?php
switch ( $type ) {
	case 'text':
?>
		<label>
		<input name="<?php echo $id; ?>" class="regular-text" id="<?php echo $id; ?>" type="text" value="<?php echo esc_attr(gdk_option( $id )) ?>" />
		</label>
		<p class="description"><?php echo $option['desc']; ?></p>
<?php
	break;
	case 'number':
?>
		<label>
		<input name="<?php echo $id; ?>" class="small-text" id="<?php echo $id; ?>" type="number" value="<?php echo esc_attr(gdk_option( $id )) ?>" />
		<span class="description"><?php echo $option['desc']; ?></span>
		</label>
<?php
	break;
	case 'textarea':
?>
		<p><label for="<?php echo $id; ?>"><?php echo $option['desc']; ?></label></p>
		<p><textarea name="<?php echo $id; ?>" id="<?php echo $id; ?>" rows="10" cols="50" class="large-text code"><?php echo esc_textarea(gdk_option( $id )) ?></textarea></p>
<?php
	break;
	case 'radio':
?>
		<fieldset>
		<?php foreach ($option['options'] as $val => $name) : ?>
		<label>
			<input type="radio" name="<?php echo $id; ?>" id="<?php echo $id . '_' . $val; ?>" value="<?php echo $val; ?>" <?php checked( gdk_option( $id ), $val); ?>>
			<?php echo $name; ?>
		</label>
		<?php endforeach; ?>
		</fieldset>
		<p class="description"><?php echo $option['desc']; ?></p>
<?php
	break;
	case 'checkbox':
?>
		<label>
			<input type='checkbox' name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="1" <?php echo checked(gdk_option($id)); ?> />
			<span><?php echo $option['desc']; ?></span>
		</label>
<?php
	break;
	case 'checkboxs':
?>
		<fieldset>
		<?php $checkboxValues = gdk_option( $id );
		if ( !is_array($checkboxValues) ) $checkboxValues = [];
		foreach ( $option['options'] as $id => $name ) : ?>
		<label>
			<input type="checkbox" name="<?php echo $id; ?>[]" id="<?php echo $id; ?>[]" value="<?php echo $id; ?>" <?php checked( in_array($id, $checkboxValues), true); ?>>
			<?php echo $name; ?>
		</label>
		<?php endforeach; ?>
		</fieldset>
		<p class="description"><?php echo $option['desc']; ?></p>
<?php
	break;
	default:
?>
		<label>
		<input name="<?php echo $id; ?>" class="regular-text" id="<?php echo $id; ?>" type="<?php echo $type; ?>" value="<?php echo esc_attr(gdk_option( $id )) ?>" />
		</label>
		<p class="description"><?php echo $option['desc']; ?></p>
<?php
	break;
}
	echo '</td></tr>';
	}
		echo '</table></div>';
		$index++;
}
?>
	<div class="panel" id="panel_data">
	<table class="form-table">
	<?php echo wp_clean_up_page();?>
	</table>
	</div>
	<div class="panel" id="panel_about">
		<table class="form-table">
			<tr>
				<th><h4>云落小贴士</h4></th>
				<td>
					<p>哈哈</p>
				</td>
			</tr>
			<tr>
				<th><h4>联系方式</h4></th>
				<td>
					<ul>
						<li>ＱＱ：865113728（推荐）</li>
						<li>邮箱：<a href="mailto:sp91@qq.com">sp91@qq.com</a></li>
						<li><p style="font-size:14px;color:#72777c">* 和主题无关的问题恕不回复</p></li>
					</ul>
				</td>
			</tr>
			<tr>
				<th><h4>相关链接</h4></th>
				<td>
					<ul>
						<li>主题发布页面：<a target="_blank" href="https://gitcafe.net/archives/3589.html">https://gitcafe.net/archives/3589.html</a></li>
						<li>使用文档页面：<a target="_blank" href="https://gitcafe.net/archives/3275.html">https://gitcafe.net/archives/3275.html</a></li>
						<li>代码托管页面：<a target="_blank" href="https://dev.tencent.com/u/googlo/p/Git/git">https://dev.tencent.com/u/googlo/p/Git/git</a></li>
						<li>更新日志页面：<a target="_blank" href="https://gitcafe.net/tool/gitrss.php">https://gitcafe.net/tool/gitrss.php</a></li>
						<li>主题反馈页面：<a target="_blank" href="https://support.qq.com/products/51158">https://support.qq.com/products/51158</a></li>
					</ul>
				</td>
			</tr>
			<tr>
				<th><h4>第三方支持</h4></th>
				<td>
					<ul>
						<li>感谢以下组织或个人：</li>
						<li>PayJs 、Eapay、WeAuth小程序、Cloud9 、Cloud Studio、Coding 、Gitee 、Github、Server酱、jsDelivr、V2EX</li>
						<li>露兜、畅萌、小影、大前端、知更鸟、yusi等等</li>
					</ul>
				</td>
			</tr>
		</table>
	</div>
	<hr />
	<p class="submit" style="display:inline;float:left;margin-right:50px;">
		<input name="submit" type="submit" class="button button-primary" value="保存更改"/>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="panel" value="<?php echo $activePanelIdx; ?>" id="active_panel_name" />
	</p>
</form>

<form method="post"  style="display:inline;float:left;margin-right:50px;">
	<p class="submit">
		<input name="reset" type="submit" class="button button-secondary" value="重置选项" onclick="return confirm('你确定要重置选项吗？');"/>
		<input type="hidden" name="action" value="reset" />
	</p>
</form>
<form style="display:inline;float:left;margin-right:50px;">
	<p class="submit">
		<input  type="button" class="button button-secondary mail_test" value="SMTP测试" onclick="return confirm('点击后点击后,网站会向指定邮箱发送测试邮件,如果发送有响应则证明邮箱成功,如果没有任何响应说明邮箱配置失败!');"/>
	</p>
</form>
</div>

<!-- 静态资源css&js -->
<style>
.panel {
	display: none;
	margin: 0 20px;
}
.panel h3 {
	margin: 0;
	border-bottom: 1px solid #d2d3e0;
	padding-bottom: 5px;
}
.key_word{
	color:#f70044;
	font-weight:bold;
	text-decoration:none;
	margin: 10px;
}
.panel th {
	font-weight: normal;
}

.wp-filter {
	padding: 0 20px;
	margin-bottom: 0;
	font-size: 15px;
}

.filter-links .current,.filter-links.current:focus {
	color:#6b48ff!important;
    border-bottom: 4px solid #6b48ff;
}

.filter-links li>a:hover{color:#666}
.filter-links a:focus{color:#6b48ff;box-shadow:none}

.gdk_option input[type=radio]:checked::before {
    background-color: #6b48ff;
}
.gdk_option input[type=radio]:focus, .gdk_option input[type=checkbox]:focus{
    box-shadow: 0 0 0 1px #6b48ff;
}

.wp-filter .drawer-toggle:before {
	content: "\f463";
	color: #fff!important;
	background: #e14d43;
	border-radius: 50%;
	box-shadow: inset 0 0 0 2px #e14d43, 0 0 0 2px #e14d43;
}

.wrap.searching .nav-tab-wrapper a,
.wrap.searching .panel tr,
body.show-filters .wrap form {
	display: none
}

.wrap.searching .panel {
	display: block!important;
}

.filter-drawer {
	padding-top: 0;
	padding-bottom: 0;
}
.filter-drawer ul {
	list-style: disc inside;
}

</style>
<style id="theme-options-filter"></style>
<script>
/* global wp */
jQuery(function ($) {
	var $body = $("body");
	var $themeOptionsFilter = $("#theme-options-filter");
	var $wpFilterSearchInput = $("#wp-filter-search-input");

	$(".filter-links a").click(function () {
		$(this).addClass("current").parent().siblings().children(".current").removeClass("current");
		$(".panel").hide();
		$($(this).attr("href")).show();
		$("#active_panel_name").val($(this).data("panel"));
		$body.removeClass("show-filters");
		return false;
	});

	if ($wpFilterSearchInput.is(":visible")) {
		var wrap = $(".wrap");

		$(".panel tr").each(function () {
			$(this).attr("data-searchtext", $(this).text().replace(/\r|\n|px/g, '').replace(/ +/g, ' ').replace(/^\s+|\s+$/g, '').toLowerCase());
		});

		$wpFilterSearchInput.on("input", function () {
			var text = $(this).val().trim().toLowerCase();
			if (text) {
				wrap.addClass("searching");
				$themeOptionsFilter.text(".wrap.searching .panel tr[data-searchtext*='" + text + "']{display:block}");
			} else {
				wrap.removeClass("searching");
				$themeOptionsFilter.text("");
			}
		});
	}

	$(".wrap form").submit(function(){
		$(".submit .button").prop("disabled", true);
		$(this).find(".submit .button").val("正在提交…");
	});

	$(".mail_test").click(function () {
		var ajax_data = { action: 'gdk_test_email' };
    $.post(ajaxurl, ajax_data,
        function(a) {
            if (a == '1') {
				swal("测试成功", "您的SMTP邮箱邮件发送已成功,Enjoy it", "success");
            }else{
				swal("测试失败", "您的SMTP邮箱邮件响应失败,请重试", "error");
			}
        });
	});



/* 配置文本框以隐藏显示功能,可以同时伸缩8个元素*/ 
function depend(n, e, i, c, t, u, v, g, d) {
    $("input[name=" + n + "]:checked").val(function() {
        "0" == this.value && $(e, i, c, t, u, v, g, d).hide();
    }), $("input[name=" + n + "]").change(function() {
        $(e, i, c, t, u, v, g, d).toggle();
    });
}
//依赖关系,第一个是需要点击的name值,后面是需要伸缩的ID值,参照下面写
depend('gdk_lock_login','#row-gdk_failed_login_limit,#row-gdk_lockout_duration');
depend('gdk_smtp','#row-gdk_smtp_username,#row-gdk_smtp_host,#row-gdk_smtp_port,#row-gdk_smtp_mail,#row-gdk_smtp_password');
depend('gdk_baidu_push','#row-gdk_baidu_token');
depend('gdk_tag_link','#row-gdk_tag_num');
depend('gdk_cdn','#row-gdk_cdn_host,#row-gdk_cdn_ext,#row-gdk_cdn_dir,#row-gdk_cdn_style,#row-gdk_cdn_water');
depend('gdk_h5notice','#row-git_notification_title,#row-git_notification_days,#row-git_notification_cookie,#row-git_notification_icon,#row-git_notification_link,#row-git_notification_body');
depend('gdk_payjs','#row-gdk_rate,#row-git_payjs_rate,#row-gdk_payjs_id,#row-gdk_payjs_key');

});
</script>
<?php
}



function gdk_add_options_page() {
	global $gdk_options;
	if ( isset($_POST['action']) && isset($_GET['page']) && $_GET['page'] == 'gdk-options' ) {
		$action = $_POST['action'];
		switch ( $action ) {
			case 'update':
				$_POST['uid'] = uniqid();
				update_option('gdk_options_setup', $_POST);
				gdk_update_options();
				header('Location: admin.php?page=gdk-options&update=true&panel=' . $_POST['panel']);
				break;
			case 'reset':
				delete_option('gdk_options_setup');
				gdk_update_options();
				header('Location: admin.php?page=gdk-options&reset=true&panel=' . $_POST['panel']);
				break;
		}
		exit;
	}
	add_menu_page( 'GDK选项', 'GDK选项', 'manage_options', 'gdk-options', 'gdk_options_page','dashicons-buddicons-replies' );
}
add_action( 'admin_menu', 'gdk_add_options_page' );