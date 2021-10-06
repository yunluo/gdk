<?php
/**
 * Git 插件后台选项
 */

if (!defined('WPINC')) {
    die;
}

$gdk_default = [];
$gdk_options         = [];
include 'options-config.php';
$gdk_config = get_option('gdk_options_setup');

function gdk_update_options()
{
    global $gdk_default, $gdk_options, $gdk_config;
    foreach ($gdk_options as $panel) {
        foreach ($panel as $option) {
            $id   = $option['id'] ?? '';
            $type = $option['type'] ?? '';
            $std  = $option['std'] ?? '';
            if (!$id) {
                continue;
            }

            $gdk_default[$id] = $std;
            if (isset($gdk_config[$id])) {
                continue;
            }

            $gdk_config[$id] = $std;
			
        }
    }
}
//gdk_update_options();

//获取设置选项
function gdk_option($id, $Default = false)
{
    global $gdk_default, $gdk_config;
    return stripslashes($Default ? $gdk_default[$id] : $gdk_config[$id]);
}

//设置页面模板
function gdk_options_page()
{
    global $gdk_options;
    ?>

<div class="wrap">
	<h2>GDK选项  <input type="button" class="add-new-h2 get_new_version" value="检测更新"><input type="button" style="display:none;" class="add-new-h2 install_new_version" value="安装更新"></h2>
	<hr/>
<?php
if (isset($_GET['update'])) {
        echo '<div class="updated"><p><strong>设置已保存。</strong></p></div>';
    }

    if (isset($_GET['reset'])) {
        echo '<div class="updated"><p><strong>设置已重置。</strong></p></div>';
    }

    ?>

	<div class="wp-filter">
		<ul class="filter-links">
<?php
$activePanelIdx = $_GET['panel'] ?? 0;
    foreach (array_keys($gdk_options) as $i => $name) {
        echo '<li><a href="#panel_' . $i . '" data-panel="' . $i . '" ' . ($i == $activePanelIdx ? 'class="current"' : '') . '>' . $name . '</a></li>';
    }
    ?>
			<li><a href="#panel_about" data-panel="about" class="about">关于插件</a></li>
		</ul>
<div class="search-form"><label class="screen-reader-text" for="wp-filter-search-input">筛选插件选项…</label><input placeholder="筛选插件选项…" type="search" id="wp-filter-search-input" class="wp-filter-search"></div>
</div>

<form method="post">
<?php
$index = 0;
    foreach ($gdk_options as $panel) {
        echo '<div class="panel gdk_option" id="panel_' . $index . '" ' . ($index == $activePanelIdx ? ' style="display:block"' : '') . '><table class="form-table">';
        foreach ($panel as $option) {
            $type = $option['type'];
            if ($type == 'title') {
                ?>
<tr class="title">
	<th colspan="2">
		<h3><?php echo $option['title']; ?></h3>
		<?php if (isset($option['desc'])) {
                    echo '<p>' . $option['desc'] . '</p>';
                }
                ?>
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
switch ($type) {
                case 'text':
                    ?>
		<label>
		<input name="<?php echo $id; ?>" class="regular-text" id="<?php echo $id; ?>" type="text" value="<?php echo esc_attr(gdk_option($id)) ?>" />
		</label>
		<p class="description"><?php echo $option['desc']; ?></p>
<?php
break;
                case 'number':
                    ?>
		<label>
		<input name="<?php echo $id; ?>" class="small-text" id="<?php echo $id; ?>" type="number" value="<?php echo esc_attr(gdk_option($id)) ?>" />
		<span class="description"><?php echo $option['desc']; ?></span>
		</label>
<?php
break;
                case 'textarea':
                    ?>
		<p><label for="<?php echo $id; ?>"><?php echo $option['desc']; ?></label></p>
		<p><textarea name="<?php echo $id; ?>" id="<?php echo $id; ?>" rows="10" cols="50" class="large-text code"><?php echo esc_textarea(gdk_option($id)) ?></textarea></p>
<?php
break;
                case 'radio':
                    ?>
		<fieldset>
		<?php foreach ($option['options'] as $val => $name): ?>
		<label>
			<input type="radio" name="<?php echo $id; ?>" id="<?php echo $id . '_' . $val; ?>" value="<?php echo $val; ?>" <?php checked(gdk_option($id), $val); ?>>
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
		<?php $checkboxValues = gdk_option($id);
                    if (!is_array($checkboxValues))  $checkboxValues = [];
                    
                    foreach ($option['options'] as $id => $name): ?>
		<label>
			<input type="checkbox" name="<?php echo $id; ?>[]" id="<?php echo $id; ?>[]" value="<?php echo $id; ?>" <?php checked(in_array($id, $checkboxValues), true); ?>>
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
		<input name="<?php echo $id; ?>" class="regular-text" id="<?php echo $id; ?>" type="<?php echo $type; ?>" value="<?php echo esc_attr(gdk_option($id)) ?>" />
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
	<div class="panel" id="panel_about">
		<table class="form-table">
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
			<th><h4>意见反馈</h4></th>
			<td>
			<input type="button" class="add-new-h2 feedback-btn" value="意见反馈">
			</td></tr>

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
.install_new_version,
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
.get_update_res,.get_new_version{
    margin-left: 20px;
    padding: 5px;
    font-size: medium;
}
.g-load{
    background: url(images/spinner.gif) no-repeat;
    background-size: 20px 20px;
    display: inline-block;
    vertical-align: middle;
    opacity: .7;
    filter: alpha(opacity=70);
    width: 20px;
    height: 20px;
    margin: 4px 10px 0;
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
			a = $.trim(a);
            if (a == '200') {
				alert("测试成功，您的SMTP邮箱邮件发送已成功，Enjoy it");
            }else{
				alert("测试失败，您的SMTP邮箱邮件响应失败，请重试");
			}
        });
	});

	$(".get_new_version").click(function () {
		$(".get_new_version").after("<span class='g-load'></span>");
		var ajax_data = { action: 'get_new_version' };
    $.get(ajaxurl, ajax_data,
        function(a) {
			a = $.trim(a);
			$(".g-load").hide();
            if (a !== '400') {
				if ($(".get_update_res").length > 0) return;
				$(".get_new_version").after(a);
				if ($(".has_new_version").length > 0) {
					$(".install_new_version").show();
				}
            }else{
				$(".get_new_version").after("检测失败，网络错误");
			}
        });
	});

	$(".install_new_version").click(function () {
		$(".install_new_version").after("<span class='g-load'></span>");
		var ajax_data = { action: 'install_new_version' };
    $.get(ajaxurl, ajax_data,
        function() {
				$(".g-load").hide();
				//window.location.reload();
        });
	});

$(".feedback-btn").click(function() {
    $(".feedback-btn").after("<span class='g-load'></span>");
    $("<link>").attr({
        rel: "stylesheet",
        type: "text/css",
        href: "https://cdn.bootcss.com/fancybox/3.0.39/jquery.fancybox.min.css"
    }).appendTo("head");

    $.getScript("https://cdn.bootcss.com/fancybox/3.0.39/jquery.fancybox.min.js",
    function() {
        $(".g-load").hide();
        $.fancybox.open({
            src: 'https://support.qq.com/products/51158/',
            type: 'iframe',
            opts: {
                afterShow: function(instance, current) {
                    console.info('done!');
                }
            }
        })
    });
});


});
</script>
<?php
}

function gdk_add_options_page()
{
    global $gdk_options;
    if (isset($_POST['action']) && isset($_GET['page']) && $_GET['page'] == 'gdk-options') {
        $action = $_POST['action'];
        switch ($action) {
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
    add_menu_page('GDK选项', 'GDK选项', 'manage_options', 'gdk-options', 'gdk_options_page', 'dashicons-buddicons-replies');
}
add_action('admin_menu', 'gdk_add_options_page');