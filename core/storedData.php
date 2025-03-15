<?php

foreach ([
  "categories"
] as $value) {
  require_once theme_dir . "/core/traits/{$value}.php";
}
(function(){
  class ThemStoredData{
    use Categories;
    public $currentuser = null;
    public $users = [];
    public $posts = [];
    public $taxonomies = [];
    public $accountLinks = [];
    public $request = null;
    public $uri = null;
    public $home_url = null;
    public $options = [];
    public $theme_dark = null;
    public $def_img_uri = "";
    public $def_image_alt = "";
    public $paths = array();
    public $def_image_sizes = array(
      "large" => [1280,720],
      'medium_large' =>[720,405],
      'medium' => [480,270],
      "thumbnail" => [266,150]
    );



    public function __construct(){
      $this->define_paths();
      $this->get_request();
      $this->define_home();
      $this->default_image_data();
      $this->accountLinks = array(
        'edit' => array(
          'link' => "/account/edit/",
          'title' => __("Edit",theme_lang),
        ),
        // 'login' => array(
        //   'link' => get_home_url(null,"/account/login/"),
        //   'title' => __("Log in",theme_lang),
        // ),
        'post' => array(
          'link' => "/account/add_post/",
          'title' => __("New post",theme_lang),
        ),
        'profile' => array(
          'link' => "/account/profile/",
          'title' => __("My Profile",theme_lang),
        ),
        'loggout' => array(
          'link' => "/account/loggout/",
          'title' => __("Log out",theme_lang),
        ),
      );
    }
    public function define_paths(){
      $reg = "/\\\\/i";
      $path = preg_replace($reg,"/",mb_strtolower(ABSPATH));
      $dir = preg_replace($reg,"/",mb_strtolower(theme_assets_dir));
      $this->paths["assets"] =  str_replace($path,"",$dir);
      $uploads = preg_replace($reg,"/",mb_strtolower(wp_upload_dir()["basedir"]));

      $this->paths["uploads"] = str_replace($path,"",$uploads);
    }

    public function default_image_data(){
      $this->theme_dark = get_option(theme_domain. "/default_image/dark",null);
      $this->def_img_uri = $this->theme_dark ? "/empty_dark.jpg" : "/empty.jpg";
      $this->def_img_uri = theme_img_uri . $this->def_img_uri;

      $this->def_image_alt = get_option(theme_domain. "/default_image/alt",__("Image not found",theme_lang));
    }

    public function get_option($option, $default_value = false){
      if (!isset($this->options[$option])) {
        $this->options[$option] = get_option( $option, $default_value);
      }
      return $this->options[$option];
    }
    public function define_home(){
      $this->home_url = get_home_url();

    }
    public function get_request(){
      if ($this->request) {
        return $this->request;
      }
      $this->uri = sprintf("%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'],
        $_SERVER['REQUEST_URI']
      );
      $reg = "/^htt.*?\/+(.*?)(\?.*?$|$)/";
      $baseuri = mb_strtolower(preg_replace($reg,"//$1",$this->uri));
      $home = preg_replace($reg,"//$1",get_home_url());
      $home = mb_strtolower(preg_replace("/\/$/","",$home));
      $this->request = str_replace($home,"",$baseuri);
      if (substr($this->request,-1) != "/") {
        $this->request = $this->request . "/";
      }
      return $this->request;

    }


    public function render_accountLinks(array $classes = []){
      $classes = implode(" ",$classes);
      $uri = $_SERVER['REQUEST_URI'];
      $classes = empty($classes) ? "" : "class=\"$classes\"";
      $output = "<ul id=\"account_links\"$classes>";
      foreach ($this->accountLinks as $key => $value) {
        $link = $value['link'];
        $current = $link == $this->request;
        if (!$current) {
          $link = "<a href=\"{$value['link']}\" alt=\"{$value['title']}\">{$value['title']}</a>";
        }else{
          $link = "<span>{$value['title']}</span>";
        }
        $active = $current ? ' class="active"' : "";
        $output .= "<li id=\"account_links_$key\"$active>$link</li>";
      }
      return $output;
    }
    public function get_current_user(){
      if (!is_user_logged_in()) {
        return null;
      }
      if ($this->currentuser) {
        return $this->currentuser;
      }
      $user = wp_get_current_user();
      if ($user) {
        $this->currentuser = $user;
        $custom = $this->got_custom_picture($user->data->user_login);
        $this->currentuser->profile_picture = $custom ? $this->get_user_picture($user->data->user_login,$user->ID) : null;
      }
      return $this->currentuser;

    }
    public function got_custom_picture($user_login){
      $path = "/assets/users/$user_login.jpg";
      $dir = ABSPATH . $path;;
      return file_exists($dir) ?  $path : null;
    }
    public function get_user_picture($user_login,$id, $size = null){
      $path = $this->got_custom_picture($user_login);
      if ($path) {
        list($width, $heigth) = getimagesize(ABSPATH . $path);
        return array(
    			'size'           => $width,
    			'height'         => $heigth,
    			'width'          => $width,
    			'default'        => 'mystery',
    			'force_default'  => false,
    			'rating'         => null,
    			'scheme'         => null,
    			'processed_args' => null, // If used, should be a reference.
    			'extra_attr'     => '',
          'is_custom_pic'  => true,
          'url' => get_home_url(null,$path),
    		);
      }
      return get_avatar_data($id);
    }

    public function get_render_userPicture($user_login,$id,$class = ""){
      $data = $this->get_user_picture($user_login,$id);
      if (!$data) {
        return "";
      }
      extract($data);
      $class = !empty($class) ? $class : "custom_profile_image";
      return sprintf('<img class="%s" src="%s" width="%s" height="%s">',$class,$url,$width,$height);
    }

    public function get_user(int|string $id){
      if (!is_numeric($id)) {
        return false;
      }
      if (isset($this->users["user_$id"])) {
        return $this->users["user_$id"];
      }
      $data = get_userdata($id);
      if ($data) {
        $data = (array) $data;
        $data["profile_picture"] = $this->get_user_picture($data["user_login"],$id);
      }
    }
  }
  global $storedData;
  $storedData = new ThemStoredData();
})();
