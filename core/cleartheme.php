<?php
(function(){

  class ThemeDefaults{
    public function __construct(){
      add_action("after_setup_theme",[$this,"initials"]);
      add_action( 'init', [$this,"clean_init"]);
      add_action("wp_head",[$this,"disable_guttemberg_scripts"],10);
      add_filter("wp_img_tag_add_auto_sizes","__return_false");
      add_filter('style_loader_src', [$this,'remove_file_version']);
      add_filter('script_loader_src', [$this,'remove_file_version']);

      //make relative paths;
      add_filter('post_link','clear_home_url');
      add_filter('clean_url','clear_home_url');

    }
    public function remove_file_version($src){
      if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) )
        $src = remove_query_arg( 'ver', $src );
      return clear_home_url($src);
    }
    public function initials(){
      $lang = theme_dir . '/languages';
    	load_child_theme_textdomain( theme_lang, $lang );
    }


    public function clean_init(){
      //remove emojis
      remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
      remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
      remove_action( 'wp_print_styles', 'print_emoji_styles' );
      remove_action( 'admin_print_styles', 'print_emoji_styles' );
      remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
      remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
      remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

      //remove wp block styles
      remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
      remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
      remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
      remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
      remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );


      remove_action('wp_head', 'rsd_link');
      remove_action('wp_head', 'wlwmanifest_link');
      remove_action( 'wp_head','rest_output_link_wp_head');
      remove_action( 'wp_head','wp_oembed_add_discovery_links');
      remove_action( 'wp_head', 'wp_generator' );
    }

    public function disable_guttemberg_scripts(){
      wp_dequeue_style( 'wp-block-library' );

      // Remove Gutenberg theme.
      wp_dequeue_style( 'wp-block-library-theme' );

      // Remove inline global CSS on the front end.
      wp_dequeue_style( 'global-styles' );

      // Remove classic-themes CSS for backwards compatibility for button blocks.
      wp_dequeue_style( 'classic-theme-styles' );

      wp_dequeue_style( 'wp-block-library' );
      wp_dequeue_style( 'wp-block-library-theme' );
      wp_dequeue_style( 'wc-block-style' ); // REMOVE WOOCOMMERCE BLOCK CSS
      wp_dequeue_style( 'global-styles' ); // REMOVE THEME.JSON
    }

  }

  new ThemeDefaults();
})();
