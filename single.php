<?php 

/**
 *  Template usado para o single post
 */
get_header();
setPostViews( get_the_ID() );

while ( have_posts() ) : the_post(); ?>
    
    <div class="menu-search-mobile">
        <div class="menu-search-mobile-wrapper">
            <div class="blog-search">
                <div class="post-aside-title-padrao">
                    <h3>O que você quer ler?</h3>
                </div>
                <?php get_search_form(); ?>
            </div>
            <div class="post-featured-loop-mobile">
                <?php $args = array(
                            'post__not_in'      => array(get_the_ID()),
                            'post_type'         => 'post',
                            'post_status'       => 'publish',
                            'order'             => 'DESC',
                            'orderby'           => 'meta_value_num',
                            'meta_key'          => 'post_views_count',
                            'posts_per_page'    => 3
                        ); 
                
                        $query = new WP_Query($args);
                        
                        if($query->have_posts()) : 
                            while($query->have_posts()) : $query->the_post(); ?>

                                <div class="post-aside-card">
                                    <div class="post-aside-image-wrapper">
                                        <div class="post-aside-image" style="background-image: url('<?= get_the_post_thumbnail_url(); ?>')"></div>
                                        <a href="<?= get_the_permalink() ?>"></a>
                                    </div>
                                    <div class="post-aside-text">
                                        <div class="post-aside-text-title">
                                            <h4><?= get_the_title(); ?></h4>
                                        </div>
                                        <div class="post-aside-text-link">
                                            <a href="<?= get_the_permalink(); ?>">Ler ></a>
                                        </div>
                                    </div>
                                </div>

                <? endwhile; endif; wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
    <div class="menu-search-icon">
        <div class="icon"></div>
    </div>
    <section id="post-container">
        <div class="main-grid">
            <div class="blog-menu">
                <nav class="blog-menu-navigation">
                    <?php
                        $category = get_the_category();

                        $cat_array = array();
                        if(!empty($category) && $category[0]->cat_name != 'Uncategorized') {
                            
                            $last_category = end($category);
                            $category_parent_id = $last_category->category_parent;

                            $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
                            $cat_array = explode(',',$get_cat_parents);
                        } 

                        echo breadcrumbPadrao(true, array("Nosso Blog" => SITEURL . "/nosso-blog"), $cat_array, ''); 

                    ?> 
                </nav>
            </div>
            <div class="post-title">
                <h1><?php the_title(); ?></h1>
            </div>
            <div class="post-info">
                <span class="post-info-tag"><?= get_first_category(get_the_category());?></span> <!-- TODO: Tornar dinâmico-->
                <span class="post-info-date"><?php the_date(); ?></span>
            </div>
            <div class="post-body">
                <div class="post-content">
                    <div class="post-content-image-wrapper">
                        <div class="post-content-image" style="background-image: url('<?= get_the_post_thumbnail_url() ?>'); "></div>
                    </div>
                    <div class="post-content-text">
                        <?php the_content() ?>
                    </div>
                    <div class="post-content-social">
                        <h4>Compartilhe</h4>
                        <div id="share-facebook" class="social-link">
                            <a href="https://www.facebook.com/sharer.php?u=<?php the_permalink() ?>" target="_blank"></a>
                        </div>
                        <div id="share-linkedin" class="social-link">
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= get_the_permalink(); ?>&title=<?= get_the_title(); ?>" target="_blank"></a>
                        </div>
                    </div>
                    <div class="post-content-link">
                        <?php             
                            $page_blog_template_query = get_pages(array(
                                'meta_key' => '_wp_page_template',
                                'meta_value' => 'templates/blog.php'
                            ));
                            
                            $page_blog_template = end($page_blog_template_query); ?>

                        <a href="<?= $page_blog_template->guid ?>" class="botao-padrao-blog">Voltar ao blog</a>
                    </div>
                </div>
                <div class="post-aside">
                    <div class="blog-search">
                        <div class="post-aside-title-padrao">
                            <h3>O que você quer ler?</h3>
                        </div>
                        <?php get_search_form(); ?>
                    </div>
                    <div class="post-aside-featured">
                        <div class="post-aside-title-padrao">
                            <h3>Mais lidas</h3>
                        </div>
                        <div class="posts-featured-loop">
                            <?php 
                            if($query->have_posts()) : 
                                while($query->have_posts()) : $query->the_post(); ?>
                                    <div class="post-aside-card">
                                        <div class="post-aside-image-wrapper">
                                            <div class="post-aside-image" style="background-image: url('<?= get_the_post_thumbnail_url(); ?>')"></div>
                                            <a href="<?= get_the_permalink() ?>"></a>
                                        </div>
                                        <div class="post-aside-text">
                                            <div class="post-aside-text-title">
                                                <h4><?= get_the_title(); ?></h4>
                                            </div>
                                            <div class="post-aside-text-link">
                                                <a href="<?= get_the_permalink(); ?>">Ler ></a>
                                            </div>
                                        </div>
                                    </div>
                            <? endwhile; endif; wp_reset_postdata(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endwhile;

get_footer();
