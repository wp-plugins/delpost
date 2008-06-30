<?php
	/*
	Plugin Name: Post Data Delete Advanced
	Plugin URI: http://made.com.ua/delpost
	Description: This plugin delete attachments of deleted post and related databse records. Actualy this plugin is a fix for Wordpress.
	Version: 0.1
	Author: Oleg Butuzov
	Author URI: http://made.com.ua/
	*/



	add_action('delete_post', 'deletepostdata');
	
	
	function deletepostdata($id = 0){
		global $wpdb;
		
		if ($id == 0 || $id === FALSE) 
			return false;
		if ( !current_user_can('delete_post', $id) ) 
			return false;
		
		$res = get_children("post_parent=".intval($id)."&post_type=attachment&orderby=menu_order ASC, ID&order=DESC");
		if (is_array($res)){
			foreach($res as $array){
				if (current_user_can('delete_post', $array->ID) ) {
					wp_delete_attachment($array->ID);
				}
			}
		}
	}
?>