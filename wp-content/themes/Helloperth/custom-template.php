<?php
  get_header();
  global $wp_query;
  $temp = $wp_query;
  $wp_query = null;
  $wp_query = new WP_Query();
  $show_posts = 1;  //How many post you want on per page
  $permalink = 'Post name'; // Default, Post name
  $post_type = 'projects';
   
  //Know the current URI
  $req_uri =  $_SERVER['REQUEST_URI']; 
   
  //Permalink set to default
  if($permalink == 'Default') {
  $req_uri = explode('paged=', $req_uri);
   
  if($_GET['paged']) {
  $uri = $req_uri[0] . 'paged=';
  } else {
  $uri = $req_uri[0] . '&paged=';
  }
  //Permalink is set to Post name
  } elseif ($permalink == 'Post name') {
  if (strpos($req_uri,'page/') !== false) {
  $req_uri = explode('page/',$req_uri);
  $req_uri = $req_uri[0] ;
  }
  $uri = $req_uri . 'page/';
   
  }
   
  //Query
  $wp_query->query('showposts='.$show_posts.'&post_type='. $post_type .'&paged='.$paged);
  //count posts in the custom post type
 $count_posts = wp_count_posts('projects');
 
  while ($wp_query->have_posts()) : $wp_query->the_post();
  ?>
  <!--Do stuff-->
  <section class="main-container clearfix">
      <section class="main wrapper clearfix">
  <h1>
  <?php the_title(); ?>
  </h1>
  <?php the_content(); ?>
  <?php endwhile;?>
  <nav>
  <?php previous_posts_link('&laquo; ') ?>
  <?php
  $count_post = $count_posts->publish / $show_posts;
   
  if( $count_posts->publish % $show_posts == 1 ) {
  $count_post++;
  $count_post = intval($count_post);
  };
   
  for($i = 1; $i <= $count_post ; $i++) { ?>
  <a <?php if($req_uri[1] == $i) { echo 'class=active_page'; } ?> href="<?php echo $uri . $i; ?>"><?php echo $i; ?></a>
  <?php }
  ?>
  <?php next_posts_link(' &raquo;') ?>
  </nav>
 </section>
      </section>
  <?php
  $wp_query = null;
  $wp_query = $temp;  // Reset
   
  get_footer();
  ?>