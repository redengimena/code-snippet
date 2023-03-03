<?php

// Enqueue child theme style.css
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'child-style', get_stylesheet_uri() );

    if ( is_rtl() ) {
    	wp_enqueue_style( 'mylisting-rtl', get_template_directory_uri() . '/rtl.css', [], wp_get_theme()->get('Version') );
    }
}, 500 );

// Happy Coding :)

function custom_search_form( $form ) {
      $form = '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
        <div class="custom-form"><label class="screen-reader-text" for="s">' . __( 'Search:' ) . '</label>
        <input type="text" value="' . get_search_query() . '" name="s" id="s" />
        <input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search' ) .'" />
		<input type="hidden" name="post_type" value="job_listing">
      </div>
      </form>';

      return $form;
    }
    add_filter( 'get_search_form', 'custom_search_form', 40 );

// Registration Form
add_action( 'elementor_pro/forms/new_record',  'p4wp_elementor_reg_form' , 10, 2 );

function p4wp_elementor_reg_form($record,$ajax_handler)
{
    $form_name = $record->get_form_settings('form_name');
    //Check that the form is the "create new user form" if not - stop and return;
    if ('User Registration Form' !== $form_name) {
        return;
    }
    $form_data = $record->get_formatted_data();
    $username=$form_data['Username']; //Get tne value of the input with the label "User Name"
    $password = $form_data['Password']; //Get tne value of the input with the label "Password"
    $email=$form_data['Email'];  //Get tne value of the input with the label "Email"
    $user = wp_create_user($username,$password,$email); // Create a new user, on success return the user_id no failure return an error object
    if (is_wp_error($user)){ // if there was an error creating a new user
        $ajax_handler->add_error_message("Failed to create new user: ".$user->get_error_message()); //add the message
        $ajax_handler->is_success = false;
        return;
    }
    $first_name=$form_data["First Name"]; //Get tne value of the input with the label "First Name"
    $last_name=$form_data["Last Name"]; //Get tne value of the input with the label "Last Name"
    wp_update_user(array("ID"=>$user,"first_name"=>$first_name,"last_name"=>$last_name)); // Update the user with the first name and last name

    /* Automatically log in the user and redirect the user to the home page */
    $creds= array( // credientials for newley created user
        "user_login"=>$username,
        "user_password"=>$password,
        "remember"=>true
    );
    $signon = wp_signon($creds); //sign in the new user
    if ($signon)
        $ajax_handler->add_response_data( 'redirect_url', get_home_url() ); // optinal - if sign on succsfully - redierct the user to the home page
}


function accomm_is_post_to_update( $continue, $post_id, $xml_node, $import_id ) {
    // Run this code on a specific import
    if ($import_id == 42) {
        // Do something to decide if this post should update
        $rules = get_post_meta($post_id, '_listing-update-rules', true);
        $rule = 'I have improved my listing profile on The Fold, please do not make any updates if the ATDW details change.';
        if (in_array($rule, $rules)) {
            return false;
        }
        return $continue;
    }
}

add_filter( 'wp_all_import_is_post_to_update', 'accomm_is_post_to_update', 10, 4 );

add_filter('media-cloud/storage/prefix', function($prefix, $postId) {
  // Adds a @{id} token that will be replaced with the attachment's ID, or 0 if 
  // the attachment doesn't exist yet.
  $prefix = str_replace("@{id}", $postId ?? 0, $prefix);
  
  return $prefix;
}, 10, 2);
