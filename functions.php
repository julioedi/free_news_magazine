<?php
/**
 * Free News And Magazine functions and definitions
 *
 * @link https://julioedi.com/themes/free_news_and_magazine/
 *
 * @package Free News And Magazine
 * @since Free News And Magazine 1.0
 */
 (function(){
   if (!defined("theme_dir")) define("theme_dir",__DIR__);
   if (!defined("theme_lang")) define("theme_lang","free_news_and_magazine");
   if (!defined("theme_uri")) define("theme_uri",get_template_directory_uri());
  $requires = [
    "core/defines",
    "core/cleartheme"
  ];
  foreach ($requires as $value) {
    require_once theme_dir . "/{$value}.php";
  }
})();
