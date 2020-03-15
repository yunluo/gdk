<?php
/**
 * GDK_Points Table class
 */

// WP_List_Table is not loaded automatically so we need to load it in our application
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class GDK_Points_List_Table extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns  = $this->get_columns();
        $hidden   = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data     = $this->table_data();
        usort($data, [ & $this, 'sort_data']);
        $perPage     = 30; //每页30个数据
        $currentPage = $this->get_pagenum();
        $totalItems  = count($data);
        $this->set_pagination_args([
            'total_items' => $totalItems,
            'per_page'    => $perPage,
        ]);

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = [
            $columns,
            $hidden,
            $sortable,
        ];
        $this->items = $data;
    }

    /**
     * Override the parent columns method.
     * Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = [
            'point_id'    => 'ID',
            'user_id'     => '用户ID',
            'points'      => '金币',
            'description' => '描述',
            'datetime'    => '日期&时间',
            'status'      => '状态',
            'actions'     => '操作',
        ];

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return [];
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return [
            'point_id'    => [
                'point_id',
                false,
            ],
            'user_id'     => [
                'user_id',
                false,
            ],
            'points'      => [
                'points',
                false,
            ],
            'description' => [
                'description',
                false,
            ],
            'datetime'    => [
                'datetime',
                false,
            ],
            'status'      => [
                'status',
                false,
            ],
        ];
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function __table_data()
    {
        $data = [];
        $data = GDK_Points::get_points(null, null, null, ARRAY_A);
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param Array $item
     *            Data
     * @param String $column_name
     *            - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'point_id':
            case 'user_id':
            case 'description':
            case 'points':
            case 'datetime':
            case 'status':
                return $item[$column_name];
                break;
            case 'actions':
                $actions = [
                    'edit'   => sprintf('<a href="?page=%s&action=%s&point_id=%s">编辑</a>', $_REQUEST['page'], 'edit', $item['point_id']),
                    'delete' => sprintf('<a href="?page=%s&action=%s&point_id=%s">删除</a>', $_REQUEST['page'], 'delete', $item['point_id']),
                ];

                //Return the title contents
                return sprintf('%1$s%2$s', $item[$column_name] ?? "",
                    $this->row_actions($actions, true)
                );
                break;
            default:
                return print_r($item, true);
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function __sort_data($a, $b)
    {
        // Set defaults
        $orderby = 'point_id';
        $order   = 'desc';

        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = strnatcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }
}

/**
 * GDK_Points Admin class
 */
class GDK_Points_Admin
{

    public static function init()
    {
        add_action('admin_notices', [__CLASS__, 'admin_notices']);
        add_action('admin_menu', [__CLASS__, 'admin_menu'], 'manage_options');
    }

    public static function admin_notices()
    {
        if (!empty(self::$notices)) {
            foreach (self::$notices as $notice) {
                echo $notice;
            }
        }
    }

    /**
     * Adds the admin section.
     */
    public static function admin_menu()
    {
        $admin_page = add_menu_page('金币', '金币', 'manage_options', 'points', [__CLASS__, 'points_menu'], 'dashicons-awards');
    }

