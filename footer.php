<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
echo "</main>";
do_action( 'frontend/footer/before' );
require_once(theme_dir . "/templates/footer.php");
do_action( 'frontend/footer/after' );
do_action( 'wp_footer' );
do_action( 'wp_body_close' );
echo "</body></html>";
