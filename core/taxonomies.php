<?php

(function(){
  class Taxonomies{
    public $column = "_thumbnail_id";
    public $taxonomies = null;
    public $taxonomy_table_col_id = "cover_image_column";
    public $featured_media_taxonomies = [
      "category",
      "post_tag"
    ];
    public function __construct(){
      add_action('init',[$this,'taxonomies_featured_image']);
      add_action("admin_head",[$this,"enqueue_admin"]);

      $tax = apply_filters(theme_domain . "/taxonomies/featured/edit",$this->featured_media_taxonomies);
      foreach ($tax as $value) {
        //edit add cover image to taxonomies
        add_action( "{$value}_edit_form_fields", [$this,"taxonomies_include_cover_image"] );
        add_action( "{$value}_term_new_form_tag", [$this,"taxonomies_include_cover_image"] );

        add_action( "manage_edit-{$value}_columns", [$this,"taxonomies_table_head"] );
        add_action( "manage_{$value}_custom_column", [$this,"taxonomies_table_column"],10,3);
      }

      add_action('create_term', [$this,'save_thumbnail_id']);
      add_action('edited_term', [$this,'save_thumbnail_id']);
    }

    public function taxonomies_table_head($columns){
      foreach ($columns as $key => $value) {
        $new[$key] = $value;
        if ($key == "cb" && !isset($columns[$this->taxonomy_table_col_id])) {
          $new[$this->taxonomy_table_col_id] = "&nbsp;";
        }
      }
      return $new;
    }

    public function taxonomies_table_column($content, $column_name, $term_id){
      if ($column_name === $this->taxonomy_table_col_id){
        $term = (array) get_term($term_id);
        $content = wp_get_attachment_image($term["_thumbnail_id"] ?? "0");
      }
      return $content;
    }

    public function enqueue_admin(){
      global $pagenow;
      if (in_array($pagenow,["term.php","edit-tags.php"])) {
        enqueue_font_awesome();
        wp_enqueue_media();
        wp_enqueue_style(theme_domain ."-tax_edit",theme_css_uri . "/edit_taxonomy.css");
        wp_enqueue_script(theme_domain ."-tax_edit",theme_js_uri . "/edit_taxonomy.js");
      }
    }

    public function save_thumbnail_id($term_id){
      $_thumbnail_id = sanitize_text_field($_POST['_thumbnail_id'] ?? "0");
      if (!is_numeric($_thumbnail_id)) {
        return;
      }
      $is_image = wp_get_attachment_metadata($_thumbnail_id);
      $_thumbnail_id = (int) $_thumbnail_id;
      if (!$is_image) {
        $_thumbnail_id = "0";
      }
      global $wpdb;
      $wpdb->update(
            $wpdb->term_taxonomy,
            array(
                '_thumbnail_id' => $_thumbnail_id,
            ),
            array('term_id' => $term_id),
            array("%d"),
            array("%d"),
      );
      return $term_id;
    }


    public function taxonomies_include_cover_image($tag){
      global $pagenow;
      $is_new = $pagenow == "edit-tags.php";
      if ($is_new) {

        //prevent open form tag;
        echo ">";
      }
      $list = (array) $tag;
      $thumnail_id = $list["_thumbnail_id"] ?? "0";
      $is_image = wp_get_attachment_url($thumnail_id);
      $deletebtn = '<div class="delete_cover"><div class="icon_button"><i class="fa-solid fa-trash"></i></div></div>';
      if (!empty($is_image)) {
        $is_image = sprintf('<img src="%s" data-id="%s">'. $deletebtn,$is_image,$thumnail_id);
      }
      $preview = sprintf('<div id="preview_cover">%s</div>',$is_image);
      ?>
        <div id="cover_image" class="form-field term-thumbnail_id-wrap <?php echo $is_new ? "new_tag" : "edit_tag" ?>">
          <div id="cover_image_input_wrap">
              <input id="thumbnail_id" type="text" name="_thumbnail_id" value="<?php echo $list["_thumbnail_id"] ?? "0"  ?>">
          </div>
          <?php echo $preview ?>
          <div id="cover_no_image">
              <div class="theme_btn"><?php _e("Select featured image",theme_lang) ?></div>
          </div>
        </div>
      <?php

      if ($is_new) {
        //prevent open form tag;
        echo "<div></div";
      }
      return $tag;
    }

    /**
    * add new  thumbnail code to taxonomies
    */
    public function taxonomies_featured_image(){
      global $wpdb;

      $table_name = $wpdb->prefix . 'term_taxonomy';

      // Check if the column doesn't exist
      $column_exists = $wpdb->get_results(
        $wpdb->prepare(
           "SHOW COLUMNS FROM $table_name LIKE %s",
           $this->column
        )
      );

      // Add the column if it doesn't exist
      if (empty($column_exists)) {
        $wpdb->query(
            "ALTER TABLE $table_name ADD {$this->column} INT(11) DEFAULT 0"
        );
      }

    }
  }
  global $customTax;
  $customTax = new Taxonomies();
})();