    public static function points_menu()
    {
        $alert = "";
        if (isset($_POST['psearch'])) {
            $sdata = trim($_POST['psearch']);
            if (preg_match('/E20/', $sdata)) { //order id
                global $wpdb;
                $point_id = $wpdb->get_row("SELECT point_id FROM " . GDK_Points_Database::points_get_table("users") . " WHERE description = '{$sdata}'", ARRAY_A)['point_id'];
                $points   = GDK_Points::get_point($point_id);
            } elseif (filter_var($sdata, FILTER_VALIDATE_EMAIL)) { //email
                $user   = get_user_by('email', $sdata);
                $points = GDK_Points::get_points_by_user($user->ID);
                $k[]    = '<div style="margin-bottom:10px;">用户ID：' . $user->ID . '  &nbsp;&nbsp;总金币为：' . GDK_Points::get_user_total_points($user->ID) . '</div>';
            } else { //userid
                $points = GDK_Points::get_points_by_user($sdata);
                $k[]    = '<div style="margin-bottom:10px;">用户ID：' . $sdata . '  &nbsp;&nbsp;总金币为：' . GDK_Points::get_user_total_points($sdata) . '</div>';
            }
            if (is_array($points)) {
                foreach ($points as $point) {
                    $k[] = '<div style="margin-bottom:5px;">金币：' . $point->points . ' &nbsp;&nbsp;描述：' . $point->description . ' &nbsp;&nbsp;日期：' . $point->datetime . '</div>';
                }
            } else {
                $k[] = '<div style="margin-bottom:5px;">金币：' . $points->points . ' &nbsp;&nbsp;描述：' . $points->description . ' &nbsp;&nbsp;日期：' . $points->datetime . '</div>';
            }
            $alert = implode(" ", $k);
        }

        if (isset($_POST['save']) && isset($_POST['action'])) {
            if ($_POST['action'] == "edit") {
                $point_id = isset($_POST['point_id']) ? intval($_POST['point_id']) : null;
                $points   = GDK_Points::get_point($point_id);
                $data     = array();
                if (isset($_POST['user_mail'])) {
                    $data['user_mail'] = $_POST['user_mail'];
                }
                if (isset($_POST['user_id'])) {
                    $data['user_id'] = $_POST['user_id'];
                }
                if (isset($_POST['datetime'])) {
                    $data['datetime'] = $_POST['datetime'];
                }
                if (isset($_POST['description'])) {
                    $data['description'] = $_POST['description'];
                }
                if (isset($_POST['status'])) {
                    $data['status'] = $_POST['status'];
                }
                if (isset($_POST['points'])) {
                    $data['points'] = $_POST['points'];
                }

                if ($points) { // 编辑金币
                    GDK_Points::update_points($point_id, $data);
                } else { // 增加金币
                    if (isset($_POST['user_mail'])) { //如果输入邮箱的话
                        $usermail = $data['user_mail'];
                        $user     = get_user_by('email', $usermail);
                        $userid   = $user->ID;
                        $username = $user->display_name;
                    }
                    if (isset($_POST['user_id'])) { //如果输入用户ID的话
                        $user     = get_user_by('id', $data['user_id']);
                        $usermail = $user->user_email;
                        $userid   = $data['user_id'];
                        $username = $user->display_name;
                    }
                    GDK_Points::set_points($_POST['points'], $userid, $data);
                    $message = '<div class="emailcontent" style="width:100%;max-width:720px;text-align:left;margin:0 auto;padding-top:80px;padding-bottom:20px"><div class="emailtitle"><h1 style="color:#fff;background:#51a0e3;line-height:70px;font-size:24px;font-weight:400;padding-left:40px;margin:0">金币金额调整通知</h1><div class="emailtext" style="background:#fff;padding:20px 32px 40px"><div style="padding:0;font-weight:700;color:#6e6e6e;font-size:16px">尊敬的' . $username . ',您好！</div><p style="color:#6e6e6e;font-size:13px;line-height:24px">您的金币金额被管理员调整，请查收！</p><table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-top:1px solid #eee;border-left:1px solid #eee;color:#6e6e6e;font-size:16px;font-weight:normal"><thead><tr><th colspan="2" style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;background:#f8f8f8">您的金币详细情况</th></tr></thead><tbody><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">用户名</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">' . $username . '</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center">调整金币</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">' . $_POST['points'] . '</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center">金币总额</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">' . GDK_Points::get_user_total_points($userid, 'accepted') . '</td></tr></tbody></table><p style="color:#6e6e6e;font-size:13px;line-height:24px">如果您的金币金额有异常，请您在第一时间和我们取得联系哦，联系邮箱：' . get_bloginfo('admin_email') . '</p></div><div class="emailad" style="margin-top:4px"><a href="' . home_url() . '"><img src="http://reg.163.com/images/secmail/adv.png" alt="" style="margin:auto;width:100%;max-width:700px;height:auto"></a></div></div></div>';
                    $headers = "Content-Type:text/html;charset=UTF-8\n";
                    wp_mail($usermail, 'Hi,' . $username . '，金币账户金额增加通知！', $message, $headers);
                }
            }
            $alert = "金币已更新";
        }
        if (isset($_GET["action"])) {
            $action = $_GET["action"];
            if ($action !== null) {
                switch ($action) {
                    case 'edit':
                        if (isset($_GET['point_id']) && ($_GET['point_id'] !== null)) {
                            return self::points_admin_points_edit(intval($_GET['point_id']));
                        } else {
                            return self::points_admin_points_edit();
                        }
                        break;
                    case 'delete':
                        if ($_GET['point_id'] !== null) {
                            if (current_user_can('administrator')) {
                                GDK_Points::remove_points($_GET['point_id']);
                                global $wpdb;
                                $wcu_sql = "DELETE FROM " . GDK_Points_Database::points_get_table("users") . " WHERE status = 'removed'";
                                $wpdb->query($wcu_sql);
                                $alert = "金币已删除";
                            }
                        }
                        break;
                }
            }
        }

        if ($alert != "") {
            echo '<div style="background-color: #ffffe0;border: 1px solid #993;padding: 1em;margin-right: 1em;">' . $alert . '</div>';
        }

        $current_url      = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $cancel_url       = remove_query_arg('point_id', remove_query_arg('action', $current_url));
        $current_url      = remove_query_arg('point_id', $current_url);
        $current_url      = remove_query_arg('action', $current_url);
        $exampleListTable = new GDK_Points_List_Table();
        $exampleListTable->prepare_items();
        ?>
		<div class="wrap">
			<h2>金币管理</h2>
			<span class="manage add">
				<a class="add button" href="<?php echo esc_url(add_query_arg('action', 'edit', $current_url)); ?>" title="点击手动添加金币">添加金币</a>
			</span>
			<form method="POST" style="float:right;">
				<input size="40" placeholder="搜索用户ID/用户邮箱/订单号" type="search" name="psearch" value="" />
				</form>
			<?php echo '<style type="text/css">tbody#the-list tr:hover{background:rgba(132,219,162,.61)}</style>';
        $exampleListTable->display(); ?>
		</div>
		<?php
}

