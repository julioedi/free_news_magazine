<?php

$logo = get_custom_logo();
if (empty($logo)) {
  $logo = "MEGA.news";
}
$logo = '<div class="logo">' . $logo . '</div>';
$menu = wp_nav_menu( array(
  'container' => "nav",
  'echo' => false,
	'theme_location' => "header-navbar",
) );


$wrapAtts = array(
  "id" => "main_header"
);

$wrap = apply_filters(theme_domain . "/frontend/header_wrap",'<header%2$s>%1$s</header>',$wrapAtts);
$renderWrapAtts = renderAtts($wrapAtts);


$search = '<div id="main_search">';
$search .= '<input name="s" placeholder="' . __('Search anything',theme_lang) . '" value="' . get_query_var('s','') . '">';
$search .= '<div class="full-h full-center clickable"><i class="fa-solid fa-magnifying-glass"></i></div>';
$search .= '</div>';

$account = '';
require_once theme_dir ."/templates/header_account.php";

$output = "<div class=\"mws wrap\">{$logo}{$menu}<div class=\"spacer\"></div>{$search}{$account}</div> ";

echo sprintf($wrap,$output,$renderWrapAtts);
