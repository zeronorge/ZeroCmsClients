<?php
/*  
    Copyright (c) 2009 Matt Weber

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
*/

#set defaults if not initialized
if (zero_cms_get_option('zero_cms_solr_initialized') != '1') {
    update_site_option('zero_cms_index_all_sites', '0');
    zero_cms_update_option('zero_cms_post_url', 'localhost');
    zero_cms_update_option('zero_cms_site_id', 'wordpress');
    zero_cms_update_option('zero_cms_index_pages', '1');
    zero_cms_update_option('zero_cms_index_posts', '1');
    zero_cms_update_option('zero_cms_delete_page', '1');
    zero_cms_update_option('zero_cms_delete_post', '1');
    zero_cms_update_option('zero_cms_private_page', '1');
    zero_cms_update_option('zero_cms_private_post', '1');
    zero_cms_update_option('zero_cms_output_info', '1');
    zero_cms_update_option('zero_cms_output_pager', '1');
    zero_cms_update_option('zero_cms_output_facets', '1');
    //zero_cms_update_option('zero_cms_exclude_pages', array());
    zero_cms_update_option('zero_cms_exclude_pages', '');  
    zero_cms_update_option('zero_cms_num_results', '5');
    zero_cms_update_option('zero_cms_cat_as_taxo', '1');
    zero_cms_update_option('zero_cms_solr_initialized', '1');
    zero_cms_update_option('zero_cms_max_display_tags', '10');
    zero_cms_update_option('zero_cms_facet_on_categories', '1');
    zero_cms_update_option('zero_cms_facet_on_taxonomy', '1');
    zero_cms_update_option('zero_cms_facet_on_tags', '1');
    zero_cms_update_option('zero_cms_facet_on_author', '1');
    zero_cms_update_option('zero_cms_facet_on_type', '1');
    zero_cms_update_option('zero_cms_enable_dym', '1');
    zero_cms_update_option('zero_cms_index_comments', '1');
    zero_cms_update_option('zero_cms_connect_type', 'solr');
    //zero_cms_update_option('zero_cms_index_custom_fields', array());
    //zero_cms_update_option('zero_cms_facet_on_custom_fields', array());
    zero_cms_update_option('zero_cms_index_custom_fields', '');  
    zero_cms_update_option('zero_cms_facet_on_custom_fields', '');  
}

wp_reset_vars(array('action'));

# save form settings if we get the update action
# we do saving here instead of using options.php because we need to use
# zero_cms_update_option instead of update option.
if ($_POST['action'] == 'update') {
    $options = array('zero_cms_post_url', 'zero_cms_site_id', 'zero_cms_index_pages',
                     'zero_cms_index_posts', 'zero_cms_delete_page', 'zero_cms_delete_post', 'zero_cms_private_page',
                     'zero_cms_private_post', 'zero_cms_output_info', 'zero_cms_output_pager', 'zero_cms_output_facets',
                     'zero_cms_exclude_pages', 'zero_cms_num_results', 'zero_cms_cat_as_taxo', 'zero_cms_max_display_tags',
                     'zero_cms_facet_on_categories', 'zero_cms_facet_on_tags', 'zero_cms_facet_on_author', 'zero_cms_facet_on_type',
                     'zero_cms_enable_dym', 'zero_cms_index_comments', 'zero_cms_connect_type', 'zero_cms_index_all_sites', 
                     'zero_cms_index_custom_fields', 'zero_cms_facet_on_custom_fields', 'zero_cms_facet_on_taxonomy');
        
    foreach ( $options as $option ) {
        $option = trim($option);
        $value = null;
        if ( isset($_POST[$option]) )
            $value = $_POST[$option];
            
        if ( !is_array($value) ) $value = trim($value);
        $value = stripslashes_deep($value);
        
        if ( $option == 'zero_cms_index_all_sites') {   
            update_site_option($option, $value);
        } else {
            zero_cms_update_option($option, $value);
        }
    }
    
    ?>
    <div id="message" class="updated fade"><p><strong><?php _e('Success!', 'zero_cms4wp') ?></strong></p></div>
    <?php
}

# checks if we need to check the checkbox
function zero_cms_checkCheckbox( $theFieldname ) {
    if ($theFieldname == 'zero_cms_index_all_sites') {
        if (get_site_option($theFieldname) == '1') {
            echo 'checked="checked"';
        }
    } else {
	    if( zero_cms_get_option( $theFieldname ) == '1'){
		    echo 'checked="checked"';
	    }
	}
}

