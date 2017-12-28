$(document).ready(function(){
    var url = (typeof is_paid_url == 'undefined') ? '/' : is_paid_url;

    var isPaid = function(){
        $.ajax({
            type: "GET",
            url: url,
            success: function(data){
                if(data.is_paid){
                    window.location = data.redirect_url;
                }
            }
        });
    };
    setInterval(isPaid, 3000);
});