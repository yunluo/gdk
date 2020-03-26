<?php
/**
 * class-points-shortcodes.php
 */
class GDK_Points_Shortcodes
{
    /**
     * Add shortcodes.
     */
    public static function init()
    {
        add_shortcode('points_users_list', [__CLASS__, 'points_users_list']);
        add_shortcode('points_user_points', [__CLASS__, 'points_user_points']);
        add_shortcode('pay', [__CLASS__, 'pay']);
        add_shortcode('points_user_points_details', [__CLASS__, 'points_user_points_details']);

    }
    public static function points_users_list($atts, $content = null)
    {
        $options = shortcode_atts(
            [
                'limit'    => 10,
                'order_by' => 'points',
                'order'    => 'DESC',
            ],
            $atts
        );
        extract($options);
        $output      = "";
        $pointsusers = GDK_Points::get_users();
        if (sizeof($pointsusers) > 0) {
            foreach ($pointsusers as $pointsuser) {
                $total = GDK_Points::get_user_total_points($pointsuser);
                $output .= '<div class="points-user">';
                $output .= '<span style="font-weight:bold;width:100%;" class="points-user-username">';
                $output .= get_user_meta($pointsuser, 'nickname', true);
                $output .= ':</span>';
                $output .= '<span class="points-user-points">';
                $output .= " " . $total . " 金币";
                $output .= '</span>';
                $output .= '</div>';
            }
        } else {
            $output .= '<p>No users</p>';
        }
        return $output;
    }
    public static function points_user_points($atts, $content = null)
    {
        $output  = "";
        $options = shortcode_atts(['id' => ""],
            $atts
        );
        extract($options);
        if ($id == "") {
            $id = get_current_user_id();
        }
        if ($id !== 0) {
            $points = GDK_Points::get_user_total_points($id, 'accepted');
            $output .= $points;
        }
        return $output;
    }

    /*付费可见短代码开始*/

    public static function pay($atts, $content = null)
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $pid     = get_the_ID();
        $result  = $wpdb->get_row("SELECT description FROM " . GDK_Points_Database::points_get_table("users") . " WHERE user_id=" . $user_id . " AND description=" . $pid . " AND status='accepted' LIMIT 0, 3;", ARRAY_A)['description']; //验证是否支付
        extract(shortcode_atts(['point' => "10"], $atts));
        $notice = '';
        add_post_meta($pid, '_point_content', $content, true) or update_post_meta($pid, '_point_content', $content); //没有新建,有就更新
        if (is_user_logged_in()) {
            if ($result == $pid || current_user_can('administrator')) {
                $notice .= '<div class="cm-alert success">';
                $notice .= $content;
                $notice .= '</div>';
            } else {
                if (GDK_Points::get_user_total_points($user_id, 'accepted') < $point) {
                    $notice .= '<fieldset id="hide_notice" class="fieldset ta-center"><legend class="legend ta-left">付费内容</legend>';
                    $notice .= '<p>当前隐藏内容需要支付</p><span class="cm-coin">' . $point . '金币</span>';
                    $notice .= '<p>您当前拥有<span class="red">' . GDK_Points::get_user_total_points($user_id, 'accepted') . '</span>金币，金币不足，请充值</p>';
                    $notice .= buy_points();
                    $notice .= '</fieldset>';
                } else {
                    $notice .= '<fieldset id="hide_notice" class="fieldset ta-center"><legend class="legend ta-left">付费内容</legend>';
                    $notice .= '<p>当前隐藏内容需要支付</p><span class="cm-coin">' . $point . '金币</span>';
                    $notice .= '<p>您当前拥有<span class="red">' . GDK_Points::get_user_total_points($user_id, 'accepted') . '</span>金币</p>';
                    $notice .= '<p><button class="cm-btn primary" id="pay_points" data-point="' . $point . '" data-userid="' . $user_id . '" data-action="gdk_pay_buy" data-id="' . $pid . '">点击购买</button></p>';
                    $notice .= '</fieldset>';
                }
            }
        } else {
            $notice .= '<fieldset id="hide_notice" class="fieldset ta-center"><legend class="legend ta-left">付费内容</legend>';
            $notice .= '<p>当前隐藏内容需要支付</p><span class="cm-coin">' . $point . '金币</span>';
            $notice .= '<p>您当前尚未登陆,请登陆后查看</p>';
            $notice .= weixin_login_btn();
            $notice .= '</fieldset>';
        }
        return $notice;
    }
    /*付费可见短代码结束*/
    /**
     * Shortcode. 显示用户的金币细节
     */
    public static function points_user_points_details($atts, $content = null)
    {
        $options = shortcode_atts(
            [
                'user_id'     => '',
                'order_by'    => 'point_id',
                'order'       => 'DESC',
                'description' => true,
            ],
            $atts
        );
        extract($options);
        date_default_timezone_set('Asia/Shanghai');
        if (is_string($description) && (($description == '0') || (strtolower($description) == 'false'))) {
            $description = false;
        }

        $desc_th = '';
        if ($description) {
            $desc_th = '<th>描述</th>';
        }
        global $wp_query;
        $curauth = $wp_query->get_queried_object();
        $user_id = $curauth->ID;
        $points  = GDK_Points::get_points_by_user($user_id);
        $output  = '<table class="points_user_points_table">' .
            '<tr>' .
            '<th>日期时间' .
            '<th>金币</th>' .
            '<th>类别</th>' .
            '<th>状态</th>' .
            $desc_th .
            '</tr>';
        if ($user_id !== 0) {
            if (sizeof($points) > 0) {
                foreach ($points as $point) {
                    $desc_td = '';
                    if ($description) {
                        $desc_td = '<td>' . $point->description . '</td>';
                    }
                    if ($point->points > 0) {$leibie = '充值';} elseif ($point->points < 0) {$leibie = '消费';}
                    $output .= '<tr>' .
                    '<td>' . $point->datetime . '</td>' .
                    '<td>' . $point->points . '</td>' .
                    '<td>' . $leibie . '</td>' .
                    '<td>' . $point->status . '</td>' .
                        $desc_td .
                        '</tr>';
                }
            }
        }

        $output .= '</table>';

        return $output;
    }
}
GDK_Points_Shortcodes::init();
