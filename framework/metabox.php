<?php

if (!class_exists('myCustomFields')) {

    class myCustomFields
    {
        /**
         * @var  string  $prefix  自定义栏目前缀，一个完整的自定义栏目是需要前缀+name的，比如我的前缀是git_,name下面有baidu_submit，那么完整的自定义栏目就是git_baidu_submit.
         */
        public $prefix = 'gdk_';
        /**
         * @var  array  $postTypes  这是自定义面板的使用范围，这里一般就是在文章以及页面
         */
        public $postTypes = array("post");
        /**
         * @var  array  $customFields  开始组件自定义面板数组
         */
        public $customFields = array(
            array(
                "name"        => "thumb",
                "title"       => "自定义缩略图",
                "description" => "这里可以输入您的自定义缩略图链接",
                "type"        => "text",
                "scope"       => ['post'],
                "capability"  => "edit_posts",
            ),
            array(
                "name"        => "download_name",
                "title"       => "单页下载文件名字",
                "description" => "这里可以输入您的下载文件的名字",
                "type"        => "text",
                "scope"       => ['post'],
                "capability"  => "edit_posts",
            ),
            array(
                "name"        => "download_size",
                "title"       => "单页下载文件大小",
                "description" => "这里可以输入您的下载文件的大小，可以加上单位，比如：233KB或者233MB",
                "type"        => "text",
                "scope"       => ['post'],
                "capability"  => "edit_posts",
            ),
            array(
                "name"        => "download_link",
                "title"       => "单页下载下载链接",
                "description" => "按照链接,名字,备注的格式,注意中间是用英文逗号,换行可添加多个,举个栗子：<code>https://www.baidu.com,百度官网,中国最大的搜索引擎网站</code>",
                "type"        => "textarea",
                "scope"       => ['post'],
                "capability"  => "edit_posts",
            ),
        );
        /**
         * PHP 5 Constructor
         */
        public function __construct()
        {
            add_action('admin_menu', array($this, 'createCustomFields'));
            add_action('save_post', array($this, 'saveCustomFields'), 1, 2);
        }
        /**
         * 创建一组你自己的自定义栏目
         */
        public function createCustomFields()
        {
            if (function_exists('add_meta_box')) {
                foreach ($this->postTypes as $postType) {
                    add_meta_box('my-custom-fields', '文章选项', array($this, 'displayCustomFields'), $postType, 'normal', 'high');
                }
            }
        }
        /**
         * 在文章发布页显示出来面板
         */
        public function displayCustomFields()
        {
            global $post;
            ?>
            <div class="form-wrap">
                <?php wp_nonce_field('my-custom-fields', 'my-custom-fields_wpnonce', false, true);
            foreach ($this->customFields as $customField) {
                // Check scope
                $scope  = $customField['scope'];
                $output = false;
                foreach ($scope as $scopeItem) {
                    switch ($scopeItem) {
                        default:{
                                if ($post->post_type == $scopeItem) {
                                    $output = true;
                                }

                                break;
                            }
                    }
                    if ($output) {
                        break;
                    }

                }
                // 检查权限
                if (!current_user_can($customField['capability'], $post->ID)) {
                    $output = false;
                }

                // 通过则输出
                if ($output) {
                    ?>
                        <div class="form-field form-required form-field-<?php echo $customField['name'];?>">
                            <?php switch ($customField['type']) {
                        case "checkbox":{
                                // Checkbox 组件
                                echo '<label for="' . $this->prefix . $customField['name'] . '" style="display:inline;"><b>' . $customField['title'] . '</b></label>  ';
                                echo '<input type="checkbox" name="' . $this->prefix . $customField['name'] . '" id="' . $this->prefix . $customField['name'] . '" value="1"';
                                if (get_post_meta($post->ID, $this->prefix . $customField['name'], true) == "1") {
                                    echo ' checked="checked"';
                                }
                                echo '" style="width: auto;" />';
                                break;
                            }
                        case "textarea":
                        case "wysiwyg":{
                                // Text area
                                echo '<label for="' . $this->prefix . $customField['name'] . '"><b>' . $customField['title'] . '</b></label>';
                                echo '<textarea name="' . $this->prefix . $customField['name'] . '" id="' . $this->prefix . $customField['name'] . '" columns="30" rows="5">' . htmlspecialchars(get_post_meta($post->ID, $this->prefix . $customField['name'], true)) . '</textarea>';
                                // WYSIWYG
                                if ($customField['type'] == "wysiwyg") { ?>
                                        <script type="text/javascript">
                                            jQuery( document ).ready( function() {
                                                jQuery( "<?php echo $this->prefix . $customField['name']; ?>" ).addClass( "mceEditor" );
                                                if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
                                                    tinyMCE.execCommand( "mceAddControl", false, "<?php echo $this->prefix . $customField['name']; ?>" );
                                                }
                                            });
                                        </script>
                                    <?php }
                                break;
                            }
                        default:{
                                // Plain text field
                                echo '<label for="' . $this->prefix . $customField['name'] . '"><b>' . $customField['title'] . '</b></label>';
                                echo '<input type="text" name="' . $this->prefix . $customField['name'] . '" id="' . $this->prefix . $customField['name'] . '" value="' . htmlspecialchars(get_post_meta($post->ID, $this->prefix . $customField['name'], true)) . '" />';
                                break;
                            }
                    }
                    ?>
                            <?php if ($customField['description']) {
                        echo '<p>' . $customField['description'] . '</p>';
                    }
                    ?>
                        </div>
                    <?php
}
            } ?>
            </div>
            <?php
}
        /**
         * 保存自定义栏目数据
         */
        public function saveCustomFields($post_id, $post)
        {
            if (!isset($_POST['my-custom-fields_wpnonce']) || !wp_verify_nonce($_POST['my-custom-fields_wpnonce'], 'my-custom-fields')) {
                return;
            }

            if (!current_user_can('edit_post', $post_id)) {
                return;
            }

            if (!array_key_exists($post->post_type, $this->postTypes)) {
                return;
            }

            foreach ($this->customFields as $customField) {
                if (current_user_can($customField['capability'], $post_id)) {
                    if (isset($_POST[$this->prefix . $customField['name']]) && trim($_POST[$this->prefix . $customField['name']])) {
                        $value = $_POST[$this->prefix . $customField['name']];
                        // Auto-paragraphs for any WYSIWYG
                        if ($customField['type'] == "wysiwyg") {
                            $value = wpautop($value);
                        }

                        update_post_meta($post_id, $this->prefix . $customField['name'], $value);
                    } else {
                        delete_post_meta($post_id, $this->prefix . $customField['name']);
                    }
                }
            }
        }

    } // End Class

} // End if class exists statement

// Instantiate the class
if (class_exists('myCustomFields')) {
    $myCustomFields_var = new myCustomFields();
}
?>