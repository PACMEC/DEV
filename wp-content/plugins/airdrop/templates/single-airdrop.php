<?php
/**
 * The template for displaying single airdrop post type
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package airdrop
 */

get_header(); ?>

	<?php
	do_action( 'airdrop_wrapper' ); ?>

		<?php
		while ( have_posts() ) : the_post(); ?>
		<div class="row">
			<div class="airdrop-thumbnail-wrap col-lg-5 col-md-5 col-sm-4 col-xs-12">
		<figure class="airdrop-thumbnail">
		<?php if( has_post_thumbnail() ) {
			echo '<a rel="bookmark" href="'. esc_url( get_permalink() ) . '">';
			the_post_thumbnail( 'airdrop-main-thumbnail' );
			echo '</a>';	
		}
		 ?>
	</figure>
	</div>
	<?php
	$airdrop_symbol_value 		= get_post_meta( get_the_ID(), 'airdrop_symbol', true );
	$airdrop_website_value 		= get_post_meta( get_the_ID(), 'airdrop_website', true );
    $airdrop_enddate_value 		= get_post_meta( get_the_ID(), 'airdrop_enddate', true );
    $airdrop_estimated_value 	= get_post_meta( get_the_ID(), 'airdrop_estimatedvalue', true );
    $airdrop_requirement_value 	= get_post_meta( get_the_ID(), 'airdrop_requirement', true );
	?>
		<div class="col-lg-7 col-md-7 col-sm-8 col-xs-12">	
			<table class="airdrop-single-summary" style="width: 100%; border-collapse: collapse;">
				<tr>
					<td colspan="2"><?php the_title( '<h1 class="airdrop-name">', '</h1>' ); ?></td>
				</tr>
				<?php if( function_exists( 'airdrop_rating_addon' ) ) { ?>
					<tr>
						<td><?php _e( "Rating", "airdrop" ); ?></td>
						<td><span class="airdrop-rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i></span></td>
					</tr>
				<?php } ?>

				<!-- Airdrop Token Symbol -->
				<tr>
					<td><span class="airdrop-symbol"><?php _e( "Token Symbol", 'airdrop' ); ?></span></td>
					<td><?php echo esc_textarea( $airdrop_symbol_value ); ?></td>
				</tr>

				<tr>
					<td><span class="airdrop-value"><?php _e( "Estimated value", 'airdrop' ); ?></span></td>
					<td><span class="airdrop-value--number"><?php
					$airdrop_currency = "$";
					if( 0 < $airdrop_estimated_value ) {echo $airdrop_currency . $airdrop_estimated_value;
					} else {
						_e( "N/A", "airdrop" ); 
					} ?>
				</span></td>
				</tr>

				<tr>
					<td><span class="airdrop-timeleft"><?php _e( "Expiry date", 'airdrop' ); ?></span></td>
					<?php if( isset( $airdrop_enddate_value ) ) { ?>
					<td><?php echo $airdrop_enddate_value; ?></td>
					<?php } else { ?>
					<td><?php _e( "n/a", "airdrop" ); ?></td>
					<?php } ?>
				</tr>
				
				<tr>
					<td><span class="airdrop-publishdate"><?php _e( "Published date", 'airdrop' ); ?></span></td>
					<td><?php echo get_the_date( get_option( 'date_format' ) ); ?></td>
				</tr>
			</table>

		</div>
	</div>
			<?php
			the_content();
			the_post_navigation();

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;			
		endwhile; // End of the loop.
		?>
		
	</div><!-- #primary -->
<?php do_action( 'airdrop_wrapper_close' ); ?>
<?php
get_footer();
