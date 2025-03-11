<?php
(function(){
  class Roles{
    public $roles = null;
    public $rolesClass = null;
    public $editCaps =  array(
        "edit_posts" => true,
        "publish_posts" => true,
        "delete_posts" => true,
        "upload_files" => true,
      );
    public function __construct(){
      if ($this->rolesClass == null) {
        $this->rolesClass = wp_roles();
      }
      add_action("after_setup_theme",[$this,'inviteRole']);
    }
    public function inviteRole(){
      //user without access to admin menu
      add_role(
        theme_domain . "_contributor",
        __("Reader",theme_lang),
        $this->editCaps,
      );
      do_action(theme_domain . "/contributor_added");

      $caps = [
        "bookmarks",
        "newsletter",
        "follow",
      ];
      $roles = $this->core_roles();
      foreach ($roles as $key) {
        $el = $this->rolesClass->get_role($key);
        foreach ($caps as $cap) {
          $filter = apply_filters(theme_domain . "/users/capability/$cap",true,$key);
          if ($filter) {
            $el->add_cap( $cap, true );
          }
        }
      }

    }

    public function core_roles(array|null $roles = null){
      global $wp_roles;
      $all_roles = $wp_roles->roles;
      $editable_roles = apply_filters('editable_roles', $all_roles);
      return array_keys($editable_roles);
    }
  }
  new Roles();

})();
