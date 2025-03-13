<?php
(function(){

  class ThemStoredData{
    public $currentuser = null;
    public $users = [];
    public $posts = [];
    public $taxonomies = [];
    public function __construct(){

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
    public function get_user_picture($user_login,$id){

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
