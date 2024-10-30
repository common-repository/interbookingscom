<?php
/*
Plugin Name: Interbookings.com
Plugin URI: http://www.interbookings.com
Description: Interbookings.com plugin for <strong>easy implementation</strong> of the bookings form
Author: Interbookings.com
Version: 1.0.0
Author URI: http://www.inerbookings.com
*/

function control_interbookings()
{
	// Get options from database
	$options = get_option("interbookings-settings");  
	if(!array($options)){
		$options = array(
			"promote_on" => '0'
		);	
	}
    
    // Set allowed colors
    $settheme = array();
    $settheme[] = "grey";
    $settheme[] = "red";
    $settheme[] = "green";
	$settheme[] = "blue";
	$settheme[] = "purple";
	$settheme[] = "orange";
	$settheme[] = "pink";

	// Set pattern
	$pattern = "/^([0-9A-Z]{5}-){3}+([0-9A-Z]{5})/";

    if(!empty($_POST)) {
		$options['accesskey'] 	= (preg_match($pattern, $_POST['accesskey']))? $_POST['accesskey'] : "";   
		$options['promote_on'] 	= $_POST['interbookings-promote_on']; 
    	$options['buttontheme'] = (in_array($_POST['buttontheme'], $settheme))? $_POST['buttontheme'] : "grey";
	    update_option("interbookings-settings", $options);
    }
 ?>   
    <h2>Interbookings.com Settings</h2>

	<div>Please submit the accesskey in the form below. You can make an accesskey in your Interbookings.com backoffice-system. Visit our <a href='http://knowledgebase.interbookings.com' target='_blank'>Knowledge base</a> for more information about accesskeys.</div>
    <div style="clear:both;"></div>
    <form name="interbookings_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	    <table class="form-table">
		    <tr> 
		        <th><label for="aboutme-title">Accesskey: </label></th>  
		        <td>
		            <input type="text" name="accesskey" style='width:200px;' value="<?=(!empty($options['accesskey']))? $options['accesskey'] : "";?>" /><br />
		            <input class="checkbox" type="checkbox" id="interbookings-promote_on" name="interbookings-promote_on" value="1" <?php echo (($options["promote_on"]=='1')?' checked=1':''); ?> />
		            <label for="interbookings-promote_on">Help us to promote our product</label><br />
		        </td>
		    </tr>
		    <tr>
		        <th><label for="aboutme-email">Widget theme: <small><em>(used for the button)</em></small></label></th>  
		        <td>
		        	<select name="buttontheme">
		        		<? foreach($settheme as $theme):?>
		        			<option value="<?=$theme?>" <?=($theme == $options['buttontheme'])? "selected='selected'" : ""?>><?=$theme?></option>
		        		<? endforeach; ?>	
		        	</select>
		        </td>    
		    </tr>
	    </table>
	    <p class="submit">
	      <input class="button-primary" type="submit" value="<?php _e('Update Settings') ?>" />
	    </p>
    </form>
    </div>
<?
    
    
    
}

function widget_interbookings()
{
	$options = get_option("interbookings-settings"); 
	$key 	 = $options["accesskey"];
	$theme	 = $options["buttontheme"];
	
	if(!empty($key)){

		$html = "<!-- Interbookings.com Plugin -->
		<script type='text/javascript'>
		var _ibdata = [];
		_ibdata.push(['_authKey','{$key}']);";
		$html .= (!empty($theme))? "_ibdata.push(['_setTheme','{$theme}']);" : "";
		$html .="
		</script>
		<script type='text/javascript' async src='http://api.interbookings.com/plugins/ib_form_plugin-1.0.2.min.js' id='interbookings_plugin'></script>
		<!-- / Interbookings.com Plugin -->";
		$html .= ($options['promote_on'] == '1')? "<span style='float:left; margin:3px 0 3px 5px; font-style:italic; font-size:10px;'>Powered by <a href='http://www.interbookings.com' target='_blank'>Interbookings.com</a></span>" : "";
		echo $html;
	}
}

function replace_content($content)
{
	$options = get_option("interbookings-settings"); 
	$key 	 = $options["accesskey"];

	if(!empty($key)){
		$html = '<iframe src="http://api.interbookings.com/bookingform/iframe/?AuthKey='.$key.'" style="width:960px; margin-left: 0px; padding: 0; height:1200px;border:none;" marginheight="0" marginwidth="0" frameborder="0"></iframe>';
		$html .= ($options['promote_on'] == '1')? "<span style='float:left; margin:3px 0 3px 5px; font-style:italic; font-size:10px;'>Powered by <a href='http://www.interbookings.com' target='_blank'>Interbookings.com</a></span>" : "";	
		$content = str_replace("{interbookings_form}", $html, $content);
	}

	return $content;
}

function interbookings_admin_actions() {
    add_options_page("Interbookings.com", "Interbookings.com", 10, "Interbookings", "control_interbookings");
}

function control_interbookings_() {
   ?>
   <p>  
     To configure this widget go to (Settings/Interbookings) or click <a href='<?php echo get_bloginfo('wpurl')?>/wp-admin/options-general.php?page=Interbookings'>here</a>
   </p>
   <?php  
}
function init_interbookings(){
    wp_register_sidebar_widget('interbookings', 'Interbookings.com', 'widget_interbookings', array("description" => "Places an Interbookings.com button for direct access to the bookingform"));
    wp_register_widget_control('interbookings', 'Interbookings.com', 'control_interbookings_');  
}


add_filter('the_content','replace_content');
add_action('plugins_loaded', 'init_interbookings');
add_action('admin_menu', 'interbookings_admin_actions');