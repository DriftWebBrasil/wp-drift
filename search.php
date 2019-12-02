<?php
    get_header();
    $current = get_query_var( 'paged' ) > 1 ? get_query_var('paged') : 1;
    $search = get_query_var('s');

    $articles = new WP_Query(array( 
        's' => $search,
        'posts_per_page'    => 8,
        'order'             => 'DESC',
        'orderby'           => 'date',
        'paged'             => $current
    ));

    $pagination = array(
        'base'         => @add_query_arg( 'paged', '%#%' ),
        'format'       => '',
        'total'        => $articles->max_num_pages,
        'current'      => $current,
        'show_all'     => false,
        'end_size'     => 2,
        'mid_size'     => 1,
        'type'         => 'plain',
        'prev_text' => '<img class="pagination-prev" src="'.THEME_DIR.'/assets/img/seta.png">',
        'next_text' => '<img class="pagination-next" src="'.THEME_DIR.'/assets/img/seta.png">'
    );

    if ( ! empty( $wp_query->query_vars['s'] ) )
        $pagination['add_args'] = array( 's' => get_query_var( 's' ) );

    ?>
    <section class="search-page-container">
        <div class="main-grid">
            <div class="blog-posts-container-padrao">
                <div class="blog-posts-header">
                    <div class="blog-posts-title">
                        <h3>Resultados da Busca</h3>
                    </div>
                    <div class="blog-search">
                        <?php get_search_form(); ?>
                    </div>
                </div>
                <div class="blog-posts-pagination mobile">
                    <div class="pagination-numbers">
                        <?= paginate_links($pagination); ?>
                    </div>
                </div>
                <div class="blog-posts-loop">

                    <?php if ( $articles->have_posts() ) : 
                        while ( $articles->have_posts() ) : $articles->the_post(); ?>
                                <div class="blog-card-padrao">
                                    <div class="blog-card-image-box">
                                        <div class="blog-card-image" style="background-image: url('<?= get_the_post_thumbnail_url(get_the_ID(), 'medium') ?>')"></div>
                                        <a href="<?php the_permalink() ?>" class="block-card-image-link"></a>
                                    </div>
                                    <div class="blog-card-content">
                                        <div class="card-title">
                                            <h4><?php the_title() ?></h4>
                                        </div>
                                        <div class="card-tag">
                                            <span><?= get_first_category(get_the_category());?></span>
                                        </div>
                                        <div class="card-time">
                                            <img src="<?= THEME_DIR . '/assets/img/relogio.png'?>" alt="Ícone Relógio">
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
                        <?= paginate_links($pagination); ?>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <?php  get_footer(); ?>