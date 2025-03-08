<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
do_action( 'wp_footer' );
do_action( 'wp_body_close' );
echo "</body></html>";
