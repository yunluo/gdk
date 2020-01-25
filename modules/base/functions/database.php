<?php
if (!function_exists('nc_database_optimize')):
	add_action('wp_ajax_nopriv_nc_database_optimize', 'nc_database_optimize');
	add_action('wp_ajax_nc_database_optimize', 'nc_database_optimize');
	function nc_database_optimize(){
		global $wpdb;
		$wcu_sql = 'SHOW TABLE STATUS FROM `'.DB_NAME.'`';
		$result = $wpdb->get_results($wcu_sql);
		$is_error = false;
		foreach($result as $row){
			$wcu_sql = 'OPTIMIZE TABLE '.$row->Name;
			$wpdb_result = $wpdb->query($wcu_sql);
			if (!$wpdb_result) $is_error = true;
		}

		if ($is_error) {
			echo json_encode(array(
				's' => 400
			));
			die();
		}
		echo json_encode(array(
			's' => 200,
			'm' => '优化成功'
		));
		die();
	}
endif;

if (!function_exists('nc_database_cleanup')):
	add_action('wp_ajax_nopriv_nc_database_cleanup', 'nc_database_cleanup');
	add_action('wp_ajax_nc_database_cleanup', 'nc_database_cleanup');
	function nc_database_cleanup() {
		global $wpdb;
		$type = sanitize_text_field($_POST['action_type']);
		switch($type) {
			case "revision":
				$wcu_sql = "DELETE FROM $wpdb->posts WHERE post_type = 'revision'";
				$result = $wpdb->query($wcu_sql);
				break;
			case "draft":
				$wcu_sql = "DELETE FROM $wpdb->posts WHERE post_status = 'draft'";
				$result = $wpdb->query($wcu_sql);
				break;
			case "autodraft":
				$wcu_sql = "DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'";
				$result = $wpdb->query($wcu_sql);
				break;
			case "moderated":
				$wcu_sql = "DELETE FROM $wpdb->comments WHERE comment_approved = '0'";
				$result = $wpdb->query($wcu_sql);
				break;
			case "spam":
				$wcu_sql = "DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'";
				$result = $wpdb->query($wcu_sql);
				break;
			case "trash":
				$wcu_sql = "DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'";
				$result = $wpdb->query($wcu_sql);
				break;
			case "postmeta":
				$wcu_sql = "DELETE pm FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL";
				$result = $wpdb->query($wcu_sql);
				break;
			case "commentmeta":
				$wcu_sql = "DELETE FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)";
				$result = $wpdb->query($wcu_sql);
				break;
			case "relationships":
				$wcu_sql = "DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM $wpdb->posts)";
				$result = $wpdb->query($wcu_sql);
				break;
			case "feed":
				$wcu_sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'";
				$result = $wpdb->query($wcu_sql);
				break;
		}
		if ($result) {
			echo json_encode(array(
				's' => 200,
				'm' => '清理成功'
			));
		} else {
			echo json_encode(array(
				's' => 400,
				'm' => '清理失败'
			));
		}
		die();
	}
endif;

if (!function_exists('nc_database_clean_up_count')):
	add_action('wp_ajax_nopriv_nc_database_clean_up_count', 'nc_database_clean_up_count');
	add_action('wp_ajax_nc_database_clean_up_count', 'nc_database_clean_up_count');
	function nc_database_clean_up_count() {
		$result = array(
			's' => 200,
			'counts' => array(
				'revision' => nc_database_clean_up_count_action('revision'),
				'draft' => nc_database_clean_up_count_action('draft'),
				'autodraft' => nc_database_clean_up_count_action('autodraft'),
				'moderated' => nc_database_clean_up_count_action('moderated'),
				'spam' => nc_database_clean_up_count_action('spam'),
				'trash' => nc_database_clean_up_count_action('trash'),
				'postmeta' => nc_database_clean_up_count_action('postmeta'),
				'commentmeta' => nc_database_clean_up_count_action('commentmeta'),
				'relationships' => nc_database_clean_up_count_action('relationships'),
				'feed' => nc_database_clean_up_count_action('feed')
			)
		);
		echo json_encode($result);
		die();
	}
endif;

if (!function_exists('nc_database_clean_up_count_action')):
	function nc_database_clean_up_count_action($type){
		global $wpdb;
		switch($type) {
			case "revision":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "draft":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'draft'";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "autodraft":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'auto-draft'";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "moderated":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "spam":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "trash":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "postmeta":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL";
				//$wcu_sql = "SELECT COUNT(*) FROM $wpdb->postmeta WHERE NOT EXISTS ( SELECT * FROM $wpdb->posts WHERE $wpdb->postmeta.post_id = $wpdb->posts.ID )";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "commentmeta":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "relationships":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM $wpdb->posts)";
				$count = $wpdb->get_var($wcu_sql);
				break;
			case "feed":
				$wcu_sql = "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'";
				$count = $wpdb->get_var($wcu_sql);
				break;
		}
		return $count;
		die();
	}
endif;
