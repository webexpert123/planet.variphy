<?php
/**
 * the template file to display content search result page
 * instead create a folder 'buddyboss-global-search' inside your theme, copy this file over there, and make changes there
 */

//$no_results_class = ! BP_Search::instance()->has_search_results() ?  'bp-search-no-results' : '';


$is_posts = false;
$total_count = 0;
$kb_posts_total = 0;
//First 100 Posts
$htkb_posts_req = wp_remote_get( 'https://kb.variphy.com/wp-json/wp/v2/ht-kb?search='.$_GET['s'].'&per_page=100&page=1');
$htkb_posts = json_decode( wp_remote_retrieve_body( $htkb_posts_req ) );
$htkb_posts_X_WP_Total = wp_remote_retrieve_header( $htkb_posts_req, 'X-WP-Total' );


$htkb_posts_count = 0;
if(count($htkb_posts) > 0){
	$htkb_posts_count = count($htkb_posts);
	$total_count += count($htkb_posts);
	$kb_posts_total += count($htkb_posts);
	$is_posts = true;
}

//Next 100 Posts
$htkb_posts_req2 = wp_remote_get( 'https://kb.variphy.com/wp-json/wp/v2/ht-kb?search='.$_GET['s'].'&per_page=100&page=2');
$htkb_posts2 = json_decode( wp_remote_retrieve_body( $htkb_posts_req2 ) );
$htkb_posts2_X_WP_Total = wp_remote_retrieve_header( $htkb_posts_req2, 'X-WP-Total' );

$htkb_posts2_count = 0;
if(count($htkb_posts2) > 0 && $htkb_posts_count >=100){
	$htkb_posts2_count = count($htkb_posts2);
	$total_count += count($htkb_posts2);
	$kb_posts_total += count($htkb_posts2);
	$is_posts = true;
}

$post_couner = 1;

$loop = new WP_Query( array(
    'post_type' => 'post',
    'posts_per_page' => -1,
   	'post_status' => 'publish',

    's' => $_GET['s']
));
$total_found_posts = $loop->found_posts;

/*if($total_found_posts > 0){
	$is_posts = true;
	$total_count += intval($total_found_posts);	
}*/

$show_knowledge_menu = true;
if(isset($_GET['type']) && $_GET['type'] == 'knowledge-base' ){
	if ($total_count < 1) {
		$show_knowledge_menu = false;
	}
}




$post_title = '';


$_SESSION['kb_total_count'] = $total_count ; 



if ( empty( $_GET['s'] ) || '' === $_GET['s'] ) {
	$post_title = __( 'No results found', "buddyboss-theme" );
} elseif ( BP_Search::instance()->has_search_results() ) {
	$post_title = sprintf( __( 'Showing results for \'%s\'', "buddyboss-theme" ), esc_html( $_GET['s'] ) );
} else {
	if($htkb_posts_count > 0){
		$post_title = sprintf( __( 'Showing results for \'%s\'', "buddyboss-theme" ), esc_html( $_GET['s'] ) );
	}else{
		$post_title = sprintf( __( 'No results for \'%s\'', "buddyboss-theme" ), esc_html( $_GET['s'] ) );
		$show_knowledge_menu = false;
	}
}
?>

