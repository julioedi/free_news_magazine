<?php
// if (($_SERVER["HTTP_SEC_FETCH_SITE"] ?? null) !== 'same-origin') {
//   header("HTTP/1.0 404 Not Found");
//   header("Location: /404");
//   die();
// }

define("root",__DIR__);
header("Content-type: text/css", true);
header('Content-Disposition: inline; filename="style.css"');
echo ":root{";
require_once root . "/colors.php";
// echo "\n/*--spaces--*/\n";
for ($i=0; $i < 20 ; $i++) {
  $size = $i * 4;
  echo "--sp-${i}:{$size}px;";
}
echo "}";

function compressCss(string $css =''){
  //remove comments
  $css = preg_replace("/\/\/+.*?\n/","",$css);
  $css = preg_replace("/(\n+|\r+|\t+)/","",$css);
  $css = preg_replace("/\/\*(.*?)\*\//","",$css);
  //
  // //remove extra spaces
  $ch = "(\}|\;|\:|\{)";
  $css = preg_replace("/\s+$ch/","$1",$css);
  $css = preg_replace("/$ch\s+/","$1",$css);
  $css = preg_replace("/;}/","}",$css);

  return $css;
}
function get_file(string $value = ""){
  $path = root ."/$value.css";
  if (file_exists($path)) {
    return file_get_contents($path);
  }
  return null;
}

$files_list = [];
$file = $_GET["file"] ?? "desktop";
if (!is_array($file)) {
  $file = [$file];
}
$importReg = '/@import\s+[\"|\'](.*?)\.css[\"|\'].*?(;\n|;\s+\n|\n|)/';

$imported = [];
foreach ($file as $value) {
  $css = get_file($value);
  if (!$css) continue;
  preg_match_all($importReg,$css,$item);
  if (!empty($item[1] ?? null)) {
    foreach ($item[1] as $key => $name) {
      //avoid import multiple times same css file;
      if (isset($imported[$name])) continue;
      $imported[$name] = true;

      $tmp = get_file($name);
      if (!$tmp) continue;
      $css = str_replace($item[0][$key],$tmp,$css);
    }
    echo compressCss($css);
  }
}
