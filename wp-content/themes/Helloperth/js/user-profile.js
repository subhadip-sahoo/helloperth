(function($){
  function psmb(){
    var e = $("#user_pass").val(),d = $("#user_login").val(),c = $("#cuser_pass").val(),f;
    $("#psm-pass-strength-result").removeClass("short bad good strong");
    if(!e){$("#psm-pass-strength-result").html("<p>Strength Indicator</p>");return}
    f = passwordStrength(e,d,c);
    switch(f){
        case 2: 
            $("#psm-pass-strength-result").addClass("bad").html("<p>Weak</p>");
            break;
        case 3: 
            $("#psm-pass-strength-result").addClass("good").html("<p>Medium</p>");
            break;
        case 4: 
            $("#psm-pass-strength-result").addClass("strong").html("<p>Strong</p>");
            break;
        case 5: 
            $("#psm-pass-strength-result").addClass("short").html("<p>Mismatch</p>");
            break;
        default: 
            $("#psm-pass-strength-result").addClass("short").html("<p>Very weak</p>")
    }
  }
    $(document).ready(function(){
        $("#user_pass").val("").keyup(psmb);
        $("#cuser_pass").val("").keyup(psmb);

        $('.txn-log-container').magnificPopup({
            delegate: '.txn-log a',
            type: 'inline',
            overflowY: 'hidden',
            preloader: false
        });
    
        $(document).delegate('.txn-details', 'click', function(){
            var id = $(this).data('id');
            var $this = $('#txn-invoice');
            $this.is(':has(table)') ? $this.children('table').remove() : '';
            $this.append('<div id="before-pop">' + '<h3>Please wait, fetching data...</h3>' + '<img src="' + template.uri + '/images/txn-loader.gif" class="txn-loader" alt="Loader"></div>');
            $.ajax({
                url: userSettings.url + 'wp-admin/admin-ajax.php',
                type: 'POST',
                data: {action: 'txn_invoice_callback', id: id},
                success: function(response){
                    $('#before-pop').remove();
                    $this.append(response);
//                    $this.is(':has(table)') ? $this.children('table').replaceWith(response) : $this.append(response);
                }
            });
        });
    });
})(jQuery);