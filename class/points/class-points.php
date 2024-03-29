<?php

/**
 * GDK_Points_Database class.
 */
class GDK_Points_Database
{
    public static $prefix = 'points_';

    public static function points_get_table($table)
    {
        global $wpdb;
        $result = '';

        switch ($table) {
            case 'users':
                $result = $wpdb->prefix.self::$prefix.'users';

                break;
        }

        return $result;
    }
}

/**
 * GDK_Points class.
 */
class GDK_Points
{
    public static function get_points_by_user($user_id, $limit = null, $order_by = null, $order = null, $output = OBJECT, $offset = 0)
    {
        global $wpdb;

        $limit_str = '';
        if (isset($limit) && (null !== $limit)) {
            $limit_str = ' LIMIT '.$offset.' ,'.$limit;
        }
        $order_by_str = '';
        if (isset($order_by) && (null !== $order_by)) {
            $order_by_str = ' ORDER BY '.$order_by;
        }
        $order_str = '';
        if (isset($order) && (null !== $order)) {
            $order_str = ' '.$order;
        }

        $result = $wpdb->get_results('SELECT * FROM '.GDK_Points_Database::points_get_table('users')." WHERE user_id = '{$user_id}'".$order_by_str.$order_str.$limit_str, $output);

        return $result;
    }

    public static function get_user_total_points($user_id, $status = null)
    {
        global $wpdb;

        $result = 0;

        $where_status = '';
        if (null !== $status) {
            $where_status = " AND status = '".$status."'";
        }
        $points = $wpdb->get_row('SELECT SUM(points) as total FROM '.GDK_Points_Database::points_get_table('users')." WHERE user_id = '{$user_id}' ".$where_status);

        if ($points && (null !== $points->total)) {
            $result = $points->total;
        }

        return $result;
    }

    public static function get_users_total_points($limit = null, $order_by = null, $order = null, $status = null)
    {
        global $wpdb;

        $where_status = '';
        if (null !== $status) {
            $where_status = " WHERE status = '".$status."'";
        }
        $points = $wpdb->get_results('SELECT SUM(points) as total, user_id FROM '.GDK_Points_Database::points_get_table('users').$where_status.' GROUP BY user_id');

        return $points;
    }

    /**
     * Get users id who have some points.
     *
     * @param $user_id
     *
     * @return array
     */
    public static function get_users()
    {
        global $wpdb;

        $users_id = $wpdb->get_results('SELECT user_id FROM '.GDK_Points_Database::points_get_table('users').' GROUP BY user_id');

        $result = [];
        if (sizeof($users_id) > 0) {
            foreach ($users_id as $user_id) {
                $result[] = $user_id->user_id;
            }
        }

        return $result;
    }

    public static function set_points($points, $user_id, $info = [])
    {
        global $wpdb;

        $values = ['points' => $points];

        if (isset($info['datetime']) && ('' !== $info['datetime'])) {
            $values['datetime'] = $info['datetime'];
        } else {
            $values['datetime'] = date('Y-m-d H:i:s', time());
        }
        if (isset($info['description'])) {
            $values['description'] = $info['description'];
        }
        if (isset($info['status'])) {
            $values['status'] = $info['status'];
        }
        if (isset($info['type'])) {
            $values['type'] = $info['type'];
        }
        if (isset($info['data'])) {
            $values['data'] = $info['data']; // yet serialized
        }
        if (isset($info['ip'])) {
            $values['ip'] = $info['ip'];
        }
        if (isset($info['ipv6'])) {
            $values['ipv6'] = $info['ipv6'];
        }
        $values['user_id'] = $user_id;

        $rows_affected = $wpdb->insert(GDK_Points_Database::points_get_table('users'), $values);

        return $rows_affected;
    }

    /**
     * Get a points list.
     *
     * @param int    $limit
     * @param string $order_by
     * @param string $order
     * @param mixed  $output
     *
     * @return Ambigous <mixed, NULL, multitype:, multitype:multitype: , multitype:Ambigous <multitype:, NULL> >
     */
    public static function get_points($limit = null, $order_by = null, $order = null, $output = OBJECT)
    {
        global $wpdb;

        $where_str = " WHERE status != 'removed'";

        $limit_str = '';
        if (isset($limit) && (null !== $limit)) {
            $limit_str = ' LIMIT 0 ,'.$limit;
        }
        $order_by_str = '';
        if (isset($order_by) && (null !== $order_by)) {
            $order_by_str = ' ORDER BY '.$order_by;
        }
        $order_str = '';
        if (isset($order) && (null !== $order)) {
            $order_str = ' '.$order;
        }

        $result = $wpdb->get_results('SELECT * FROM '.GDK_Points_Database::points_get_table('users').$where_str.$order_by_str.$order_str.$limit_str, $output);

        return $result;
    }

    public static function get_point($point_id = null)
    {
        global $wpdb;

        $result = null;

        if (isset($point_id) && (null !== $point_id)) {
            $points_id_str = ' WHERE point_id = '.(int) $point_id;
            $result = $wpdb->get_row('SELECT * FROM '.GDK_Points_Database::points_get_table('users').$points_id_str);
        }

        return $result;
    }

    public static function remove_points($point_id)
    {
        global $wpdb;

        $values = [];
        $values['status'] = 'removed';

        $rows_affected = $wpdb->update(GDK_Points_Database::points_get_table('users'), $values, ['point_id' => $point_id]);

        if (!$rows_affected) {
            $rows_affected = null;
        }

        return $rows_affected;
    }

    public static function update_points($point_id, $info = [])
    {
        global $wpdb;

        $values = [];

        if (isset($info['user_id'])) {
            $values['user_id'] = $info['user_id'];
        }
        if (isset($info['datetime'])) {
            $values['datetime'] = $info['datetime'];
        }
        if (isset($info['description'])) {
            $values['description'] = $info['description'];
        }
        if (isset($info['status'])) {
            $values['status'] = $info['status'];
        }
        if (isset($info['points'])) {
            $values['points'] = $info['points'];
        }
        if (isset($info['type'])) {
            $values['type'] = $info['type'];
        }
        if (isset($info['data'])) {
            $values['data'] = $info['data']; // yet serialized
        }
        if (isset($info['ip'])) {
            $values['ip'] = $info['ip'];
        }
        if (isset($info['ipv6'])) {
            $values['ipv6'] = $info['ipv6'];
        }

        $rows_affected = $wpdb->update(GDK_Points_Database::points_get_table('users'), $values, ['point_id' => $point_id]);

        if (!$rows_affected) { // insert
            $rows_affected = null;
        }

        return $rows_affected;
    }
}
