<?php

if ( ! defined( 'ABSPATH' ) ) exit;
  function slwsm_register_settings (){

    register_setting('fblogin', 'fb_api_id',['type' => 'string','default' => '']);
    register_setting('fblogin', 'fb_app_secret', ['type' => 'string','default' => '']);
    register_setting('fblogin', 'fb_login_page', ['type' => 'string','default' => '']);

    add_settings_section(
      'facebook_login_section', // identificador de la seccion a la que pertenece
      'Facebook App Login info', //informacion que aparecera en el website
      'slwsm_login_sections_cb', // funcion para los encabezados
      'fblogin'); //nombre de la pagina que se mostrara en el menu (slug)

    add_settings_field(
      'fb_api_id', //identificador del campo agregardo
      'Identificador de la API', //nombre del campo que aparecera en el website
      'slwsm_get_input_text_field', //funcion que se ejecutara
      'fblogin', //identificador de ajustes registrados en el plugin
      'facebook_login_section', // identificador de la seccion a la que pertenece
      [
        'label_for' => 'fb_api_id',
        'input_type' => 'text'

      ]);
      add_settings_field(
        'fb_app_secret', //identificador del campo agregardo
        'Token de acceso', //nombre del campo que aparecera en el website
        'slwsm_get_input_text_field', //funcion que se ejecutara
        'fblogin', //identificador de ajustes registrados en el plugin
        'facebook_login_section', // identificador de la seccion a la que pertenece
        [
          'label_for' => 'fb_app_secret',
          'input_type' => 'password'

        ]);

        add_settings_field(
          'fb_login_page', //identificador del campo agregardo
          'Pagina de inicio de sesion', //nombre del campo que aparecera en el website
          'slwsm_get_input_text_field', //funcion que se ejecutara
          'fblogin', //identificador de ajustes registrados en el plugin
          'facebook_login_section', // identificador de la seccion a la que pertenece
          [
            'label_for' => 'fb_login_page',
            'input_type' => 'text'

          ]);

  }

  add_action('admin_init', 'slwsm_register_settings');



  function slwsm_get_input_text_field ($args){
      ?>
        <?php
          $option = get_option($args['label_for']);
         ?>
      <input type= <?php
      echo esc_attr($args['input_type'])
      ?> name= <?php
      echo esc_attr($args['label_for'])
      ?> value=
      <?php echo isset($option)? $option : ''
      ?> >

      <?php

    }


  function slwsm_login_sections_cb (){
    echo '<p><i>
        Please fill up fields below with required info.
     </i></p>';

  }

  function slwsm_login_dependences_cb (){
    echo '<p><i>
        If you are able to install the dependences on you own, you can leave this unchecked.
     </i></p>';
  }

?>
<?php
  function slwsm_login_options_page_html() {
      ?>
      <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
          <?php

          settings_fields('fblogin');

          do_settings_sections('fblogin');

          submit_button('Save Settings');
          ?>
        </form>
      </div>
      <?php
  }

?>
