<?php
// if (($_SERVER["HTTP_SEC_FETCH_SITE"] ?? null) !== 'same-origin') {
//   header("HTTP/1.0 404 Not Found");
//   header("Location: /");
//   die();
// }

define("root",__DIR__);
header("Content-type: text/css", true);
header('Content-Disposition: inline; filename="style.css"');
require_once root . "/colors.php";
