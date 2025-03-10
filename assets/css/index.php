<?php
// if (($_SERVER["HTTP_SEC_FETCH_SITE"] ?? null) !== 'same-origin') {
//   header("HTTP/1.0 404 Not Found");
//   die();
// }

define("root",__DIR__);
$customColors = file_exists(root . "/custom_colors.json") ? file_get_contents(root . "/custom_colors.json") : '{}';
$customColors = json_decode(
  $customColors,
  true
);

$codes = array(
  "primary" => "blue",
  "secondary" => "purple",
  "success" => "green",
  "danger" => "red",
);

$customCodes = file_exists(root . "/selections.json") ? file_get_contents(root . "/selections.json") : '{}';
$customCodes = json_decode(
  $customCodes,
  true
);
$customCodes = array_merge(
  $customCodes,
  $codes,
);

$colors = json_decode(
  file_get_contents(root . "/default_colors.json"),
  true
);
foreach ($customColors as $key => $value) {
  if (isset($colors[$key])) {
    $colors[$key] = array_merge($colors[$key],$value);
  }else{
    $colors[$key] = $value;
  }
}
header("Content-type: text/css", true);
echo "/*--palette-colors--*/\n";
$codesList = [];
echo ":root{";
foreach ($colors as $code => $value) {
  if (isset($value["color"])) {
    $codesList[$code] = $value["color"];
    echo "--{$code}:{$value["color"]};";
    unset($value["color"]);
  }
  foreach ($value as $dens => $hex) {
    $codesList["$code-$dens"] = $hex;
    echo "--$code-$dens:{$hex};";
  }
}
foreach ($customCodes as $key => $value) {
  if (isset($codesList[$value])) {
    echo "--{$key}:{$codesList[$value]};";
  }else{
    echo "--{$key}:{$value};";
  }
}
echo "}";
