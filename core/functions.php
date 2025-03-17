<?php

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

function getHashTag($name = ""){
  if (!is_string($name) || empty($name)) {
    return "#404";
  }
  $name = preg_replace_callback("/(\s+|\-+|_+)(\w)/i",function($txt){
    return ucfirst($txt[2]);
  },$name);
  $name = preg_replace("/[^a-zA-Z0-9]/","",$name);
  $name = ucfirst($name);
  return "#$name";
}

function clear_home_url($url = false){
  if (!is_string($url)) {
    return $url;
  }
  return preg_replace(home_regex,"/$2",$url);
}

function enqueue_general_css(array|null $array = null){
  $query = [];
  if ($array !== null) {
    foreach ($array as $value) {
      $query[] = "$value";
    }
  }
  $query = array_unique($query);
  $query = implode(",",$query);
  $query = !empty($query) || $array == null ? "?file=$query" : "";
  return '<link rel="stylesheet" href="' . clear_home_url(theme_css_uri) . "/$query\">";
}

function enqueue_font_awesome(){
  return '<link rel="stylesheet" href="' . clear_home_url(theme_fonts_uri) . '/font_awesome/load.css">';
}

function enqueue_horizontal_slider(){
  return wp_enqueue_script(theme_domain . "_horizontal_slider",clear_home_url(theme_js_uri . "/horizontal_slider.js"));
}


function renderAtts(array $atts){
  $renderAtts = '';
  if (!empty($atts)) {
    foreach ($atts as $key => $value) {
      if (is_array($value)) {
        $value = implode(" ",$value);
      }
      elseif (is_bool($value) || !$value) {
        $value = $value ? "true" : "false";
      }
      $renderAtts .= " $key=\"$value\"";
    }
  }
  return $renderAtts;
}

function compressCss(string $css =''){
  //remove comments
  $css = preg_replace("/\/+.*?\n/","",$css);
  $css = preg_replace("/(\n+|\r+|\t+)/","",$css);
  $css = preg_replace("/\/\*.*?\/\*/","",$css);

  //remove extra spaces
  $csc = preg_replace("/\s+/"," ",$css);
  $csc = preg_replace("/(\:|\;|,)\s+/","$1",$css);
  $csc = str_replace("/\s+\{/","{",$css);
  $csc = str_replace("/\;\}/","}",$css);

  return $css;
}

function get_custom_avatar_data($nickname){
  if (!file_exists(ABSPATH . "/assets/avatars/$nickname.jpg")) {
    $user = get_user_by('login',"$nickname");
    return  get_userdata($user->ID);
  }
}
