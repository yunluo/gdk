<?php

include 'class-points.php';

include 'class-points-admin.php';

include 'class-points-shortcodes.php';

class GDK_Points_Class
{
    private static $__notices = [];

    public static function init()
    {
        add_action('init', [__CLASS__, 'wp_init']);
        add_action('admin_init', [__CLASS__, 'activate']);
    }

    public static function wp_init()
    {
        GDK_Points_Admin::init();
    }

    /**
     * activation work.
     */
    public static function activate()
    {
        global $wpdb;

        $charset_collate = '';
        if (!empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }
        if (!empty($wpdb->collate)) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        // create tables
        $points_users_table = GDK_Points_Database::points_get_table('users');
        if ($wpdb->get_var("SHOW TABLES LIKE '{$points_users_table}'") != $points_users_table) {
            $queries[] = "CREATE TABLE {$points_users_table} (
			point_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			points   BIGINT(20) DEFAULT 0,
			datetime     datetime NOT NULL,
			description  varchar(5000),
			ref_id       BIGINT(20) DEFAULT null,
			ip           int(10) unsigned default NULL,
			ipv6         decimal(39,0) unsigned default NULL,
			data         longtext default NULL,
			status       varchar(10) NOT NULL DEFAULT 'accepted',
			type         varchar(32) NULL,
			PRIMARY KEY   (point_id)
			) {$charset_collate};";
        }
        if (!empty($queries)) {
            require_once ABSPATH.'wp-admin/includes/upgrade.php';
            dbDelta($queries);
        }
    }
}
GDK_Points_Class::init();
