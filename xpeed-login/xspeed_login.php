<?php 
/*
Plugin Name: XpeedLogin
Plugin URI: https://github.com/Badhan33/xpeed-login
Description: A simple front-end login/registration plugin.
Shortcodes: For Login - [xpeed_form_login], For Signup - [xpeed_form_register].
Version: 0.1
Author: MD. Kamrul Hasan Khan
Author URI: http://github.com/Badhan33
Text Domain: xpeed_login
*/
if ( !function_exists( 'add_action' ) ) {
  exit;
}

// LOGIN

function get_xpeed_form_login($redirect=false) {
  global $xpeed_form_count;
  ++$xpeed_form_count;
  if (!is_user_logged_in()) :
    $return = "<form action=\"\" method=\"post\" class=\"xpeed_form xpeed_form_login\">\r\n";
    $error = get_xpeed_error($xpeed_form_count);
    if ($error)
      $return .= "<p class=\"error\">{$error}</p>\r\n";
    $success = get_xpeed_success($xpeed_form_count);
    if ($success)
      $return .= "<p class=\"success\">{$success}</p>\r\n";

    $return .= "  <p>
      <label for=\"xpeed_username\">".__('Username','xpeed_login')."</label>
      <input type=\"text\" id=\"xpeed_username\" name=\"xpeed_username\"/>
    </p>\r\n";

    $return .= "  <p>
      <label for=\"xpeed_password\">".__('Password','xpeed_login')."</label>
      <input type=\"password\" id=\"xpeed_password\" name=\"xpeed_password\"/>
    </p>\r\n";
   
    if ($redirect)
      $return .= "  <input type=\"hidden\" name=\"redirect\" value=\"{$redirect}\">\r\n";
   
    $return .= "  <input type=\"hidden\" name=\"xpeed_action\" value=\"login\">\r\n";
    $return .= "  <input type=\"hidden\" name=\"xpeed_form\" value=\"{$xpeed_form_count}\">\r\n";
    $return .= "  <button type=\"submit\">".__('Login','xpeed_login')."</button>\r\n";
    $return .= "</form>\r\n";
  else : 
    $return = __('User is logged in.','xpeed_login');
  endif;
  return $return;
}

// shortcode: [xpeed_form_login] in post/page content
add_shortcode('xpeed_form_login','xpeed_form_login_shortcode');
function xpeed_form_login_shortcode ($atts,$content=false) {
  $atts = shortcode_atts(array(
    'redirect' => false
  ), $atts);
  return get_xpeed_form_LOGIN($atts['redirect']);
} 
 
// REGISTRATION
 
function get_xpeed_form_register($redirect=false) {
  global $xpeed_form_count;
  ++$xpeed_form_count;
  if (!is_user_logged_in()) :
    $return = "<form action=\"\" method=\"post\" class=\"xpeed_form xpeed_form_register\">\r\n";
    $error = get_xpeed_error($xpeed_form_count);
    if ($error)
      $return .= "<p class=\"error\">{$error}</p>\r\n";
    $success = get_xpeed_success($xpeed_form_count);
    if ($success)
      $return .= "<p class=\"success\">{$success}</p>\r\n";

    $return .= "  <p>
      <label for=\"xpeed_username\">".__('Username','xpeed_login')."</label>
      <input type=\"text\" id=\"xpeed_username\" name=\"xpeed_username\"/>
    </p>\r\n";
    $return .= "  <p>
      <label for=\"xpeed_email\">".__('Email','xpeed_login')."</label>
      <input type=\"email\" id=\"xpeed_email\" name=\"xpeed_email\"/>
    </p>\r\n";
  // redirect if successful
    if ($redirect)
      $return .= "  <input type=\"hidden\" name=\"redirect\" value=\"{$redirect}\">\r\n";
   
    $return .= "  <input type=\"hidden\" name=\"xpeed_action\" value=\"register\">\r\n";
    $return .= "  <input type=\"hidden\" name=\"xpeed_form\" value=\"{$xpeed_form_count}\">\r\n";
   
    $return .= "  <button type=\"submit\">".__('Register','xpeed_login')."</button>\r\n";
    $return .= "</form>\r\n";
  else : 
    $return = __('User is logged in.','xpeed_login');
  endif;
  return $return;
}