<div class="bp-search-page buddypress-wrap child-theme-search-result">

	<header class="search-results-header">
		<h1 class="entry-title"><?php echo stripslashes($post_title); ?></h1>
	</header>

	<div class="bp-search-results-wrapper dir-form <?php echo ( isset( $no_results_class ) ) ? $no_results_class : ''; ?>">

		<nav class="search_filters item-list-tabs bp-navs dir-navs bp-subnavs no-ajax flex-1" role="navigation">
			<ul class="component-navigation search-nav ">
				<?php bp_search_filters();?>
			</ul>

			<?php /* if($total_count > 0){?>

			<div class="additional-search">

				<p class=" knowledge-base" data-item="knowledge-base">
					<input type="hidden" name="kb_posts_total" id="kb_posts_total_hidden" value="<?php echo $total_count;?>">
					<?php $knowledge_url = site_url().'/?s='.$_GET['s'].'&view=content&no_frame=1&bp_search=1&type=knowledge-base'; ?>
					<a class="knowledge-base-link" href="<?php echo $knowledge_url;?>">Knowledge Base 
						<?php if($total_count > 0){ ?>
						<span class="count"><?php echo $total_count;?></span>
						<?php } ?>
					</a>
				</p>
			</div>
			<?php } */?>
		</nav>

		<div class="search_results">
			
			<?php do_action( 'bp_search_before_result' ); ?>
			<?php 
			if((isset($_GET['type']) && $_GET['type'] == 'knowledge-base' ) || (isset($_GET['s']) && $_GET['type'] != 'knowledge-base')  ){

				if(is_null($_GET['subset'])){
						$is_posts = true;
	
				}else{
			 				$is_posts = false;
				}
				
					if($is_posts == true ){ ?>
                        <div class="results-group results-knowledge-base-posts bp-search-results-wrap">
						<header class="results-group-header clearfix"><h3 class="results-group-title"><span>Knowledge Base </span></h3> <span class="total-results"><?php  echo $htkb_posts_count ; ?> results</span> </header>

						<?php
						
						if($htkb_posts_count >=1){

							foreach ($htkb_posts as $post) {
								if( isset($_GET['s']) && $_GET['type'] != 'knowledge-base' && $post_couner > 5 ){
									continue;

						     	}
								$post_date = date( " j F\, Y ", strtotime( $post->date ) );
								if(!empty($post->id)){ 
									?>
									<article class="remote-articles kb-article post <?php echo 'post-'.$post->id ?>" id="<?php echo 'post-'.$post->id ?>">
										<div class="post-inner-wrap">
											<div class="entry-content-wrap">
												<!-- <span><?php //echo $post_couner;?></span> -->
												<header class="entry-header">
													<h2 class="entry-title"><a target="_blank" class="hkb-article__link" href="<?php  echo esc_url( $post->link ); ?>"><?php echo esc_html( $post->title->rendered ); ?></a></h2>
												</header><!-- .entry-header -->
												<div class="entry-content">
													<?php echo wp_trim_words( $post->excerpt->rendered, 32 ); ?>
												</div>
											</div>
										</div><!--Close '.post-inner-wrap'-->
									</article>
									<?php 
								}
								$post_couner++;
							}
						}

						

						if($htkb_posts2_count >=1){
							foreach ($htkb_posts2 as $post) {
								if( isset($_GET['s']) && $_GET['type'] != 'knowledge-base' && $post_couner > 5 ){
									continue;

						     	}
								$post_date = date( " j F\, Y ", strtotime( $post->date ) );
								if(!empty($post->id)){ 
									?>
									<article class="remote-articles kb-article post <?php echo 'post-'.$post->id ?>" id="<?php echo 'post-'.$post->id ?>">
										<div class="post-inner-wrap">
										<!-- 	<span><?php // echo $post_couner;?></span> -->
											<div class="entry-content-wrap">
												<header class="entry-header">
													<h2 class="entry-title"><a target="_blank" class="hkb-article__link" href="<?php  echo esc_url( $post->link ); ?>"><?php echo esc_html( $post->title->rendered ); ?></a></h2>
												</header><!-- .entry-header -->
												<div class="entry-content">
													<?php echo wp_trim_words( $post->excerpt->rendered, 32 ); ?>
												</div>
											</div>
										</div><!--Close '.post-inner-wrap'-->
									</article>
									<?php 
									$post_couner++;
								}
							}
						}
						?>
                          	
						<?php
						/*if( $loop->have_posts() ) {
							while( $loop->have_posts() ):$loop->the_post();
								$post_id    = get_the_ID();
			                    $post_title = get_the_title($post_id);
			                    $content    = get_the_content($post_id);
			                    $post_link  = get_the_permalink($post_id);
			                    $post_date  = get_the_date( 'Y-m-d',$post_id );
			                    $thumbnail  = '';
			                    $post_title = strtolower($post_title);
			                    ?>
			                    <article class="remote-articles blog-posts post <?php echo 'post-'.$post_id ?>" id="<?php echo 'post-'.$post_id ?>">
									<div class="post-inner-wrap">
										<span><?php echo $post_couner;?></span>
										<div class="entry-content-wrap">
											<header class="entry-header">
												<h2 class="entry-title"><a target="_blank" class="hkb-article__link" href="<?php  echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a></h2>
											</header><!-- .entry-header -->
											<div class="entry-content">
												<?php echo wp_trim_words( strip_shortcodes( strip_tags( get_the_content( '', false ) ) ), 32 ); ?>
											</div>
										</div>
									</div><!--Close '.post-inner-wrap'-->
								</article>
			                    <?php 
			                    $post_couner++;
			                endwhile;
	                		wp_reset_query();
						}*/
						?>
					</div>
					<?php

					if( (isset($_GET['s']) && $_GET['type'] != 'knowledge-base')  ){
						bp_search_results();
					}
					}else{
						bp_search_results();
					}

				}else{
					bp_search_results();
				}
			?>
	
			<?php //bp_search_results();?>
			<?php do_action( 'bp_search_after_result' ); ?>

 	       
 
		</div>

