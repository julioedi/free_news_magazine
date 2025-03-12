<?php

/**
 * Original url: "https://gist.github.com/RadGH/966f8c756c5e142a5f489e86e751eacb"
 * This function uploads a file from a URL to the media library, designed to be placed in your own theme or plugin.
 * Metadata will be generated and images will generate thumbnails automatically.
 *
 * HOW TO USE:
 *    1. Add the function below to your theme or plugin
 *    2. Call the function and provide the URL to an image or other file.
 *    3. If successful, the attachment ID will be returned.
 *
 * BASIC USAGE:
 *    $attachment_id = rs_upload_from_url( "https://www.gstatic.com/webp/gallery/1.jpg" );
 *    if ( $attachment_id ) echo wp_get_attachment_image( $attachment_id, 'large' );
 *
 * DOWNLOAD AS A PLUGIN (optional):
 *    Below is a link to a separate plugin you can upload to your site to help get started.
 *    It is a fully-functional example and demonstrates upload both JPG and WEBP image by using a "secret" url.
 *    It also provides an easy way to delete the test images that were created.
 *    @see https://gist.github.com/RadGH/be30af96617b13e7848a4626ef179bbd
 *
 * To upload from a *LOCAL FILE PATH* instead of a URL:
 * @see: https://gist.github.com/RadGH/3b544c827193927d1772
 */


/**
 * Upload a file to the media library using a URL.
 *
 * @version 1.3
 * @author  Radley Sustaire
 * @see     https://gist.github.com/RadGH/966f8c756c5e142a5f489e86e751eacb
 *
 * @param string $url           URL to be uploaded
 * @param null|string $title    Override the default post_title
 * @param null|string $content  Override the default post_content (Added in 1.3)
 * @param null|string $alt      Override the default alt text (Added in 1.3)
 *
 * @return int|false
 */
function rs_upload_from_url( $url, $title = null, $content = null, $alt = null ) {
	require_once( ABSPATH . "/wp-load.php");
	require_once( ABSPATH . "/wp-admin/includes/image.php");
	require_once( ABSPATH . "/wp-admin/includes/file.php");
	require_once( ABSPATH . "/wp-admin/includes/media.php");

	// Download url to a temp file
	$tmp = download_url( $url );
	if ( is_wp_error( $tmp ) ) return false;

	// Get the filename and extension ("photo.png" => "photo", "png")
	$filename = pathinfo($url, PATHINFO_FILENAME);
	$extension = pathinfo($url, PATHINFO_EXTENSION);

	// An extension is required or else WordPress will reject the upload
	if ( ! $extension ) {
		// Look up mime type, example: "/photo.png" -> "image/png"
		$mime = mime_content_type( $tmp );
		$mime = is_string($mime) ? sanitize_mime_type( $mime ) : false;

		// Only allow certain mime types because mime types do not always end in a valid extension (see the .doc example below)
		$mime_extensions = array(
			// mime_type         => extension (no period)
			'text/plain'         => 'txt',
			'text/csv'           => 'csv',
			'application/msword' => 'doc',
			'image/jpg'          => 'jpg',
			'image/jpeg'         => 'jpeg',
			'image/gif'          => 'gif',
			'image/png'          => 'png',
			'video/mp4'          => 'mp4',
		);

		if ( isset( $mime_extensions[$mime] ) ) {
			// Use the mapped extension
			$extension = $mime_extensions[$mime];
		}else{
			// Could not identify extension. Clear temp file and abort.
			wp_delete_file($tmp);
			return false;
		}
	}

	// Upload by "sideloading": "the same way as an uploaded file is handled by media_handle_upload"
	$args = array(
		'name' => "$filename.$extension",
		'tmp_name' => $tmp,
	);

	// Post data to override the post title, content, and alt text
	$post_data = array();
	if ( $title )   $post_data['post_title'] = $title;
	if ( $content ) $post_data['post_content'] = $content;

	// Do the upload
	$attachment_id = media_handle_sideload( $args, 0, null, $post_data );

	// Clear temp file
	wp_delete_file($tmp);

	// Error uploading
	if ( is_wp_error($attachment_id) ) return false;

	// Save alt text as post meta if provided
	if ( $alt ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
	}

	// Success, return attachment ID
	return (int) $attachment_id;
}
