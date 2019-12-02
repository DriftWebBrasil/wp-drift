<?php 

/**
 * Loop de principais posts e link para o blog
 * 
 * Exemplos:
 * [principais-posts colunas="4" texto_botao="Ver mais"]
 */
function principais_posts($atts) {
    /** 
     * Variável que recebe o numero de colunas
     * @name $colunas
     * @default 4
    */
    $colunas = isset($atts['colunas']) ? $atts['colunas'] : 4;
    $colunas = $colunas < 4 ? $colunas : 4;

    /**
     * Variável que contém o texto a ser visualizado no título
     * @name $titulo
     */
    $titulo = isset($atts['titulo']) ? $atts['titulo'] : "Últimas Notícias";
    
    /**
     * Variável que define se será exibido um botão de ver mais posts
     * @name $botao_ver_mais
     */
    $botao_ver_mais = isset($atts['botao_ver_mais']) ? $atts['botao_ver_mais'] : true;

    /**
     * Variavel que define o texto do Botão de ver mais posts
     * @name $texto_botao
     */
    $texto_botao = isset($atts['texto_botao']) ? $atts['texto_botao'] : "Ver todas";

    /**
     * Variável que define o link do Blog. Por padrão, é o slug "blog"
     */
    $blog_url = isset($atts['blog_url']) ? $atts['blog_url'] : SITEURL . '/blog';
    
    $args =  array( 
        'post_type'         => 'post',
        'posts_per_page'    => $colunas,
        'orderby'           => 'date',
        'order'             => 'DESC'
    );

    $query = new WP_Query($args); 

    $loop_html_template = "";
    while($query->have_posts()) : $query->the_post();
        $loop_html_template .= "<div class='blog-card col-1-" . $colunas ."'>
        <div class='blog-card-image-box'>
            <div class='blog-card-image' style='background-image: url(". get_the_post_thumbnail_url(get_the_ID()) .")'></div>
            <a href='" . get_the_permalink() ."' class='block-card-image-link'></a>
        </div>
        <div class='blog-card-content'>
            <div class='card-title'>
                <h4>" . get_the_title() ."</h4>
            </div>
            <div class='card-tag'>
                <span>" .  get_first_category(get_the_category()) . "</span>
            </div>
            <div class='card-time'>
                <img src='". THEME_DIR ."/assets/img/relogio.png' alt='Ícone Relógio'>
                <!-- TODO - Tornar Dinâmico -->
                <span>". get_post_meta( get_the_ID(), 'numero_palavra', true ) ." min de leitura</span>  
            </div>
            <div class='card-link'>
                <a href='". get_the_permalink() ."' class='ler-mais'>Ver mais ></a>
            </div>
        </div>
    </div>";
    endwhile;

    return "<div class='short-principais-posts'>
                <div class='posts-container'>
                    <div class='posts-header'>
                        <h2>".$titulo."</h2>
                    </div>
                    <div class='posts-loop'>"
                       . $loop_html_template .
                    "</div>"  
                       . ($botao_ver_mais ? "<div class='posts-link-wrapper'><a href='" . $blog_url ."' class='botao-padrao-blog'>".$texto_botao."</a></div>" : "") .   
                "</div>
            </div>";
}
add_shortcode("principais-noticias", "principais_posts");
