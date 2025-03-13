<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
echo "<!DOCTYPE html><html " . get_language_attributes("html") . '><meta http-equiv="X-UA-Compatible" content="IE=edge" /><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0">';
do_action("before_frontend_head");
do_action( 'wp_head' );
echo enqueue_font_awesome();
echo enqueue_general_css();
do_action("after_frontend_head");

$bodyClass = esc_attr( implode( ' ', get_body_class() ) );
$bodyClass = (string) apply_filters("theme/frontend/body_class",$bodyClass);
$bodyClass = !empty($bodyClass) ? " class=\"$bodyClass\"": "";

$bodyAtts = (array) apply_filters("theme/frontend/body_atts",array(
  //attname => value,
));

//include custom body atts to the theme
if (!empty($bodyAtts)) {
  $pre = [];
  foreach ($bodyAtts as $key => $value) {
    //prevents class or jsons
    if (!is_bool($value) && !is_string($value) && !is_numeric($value) && $value !== null) continue;
    if (!$value) {
      $value ="false";
    }else{
      $value = is_bool($value) ? "true" : $value;
    }
    $pre[] = (string) "$key=\"$value\"";
  }
  $bodyAtts = " " . esc_attr( implode( ' ', $pre ) );
}else{
  $bodyAtts = "";
}
echo "</head>";
echo "<body{$bodyClass}{$bodyAtts}>";
do_action( 'frontend/header/before' );
require_once(theme_dir . "/templates/header.php");
do_action( 'frontend/header/after' );
echo "<main>";
do_action( 'wp_body_open' );
global $storedData;
print_r($storedData);
echo "waaa";
