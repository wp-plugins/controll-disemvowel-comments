<?php

load_plugin_textdomain('disemvoweler');

// Adds options page
function tm_menu() {
	add_options_page ( 'Disemvowel Comments Options', 'Disemvowel Comments', 8, __FILE__, 'tm_menu_options' );
}

function tm_menu_options() { ?>
<div class="wrap">
	<h2><?php _e('Disemvowel Comments Options','disemvoweler'); ?></h2>
	
	<?php if(isset($_POST['deleteallids'])) : 
	$tm_comments = array();
	update_option('tm_comments',$tm_comments); ?>	
	<div id="message" class="updated fade" style="background-color: rgb(255,251,204);">
		<p><strong><?php _e('Disemvowelment removed. All comments are back to normal.','disemvoweler'); ?></strong></p>
	</div>
	<?php endif; ?>
	
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Show description link?','disemvoweler'); ?></th>
				td><label for="tm_showdesclink"><input type="checkbox" name="tm_showdesclink" value="yes" <?php if(get_option('tm_showdesclink') == "yes") echo 'checked="checked"'; ?> /> <?php _e('Display a link below every disemvoweled comment to provide an explanation','disemvoweler'); ?></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description link title','disemvoweler'); ?></th>
				<td><label for="tm_descriptiontitle"><input type="text" name="tm_descriptiontitle" value="<?php echo get_option('tm_descriptiontitle'); ?>" class="regular-text" /> <?php _e('Name for description link','disemvoweler'); ?></label></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description','disemvoweler'); ?></th>
				<td><p><label for="tm_description"><?php _e('Description text','disemvoweler'); ?> <small>(<?php _e('use %commentauthor% for comment authors name','disemvoweler'); ?>)</small></label></p>
				<textarea class="large-text" name="tm_description" rows="10" cols="50"><?php echo get_option('tm_description'); ?></textarea></td>
			</tr>
		</table>
		
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="tm_delete,tm_showdesclink,tm_descriptiontitle,tm_description" />
		
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Update options','disemvoweler'); ?>" />
		</p>
	</form>
	
	<hr />
	
	<h2><?php _e('Remove disemvowelment','disemvoweler'); ?></h2>
	<p><?php _e('Using the button below sets all comments back to normal. Warning: This cannot be undone!','disemvoweler'); ?></p>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']; ?>">
		<p><input type="submit" name="deleteallids" class="button-primary" value="<?php _e('Remove disemvowelment','disemvoweler'); ?>" /></p>
	</form>
</div>
<?php }

add_action ( 'admin_menu', 'tm_menu'); 
?>