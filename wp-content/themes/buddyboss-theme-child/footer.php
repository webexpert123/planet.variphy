<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package BuddyBoss_Theme
 */

?>

<?php do_action( THEME_HOOK_PREFIX . 'end_content' ); ?>

</div><!-- .bb-grid -->
</div><!-- .container -->
</div><!-- #content -->

<?php do_action( THEME_HOOK_PREFIX . 'after_content' ); ?>

<?php do_action( THEME_HOOK_PREFIX . 'before_footer' ); ?>
<?php do_action( THEME_HOOK_PREFIX . 'footer' ); ?>
<?php do_action( THEME_HOOK_PREFIX . 'after_footer' ); ?>

</div><!-- #page -->

<?php do_action( THEME_HOOK_PREFIX . 'after_page' ); ?>

<?php if(is_page(13)){ ?>

<script type="text/javascript">

jQuery( document ).ready(function() {
	console.log('ready gg');
		setTimeout(function(){ 

		idealStatus();

		 }, 3000);


});


setInterval(function () {
      idealStatus();
}, 10000);


jQuery(document).on("click",".page-numbers",function() {
console.log('page-numbers');
		 idealStatus();
});

function idealStatus(){
	console.log('idle-status');
	jQuery(".item-entry").each(function(){
		var last_activity = 	jQuery(this).find('.last-activity').text();
		const myArr = last_activity.split(" ");

		if(jQuery(this).find('.member-status').hasClass('online')){

			if( ( (myArr[2] !='seconds') && myArr[1] >= 10 ) || ( myArr[2] =='hours' )  || ( myArr[2] =='day' ) || ( myArr[2] =='day,' )  || ( myArr[2] =='days' ) || ( myArr[2] =='days,' ) || ( myArr[2] =='week' ) || ( myArr[2] =='week,' )   || ( myArr[2] =='weeks' ) || ( myArr[2] =='weeks,' )    || ( myArr[2] =='month' )  || ( myArr[2] =='month,' )  || ( myArr[2] =='year' )  || ( myArr[2] =='year,' )    || ( myArr[2] =='years' )  || ( myArr[2] =='years,' )   ) {
				jQuery(this).find('.member-status').addClass('idle');
				jQuery(this).find('.member-status').removeClass('online');
				jQuery(this).find('.member-status').removeClass('offline');


			}  

		}      
	});
}




</script>



<?php } ?>


<script type="text/javascript">



 //    console.log('test');

	// jQuery('.additional-search').insertAfter(jQuery('.active.current.selected '));

	// jQuery('.component-navigation li').on('click',function(){
         
	//      jQuery('.additional-search').insertAfter(jQuery('.active.current.selected '));
	// });
</script>




<?php wp_footer(); ?>

</body>
</html>
