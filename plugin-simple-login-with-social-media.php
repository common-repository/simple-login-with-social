<?php
/*
 * Plugin Name:       Simple Login with Social Media for Wordpress
 * Plugin URI:        https://github.com/GALUSARO91/simple-login-with-social-media
 * Description:       Use it to create a passwordless simple alternative for user to login to wordpress. Make user log in with just a click.
 * Version:           1.0.0
 * Requires at least: 4.8
 * Requires PHP:      5.3
 * Author:            galusaro
 * Author URI:        https://www.facebook.com/Luis-script-kiddie
 * License:           GNU General Public License v2.0 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html

  Copyright (C) 2020 Luis Gabriel Rodriguez Sandino.
  Simple Login with Social Media  is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  Simple Login with FB is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Simple Login with FB. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

if ( ! defined( 'ABSPATH' ) ) exit;

function slwsm_plugin_init(){
  require_once dirname( __FILE__ ) . '/class-simple-login-with-social-media.php';
  global $fb_login;
  $fb_login = new Simple_Login_With_Social_Media;

  }

function slwsm_write_login_link($fb_api_id,$fb_app_secret,$fb_login_page,$fb_login_page_old = null){
    $link_exist = false;
    $all_menus = wp_get_nav_menus();
    foreach($all_menus as $menu){
        $menu_items = wp_get_nav_menu_items($menu);

        foreach($menu_items as $item){

            if(isset($fb_login_page_old) && $item->title == $fb_login_page_old) {
                $link_exist = true;
                $callback_url =$GLOBALS['fb_login']->set_fb_callback($fb_api_id, $fb_app_secret,$fb_login_page);
                $menu_object_args = array(
                  'menu-item-title' => $fb_login_page,
                  'menu-item-url' => $callback_url,
                  'menu-item-status' => 'publish',
                  'menu-item-type' => 'custom'
                );
                wp_update_nav_menu_item($menu->term_id,$item->ID,$menu_object_args);
            }
        }

        if(!$link_exist){

        $callback_url =$GLOBALS['fb_login']->set_fb_callback($fb_api_id, $fb_app_secret,$fb_login_page);
                $menu_object_args = [
                  'menu-item-title' => $fb_login_page,
                  'menu-item-url' => $callback_url,
                  'menu-item-status' => 'publish',
                  'menu-item-type' => 'custom'
                ];
                wp_update_nav_menu_item($menu->term_id,0,$menu_object_args);


            }
        }

}

function slwsm_load_login_link_in_header(){

    slwsm_write_login_link(get_option('fb_api_id'),get_option('fb_app_secret'),get_option('fb_login_page'),get_option('fb_login_page'));
}


function slwsm_set_options($option_name,$old_value,$value){

    $options_set = [
        'fb_api_id' => '',
        'fb_app_secret' => '',
        'fb_login_page' =>''
      ];

  if(array_key_exists($option_name, $options_set)){

    foreach($options_set as $option => $option_value){
        if($option_name == $option){
          $options_set[$option] = strval($value);
        } else{
          $options_set[$option] = strval(get_option($option));
        }

    }
    if($options_set['fb_api_id'] !='' && $options_set['fb_app_secret'] != '' && $options_set['fb_login_page'] !='') {


    if ($option_name == 'fb_login_page'){

        slwsm_write_login_link($options_set['fb_api_id'],$options_set['fb_app_secret'],$value,$old_value);
        $origen = dirname(__FILE__).'/'.str_replace('_','-',$option_name).'.php';
        $destino = get_stylesheet_directory().'/page-'.$value.'.php';
        copy($origen,$destino);
        $old_page = get_page_by_title($old_value);

        if(!$old_page->ID){

            wp_insert_post(array('post_title' => $value,'post_content' => '', 'post_type' => 'page', 'post_status' => 'publish'),true);

          } else{

            $args = array(
              'ID' => $old_page->ID,
              'post_title' => $value,
              'post_name' => $value
            );
            wp_update_post($args);
          }
        }
      }
    }
  }

try{

  add_action( 'plugins_loaded', 'slwsm_plugin_init' );
  add_action('updated_option','slwsm_set_options',10,3);
  add_action('get_header','slwsm_load_login_link_in_header');

}



catch (Exception $e) {

  $GLOBALS['fb_login']->write_error_log($e->getMessage());
}
