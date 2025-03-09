<?php

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}


function clear_home_url($url = false){
  if (!is_string($url) || !is_localhost || !preg_match(home_regex,$url) ) {
    return $url;
  }
  return preg_replace("/^(http|https)\:\/\/.*?(\/|$)/","$2",$url);
}
