<?php

class Auto_Widget_cache
{
    public $cache_time = 18000;

    /*
    MINUTE_IN_SECONDS = 60 seconds
    HOUR_IN_SECONDS = 3600 seconds
    DAY_IN_SECONDS = 86400 seconds
    WEEK_IN_SECONDS = 604800 seconds
    YEAR_IN_SECONDS = 3153600 seconds
     */
    public function __construct()
    {
        add_filter('widget_display_callback', [$this, '_cache_widget_output'], 10, 3);
        add_action('in_widget_form', [$this, 'in_widget_form'], 5, 3);
        add_filter('widget_update_callback', [$this, 'widget_update_callback'], 5, 3);
    }

    public function get_widget_key($i, $a)
    {
        return 'WC-'.md5(serialize([$i, $a]));
    }

    public function cache_widget_output($instance, $widget, $args)
    {
        if (false === $instance) {
            return $instance;
        }

        if (isset($instance['wc_cache']) && true == $instance['wc_cache']) {
            return $instance;
        }

        $timer_start = microtime(true);
        $transient_name = $this->get_widget_key($instance, $args);
        if (false === ($cached_widget = get_transient($transient_name))) {
            ob_start();
            $widget->widget($args, $instance);
            $cached_widget = ob_get_clean();
            set_transient($transient_name, $cached_widget, $this->cache_time);
        }
        echo $cached_widget;
        echo '<!-- From widget cache in '.number_format(microtime(true) - $timer_start, 5).' seconds -->';

        return false;
    }

    public function in_widget_form($t, $return, $instance)
    {
        $instance = wp_parse_args((array) $instance, ['title' => '', 'text' => '', 'wc_cache' => null]);
        if (!isset($instance['wc_cache'])) {
            $instance['wc_cache'] = null;
        } ?>
<p>
    <input id="<?php
echo $t->get_field_id('wc_cache'); ?>" name="<?php
echo $t->get_field_name('wc_cache'); ?>" type="checkbox" <?php checked($instance['wc_cache'] ?? 0); ?> />
    <label for="<?php
echo $t->get_field_id('wc_cache'); ?>">禁止缓存本工具?</label>
</p>
<?php
    }

    public function widget_update_callback($instance, $new_instance, $old_instance)
    {
        $instance['wc_cache'] = isset($new_instance['wc_cache']);

        return $instance;
    }
}
if (gdk_option('gdk_sidebar_cache')) {
    $GLOBALS['Auto_Widget_cache'] = new Auto_Widget_cache();
}
