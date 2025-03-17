<?php


/*
 * The error_reporting() function can be disabled in php.ini. On systems where that is the case,
 * it's best to add a dummy function to the wp-config.php file, but as this call to the function
 * is run prior to wp-config.php loading, it is wrapped in a function_exists() check.
 */
if ( function_exists( 'error_reporting' ) ) {
	/*
	 * Disable error reporting.
	 *
	 * Set this to error_reporting( -1 ) for debugging.
	 */
	error_reporting( 0 );
}

// Set ABSPATH for execution.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/' );
}


define( 'WPINC', 'wp-includes' );
define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

require ABSPATH . 'wp-admin/includes/noop.php';
require ABSPATH . WPINC . '/theme.php';
require ABSPATH . WPINC . '/class-wp-theme-json-resolver.php';
require ABSPATH . WPINC . '/global-styles-and-settings.php';
require ABSPATH . WPINC . '/script-loader.php';
require ABSPATH . WPINC . '/version.php';

$protocol = $_SERVER['SERVER_PROTOCOL'];
if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3' ), true ) ) {
	$protocol = 'HTTP/1.0';
}

$load = $_GET['load'];
if ( is_array( $load ) ) {
	ksort( $load );
	$load = implode( '', $load );
}

$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $load );
$load = array_unique( explode( ',', $load ) );

if ( empty( $load ) ) {
	header( "$protocol 400 Bad Request" );
	exit;
}

$rtl            = ( isset( $_GET['dir'] ) && 'rtl' === $_GET['dir'] );
$expires_offset = 31536000; // 1 year.
$out            = '';

$wp_styles = new WP_Styles();
wp_default_styles( $wp_styles );

$etag = $wp_styles->get_etag( $load );

if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) === $etag ) {
	header( "$protocol 304 Not Modified" );
	exit;
}

foreach ( $load as $handle ) {
	if ( ! array_key_exists( $handle, $wp_styles->registered ) ) {
		continue;
	}

	$style = $wp_styles->registered[ $handle ];

	if ( empty( $style->src ) ) {
		continue;
	}

	$path = ABSPATH . $style->src;

	if ( $rtl && ! empty( $style->extra['rtl'] ) ) {
		// All default styles have fully independent RTL files.
		$path = str_replace( '.min.css', '-rtl.min.css', $path );
	}

	$content = get_file( $path ) . "\n";

	// Note: str_starts_with() is not used here, as wp-includes/compat.php is not loaded in this file.
	if ( 0 === strpos( $style->src, '/' . WPINC . '/css/' ) ) {
		$content = str_replace( '../images/', '../' . WPINC . '/images/', $content );
		$content = str_replace( '../js/tinymce/', '../' . WPINC . '/js/tinymce/', $content );
		$content = str_replace( '../fonts/', '../' . WPINC . '/fonts/', $content );
		$out    .= $content;
	} else {
		$out .= str_replace( '../images/', 'images/', $content );
	}
}
if (isset($_GET["preview"])) {
  preg_match_all("/(#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})\b|rgba?\(\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})(?:,\s*(0|1|0?\.\d+))?\s*\))/",$out,$matches);
  echo "<pre>";

  echo '<div style="display:grid;grid-template-columns:repeat(8,1fr)">';
  $temp = array_unique($matches[0]);
  // echo json_encode(array_merge([],$temp));
  // die();
  foreach ($temp as $value) {
    ?>
    <div class="item">
      <span><?php echo $value ?></span>
      <div style="width:100%;display:flex;height:80px;background:<?php echo $value ?>"></div>
    </div>
    <?php
  }
  echo "</div>";
  exit;
}
header( "Etag: $etag" );
header( 'Content-Type: text/css; charset=UTF-8' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires_offset ) . ' GMT' );
header( "Cache-Control: public, max-age=$expires_offset" );

$cards = "#1c2125";
$borders = "#2B2F32";
$backgroundColors = [
  "#f0f0f1" => "#181B1D",//body
  "#c3c4c7" => $borders ,//dotted borders
  "#fff+" => $cards,//cards
  "#f6f7f7" => $cards,//cards paragraphs
  "#15+" => "#282B2E",
  "#8c8f94" => $borders,//inputs borders,
  "#dcdcde" => $cards, // profile selected theme scheme
];


$defaultText = "#f0f0f0";
$opacityText = "#b7b7b7";
$textColors = [
  "#000+"   => $defaultText,
  "#2c3338" => $defaultText,//inputs
  "#1d2327" => $defaultText,//body text color
  "#1e+"    => $defaultText, //editor
  "#3c434a" => "#e5e5e5",//cards paragraphs
  "#2271b1" => "#40a8ff",//link
  "#135e96" => "#89c4f5",//link hover,
  "#50575e" => $opacityText, //opacity texts,
  "#646970" => $opacityText, //topnav
  "#b32d2e" => "#db8182", //delete
  "#97191a" => "#ff7e7f",// delete hover
];
foreach ($backgroundColors as $key => $value) {
  $out = preg_replace("/(background\:|background-color\:|border-color\:|\s+)$key/","$1{$value}",$out);
}

foreach ($textColors as $key => $value) {
  $out = preg_replace("/(?<![a-zA-Z\-])(color\:)$key/","$1{$value}",$out);
  $out = preg_replace("/,{$key}/",",$value",$out);
}

// $original_colors = array(
//   "#f0f0f1",// body background,
//   "#c3c4c7",// dashed borders,
//   /*--text-colors*/
//   "#3c434a",
//   "color:#2f3437",
//   "color:#1d2327",
//   /*--text-colors*/
// );
// $new_colors = array(
//   "#121212",
//   "#7a7c7f",
//   "#f0f0f0",
//   "color:#f0f0f0"
// );

// $out = str_replace($original_colors,$new_colors,$out);
$out .= ".welcome-panel-header-image g>path{fill:{$backgroundColors["#15+"]}}.welcome-panel{background-color:{$backgroundColors["#15+"]}}";
// $out .= "input[type=color],input[type=date],input[type=datetime-local],input[type=datetime],input[type=email],input[type=month],input[type=number],input[type=password],input[type=search],input[type=tel],input[type=text],input[type=time],input[type=url],input[type=week],select,textarea{background-color:{$backgroundColors["#f0f0f1"]}}";
echo $out;
exit;
