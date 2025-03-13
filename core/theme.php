<?php
(function(){
  class Theme{
    public function __construct(){
      add_action("after_theme_setup",[$this,"init"]);
      add_action("show_password_fields",[$this,"add_custom_user_profile"],10,2);
      add_action("admin_head",[$this,'admin_head']);
    }

    public function admin_head(){
      global $pagenow;
      echo enqueue_font_awesome();
      $enqueue = array();
      if ($pagenow == "profile.php") {
        $enqueue[] = 'admin_profile';
      }
      echo enqueue_general_css($enqueue);
    }

    public function register_menus(){
      register_nav_menus(
        array(
          'header-navbar' => __( 'Header Menu' ),
         )
       );
    }
    public function add_custom_user_profile($bool, $user){
      global $storedData;
      // echo "<pre> hola perro" . print_r($user,true) . "</pre>";
      ?>
      </table>
        	<div id="user-custom-profile-picture">
            <h2><?php echo __( 'Custom Profile Image', theme_lang ); ?></h2>
            <div class="profile_picture">
              <div class="container">
                  <?php
                    $custom = $storedData->got_custom_picture($user->data->user_login);
                    if (!$custom) {
                      echo '<span class="dashicons dashicons-plus-alt2"></span>';
                    }else{
                      echo $storedData->get_user_picture($user->data->user_login,$user->ID);
                    }
                   ?>
              </div>
            </div>
          </div>
      <?php
      return $bool;
    }

    public function init(){
      $this->register_menus();
    }
  }
  new Theme();
})();
