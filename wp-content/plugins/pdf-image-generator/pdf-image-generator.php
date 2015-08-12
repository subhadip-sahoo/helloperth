<?php
/*
* plugin name: PDF Image Generator
* plugin URI: http://web.contempo.jp/weblog/tips/p1522
* description: Generate a thumbnail of a PDF document by using ImageMagick. Allow inserting PDF link with a image into the editor.
* author: Mizuho Ogino 
* author URI: http://web.contempo.jp
* Version: 1.1.5
* license: http://www.gnu.org/licenses/gpl.html GPL v2 or later
*/

function pigen_activate() { // Check the server whether or not it has imageMagick enabled.

	if (function_exists('exec')) {
		exec("convert -version", $out, $ver); //Try to get ImageMagick "convert" program version number.
		if ( $ver === 0 ) {
			exec("convert -list delegate | grep -Ei '(PDF|PS|EPS)'", $out, $lis);
			if ( $lis === 0 ) {
				update_option('_pigen_verify_imagick', 'imageMagick');
			} else {
				echo 'Sorry. Ghostscript is not installed.';
				exit();
			}
		}
	}
	if ( !get_option( '_pigen_verify_imagick' ) ){
		if( extension_loaded('imagick') ) {
			update_option('_pigen_verify_imagick', 'imagick');
		} else {
			echo 'Sorry. The plugin requires ImageMagick and GhostScript.';
			exit();
		}
	}
	// $file = path_join( dirname(__FILE__), "pigen_test.pdf" );
	// $file_url = pigen_generate( $file );
	// if( !file_exists( $file_url ) ){
	// 	echo 'Sorry. Ghostscript may be not installed. Please check it and try again.';
	// 	exit();
	// }
}
register_activation_hook( __FILE__, 'pigen_activate' );


function pigen_generate( $file ){ // Generate thumbnail from PDF
	$file_basename = str_replace( '.', '-', basename($file) );
	$file_url = str_replace( basename($file), $file_basename.'.jpg', $file );
	$opt = get_option( '_pigen_verify_imagick' );
	if ( $opt == 'imagick' ) {
		$im = new imagick();
		$im->readimage($file.'[0]');
		$im->setImageBackgroundColor('white');
		$im = $im->flattenImages();
		$im->setImageFormat('jpg'); 
		$im->writeImage( $file_url ); 
		$im->clear();
		$im->destroy();
	} else {
		exec("convert {$file}[0] -density 72 -quality 90 -background white -flatten {$file_url}"); // converte files to jpg
	}
	return $file_url;
}


function pigen_attachment( $attachment_id ){ // Generate thumbnail from PDF
	if( get_post_mime_type( $attachment_id ) === 'application/pdf' ){
		$file = get_attached_file( $attachment_id );
		$file_url = pigen_generate( $file );
		if( file_exists( $file_url ) ){
			$file_title = esc_attr( get_the_title( $attachment_id ) );
			$attachment = get_post( $attachment_id );
			$thumbnail = array(
				'post_type' => 'attachment',
				'post_mime_type' => 'image/jpeg',
				'post_title' => $file_title,
				'post_excerpt' => $attachment->post_excerpt,
				'post_content' => $attachment->post_content,
				'post_parent' => $attachment_id
			);
			$thumbnail_id = wp_insert_attachment( $thumbnail, $file_url );
			update_post_meta( $thumbnail_id, '_wp_attachment_image_alt', 'thumbnail of '.$file_title );
			$thumbnail_metadata = wp_generate_attachment_metadata( $thumbnail_id, $file_url );
			wp_update_attachment_metadata( $thumbnail_id, $thumbnail_metadata );
			update_post_meta( $attachment_id, '_thumbnail_id', $thumbnail_id );
		}
	}
	return $attachment_id;
}
add_filter( 'add_attachment', 'pigen_attachment', 10, 2 );


function pigen_insert( $html, $send_id, $attachment ) { // Insert thumbnail instead of PDF
	if( get_post_mime_type ($attachment['id']) === 'application/pdf' ){
		$thumbnail_id = get_post_meta( $attachment['id'], '_thumbnail_id', true );
		if( $thumbnail_id ){
			$attach_title = $attachment['post_title'];
			$attach_caption = $attachment['post_excerpt'];
			$attach_desc = $attachment['post_content'];
			$thumbnail = wp_get_attachment_image_src ( $thumbnail_id, 'medium' );
			$html = '<a class="link-to-pdf" href="' . $attachment['url'] . '" title="'.$attach_title.'" target="_blank"><img class="size-medium wp-image-'.$thumbnail_id.'" src="'. $thumbnail[0] .'" alt="'.get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ).'" width="'. $thumbnail[1] .'" height="'. $thumbnail[2] .'"/></a>';
			if ( $attach_caption ){
				$thumb_data=array();
				$thumb_data['ID'] = $thumbnail_id;
				$thumb_data['post_excerpt'] = $attach_caption;
				$thumb_data['post_content'] = $attach_desc;
				wp_update_post( $thumb_data );
				$html = '[caption id="attachment_'.$attachment['id'].'" align="alignnone" width="'.$thumbnail[1].'"]'.$html.' '.$attach_caption.'[/caption]';
			}
		}
	}
	return $html;
}
add_filter( 'media_send_to_editor', 'pigen_insert', 100, 3 );


function pigen_delete( $attachment_id ) { // Delete thumbnail when PDF is deleted
	if( get_post_mime_type ( $attachment_id ) === 'application/pdf' ){
		$thumbnail_id = get_post_meta( $attachment_id, '_thumbnail_id', true );
		if( isset ( $thumbnail_id ) ){
			wp_delete_post( $thumbnail_id );
		}
	}
}
add_filter( 'delete_attachment', 'pigen_delete' );


function pigen_change_icon ( $icon, $mime, $attachment_id ){ // Display thumbnail instead of document.png
	if( $mime === 'application/pdf' ){
		$metadata = wp_get_attachment_metadata ( $attachment_id );
		$thumbnail_id = get_post_meta( $attachment_id, '_thumbnail_id', true );
		if( $thumbnail_id ){
			$thumbnail = wp_get_attachment_image_src ( $thumbnail_id, 'medium' );
		    $icon = $thumbnail[0];
		}
	}
	return $icon;
}
add_filter('wp_mime_type_icon', 'pigen_change_icon', 10, 3);

