<?php

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Header Settings',
		'menu_title'	=> 'Header',
		'parent_slug'	=> 'theme-general-settings',
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Footer Settings',
		'menu_title'	=> 'Footer',
		'parent_slug'	=> 'theme-general-settings',
	));
	
}

include('acf_fields.php');

// Define request data
$request = $_REQUEST;


/* Generate Quote Ticket */
function genTicketString() {
    $length = 8;
    $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters)-1)];
    }
    return $string;
}

add_shortcode('quoteticket', 'genTicketString');

// logs a Breeders member in after submitting a form
function pippin_login_member() {

	if(isset($_POST['email']) && wp_verify_nonce($_POST['pippin_login_nonce'], 'login-nonce')) {
 
		// this returns the user ID and other info from the user name
		$user = get_userdatabylogin($_POST['email']);
 
		if(!$user) {
			// if the user name doesn't exist
			pippin_errors()->add('empty_username', __('Invalid username'));
		}
 
		if(!isset($_POST['pippin_user_pass']) || $_POST['pippin_user_pass'] == '') {
			// if no password was entered
			pippin_errors()->add('empty_password', __('Please enter a password'));
		}
 
		// check the user's login with their password
		if(!wp_check_password($_POST['pippin_user_pass'], $user->user_pass, $user->ID)) {
			// if the password is incorrect for the specified user
			pippin_errors()->add('empty_password', __('Incorrect password'));
		}
 
		// retrieve all error messages
		$errors = pippin_errors()->get_error_messages();
 
		// only log the user in if there are no errors
		if(empty($errors)) {
 
			wp_setcookie($_POST['email'], $_POST['pippin_user_pass'], true);
			wp_set_current_user($user->ID, $_POST['email']);	
			do_action('wp_login', $_POST['email']);
 
			wp_redirect(home_url()); exit;
		}
	}
}
add_action('init', 'pippin_login_member');

function pippin_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

switch($request['action']) {

	case 'login' :
		if (!is_user_logged_in()) {
			$creds = [];
			$creds['user_login'] = isset( $_POST['email'] ) ? $_POST['email'] : '';
			$creds['user_password'] = isset( $_POST['pwd'] ) ? $_POST['pwd'] : '';
			$user = wp_signon($creds, false);
			
			if (is_wp_error($user)) {
				printf($user->get_error_message());
				error_log($user->get_error_message());
			} else {
				wp_redirect('account');
    			exit;
			}
		}
		break;
}