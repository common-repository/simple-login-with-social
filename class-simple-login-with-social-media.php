<?php
/*


*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once 'menu-layout.php'; //Menu dependences

if(!class_exists( 'Simple_Login_With_Social_Media' )){

class Simple_Login_With_Social_Media{

  function __construct(){
        $this->create_menu(); //crear menu
        register_activation_hook( __FILE__, array('class-simple-login-with-social-media','create_menu'));
    }


  function  create_menu(){
    function facebook_login_option_page() {
      add_menu_page(
          'Simple Login with Social Media',// Page title
          'Simple Login with Social Media',// Menu title
          'manage_options',//capabilities
          'simple-login-with-social-media',//menu slug
          'slwsm_login_options_page_html',//function to display info
          '',
          20
        );
      }
    add_action( 'admin_menu', 'facebook_login_option_page' );

    }

  function set_fb_callback($fb_app_id, $fb_app_secret, $fb_login_page){
    try{
    require_once ABSPATH .'vendor/autoload.php';
    if(!session_id()){
        session_start();
    }

    $fb = new \Facebook\Facebook([
      'app_id' => $fb_app_id, // Replace {app-id} with your app id
      'app_secret' => $fb_app_secret,
      'default_graph_version' => 'v3.2',
      'persistent_data_handler' => 'session'
    ]);
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email']; // Optional permissions
    $loginUrl = htmlspecialchars($helper->getLoginUrl(get_home_url().'/'.$fb_login_page.'/', $permissions));
    return $loginUrl;
    }
    catch(Exception $e){
      $this->write_error_log($e->get_message());
    }
  }

  function write_ref_log($message){

  if (is_object($message)){
    $this->write_ref_log(serialize($message));

  } elseif(is_array($message)){

      foreach($message as $item_name =>$item_value){

        $this->write_ref_log($item_name. ' : '.$item_value);

      }

   } else {

    $reflog =  fopen(dirname( __FILE__ )."/reflog.text",'a+');
    $date = date(DATE_RFC1123);
    $body = "[$date] $message\n";
    fwrite($reflog,$body);
    fclose($reflog);

   }


  }


  function write_error_log($error_message){
    $errorlog =  fopen(dirname( __FILE__ )."/errorlog.text",'a+');
    $date = date(DATE_RFC1123);
    $body = "[$date] $error_message\n";
    fwrite($errorlog,$body);
    fclose($errorlog);
  }


}
}