function zero_cms_checkConnectOption($connectType) {
    if ( zero_cms_get_option('zero_cms_connect_type') === $connectType ) {
        echo 'checked="checked"';
    }
}

# check for any POST settings
if ($_POST['zero_cms_ping']) {
    if (zero_cms_get_client(true)) {
?>
<div id="message" class="updated fade"><p><strong><?php _e('Ping Success!', 'zero_cms4wp') ?></strong></p></div>
<?php
    } else {
?>
    <div id="message" class="updated fade"><p><strong><?php _e('Ping Failed!', 'zero_cms4wp') ?></strong></p></div>
<?php
    }
} else if ($_POST['zero_cms_deleteall']) {
    zero_cms_delete_all();
?>
    <div id="message" class="updated fade"><p><strong><?php _e('All Indexed Pages Deleted!', 'zero_cms4wp') ?></strong></p></div>
<?php
} else if ($_POST['zero_cms_optimize']) {
    zero_cms_optimize();
?>
    <div id="message" class="updated fade"><p><strong><?php _e('Index Optimized!', 'zero_cms4wp') ?></strong></p></div>
<?php
}
?>

<div class="wrap">
<h2><?php _e('ZeroCMS client For WordPress', 'zero_cms4wp') ?></h2>

<form method="post" action="options-general.php?page=zero-cms-client/zero-cms-client.php">
<h3><?php _e('Configure ZeroCMS', 'zero_cms4wp') ?></h3>

<div class="solr_admin clearfix">
	<div class="solr_adminR">
		<div class="solr_adminR2" id="solr_admin_tab2">
			<label><?php _e('ZeroCMS Post url', 'zero_cms4wp') ?></label>
			<p><input type="text" name="zero_cms_post_url" size="62" value="<?php _e(zero_cms_get_option('zero_cms_post_url'), 'zero_cms4wp'); ?>" /></p>
			<label><?php _e('Site ID', 'zero_cms4wp') ?></label>
			<p><input type="text" name="zero_cms_site_id" value="<?php _e(zero_cms_get_option('zero_cms_site_id'), 'zero_cms4wp'); ?>" /></p>
		</div>
	</div>
