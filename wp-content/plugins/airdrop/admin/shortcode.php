<?php
add_action( 'init', 'airdrop_register_shortcode' );
function airdrop_register_shortcode() {

    add_shortcode( 'airdrops-manager', 'airdrop_shortcode' );
    function airdrop_shortcode( $atts, $content ) {
    global $post;
    ob_start();
 
    // define attributes and their defaults
    extract( shortcode_atts( array (
        'order' => 'date',
        'num' => 4,
    ), $atts ) );
 
    // define query parameters based on attributes
    $options = array(
        'post_type' => 'airdrops-manager',
        'posts_per_page' => $num,
        'post_status'     => 'publish',
        'order'             => $order,
        
    );

    $airdroplist = new WP_Query( $options );
    
    if ( $airdroplist->have_posts() )  { 
        echo '<div class="row">';
        while( $airdroplist->have_posts() ) {
            $airdroplist->the_post(); ?>
           <article id="post-<?php the_ID(); ?>" <?php post_class( 'airdrops-manager-item col' ); ?> >
        <div class="row">
            <div class="airdrops-manager-thumbnail-wrap col-12">
        <figure class="airdrops-manager-thumbnail">
        <?php if( has_post_thumbnail() ) {
            echo '<a rel="bookmark" href="'.esc_url( get_permalink() ) . '">';
            the_post_thumbnail( 'airdrops-manager-main-thumbnail' );
            echo '</a>';    
        }
         ?>
        </figure>
    </div>
        <div class="col-12">    
            <table class="airdrops-manager-single-summary" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td colspan="2"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title( '<h1 class="airdrops-manager-name">', '</h1>' ); echo '<span class="airdrops-manager-symbol">' . $airdrop_symbol =  get_post_meta( $post->ID, 'airdrop_symbol', true ) . '</span>'; ?></a></td>
                </tr>
            </table>
        </div>
    </div><!-- /.row -->
    </article>
        <?php } echo '</div>';
        $airdropshow = ob_get_clean();
        wp_reset_postdata();
        return $airdropshow;
    }
    }
}