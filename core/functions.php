<?php

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
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
      $query[] = "file[]=$value";
    }
  }
  $query = implode("&",$query);
  $query = !empty($query) || $array == null ? "?$query" : "?file[]=";
  return '<link rel="stylesheet" href="' . clear_home_url(theme_css_uri) . "/$query\">";
}

function enqueue_font_awesome(){
  return '<link rel="stylesheet" href="' . clear_home_url(theme_fonts_uri) . '/font_awesome/load.css">';
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
// sd
/*
function updatefront(string $content = ""){
  $content = preg_replace("/(\r|\n|\t)/","",$content);
  // $content = preg_replace("/\s+/"," ",$content);
  // curl_close($ch);
  preg_match_all("/\@font.*?\{(?P<code>.*?)\}/",$content,$matches);
  if (empty($matches[0] ?? null)) {
    return;
  }

  foreach ($matches["code"] as $value) {
    $value = trim($value);
    preg_match_all("/(?<key>\w+\-\w+|\w+)\:(?<value>.*?);/",$value,$css);
    $pre = array();
    foreach ($css["key"] as $key => $value) {
      $pre[$value] = trim($css["value"][$key]);
    }
    $list[] = $pre;
  }
  foreach ($list as  $key => $value) {
    if (!isset($value["src"])) continue;
    $src = $value["src"];
    $uri = preg_replace("/^.*?\((.*?)\).*?$/","$1",$value["src"]);
    $prefolder = str_replace(["'","\""],"",$value["font-family"]);
    $folder = mb_strtolower($prefolder);
    $path = theme_fonts_dir . "\\$folder";
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }
    $name = basename($uri);
    if (!file_exists("$path/$name")) {
      $ch = curl_init($uri);
      $fp = fopen("$path/$name", 'wb');
      curl_setopt($ch, CURLOPT_FILE, $fp);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_exec($ch);
      curl_close($ch);
      fclose($fp);
    }
    // $value["path"] = $path;
    // $value["name"] = $name;
    $list[$key]["src"] = preg_replace("/^.*?\)/","local('$prefolder'), url(./$name)",$src);
  }
  $fontlist = [];
  foreach ($list as $key => $font) {
    $output = "@font-face{";
    $tmp = [];
    $prefolder = mb_strtolower(str_replace(["'","\""],"",$font["font-family"]));
    if (!isset($fontlist[$prefolder])) {
      $fontlist[$prefolder] = "";
    }
    foreach ($font as $key => $value) {
      $tmp[] = "$key:$value";
    }
    $output .= implode(";",$tmp);
    $output .= "}";
    $fontlist[$prefolder] .= $output;
  }

  foreach ($fontlist as $folder => $value) {
    $path = theme_fonts_dir . "\\$folder\load.css";
    if (!file_exists("$path")) {
      $fp = fopen($path, 'wb');
      fwrite($fp, $value);
      fclose($fp);
    }
  }


  return $fontlist;
}
require_once theme_dir . "/core/custom_functions/upload_from_url.php";
*/
