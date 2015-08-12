=== PDF Image Generator ===
Contributors: Mizuho Ogino
Tags: pdf, thumbnail, jpg, image, upload, convert, attachment
Plugin URI: http://web.contempo.jp/weblog/tips/p1522
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 1.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate a thumbnail of a PDF document by using ImageMagick. Allow inserting PDF link with a image into the editor.


== Description ==
By uploading a PDF attachment, this plugin convert the cover page to jpeg and attach it as a post thumbnail file. It also allows displaying a thumbnail icon and inserting PDF link with a cover image into the editor. 


= Available only for WordPress 4.0+, also only the server which ImageMagick and GhostScript are installed. =

This plugin hooks to the media editor and generates the first page image of PDF by using ImageMagick with GhostScript. It requires no setup, just activate the plugin via the admin install page.  


= Features =
This Plugins replaces and extends the following features.
<ul>
<li>Creates an image file for a PDF file by using ImageMagick.</li>
<li>Display an image as an icon instead of document.png in admin pages.</li>
<li>Register an image file as a post thumbnail of a PDF.</li>
<li>And generate it in three size variations, for thumbnail, medium, and large.</li>
<li>Replace link text with a JPG when inserting a PDF to the editor.</li>
<li>Delete a thumbnail When deleting a PDF file.</li>
<li>In template files, An image ID is exportable by using get_post_thumbnail_id($your_pdf_id) or get_post_meta($your_pdf_id, '_thumbnail_id', true) functions.</li>
</ul>

= Generated Items =
Generated JPGs are registered as post children of the pdf. 
<ul><li>my-file.pdf (your uploaded file)
<ul><li>my-file-pdf.jpg (generated image)
<ul><li>my-file-pdf-1024x768.jpg (large size)</li>
<li>my-file-pdf-300x225.jpg (medium size)</li>
<li>my-file-pdf-150x150.jpg (thumbnail size)</li></ul>
</li></ul></li></ul>

= Insert HTML into the editor =
Select pdf file by media uploader and insert it into the editor. The output HTML is automatically rewrote like below. 
<code>
<a class="link-to-pdf" title="dummy-pdf" href="http://exmaple.com/wp-content/uploads/2015/01/dummy-pdf.pdf" target="_blank"><img class="size-medium wp-image-9999" src="http://exmaple.com/wp-content/uploads/2015/01/dummy-pdf-pdf-227x320.jpg" alt="thumbnail-of-dummy-pdf" width="227" height="320" /></a>
</code>


== Screenshots ==
1. No setup, just upload a PDF file and insert it into the editor.
2. An inserted image is editable (align, sizes, etc…) just like ordinary image files.


== Installation ==
1. Copy the 'pdf-image-generator' folder into your plugins folder.
2. Activate the plugin via the 'Plugins' admin page. The plugin requires no setup or modifying core wordpress files. 

= Code Examples =
= Set thumbnail in template file =
The image ID is stored in ['_thumbnail_id'] custom field of PDF attachment post. When to output the image ID in template files, write codes like below.
<code>
$pdf_id = 'your pdf post ID';
$thumbnail_id = get_post_meta( $pdf_id, '_thumbnail_id', true );
if ($thumbnail_id) {
    $thumb_src = wp_get_attachment_image_src ( $thumbnail_id, 'medium' );
    echo '&lt;a class="pdf-link image-link" href="'.wp_get_attachment_url( $attachment_id ).'" title="'.esc_attr( get_the_title( $pdf_id ) ).'" target="_blank"&gt;&lt;img src="'. $thumb_src[0] .'" width="'. $thumb_src[1] .'" height="'. $thumb_src[2] .'"/&gt;&lt;/a&gt;'."\n";
}
</code>

= Display attachment data with in caption =
The plugin allows you to insert [caption] shortcode into the post content area as when insert an image. If you want display attachment title, description, file type, file size and so on, use img_caption_shortcode filter in your functions.php. 
Here's an example code…