    public static function points_admin_points_edit($point_id = null)
    {
        global $wpdb;
        $output = '';

        if (!current_user_can('administrator')) {
            wp_die('Access denied.');
        }

        $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $cancel_url  = remove_query_arg('point_id', remove_query_arg('action', $current_url));
        $current_url = remove_query_arg('point_id', $current_url);
        $current_url = remove_query_arg('action', $current_url);

        $saved = false; // temporal

        if ($point_id !== null) {
            $points = GDK_Points::get_point($point_id);

            if ($points !== null) {
                $user_id     = $points->user_id;
                $num_points  = $points->points;
                $description = $points->description;
                $datetime    = $points->datetime;
                $status      = $points->status;
            }
        } else {
            $user_id     = "";
            $num_points  = 0;
            $description = "ADD";
            $datetime    = "";
            $status      = 'accepted';
        }

        if (empty($point_id)) {
            $pointsclass = 'newpoint';
        } else {
            $pointsclass = 'editpoint';
        }
        $output .= '<div class="points ' . $pointsclass . '">';
        $output .= '<h2>';
        if (empty($point_id)) {
            $output .= '新金币';
        } else {
            $output .= '编辑金币';
        }
        $output .= '</h2>';
        $output .= '<form id="points" action="' . $current_url . '" method="post">';
        $output .= '<div>';

        if ($point_id) {
            $output .= sprintf('<input type="hidden" name="point_id" value="%d" />', intval($point_id));
        }

        $output .= '<input type="hidden" name="action" value="edit" />';
        $output .= '<p class="usermail">';
        $output .= '<label>';
        $output .= '<span class="title">用户邮箱</span>';
        $output .= ' ';
        $output .= sprintf('<input type="text" name="user_mail" value="%s" />', $user_mail);
        $output .= ' ';
        $output .= '<span class="description">用户在网站的注册邮箱</span>';
        $output .= '</label>';
        $output .= '</p>';
        $output .= '<p class="userid">';
        $output .= '<label>';
        $output .= '<span class="title">用户ID</span>';
        $output .= ' ';
        $output .= sprintf('<input type="text" name="user_id" value="%s" />', $user_id);
        $output .= ' ';
        $output .= '<span class="description">输入用户ID，与用户邮箱勿冲突</span>';
        $output .= '</label>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label>';
        $output .= '<span class="title">日期&时间</span>';
        $output .= ' ';
        $output .= sprintf('<input type="text" name="datetime" value="%s" id="datetimepicker" />', esc_attr($datetime));
        $output .= ' ';
        $output .= '<span class="description">格式 : YYYY-MM-DD HH:MM:SS【可忽略，自动生成】</span>';
        $output .= '</label>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label>';
        $output .= '<span class="title">描述</span>';
        $output .= '<br>';
        $output .= '<textarea name="description">';
        $output .= stripslashes($description);
        $output .= '</textarea>';
        $output .= '</label>';
        $output .= '</p>';
        $output .= '<p>';
        $output .= '<label>';
        $output .= '<span class="title">金币</span>';
        $output .= ' ';
        $output .= sprintf('<input type="text" name="points" value="%s" />', esc_attr($num_points));
        $output .= '</label>';
        $output .= '</p>';
        $status_descriptions = array(
            'accepted' => '正常',
            'pending'  => '待审',
            'rejected' => '驳回',
        );
        $output .= '<p>';
        $output .= '<label>';
        $output .= '<span class="title">状态</span>';
        $output .= ' ';
        $output .= '<select name="status">';
        foreach ($status_descriptions as $key => $label) {
            $selected = $key == $status ? ' selected="selected" ' : '';
            $output .= '<option ' . $selected . ' value="' . esc_attr($key) . '">' . $label . '</option>';
        }
        $output .= '</select>';
        $output .= '</label>';
        $output .= '</p>';
        $output .= wp_nonce_field('save', 'points-nonce', true, false);
        $output .= sprintf('<input class="button" type="submit" name="save" value="%s"/>', '保存');
        $output .= ' ';
        $output .= sprintf('<a class="cancel" href="%s">%s</a>', $cancel_url, $saved ? '返回' : '取消');
        $output .= '</div>';
        $output .= '</form>';
        $output .= '</div>';
        echo $output;
    }
}