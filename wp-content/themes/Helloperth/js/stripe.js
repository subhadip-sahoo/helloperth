(function($){    
    var stripeResponseHandler = function(status, response) {
        var $form = $('#checkout-form');
        if(response.error) {
            $form.find('button').empty().text('Proceed to Subscription');  
            $form.find('.checkout-errors').text(response.error.message);
//          $form.find('button').prop('disabled', false);
        }else{
            var token = response.id;
            $form.find('button').empty().text('Proceeding...');
            $form.append($('<input type="hidden" name="stripeToken" />').val(token));
            $form.get(0).submit();
        }
    };
    $(function(){
        $('#checkout-form').submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            $form.find('button').empty().text('Please wait...');
            $.ajax({
                url: userSettings.url + 'wp-admin/admin-ajax.php',
                type: 'POST',
                data: {action: 'check_captcha', code: $('#security_code').val()},
                success: function(res){
                    console.log(res);
                    if(res != 'verified'){
                        $form.find('button').empty().text('Proceed to Subscription');
                        $form.find('.checkout-errors').text(res);
                        return false;
                    }else{
//                        $form.find('button').prop('disabled', true);
                        Stripe.card.createToken($form, stripeResponseHandler);
                        return false;
                    }
                }
            });
        });
    });
})(jQuery);