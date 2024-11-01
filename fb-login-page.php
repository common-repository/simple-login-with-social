<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once ABSPATH . '/vendor/autoload.php';

if(!session_id()){
    session_start();
}

$app_id = strval(get_option('fb_api_id'));
$app_secret = strval(get_option('fb_app_secret'));

$fb = new \Facebook\Facebook([
  'app_id' => $app_id, // Replace {app-id} with your app id
  'app_secret' => $app_secret,
  'default_graph_version' => 'v3.2',
  'persistent_data_handler' => 'session'
  ]);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  $GLOBALS['fb_login']->write_error_log($e->getMessage());
  wp_redirect(home_url());
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  $GLOBALS['fb_login']->write_error_log($e->getMessage());
  wp_redirect(home_url());
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    //header('HTTP/1.0 401 Unauthorized');
    $GLOBALS['fb_login']->write_error_log($helper->getError());
    $GLOBALS['fb_login']->write_error_log($helper->getErrorCode());
    $GLOBALS['fb_login']->write_error_log($helper->getErrorReason());
    $GLOBALS['fb_login']->write_error_log($helper->getErrorDescription());
    wp_redirect(home_url());
    exit;

  } else {

    $GLOBALS['fb_login']->write_error_log('Bad request');
  }
  wp_redirect(home_url());
  exit;
}

// Logged in

$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId($app_id); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    $GLOBALS['fb_login']->write_error_log($e->getMessage());
    wp_redirect(home_url());
    exit;
  }

}
$response = $fb->get('/me?fields=email', $accessToken);
$current_user = $response->getGraphNOde();
$current_user_mail = $current_user['email'];
$user_found = get_user_by('email',$current_user_mail);


if(!is_wp_error( $user_found ) ){
  wp_set_current_user($user_found->ID);
  wp_clear_auth_cookie();
  wp_set_current_user ( $user_found->ID ); // Set the current user detail
  wp_set_auth_cookie  ( $user_found->ID ); // Set auth details in cookie
  wp_redirect(admin_url()); exit;
  } else {
    wp_redirect(home_url().'/wp-login.php?action=register');exit;

}

$_SESSION['fb_access_token'] = (string) $accessToken;

?>
