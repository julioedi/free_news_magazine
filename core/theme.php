<?php
(function(){
  class Theme{
    public function __construct(){
      add_action("after_theme_setup",[$this,"init"]);
      add_action("show_password_fields",[$this,"add_custom_user_profile"],10,2);
      add_action("admin_head",[$this,'admin_head']);
      add_action('request', [$this,"custom_search_link"]);
      add_filter("wp_get_attachment_image",[$this,"empty_attachment"],10,3);
      add_filter("post_thumbnail_html",[$this,"empty_thumbnail"],10,4);
    }
    public function def_image($size = ""){
      global $storedData;
      $size = $storedData->def_image_sizes[$size] ?? null;
      $size = $size ? $size : [1920,1080];
      $html = sprintf(
        '<img src="%s" width="%s" height="%s" alt="%s">',
        $storedData->def_img_uri,
        $size[0],
        $size[1],
        $storedData->def_image_alt
      );
      return $html;
    }

    public function empty_thumbnail($html, $post_id, $thumnail_id,$size){
      if (empty($html)) {
        $html = $this->def_image($size);
      }
      return $html;
    }

    public function empty_attachment($html, $attachment_id, $size){
      if (empty($html)) {
        $html = $this->def_image($size);
      }
      return $html;
    }

    public function custom_search_link($query_vars){
      global $storedData;
      if (isset($storedData->request)) {
        $search = $storedData->get_option("search_path","search");
        if (preg_match("/^\/$search\/(.*?)(\/|$)/", $storedData->request, $matches)) {
            $query_vars['s'] = sanitize_text_field(urldecode($matches[1]));
        }
      }
      return $query_vars;
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
      add_theme_support( 'title-tag' );
    }
  }
  new Theme();
})();
