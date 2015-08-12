jQuery(document).ready(function($){
    var custom_uploader;
    var gallery_uploader;
     $(document).delegate('#upload_image_button', 'click', function(e) {
        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Set this image'
            },
            multiple: false,
        });
        
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#dir_feature_image').remove();
            $('#figure_parent').prepend('<figure class="upload-image-img" id="dir_feature_image">'+'<img src="'+ attachment.url +'" width="200" height="150" />'+'</figure>');
            $('#dir_feature_image').append('<a href="javascript:void(0);" title="Remove image" id="remove_dir_feature_image" class="btn btn-remove-image"><i class="fa fa-times-circle"></i></a>');
            $('#upload_image_button').remove();
            $('#post_thumbnail').val(attachment.id);
        });

        custom_uploader.open();
    });
    
    $(document).delegate('#remove_dir_feature_image', 'click', function(){
        $('#dir_feature_image').remove();
        $('#control-div').prepend('<button id="upload_image_button" class="button btn upload-btn" type="button" value="Upload Image" ><i class="fa fa-plus-circle"></i><span>Upload Image</span></button>');
        $(this).remove();
        $('#post_thumbnail').val('');
    });
    
    $('#upload_gallery_image').click(function(e) {
        e.preventDefault();
        if (gallery_uploader) {
            gallery_uploader.open();
            return;
        }
        gallery_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Add images'
            },
            multiple: true,
        });
        
        gallery_uploader.on('select', function() {
            attachment = gallery_uploader.state().get('selection').toJSON();
            var attachment_images = $('#img_gal').val();
            var attachment_arr = [];
            if(attachment_images != ''){
                attachment_arr = attachment_images.split(',').map(function(x){return parseInt(x)});
            }
            var gal_images = '';
            for(var i = 0; i < attachment.length; i++){
                if(attachment_arr.indexOf(attachment[i].id) != -1){ continue; }
                attachment_arr.push(attachment[i].id);
                gal_images += '<figure class="upload-image-img">';
                gal_images += '<img src="' + attachment[i].url + '" width="200" height="150"/>';
                gal_images += '<a href="javascript:void(0);" title="Remove image" class="remove-gal-image" data-attachment="' + attachment[i].id + '"><i class="fa fa-times-circle"></i></a>';
                gal_images += '</figure>';
            }
            attachment_arr = attachment_arr.reverse().filter(function (e, i, test_arr) {
                return test_arr.indexOf(e, i+1) === -1;
            }).reverse();
            console.log(attachment_arr);
            var attachments_str = attachment_arr.join();
            $('#img_gal').val('').val(attachments_str);
            $('#bulk-image-box').append(gal_images);
        });

        gallery_uploader.open();
    });
    
    $(document).delegate('.remove-gal-image', 'click', function(){
        var id = $(this).data('attachment');
        var attachment_images = $('#img_gal').val();
        var attachment_arr = [];
        attachment_arr = attachment_images.split(',').map(function(x){return parseInt(x)});
        var attachment_pos = attachment_arr.indexOf(id);
        attachment_arr.splice(attachment_pos, 1);
        var attachments_str = attachment_arr.join();
        $('#img_gal').val('').val(attachments_str);
        $(this).parent().remove();
    });
});