</div>
<hr />
<h3><?php _e('Indexing Options', 'zero_cms4wp') ?></h3>
<table class="form-table">
    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Index Pages', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_index_pages" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_index_pages'); ?> /></td>
        <th scope="row" style="width:200px;float:left;margin-left:20px;"><?php _e('Index Posts', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_index_posts" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_index_posts'); ?> /></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Remove Page on Delete', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_delete_page" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_delete_page'); ?> /></td>
        <th scope="row" style="width:200px;float:left;margin-left:20px;"><?php _e('Remove Post on Delete', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_delete_post" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_delete_post'); ?> /></td>
    </tr>
    
    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Remove Page on Status Change', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_private_page" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_private_page'); ?> /></td>
        <th scope="row" style="width:200px;float:left;margin-left:20px;"><?php _e('Remove Post on Status Change', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_private_post" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_private_post'); ?> /></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Index Comments', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_index_comments" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_index_comments'); ?> /></td>
    </tr>
        
    <?php
    //is this a multisite installation
    if (is_multisite() && is_main_site()) {
    ?>
    
    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Index all Sites', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_index_all_sites" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_index_all_sites'); ?> /></td>
    </tr>
    <?php
    }
    ?>
    <tr valign="top">
        <th scope="row"><?php _e('Index custom fields (comma separated names list)') ?></th>
        <td><input type="text" name="zero_cms_index_custom_fields" value="<?php print( zero_cms_filter_list2str(zero_cms_get_option('zero_cms_index_custom_fields'), 'zero_cms4wp')); ?>" /></td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Excludes Posts or Pages (comma separated ids list)') ?></th>
        <td><input type="text" name="zero_cms_exclude_pages" value="<?php print( zero_cms_filter_list2str(zero_cms_get_option('zero_cms_exclude_pages'), 'zero_cms4wp')); ?>" /></td>
    </tr>
</table>
<hr />
<h3><?php _e('Result Options', 'zero_cms4wp') ?></h3>
<table class="form-table">
    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Output Result Info', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_output_info" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_output_info'); ?> /></td>
        <th scope="row" style="width:200px;float:left;margin-left:20px;"><?php _e('Output Result Pager', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_output_pager" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_output_pager'); ?> /></td>
    </tr>
 
    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Output Facets', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_output_facets" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_output_facets'); ?> /></td>
        <th scope="row" style="width:200px;float:left;margin-left:20px;"><?php _e('Category Facet as Taxonomy', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_cat_as_taxo" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_cat_as_taxo'); ?> /></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Categories as Facet', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_facet_on_categories" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_facet_on_categories'); ?> /></td>
        <th scope="row" style="width:200px;float:left;margin-left:20px;"><?php _e('Tags as Facet', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_facet_on_tags" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_facet_on_tags'); ?> /></td>
    </tr>
    
    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Author as Facet', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_facet_on_author" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_facet_on_author'); ?> /></td>
        <th scope="row" style="width:200px;float:left;margin-left:20px;"><?php _e('Type as Facet', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_facet_on_type" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_facet_on_type'); ?> /></td>
    </tr>

     <tr valign="top">
         <th scope="row" style="width:200px;"><?php _e('Taxonomy as Facet', 'zero_cms4wp') ?></th>
         <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_facet_on_taxonomy" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_facet_on_taxonomy'); ?> /></td>
      </tr>
      
    <tr valign="top">
        <th scope="row"><?php _e('Custom fields as Facet (comma separated ordered names list)') ?></th>
        <td><input type="text" name="zero_cms_facet_on_custom_fields" value="<?php print( zero_cms_filter_list2str(zero_cms_get_option('zero_cms_facet_on_custom_fields'), 'zero_cms4wp')); ?>" /></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="width:200px;"><?php _e('Enable Spellchecking', 'zero_cms4wp') ?></th>
        <td style="width:10px;float:left;"><input type="checkbox" name="zero_cms_enable_dym" value="1" <?php echo zero_cms_checkCheckbox('zero_cms_enable_dym'); ?> /></td>
    </tr>
                   
    <tr valign="top">
        <th scope="row"><?php _e('Number of Results Per Page', 'zero_cms4wp') ?></th>
        <td><input type="text" name="zero_cms_num_results" value="<?php _e(zero_cms_get_option('zero_cms_num_results'), 'zero_cms4wp'); ?>" /></td>
    </tr>   
    
    <tr valign="top">
        <th scope="row"><?php _e('Max Number of Tags to Display', 'zero_cms4wp') ?></th>
        <td><input type="text" name="zero_cms_max_display_tags" value="<?php _e(zero_cms_get_option('zero_cms_max_display_tags'), 'zero_cms4wp'); ?>" /></td>
    </tr>
</table>
<hr />
<?php settings_fields('zero_cms-options-group'); ?>

<p class="submit">
<input type="hidden" name="action" value="update" />
<input id="settingsbutton" type="submit" class="button-primary" value="<?php _e('Save Changes', 'zero_cms4wp') ?>" />
</p>

</form>
<hr />
<form method="post" action="options-general.php?page=zero-cms-client/zero-cms-client.php">
<h3><?php _e('Actions', 'zero_cms4wp') ?></h3>
<table class="form-table">
    <tr valign="top">
        <th scope="row"><?php _e('Check Server Settings', 'zero_cms4wp') ?></th>
        <td><input type="submit" class="button-primary" name="zero_cms_ping" value="<?php _e('Execute', 'zero_cms4wp') ?>" /></td>
    </tr>
 
    <tr valign="top">
        <th scope="row"><?php _e('Load All Pages', 'zero_cms4wp') ?></th>
        <td><input type="submit" class="button-primary" name="zero_cms_pageload" value="<?php _e('Execute', 'zero_cms4wp') ?>" /></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Load All Posts', 'zero_cms4wp') ?></th>
        <td><input type="submit" class="button-primary" name="zero_cms_postload" value="<?php _e('Execute', 'zero_cms4wp') ?>" /></td>
    </tr>
    
    <tr valign="top">
        <th scope="row"><?php _e('Optimize Index', 'zero_cms4wp') ?></th>
        <td><input type="submit" class="button-primary" name="zero_cms_optimize" value="<?php _e('Execute', 'zero_cms4wp') ?>" /><strong> UNSUPPORTED</strong></td>
    </tr>
        
    <tr valign="top">
        <th scope="row"><?php _e('Delete All', 'zero_cms4wp') ?></th>
        <td><input type="submit" class="button-primary" name="zero_cms_deleteall" value="<?php _e('Execute', 'zero_cms4wp') ?>" /><strong> UNSUPPORTED</strong></td>
    </tr>
</table>
</form>

</div>