// shortcode: [xpeed_form_register] in post/page content
add_shortcode('xpeed_form_register','xpeed_form_register_shortcode');
function xpeed_form_register_shortcode ($atts,$content=false) {
  $atts = shortcode_atts(array(
    'redirect' => false
  ), $atts);
  return get_xpeed_form_register($atts['redirect']);
}
 
 
// FORM SUBMISSION HANDLER
add_action('init','xpeed_handle');
function xpeed_handle() {
  $success = false;
  if (isset($_REQUEST['xpeed_action'])) {
    switch ($_REQUEST['xpeed_action']) {
      case 'login':
        if (!$_POST['xpeed_username']) {
          set_xpeed_error(__('<strong>ERROR</strong>: Empty username','xpeed_login'),$_REQUEST['xpeed_form']);
        } else if (!$_POST['xpeed_password']) {
          set_xpeed_error(__('<strong>ERROR</strong>: Empty password','xpeed_login'),$_REQUEST['xpeed_form']);
        } else {
          $creds = array();
          $creds['user_login'] = $_POST['xpeed_username'];
          $creds['user_password'] = $_POST['xpeed_password'];
          //$creds['remember'] = false;
          $user = wp_signon( $creds );
          if ( is_wp_error($user) ) {
            set_xpeed_error($user->get_error_message(),$_REQUEST['xpeed_form']);
          } else {
            set_xpeed_success(__('Log in successful','xpeed_login'),$_REQUEST['xpeed_form']);
            $success = true;
          }
        }
        break;
      case 'register':
        if (!$_POST['xpeed_username']) {
          set_xpeed_error(__('<strong>ERROR</strong>: Empty username','xpeed_login'),$_REQUEST['xpeed_form']);
        } else if (!$_POST['xpeed_email']) {
          set_xpeed_error(__('<strong>ERROR</strong>: Empty email','xpeed_login'),$_REQUEST['xpeed_form']);
        } else {
          $creds = array();
          $creds['user_login'] = $_POST['xpeed_username'];
          $creds['user_email'] = $_POST['xpeed_email'];
          $creds['user_pass'] = wp_generate_password();
          $creds['role'] = get_option('default_role');
          //$creds['remember'] = false;
          $user = wp_inser_user( $creds );
          if ( is_wp_error($user) ) {
            set_xpeed_error($user->get_error_message(),$_REQUEST['xpeed_form']);
          } else {
            set_xpeed_success(__('Registration successful. Your password will be sent via email shortly.','xpeed_login'),$_REQUEST['xpeed_form']);
            wp_new_user_notification($user,$creds['user_pass']);
            $success = true;
          }
        }
        break;
    }
 
    // if redirect is set and action was successful
    if (isset($_REQUEST['redirect']) && $_REQUEST['redirect'] && $success) {
      wp_redirect($_REQUEST['redirect']);
      die();
    }      
  }
}


// ERROR HANDLE

if (!function_exists('set_xpeed_error')) {
  function set_xpeed_error($error,$id=0) {
    $_SESSION['xpeed_error_'.$id] = $error;
  }
}
// shows error message
if (!function_exists('the_xpeed_error')) {
  function the_xpeed_error($id=0) {
    echo get_xpeed_error($id);
  }
}
 
if (!function_exists('get_xpeed_error')) {
  function get_xpeed_error($id=0) {
    if ($_SESSION['xpeed_error_'.$id]) {
      $return = $_SESSION['xpeed_error_'.$id];
      unset($_SESSION['xpeed_error_'.$id]);
      return $return;
    } else {
      return false;
    }
  }
}
if (!function_exists('set_xpeed_success')) {
  function set_xpeed_success($error,$id=0) {
    $_SESSION['xpeed_success_'.$id] = $error;
  }
}
if (!function_exists('the_xpeed_success')) {
  function the_xpeed_success($id=0) {
    echo get_xpeed_success($id);
  }
}
 
if (!function_exists('get_xpeed_success')) {
  function get_xpeed_success($id=0) {
    if ($_SESSION['xpeed_success_'.$id]) {
      $return = $_SESSION['xpeed_success_'.$id];
      unset($_SESSION['xpeed_success_'.$id]);
      return $return;
    } else {
      return false;
    }
  }
}

?>