<code>function add_attachment_data_in_caption( $empty, $attr, $content ) {
    $attr = shortcode_atts( array( 'id'=&gt;'', 'align'=&gt;'alignnone', 'width'=&gt;'', 'caption'=&gt;'' ), $attr );
    if ( 1 &gt; (int) $attr['width'] || empty( $attr['caption'] ) ) return '';
    if ( $attr['id'] ) {
        $attr['id'] = 'id="' . esc_attr( $attr['id'] ) . '" ';
        $attachment_id = explode('_', $attr['id']);
        $attachment_id = $attachment_id[1];// get attachment id
        if( get_post_mime_type ( $attachment_id ) === 'application/pdf' ){ 
            $attachment = get_post( $attachment_id );
            $bytes = filesize( get_attached_file( $attachment-&gt;ID ) );
            if ($bytes &gt;= 1073741824) $bytes = number_format($bytes / 1073741824, 2). ' GB';
            elseif ($bytes &gt;= 1048576) $bytes = number_format($bytes / 1048576, 2). ' MB';
            elseif ($bytes &gt;= 1024) $bytes = number_format($bytes / 1024, 2). ' KB';
            elseif ($bytes &gt; 1) $bytes = $bytes. ' bytes';
            elseif ($bytes == 1) $bytes = $bytes. ' byte';
            else $bytes = '0 bytes';
            $attr['caption'] = 
              'title : ' .$attachment-&gt;post_title. '&lt;br/&gt;' . // title
              'caption : ' .$attr['caption']. '&lt;br/&gt;' .// caption
              'size : ' .$bytes. '&lt;br/&gt;' . // file size
              'filetype : ' .get_post_mime_type ( $attachment_id ). '&lt;br/&gt;' . // file type
              'description : ' .$attachment-&gt;post_content. '&lt;br/&gt;'; // description
        }
    }

    return 
      '&lt;div ' .$attr['id']. 'class="wp-caption ' .esc_attr( $attr['align'] ). '" style="max-width: ' .( 10 + (int) $attr['width'] ). 'px;"&gt;' .
      do_shortcode( $content ). '&lt;p class="wp-caption-text"&gt;' .$attr['caption']. '&lt;/p&gt;' .
      '&lt;/div&gt;';
}
add_filter('img_caption_shortcode', 'add_attachment_data_in_caption', 10, 3);</code>

= Regenerate thumbnails =
Allows you to regenerate thumbnails of any already-uploaded PDFs.
Activate the plugin and add this code to your functions.php. Then, Click "Regenerate PDF-Thumbs" link in "settings". After generating images, remove function from your php. 
<code>
function regenerate_pdf_image_menu() {
  add_options_page('Force regenerate uploaded PDF thumbs, 'Regenerate PDF-Thumbs', 5, 'regenerate_pdf', 'regenerate_pdf_image_page');
}
function regenerate_pdf_image_page() {
  echo '&lt;div class="wrap"&gt;&lt;h2&gt;Regenerate uploaded PDF thumbnails&lt;/h2&gt;';
	$pdfs = get_posts(array('post_type'=&gt;'attachment','post_mime_type'=&gt;'application/pdf','numberposts'=&gt;-1));
	if($pdfs): foreach($pdfs as $pdf):
		$thumbnail_id = get_post_meta( $pdf-&gt;ID, '_thumbnail_id', true );
		if( $thumbnail_id ){
			echo 'attachment id: '.$pdf-&gt;ID.' / A thumb is already exist.&lt;br/&gt;';
		} else {
			pigen_attachment($pdf-&gt;ID);
			echo 'attachment id: '.$pdf-&gt;ID.' / A thumb is generated!! &lt;br/&gt;';
		}
	endforeach; endif;
  echo '&lt;/div&gt;';
}
add_action('admin_menu', 'regenerate_pdf_image_menu');
</code>

== ChangeLog ==

= 1.1.5 =
13.May 2015. Automatically add caption shortcode when the caption field filled.
= 1.1.4 =
02.May 2015. Remove the [0] from the image file name.
= 1.1.3 =
24.Apr 2015. Change the way to check in an activation if exec() is enabled or disabled on a server.
= 1.1.2 =
4.Apr 2015. Remove the process of generating a test file.
= 1.1.1 =
14.Mar 2015. Fixed colorspace bug, modified register_activation_hook and error messages.
= 1.1 =
3.Feb 2015. Added support for imagick API, and added uninstall.php.
= 1.0.1 =
17.Jan 2015. Added Verifying ImageMagick installation
= 1.0 =
12.Jan 2015. First public version Release
= 0.1 =
26.Sep 2014. Initial Release
