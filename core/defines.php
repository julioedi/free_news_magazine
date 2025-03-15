<?php
$paths = array(
 "assets" => "/assets",
 "img" =>"/assets/img",
 "css" => "/assets/css",
 "js"  => "/assets/js",
 "fonts"  => "/assets/fonts",
 "templates" => "/templates",
);

if (!defined("theme_domain")) {
  define("theme_domain", "theme_free_news");
}

if (!defined("is_localhost")) {
  define("is_localhost", preg_match("/^(localhost|(\d\.)+)/",($_SERVER["HTTP_HOST"] ?? ""), $matches, PREG_OFFSET_CAPTURE));
}
if (!defined("theme_home_url")) {
  define("theme_home_url",get_home_url("") . "/");
}

if (!defined("home_regex")) {
  $host = $_SERVER["HTTP_HOST"];
  define("home_regex","/^(http|https)\:\/\/$host\/(.*?$)/");
}
foreach ($paths as $key => $value) {
 if (!defined("theme_{$key}_dir")){
   define("theme_{$key}_dir",theme_dir . $value);
 }
 if (!defined("theme_{$key}_uri")){
   define("theme_{$key}_uri",theme_uri . $value);
 }
}
