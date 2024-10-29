<?php 

/**
 * Plugin Name: AWEOS Dynamic Phone Number
 * Plugin URI: https://aweos.de
 * Description: Define a Dynamic Phone Number. Choose the numbers in our custom settings page. Example: [dnum] 
 * Version: 1.2.2
 * Author: AWEOS GmbH
 * Author URI: https://aweos.de
 * License: GPLv3
 */

// protection

if ( !defined( "ABSPATH" ) ) exit;

function awpn_register_activation_hook() {
	if ( version_compare(get_bloginfo( "version" ), "4.5", "<") ) {
		wp_die( "Please update WordPress to use this plugin" );
	}

	// default values for options
	add_option("awpn-params", "gclid");
}


function awpn_deactivate() {
	delete_option("awpn-params");
	delete_option("awpn-number-1");
	delete_option("awpn-number-2");
}

register_activation_hook( __FILE__, "awpn_register_activation_hook");
register_deactivation_hook( __FILE__, "awpn_deactivate");



add_shortcode( 'dnum', 'awpn_dnum' );
function awpn_dnum() {

	$output = "";
	$isAd = false;

	$params = explode(
		", ", get_option("awpn-params")
	);

	foreach ($params as $param) {	
		if (isset($_GET[$param])) {
			$isAd = true;
		}
	}

	$number;

	if ($isAd) {
		$number = get_option("awpn-number-1");
	} else {
		$number = get_option("awpn-number-2");
	}


	$clean_number = str_replace(' ', '', $number);
	$clean_number = str_replace('/', '', $clean_number);
	$clean_number = str_replace('|', '', $clean_number);
	$clean_number = str_replace('\\', '',$clean_number);
	$clean_number = str_replace('-', '', $clean_number);
	$clean_number = str_replace(':', '', $clean_number);
	$clean_number = str_replace('.', '', $clean_number);
	$clean_number = str_replace('(', '', $clean_number);
	$clean_number = str_replace(')', '', $clean_number);
	$clean_number = trim($clean_number, "a..zA..Z");

	return "<a href='tel:$clean_number'>$number</a>";
}

// create custom plugin settings menu.
add_action('admin_menu', 'awpn_create_menu');

function awpn_create_menu() {
	
	add_menu_page(
		'AWEOS Phone Number Shortcode', 
		'Phone Number',
		'administrator', 
		__FILE__, 
		'awpn_phone_number_setting' 
	);

	add_action( 'admin_init', 'awpn_register_setting' );
}

function awpn_register_setting() {
	register_setting( 'awpn-number-group', 'awpn-number-1' );
	register_setting( 'awpn-number-group', 'awpn-number-2' );
	register_setting( 'awpn-number-group', 'awpn-params' );
}

function awpn_phone_number_setting() {
?>
<div class="wrap">
	<h1>AWEOS Dynamic Phone Number</h1>
	<p>Fill the following form with the two phone numbers you want do display if the user visited the site from a gclid (Google AdWords) or other parameters and services. <br>Change the phone number displayed on your website depending on the parameters in the URL.</p>

	<form method="post" action="options.php">

	    <?php settings_fields( 'awpn-number-group' ); ?>
	    <?php do_settings_sections( 'awpn-number-group' ); ?>

	    <table class="form-table">
	        <tr valign="top">
	        	<th scope="row">Dynamic Phone Number</th>
	    	</tr>
	    	<tr>
		        <td>
		        	<label>Parameter <br>Seperated by commas</label>
		        </td>
		        <td>
		        	<input type="text" name="awpn-params" placeholder="gclid, custom_id" value="<?php echo esc_attr( get_option('awpn-params') ); ?>" />
		        </td>
	    	</tr>
	    	<tr>
		        <td>
		        	<label>First Phone Number <br>With the parameter.</label>
		        </td>
		        <td>
		        	<input type="text" name="awpn-number-1" value="<?php echo esc_attr( get_option('awpn-number-1') ); ?>" />
		        </td>
	    	</tr>
		    <tr>
		        <td>
		        	<label>Second Phone Number<br>Without the parameter.</label>
		        </td>
		        <td>
		        	<input type="text" name="awpn-number-2" value="<?php echo esc_attr( get_option('awpn-number-2') ); ?>" />
		        </td>
	        </tr>
	    </table>
	    
	    <?php submit_button(); ?>

	</form>
</div>
<?php } 