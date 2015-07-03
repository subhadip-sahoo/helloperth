(function($){
    $(function() {
        var default_location = '';
//        if ("geolocation" in navigator) {
//            navigator.geolocation.getCurrentPosition(function(position) {
//                default_location = position.coords.latitude+','+position.coords.longitude;
//                loadWeather(default_location, ''); //load weather using your lat/lng coordinates
//            });
//        }else{
//            loadWeather(default_location, '');
//        }  
        loadWeather(default_location, '');
        setInterval(function(){
            loadWeather(default_location, '');
        }, 600000 ); /* 10 mins */
    });
    function loadWeather(location, woeid) {
        if(location == '' || typeof location === 'undefined'){
            location = 'Perth, WA';
        }
        $.simpleWeather({
            location: location,
            woeid: woeid,
            unit: 'c',
            success: function(weather) {
                html = '<div class="weataher-current">';
                var title = weather.currently;
                title = title.replace(/[&\/\\#,+()@_$~%.'":*?<>{}]/g, '');
                title = title.replace(/[, ]+/g, ' ').trim();
                var words = title.split(' ');
                var str = words.join('-');
                var status = str.toLowerCase();
                if(status != 'unknown'){
                    html += '<img src="'+template.uri+'/images/weather-icons/'+status+'.png" width="" height="">';
                }
                html += '<span>'+weather.temp+'<small>'+weather.units.temp+'</small></span>';
                html += '</div>';
                html += '<div class="weather-current-compare">';
                html += '<p class="weather-day-report">'+weather.currently+'</p>';
                html += '<p class="weather-current-from-to">';
                html += '<span>'+weather.low+' <small>'+weather.units.temp+'</small></span> To <span>'+weather.high+' <small>'+weather.units.temp+'</small></span>';
                html += '</p>';
                html += '</div>';
                $("#weather").html(html);
            },
            error: function(error) {
                $("#weather").html('<p>'+error+'</p>');
            }
        });
    }
})(jQuery);