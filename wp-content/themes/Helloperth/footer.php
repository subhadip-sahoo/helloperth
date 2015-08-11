        <?php 
            global $wp_query, $post;
            $directory_name = $wp_query->queried_object->name;
            $directory_slug = $wp_query->queried_object->slug;
            $directory_id = $wp_query->queried_object->term_id;
            $directory_taxonomy = $wp_query->queried_object->taxonomy;
        ?>
        <footer class="footer-container clearfix">
            <div class="wrapper">
                <div class="inner-footer-container clearfix">
                    <div class="footer-container-top clearfix">
                        <div class="footer-social">
                            <ul>
                                <li><a href="<?php echo(get_field('envelope_url','option')) ? get_field('envelope_url','option') : '#'; ?>"><i class="fa fa-envelope"></i></a></li>
                                <li><a href="<?php echo(get_field('facebook_url','option')) ? get_field('facebook_url','option') : '#'; ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="<?php echo(get_field('twitter_url','option')) ? get_field('twitter_url','option') : '#'; ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="<?php echo(get_field('instagram_url','option')) ? get_field('instagram_url','option') : '#'; ?>" target="_blank"><i class="fa fa-instagram"></i></a></li>
                                <li><a href="<?php echo(get_field('pinterest_url','option')) ? get_field('pinterest_url','option') : '#'; ?>" target="_blank"><i class="fa fa-pinterest"></i></a></li>
                            </ul>
                        </div>
                        <div class="footer-nav-area">
                            <nav class="foot-menu">
                                <?php
                                    $args_footer = array(
                                            'theme_location'  => 'footer',
                                            'menu'            => '',
                                            'container'       => '',
                                            'container_class' => '',
                                            'container_id'    => '',
                                            'menu_class'      => 'menu',
                                            'menu_id'         => '',
                                            'echo'            => true,
                                            'fallback_cb'     => 'wp_page_menu',
                                            'before'          => '',
                                            'after'           => '',
                                            'link_before'     => '',
                                            'link_after'      => '',
                                            'items_wrap'      => '<ul>%3$s</ul>',
                                            'depth'           => 0,
                                            'walker'          => ''
                                    );

                                    wp_nav_menu( $args_footer );

                                ?>
                            </nav>
                        </div>
                    </div>
                    <div class="footer-container-base clearfix">
                        <div class="footer-base-left footer-copyright-text">
                            <p><?php echo get_field('copyright_text','option'); ?></p>
                        </div>
                        <div class="footer-base-right hide-tablet"><p><?php echo get_field('website_bottom_right_text','option'); ?></p></div>
                    </div>
                </div>
            </div>
        </footer>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri();?>/js/vendor/jquery-1.11.0.js"><\/script>')</script>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/flexslider/2.2.2/jquery.flexslider.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/vendor/jquery.bxslider.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/vendor/jquery.meanmenu.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/vendor/jquery.fitvids.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/vendor/owl.carousel.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/vendor/jquery.magnific-popup.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;signed_in=false&amp;libraries=geometry,places"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/google-map.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/user-profile.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/password-strength-meter.js"></script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script>Stripe.setPublishableKey('<?php echo stripe_api_key(); ?>');</script>
        <script src="<?php echo get_template_directory_uri();?>/js/stripe.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/jquery.validationEngine-en.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/jquery.validationEngine.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/vendor/jquery.mCustomScrollbar.concat.min.js"></script>
        <script src="<?php echo get_template_directory_uri();?>/js/main.js"></script>
        <script type="text/javascript">
            var markers = [];
            var map;
            function initialize_directories(){
                var arr_latlang = <?php echo json_encode(get_directory_latlng($directory_slug)); ?>;
                var coordinates = [];
                for(var i=0; i < arr_latlang.length; i++){
                    coordinates[i] = new google.maps.LatLng(arr_latlang[i].lat, arr_latlang[i].lng);
                }
                var center_coordinates = <?php echo json_encode(get_center_latlng($directory_slug)); ?>;
//                var center= new google.maps.LatLng(center_coordinates[0].lat, center_coordinates[0].lng);
                var center= new google.maps.LatLng(-31.9528536, 115.8573389);
                var myOptions = {
                    zoom: 10,
                    center: center,
                    navigationControl: true
                }
                map = new google.maps.Map(document.getElementById("map"), myOptions);
                var j = 0;
                while(j < coordinates.length){
                    createMarker(coordinates[j]);
                    if(j == coordinates.length - 1){
                        break;
                    }
                    j++;
                }
            }
            function createMarker(latlng) {
                var marker = new google.maps.Marker({ 
                    map: map, 
                    position: latlng, 
                    clickable:true,
                });
                markers.push(marker);
            }
            initialize_directories();
            
            var markers_large = [];
            var map_large;
            function initialize_large_directories(){
                var arr_latlang_large = <?php echo json_encode(get_directory_latlng($directory_slug)); ?>;
                var coordinates_large = [];
                var InfoWindowArray = [];
                for(var i=0; i < arr_latlang_large.length; i++){
                    var InfoWindowContent = [];
                    coordinates_large[i] = new google.maps.LatLng(arr_latlang_large[i].lat, arr_latlang_large[i].lng);
                    InfoWindowContent[0] = arr_latlang_large[i].title;
                    InfoWindowContent[1] = arr_latlang_large[i].content;
                    InfoWindowArray.push(InfoWindowContent);
                }
                var center_coordinates_large = <?php echo json_encode(get_center_latlng($directory_slug)); ?>;
//                var center_large = new google.maps.LatLng(center_coordinates_large[0].lat, center_coordinates_large[0].lng);
                var center_large = new google.maps.LatLng(-31.9528536, 115.8573389);
                var myOptions_large = {
                    zoom: 8,
                    center: center_large,
                    navigationControl: true
                }
                map_large = new google.maps.Map(document.getElementById("map-large"), myOptions_large);
                var j = 0;
                while(j < coordinates_large.length){
                    createMarker_larger(coordinates_large[j], InfoWindowArray[j][0], InfoWindowArray[j][1]);
                     
                    if(j == coordinates_large.length - 1){
                        break;
                    }
                    j++;
                }
            }
            function createMarker_larger(latlng, title, content) {
                var marker = new google.maps.Marker({ 
                    map: map_large, 
                    position: latlng, 
                    clickable:true,
                });
                markers_large.push(marker);
                var contentString = '<div class="dir-info-window"><div class="info-title"><h2>'+ title + '</h2></div>' + '<div class="info-content"><p>' + content + '</p></div></div>';
                var infowindow = new google.maps.InfoWindow({
                   content: contentString 
                });
                google.maps.event.addListener(marker,'click', function(){ 
                    infowindow.close();
                    infowindow.open(map_large,marker);
                });
            }
            $('.inner-right-container').magnificPopup({
                delegate: '.inner-map-container a',
                type: 'inline',
                overflowY: 'hidden',
                preloader: true,
                callbacks:{
                    open: function() {
                        initialize_large_directories();
                    }
                }
            });
            
        </script>
        <script type="text/javascript">
            var markers_single = [];
            var map_single;
            function initialize_single_directories(){
                var arr_latlang_single = <?php echo json_encode(get_single_directory_latlng($post->ID)); ?>;
                var coordinates_single = [];
                var InfoWindowArray = [];
                for(var i=0; i < arr_latlang_single.length; i++){
                    var InfoWindowContent = [];
                    coordinates_single[i] = new google.maps.LatLng(arr_latlang_single[i].lat, arr_latlang_single[i].lng);
                    InfoWindowContent[0] = arr_latlang_single[i].title;
                    InfoWindowContent[1] = arr_latlang_single[i].content;
                    InfoWindowArray.push(InfoWindowContent);
                }
                var center_coordinates_single = <?php echo json_encode(get_single_directory_latlng($post->ID)); ?>;
                var center_single = new google.maps.LatLng(center_coordinates_single[0].lat, center_coordinates_single[0].lng);
                var myOptions_single = {
                    zoom: 17,
                    center: center_single,
                    navigationControl: true
                }
                map_single = new google.maps.Map(document.getElementById("map-single"), myOptions_single);
                var j = 0;
                while(j < coordinates_single.length){
                    if(typeof InfoWindowArray[j][0] == 'undefined' || typeof InfoWindowArray[j][1] == 'undefined'){
                        break;
                    }
                    createMarker_single(coordinates_single[j], InfoWindowArray[j][0], InfoWindowArray[j][1]);
                     
                    if(j == coordinates_single.length - 1){
                        break;
                    }
                    j++;
                }
            }
            function createMarker_single(latlng, title, content) {
                var marker = new google.maps.Marker({ 
                    map: map_single,
                    anchorPoint: new google.maps.Point(0, -29),
                    position: latlng, 
                    clickable:true,
                });
                markers_single.push(marker);
                var contentString = '<div class="dir-info-window"><div class="info-title"><h2>'+ title + '</h2></div>' + '<div class="info-content"><p>' + content + '</p></div></div>';
                var infowindow = new google.maps.InfoWindow({
                   content: contentString 
                });
                infowindow.close();
                infowindow.open(map_single,marker);
                google.maps.event.addListener(marker,'click', function(){ 
                    infowindow.close();
                    infowindow.open(map_single,marker);
                });
            }
            initialize_single_directories();
            
            $('.single-grid-block').magnificPopup({
                delegate: '.single-map-area a',
                type: 'inline',
                overflowY: 'hidden',
                preloader: true,
                callbacks:{
                    open: function() {
                        initialize_single_directories();
                    }
                }
            });
        </script>
        <script type="text/javascript">
            var markers_home = [];
            var map_home;
            function initialize_all_directories(){
                var arr_latlang_home = <?php echo json_encode(get_directory_latlng()); ?>;
                var coordinates_home = [];
                var InfoWindowArray = [];
                for(var i=0; i < arr_latlang_home.length; i++){
                    var InfoWindowContent = [];
                    coordinates_home[i] = new google.maps.LatLng(arr_latlang_home[i].lat, arr_latlang_home[i].lng);
                    InfoWindowContent[0] = arr_latlang_home[i].title;
                    InfoWindowContent[1] = arr_latlang_home[i].content;
                    InfoWindowArray.push(InfoWindowContent);
                }
                var center_coordinates_home = <?php echo json_encode(get_center_latlng()); ?>;
                var center = new google.maps.LatLng(center_coordinates_home[0].lat, center_coordinates_home[0].lng);
                var myOptions = {
                    zoom: 9,
                    center: center,
                    navigationControl: true
                }
                map_home = new google.maps.Map(document.getElementById("map-home"), myOptions);
                var j = 0;
                while(j < coordinates_home.length){
                    createMarker_home(coordinates_home[j], InfoWindowArray[j][0], InfoWindowArray[j][1]);
                    if(j == coordinates_home.length - 1){
                        break;
                    }
                    j++;
                }
            }
            function createMarker_home(latlng, title, content) {
                var marker = new google.maps.Marker({ 
                    map: map_home, 
                    position: latlng, 
                    clickable:true,
                });
                markers_home.push(marker);
                var contentString = '<div class="dir-info-window"><div class="info-title"><h2>'+ title + '</h2></div>' + '<div class="info-content"><p>' + content + '</p></div></div>';
                var infowindow = new google.maps.InfoWindow({
                   content: contentString 
                });
                google.maps.event.addListener(marker,'click', function(){ 
                    infowindow.close();
                    infowindow.open(map_home,marker);
                });
            }
            initialize_all_directories();
        </script>
        <?php wp_footer(); ?>
    </body>
</html>