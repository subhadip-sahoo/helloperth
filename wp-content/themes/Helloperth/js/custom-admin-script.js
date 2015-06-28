(function($){
    $(function(){
        $.fn.getPendingDirectory = function(){
            $.ajax({
                url:ajaxurl,
                type:"POST",
                data: {action: 'getDraftDir_callback'},
                success: function(data){
                    var post = JSON.parse(data);
                    if(post.count != 0){
                       $('div.wp-menu-name').each(function(){
                            if($(this).text() == 'Directories'){
                                $(this).append('<span class="advertiser-notification"><span class="plugin-count">'+ post.count +'</span></span>');
                            }else if($(this).children().hasClass('advertiser-notification')){
                                $(this).children().remove('.advertiser-notification').append('<span class="advertiser-notification"><span class="plugin-count">'+ post.count +'</span></span>');
                            }
                        }); 
                    }
                }
            });
        }
        
        $('.update-plugins').remove();
        
        $(document).getPendingDirectory();
        
        setInterval(function(){
            $(document).getPendingDirectory();
        }, 1000);
        
        $('#promoted_to_home').change(function(){
            var $this = $(this);
            $('#limit_exceed_msg').remove();
            if($this.is(':checked') == true){
                $.ajax({
                    url:ajaxurl,
                    type:"POST",
                    data: {action: 'checkPromotionCount_callback', meta_key: 'promoted_to_home'},
                    success: function(count){
                        if(count == 1){
                            $this.before('<p style="color: red;" id="limit_exceed_msg">'+ 'Maximum directory listing limit has been reached for home page.' +'</p>');
                            $this.prop('checked', false);
                        }
                    }
                });
            }
        });
        
        $('#promoted_to_banner').change(function(){
            var $this = $(this);
            $('#limit_exceed_msg').remove();
            if($this.is(':checked') == true){
                $.ajax({
                    url:ajaxurl,
                    type:"POST",
                    data: {action: 'checkPromotionCount_callback', meta_key: 'promoted_to_banner'},
                    success: function(count){
                        console.log(count);
                        if(count == 1){
                            $this.before('<p style="color: red;" id="limit_exceed_msg">'+ 'Maximum directory listing limit has been reached for banner listing.' +'</p>');
                            $this.prop('checked', false);
                        }
                    }
                });
            }
        });
    });
})(jQuery);