<?php if ( isset($_GET['type']) && $_GET['type'] == 'knowledge-base') {   ?> 
						<div id="pagination-demo1"></div> 
			<?php } ?>
		
	</div>

</div><!-- .bp-search-page -->


<?php if ( isset($_GET['type']) && $_GET['type'] == 'knowledge-base') {   ?> 
<script src="//code.jquery.com/jquery-1.8.2.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/buddypress/search/pagination.js" ></script>

 <link href="<?php echo get_stylesheet_directory_uri(); ?>/buddypress/search/pagination.css" rel="stylesheet" type="text/css">

<script>


// console.log('source==');
  (function(name) {
    var container = $('#pagination-' + name);

         var sources = [];
        

         jQuery(".remote-articles").each(function(element, index, set) {
// var id = this.attr('class');
// console.log( index + ": " + jQuery( this ).text() );
      var classname = jQuery(this).attr('class');
      var html = jQuery(this).html();
      var mainstring = '<article class = '+classname+'>'+html+'</article>';
   //var html = '<div class="test"> 101</div>';

  sources.push(mainstring);
  //console.log('element==',set);
   
    // ...
});



    var options = {
      pageSize : 10,
      container : $('.search_results'),
      dataSource: sources,
      callback: function (response, pagination) {
        window.console && console.log(response, pagination);

        var dataHtml = '';

        $.each(response, function (index, item) {
          dataHtml += '' + item + '';

        });

        dataHtml += '';

        container.prev().html(dataHtml);

        //$(".html5lightbox").html5lightbox();
        
      
      }

    };

    //$.pagination(container, options);

    container.addHook('beforeInit', function () {
    //  window.console && console.log('beforeInit...');

    });
    container.pagination(options);

    container.addHook('beforePageOnClick', function () {
    //  window.console && console.log('beforePageOnClick...');
           
    
      //return false
    });

  })('demo1');

  
</script>

<?php } ?>


<?php if ( $total_count > 0 ) {   ?>

	<script type="text/javascript">
	changeCount();
    function changeCount(){
    	setTimeout(function(){
	 		var getCount = jQuery('.search_filters .search-nav li:first-child a .count').text();
	 		var variphyPostCount = jQuery('#kb_posts_total_hidden').val();
	 		console.log('variphyPostCount '+variphyPostCount+' getCount '+getCount);
	 		if(getCount > 0 ){
	 			getCount = parseInt(getCount);
	 		}
	 		if(variphyPostCount > 0 ){
	 			variphyPostCount = parseInt(variphyPostCount);
	 			var totalCount = getCount+variphyPostCount;
	 			jQuery('.search_filters .search-nav li:first-child a .count').text('');
	 			jQuery('.search_filters .search-nav li:first-child a .count').text(totalCount);
	 		}
	 	}, 500);
    }
	</script>
<?php } ?>

<?php if ( isset($_GET['type']) && $_GET['type'] == 'knowledge-base') {   ?>

	<script type="text/javascript">
	changeCount();
    function changeCount(){
    	setTimeout(function(){
	 		jQuery('.search_filters .search-nav li:first-child').removeClass('active');
	 		jQuery('.search_filters .search-nav li:first-child').removeClass('current');
	 		jQuery('.search_filters .search-nav li:first-child').removeClass('selected');
	 		jQuery('.search_filters p.knowledge-base').addClass('active');
	 		jQuery('.search_filters p.knowledge-base').addClass('current');
	 		jQuery('.search_filters p.knowledge-base').addClass('selected');
	 	}, 500);
    }
	</script>
<?php } ?>


