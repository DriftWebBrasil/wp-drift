<?php 
/**
 * Template Name: Blog
 */
get_header(); 

/**
 * Variáveis
 */
$maximo_de_posts   = 8;
$ordem             = 'DESC';
$ordenar_por       = 'date';

$featured_query = new WP_Query(array(
    'post_type' => 'post',
    'meta_query'=> array(
        array(
            'key' => 'is_blog_post_featured', 
            'compare' => '=',
            'value' => 'true',
        )
    ),
    'meta_key' => 'is_blog_post_featured',
));

    
if($featured_query->posts != null && count($featured_query->posts) > 0) {
    $featured = $featured_query;
} else {
    $featured = new WP_Query(array(
        "posts_per_page" => 1
    ));
}

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

$featured_post = array();
?>



<section class="blog-container <?= ($paged != 1) ? 'paged' : '' ?>">
    <div class="main-grid">
        <div class="blog-menu">
            <nav class="blog-menu-navigation">
                <?php echo breadcrumbPadrao(true, array(), array(), get_the_title()); ?> 
            </nav>
        </div>
        <div class="blog-featured-container">
            <?php 
                while($featured->have_posts()) : 
                    $featured->the_post();
                    $featured_post[0] = get_the_ID(); 
                    $featured_thumb = get_the_post_thumbnail_url();
                    $featured_thumb = empty($featured_thumb) ? THEME_DIR . '/assets/img/padrao-post.png' : $featured_thumb;
            ?>
                <div class="blog-featured-card">
                    <div class="feat-card-image-box">
                        <div class="feat-card-image" style="background-image: url('<?= $featured_thumb ?>')">
                        </div>    
                        <a href="<?php the_permalink() ?>" class="feat-card-image-link"></a>
                    </div>
                    <div class="feat-card-texts">
                        <div class="feat-card-tag">
                            <span><?= get_first_category(get_the_category()); ?></span>
                        </div>
                        <div class="feat-card-title">
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <div class="feat-card-date">
                            <span><?php the_date(); ?></span>
                        </div>
                        <div class="feat-card-excerpt">
                            <p><?php the_excerpt(); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <div class="blog-posts-container-padrao">
            <?php 
        
                $query = new WP_Query( array( 
                    'post__not_in'      => $featured_post,
                    'post_type'         => 'post',
                    'posts_per_page'    => $maximo_de_posts,
                    'order'             => $ordem,
                    'orderby'           => $ordenar_por,
                    'paged'             => $paged
                ) ); 

                $paginacao = array(
                    'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                    'total'        => $query->max_num_pages,
                    'current'      => max( 1, get_query_var( 'paged' ) ),
                    'format'       => '?paged=%#%',
                    'show_all'     => false,
                    'type'         => 'plain',
                    'end_size'     => 2,
                    'mid_size'     => 1,
                    'add_args'     => false,
                    'prev_text' => '<img class="pagination-prev" src="'.THEME_DIR.'/assets/img/seta.png">',
                    'next_text' => '<img class="pagination-next" src="'.THEME_DIR.'/assets/img/seta.png">'            
                ); 

                $apagadoAnterior = get_previous_posts_link( 'Anterior', $query->max_num_pages ) ? '' : 'paginacao-apagada';
                $apagadoProximo = get_next_posts_link( 'Proximo', $query->max_num_pages ) ? '' : 'paginacao-apagada';
            ?>
            <div class="blog-posts-header">
                <div class="blog-posts-title">
                    <h3>Todas as notícias</h3>
                </div>
                <div class="blog-search">
                    <?php get_search_form(); ?>
                </div>
            </div>
            <div class="blog-posts-pagination mobile">
                <div class="pagination-numbers">
                    <?= paginate_links( $paginacao ); ?>
                </div>
            </div>
            <div class="blog-posts-loop">
                <?php 
                    if ( $query->have_posts() ):
                        while( $query->have_posts() ): $query->the_post();
                        /**
                         * Início do loop dos posts padrões
                         */

                            $post_thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                            $post_thumbnail = empty($post_thumbnail) ? THEME_DIR . '/assets/img/padrao-post-peq.png' : $post_thumbnail ;
                    

                        ?>
                            <div class="blog-card-padrao">
                                <div class="blog-card-image-box">
                                    <div class="blog-card-image" style="background-image: url('<?= $post_thumbnail ?>')"></div>
                                    <a href="<?php the_permalink() ?>" class="block-card-image-link"></a>
                                </div>
                                <div class="blog-card-content">
                                    <div class="card-title">
                                        <h4><?php the_title() ?></h4>
                                    </div>
                                    <div class="card-tag">
                                        <span><?= get_first_category(get_the_category()); ?></span>
                                    </div>
                                    <div class="card-time">
                                        <img src="<?= THEME_DIR . '/assets/img/relogio.png'?>" alt="Ícone Relógio">
                                        <!-- TODO - Tornar Dinâmico -->
                                        <span><?= get_post_meta( get_the_ID(), 'numero_palavra', true )?> min de leitura</span>  
                                    </div>
                                    <div class="card-link">
                                        <a href="<?php the_permalink(); ?>" class="ler-mais">Ver mais ></a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile;
                    else:
                    echo "<p>Sem posts cadastrados.</p>";
                endif; ?>
            </div> 
            <div class="blog-posts-pagination">
                <div class="pagination-numbers">
                    <?= paginate_links( $paginacao ); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer();



