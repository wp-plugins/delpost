<?php 
 
 /*
 Plugin Name: Post Data Delete Advanced
 Plugin URI: http://wordpress.org/extend/plugins/delpost/
 Description: Delete Attachments assigned to post if post deleted.
 Version: 0.2
 Author: Oleg Butuzov
 Author URI: http://made.ua/
 */
	
	
	// till wp 3.1
	add_action('delete_post',		 'delete_posts_before_delete_post');
	// from wp 3.2
	add_action('before_delete_post', 'delete_posts_before_delete_post');
	function delete_posts_before_delete_post($id){
		
		
		$subposts = get_children(array( 
		    'post_parent' => $id,
		    'post_type'   => 'any', 
		    'numberposts' => -1,
		    'post_status' => 'any'
		));
		
		if (is_array($subposts) && count($subposts) > 0){
			$uploadpath = wp_upload_dir();
		 	
			foreach($subposts as $subpost){
				
				$_wp_attached_file = get_post_meta($subpost->ID, '_wp_attached_file', true);
				
				$original = basename($_wp_attached_file);
				$pos = strpos(strrev($original), '.');
				if (strpos($original, '.') !== false){
					$ext = explode('.', strrev($original));
					$ext = strrev($ext[0]);
				} else {
					$ext = explode('-', strrev($original));
					$ext = strrev($ext[0]);
				}
				
				$pattern = $uploadpath['basedir'].'/'.dirname($_wp_attached_file).'/'.basename($original, '.'.$ext).'-[0-9]*x[0-9]*.'.$ext;
				$original= $uploadpath['basedir'].'/'.dirname($_wp_attached_file).'/'.basename($original, '.'.$ext).'.'.$ext;
				if (getimagesize($original)){
					$thumbs = glob($pattern);
					if (is_array($thumbs) && count($thumbs) > 0){
						foreach($thumbs as $thumb)
							unlink($thumb);
					}
				}
				wp_delete_attachment( $subpost->ID, true );
			}
		}
	}