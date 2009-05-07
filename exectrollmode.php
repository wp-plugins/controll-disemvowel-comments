<?php
// We need some WordPress functions
require('../../../wp-load.php');

global $wpdb;

// Get specific comment
if(isset($_GET['cid'])) $cid = (int)$_GET['cid'];
	else wp_die("You're not allowed to do this!");
$action = (int)$_GET['action'];	// 1 = remove
if(isset($_GET['adm'])) $adm = (int)$_GET['adm'];
$commentarr = get_comment($cid, ARRAY_A);

// If current user can't edit comments, we need to die
if ( !current_user_can( 'edit_post', $commentarr['comment_post_ID'] ) )
		wp_die("You're not allowed to do this!");

// Add or removes comment ID to troll array
$tm_comments = get_option('tm_comments');
if($action == 1) $tm_comments = array_diff($tm_comments, array($cid));
	else $tm_comments[] = $cid;
	
update_option('tm_comments', $tm_comments);


// Decides where to redirect based on variable (it's different if we are in the backend)
if(!$adm) :
	wp_redirect(get_permalink($commentarr['comment_post_ID']) . '#comment-'. $commentarr['comment_ID']);
else:
	wp_redirect(admin_url('edit-comments.php'));
endif;
	
?>