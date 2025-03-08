<?php
$paths = array(
 "assets" => "/assets",
 "img" =>"/assets/img",
 "css" => "/assets/css",
 "js"  => "/assets/js",
 "templates" => "/templates",
);
foreach ($paths as $key => $value) {
 if (!defined("theme_${key}_dir")){
   define("theme_${value}_dir",theme_dir . $value);
 }
 if (!defined("theme_${key}_uri")){
   define("theme_${value}_uri",theme_uri . $value);
 }
}
