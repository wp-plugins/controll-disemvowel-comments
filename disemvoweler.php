<?php
/*
Plugin Name: ConTroll: Disemvowel Comments
Plugin URI: http://www.spreeblick.com/controll-disemvowel-comments-plugin
Description: Selectively removes vowels from comments if you think they're annyoing. Don't feed the trolls!
Author: Christoph Boecken
Version: 0.5
Author URI: http://christophboecken.de
*/

/*  Copyright 2009  Christoph Boecken  (email : jeriko.one@gmx.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Set locale
load_plugin_textdomain('disemvoweler');

// Includes file for options page
include('disemvoweler_options.php');

function tm_disemvowel($commenttext) {
	global $comment;
	
	$tm_comments = get_option('tm_comments');
	if(!$tm_comments) return $commenttext;
	
	if(in_array($comment->comment_ID, $tm_comments)) :
		if(!is_admin()) :
			$commenttext = preg_replace('/[aeiou]/i','',$commenttext);
		else :
			$commenttext = '<strong>[' . __('Vowels removed','disemvoweler') . ']</strong> ' . $commenttext;
		endif;
	endif;
	
	return $commenttext;
	
}

// Adds a link after every comment to remove 
function tm_add_link($commenttext) {
	global $comment, $post;
	
	$tm_comments = get_option('tm_comments');
	if(!$tm_comments) $tm_comments = array();
	
	if ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return $commenttext;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return $commenttext;
	}

	$location =	WP_PLUGIN_URL . '/controll-disemvowel-comments/exectrollmode.php?cid=' . $comment->comment_ID;
	if(in_array($comment->comment_ID, $tm_comments)) :
		$commenttext .= '<br /><a href="' . $location . '&action=1">(' . __('Add vowels','disemvoweler') . ')</a>';
	else:
		$commenttext .= '<br /><a href="' . $location . '">(' . __('Remove vowels','disemvoweler') . ')</a>';
	endif;
	
	return $commenttext;
}

// Adds a link to comment actions in admin panel
function tm_add_admin_link($actions) {
	$comment = $GLOBALS['comment'];
	
	$tm_comments = get_option('tm_comments');
	if(!$tm_comments) $tm_comments = array();
	
	if(in_array($comment->comment_ID, $tm_comments)) :	
		$location = WP_PLUGIN_URL . '/controll-disemvowel-comments/exectrollmode.php?adm=1&action=1&cid=' . $comment->comment_ID;
		$actions['removevowels'] = "<a href='$location'>" . __('Add vowels','disemvoweler') . "</a>";
	else:
		$location = WP_PLUGIN_URL . '/controll-disemvowel-comments/exectrollmode.php?adm=1&cid=' . $comment->comment_ID;
		$actions['removevowels'] = "<a href='$location'>" . __('Remove vowels','disemvoweler') . "</a>";
	endif;
	
	return $actions;
}

// Adds CSS to header
function tm_add_css() {
	$output = '<style type="text/css">.trolldesc { position: relative; font-size: 10px; font-weight: normal; color: #666; } .trolldesctext { position: absolute; top: 0; left: 0; background: #EEE; border: 1px solid #CCC; width: 250px; display: none; padding: 10px; font: normal 1em/1.5 Arial, Helvetica, sans-serif; color: #666; } .trolldesc:hover .trolldesctext, .trolldesctext:hover { display: block; }</style>';
	
	echo $output;
}

// Adds a description to every comment which was disemvoweled
function tm_add_description($commenttext) {
	global $comment;
	
	$tm_comments = get_option('tm_comments');
	if(!$tm_comments) $tm_comments = array();
	
	if(in_array($comment->comment_ID, $tm_comments)) :
		$desctitle = get_option('tm_descriptiontitle');
		$description = get_option('tm_description');
		$description = str_replace('%commentauthor%',$comment->comment_author, $description);
		$commenttext .= ' <span class="trolldesc">[' . $desctitle . ']<div class="trolldesctext">' . nl2br($description) . '</div></span>';
	endif;
	
	return $commenttext;
}

// Filters
add_filter('comment_row_actions','tm_add_admin_link');
add_filter('comment_text','tm_disemvowel');

// Filters for frontend
if(!is_admin()) :
	add_filter('comment_text','tm_add_link', 1030);
	// We only need those two if a description should be displayed
	if(get_option('tm_showdesclink') == "yes") :
		add_action('wp_head','tm_add_css');
		add_filter('comment_text','tm_add_description', 1040);
	endif;
endif;

// To be called upon plugin activation
function tm_activate() {
	add_option('tm_comments','','','yes');
	add_option('tm_showdesclink','yes');
	add_option('tm_descriptiontitle',__('Where are the vowels?','disemvoweler'));
	add_option('tm_description',__('%commentauthor% is a troll. Since trolls can only grunt, his comment was adjusted as well','disemvoweler'));
}
register_activation_hook(__FILE__,'tm_activate');

// To be called upon plugin deactivation
function tm_deactivate() {
	delete_option('tm_comments');
	delete_option('tm_showdesclink');
	delete_option('tm_descriptiontitle');
	delete_option('tm_description');
}
register_deactivation_hook(__FILE__,'tm_deactivate');

?>
