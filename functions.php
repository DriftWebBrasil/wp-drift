<?php	

require_once 'theme-config.php';


function bloqueia_em_construcao() {
    if ( EM_BREVE && !is_user_logged_in() && $GLOBALS['pagenow'] != 'wp-login.php' ) {
        require 'em-breve.php';
    }
}
add_action( 'template_redirect', 'bloqueia_em_construcao' );


/**
 * Preparações iniciais do tema.
 */
add_filter( 'auto_update_theme', '__return_false' );

add_theme_support( 'menus' );
add_theme_support( 'post-thumbnails' ); 
add_theme_support( 'title-tag' ); 
add_theme_support( 'custom-logo' );
wp_create_nav_menu( 'Menu Principal' );
show_admin_bar( false );

if ( SUPORTE_WOOCOMMERCE ) {
    add_theme_support( 'woocommerce' );
}

if ( SUPORTE_PWA ) {
    require_once 'PWA/class-Drift_PWA.php';
}

if( VAGAS_PADRAO ) {
    require_once 'functions/vagas-functions.php';
}


/**
 * Altera na área admin 
 * o title das páginas
 */
function title_admin( $admin_title, $title ) {
    return get_bloginfo( 'name' ) .' - '. $title;
}
add_filter( 'admin_title', 'title_admin', 10, 2 );


/**
 * Adiciona suporte ao LESS
 */
if ( !is_admin() )
    require_once( dirname( __FILE__ ) . '/lib/wp-less/wp-less.php' );


/**
 * Carrega os shortcodes criados na pasta
 * shortcodes. Não são carregados na área admin
 */
function carrega_shortcodes() {

    if ( is_admin() )
        return;

    $arquivos = glob( dirname( __FILE__ ) . '/shortcodes/*.php' );

    foreach( $arquivos as $arquivo ) {
        include $arquivo;
    }
}
add_action( 'init', 'carrega_shortcodes' );


/**
 * Função que da print_r 
 * com <pre> automatico
 * 
 * @param mixed $print - objeto a ser printado
 */
if ( ! function_exists( 'pre' ) ) {
    function pre( $print ) {
        echo '<pre>';
            print_r( $print );
        echo '</pre>';
    }
}

// function dft_debug() {

//     pre(get_option('vagas_form_ex' ));
// }

// add_action( 'init', 'dft_debug' );



/**
 * Registrando sidebars nos widgets
 */
function dft_register_sidebars() {
    register_sidebar(array(
        'name'        => 'Blog Sidebar',
        'id'          => 'sidebar-blog',
        'description' => 'Sidebar visível apenas na página de blog.'
    ));

    register_sidebar(array(
        'name'        => 'Single Post Sidebar',
        'id'          => 'sidebar-singlepost',
        'description' => 'Sidebar visível apenas no single post.'
    ));
}
add_action( 'widgets_init', 'dft_register_sidebars' );


/**
 * Adicionando classes extras no body, 
 * as categorias do post.
 */
function dft_classes_extras_body( $classes ) {
    global $post;
    $cats = get_the_category();

    foreach( $cats as $cat ):
        $classes[] = $cat->slug;
    endforeach;

    $classes[] = $post->post_name;

    return $classes;
}
add_filter( 'body_class', 'dft_classes_extras_body' );


/**
 * Retorna o código do analytics, o ID deve
 * ser definido no theme-config.php
 */
function dft_analytics_script() {

    if ( !defined( 'ID_ANALYTICS' ) || !ID_ANALYTICS )
        return; ?>
        
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= ID_ANALYTICS ?>"></script>
    <script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '<?= ID_ANALYTICS ?>');</script>
<?php
}


/**
 * 
 *  CONFIGURAÇÕES PARA BLOG
 * 
 * ********************** IMPORTANTE **********************
 * 
 *  Para a paginação de posts funcionar na pagina 'search',
 * deverá mudar no painel em 'Configurações' > 'Leitura':
 *  • As páginas do blog mostram no máximo = 8 posts
 *  • Os feeds RSS mostram os últimos = 8 posts
 * 
 * ********************************************************
 * 
 * Adiciona os estilos e scripts para o funcionamento do blog,
 * do shorcode de Principais Noticias, página do post e página
 * de busca de posts.
 * 
 * Desativa scripts desnecessários em algumas páginas.
 * 
 * O script de emoji só será carregado se a global
 * BLOG estiver true e se for um single-post. Em
 * post types criados e páginas não será carregado.
 * 
 * 
 * 
 * 
 * 
 */

if ( !BLOG ) {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
} else {
    add_action( 'wp_enqueue_scripts', 'desativa_script_emojis' );
    add_action('wp_enqueue_scripts', 'dft_adiciona_blog_style');
    add_action( 'admin_enqueue_scripts', 'blog_admin_files' );
    add_filter('excerpt_more', 'custom_ler_mais');
    add_filter('get_search_form', 'custom_search_form');
    add_action( 'save_post', 'contagem_palavras' );
    add_filter( 'media_view_settings', 'theme_gallery_defaults' );

    // Configurações para Post em Destaque
    if( !get_current_post_type() == 'post' ) {

        // Adiciona coluna de escolha de posts em destaque
        add_action( 'wp_ajax_nopriv_setPostFeatured', 'setPostFeatured', 10 );
        add_action( 'wp_ajax_setPostFeatured', 'setPostFeatured' );
        add_filter( 'manage_posts_columns', 'posts_column_post' );
        add_action( 'manage_posts_custom_column', 'posts_custom_column_post', 5, 2 );

        // Adiciona coluna de visualizações dos posts
        add_filter( 'manage_posts_columns', 'posts_column_views' );
        add_action( 'manage_posts_custom_column', 'posts_custom_column_views', 5, 2 );    
    }

    adicionar_meta_boxes();
}

function theme_gallery_defaults( $settings ) {
    $settings['galleryDefaults']['link'] = 'file';
    $settings['galleryDefaults']['columns'] = 3;
    $settings['galleryDefaults']['size'] = 'thumbnail';
    return $settings;
}

function blog_admin_files() {
    wp_enqueue_script( 'dft-blog-js-admin', THEME_DIR . '/assets/js/blog-admin.js', array( 'jquery' ) );    
    wp_localize_script( 'dft-blog-js-admin', 'ajax_slide2', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('dft_blog-') ) );
    wp_enqueue_style( 'dft-blog-style-admin', THEME_DIR . '/assets/css/blog-style-admin.css' );
}

function adicionar_meta_boxes() {
    add_action('add_meta_boxes', ['PostDestaques_Meta_Box', 'add']);
    add_action('save_post', ['PostDestaques_Meta_Box', 'save']);    
}

function desativa_script_emojis() {
    if ( is_page() || !is_singular( 'post' ) ) {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
    }
}

function dft_adiciona_blog_style() {
    wp_enqueue_style('dft-blog-global-style', THEME_DIR . '/blog-global.less');
    wp_enqueue_script('dft-blog-scripts', THEME_DIR . '/assets/js/blog-custom.js');
    if(is_page_template('templates/blog.php')) {
        wp_enqueue_style('dft-blog-style', THEME_DIR . '/templates/blog.less');
    }
}

function custom_ler_mais($more) {
    return '<a href='. get_permalink(get_the_ID()) .' class="feat-card-excerpt-more">Ler Mais</a>';
}

function custom_search_form($form) {
        return '<form action="' . get_option('home') . '/" method="get" accept-charset="utf-8" id="searchform" role="search">
                    <div class="form-wrapper">
                        <input type="text" name="s" id="s" class="blog-posts-search-input" value="' . attribute_escape(apply_filters('the_search_query', get_search_query())) . '" />
                        <button type="submit" id="searchsubmit" class="blog-posts-search-icon" value="" ></button>
                        <button type="submit" id="searchmobile" class="blog-posts-search-mobile" value="" >Procurar</button>
                    </div>
                </form>';
}

function contagem_palavras( $postID ) {
    $count_key = 'numero_palavra';
    $minutes = get_post_meta( $postID, $count_key, true );
    $post = get_post( $postID );
    $words = str_word_count( strip_tags( $post->post_content ) );
    $minutes = floor( $words / 200 );
    if( $minutes == 0 ){
        delete_post_meta( $postID, $count_key );
        add_post_meta( $postID, $count_key, 0 );
    }else{
        update_post_meta( $postID, $count_key, $minutes );
    }
}

function breadcrumbPadrao( $home = true, $caminho = array(), $categorias = array(), $atual = '' ){
    $html = '<div class="breadcrumb">';
    $divisor = '<span class="divisor-breadcrumb">></span>';
    if($home){
        $html .= '<a class="bread-home" href="'.SITEURL.'"><span>Home</span></a>'.$divisor;
    }

    $num_caminho = count($caminho);
    $num_categorias = count($categorias);

    $counter = 1;
    foreach($caminho as $page=>$link){
        $html .= '<a class="outros-bread" href="'.$link.'">'.$page.'</a>';
        if(!($counter == $num_caminho && $num_categorias == 0)) {
            $html .= $divisor;
        }
        $counter++;
    }

    foreach($categorias as $key => $categoria) {
        $html .= $categoria;
        if($key < ($num_categorias - 1)) {
            $html .= $divisor;
        }
    }
    $html .= '<span class="pagina-atual-breadcrumb">'.$atual.'</span>';
    $html .= '</div>';
    return $html;
}

abstract class PostDestaques_Meta_Box {

    public static function add()
    {
        $screens = ['post'];
        foreach ($screens as $screen) {
            add_meta_box(
                'post_featured_box_id',   // Unique ID
                'Opções do Post',      // Box title
                [self::class, 'html'],   // Content callback, must be of type callable
                $screen,                  // Post type
                'side'
            );
        }
    }
 
    public static function save($post_id)
    {
        if (array_key_exists('post-principal', $_POST)) {
            update_post_meta(
                $post_id,
                'is_blog_post_featured',
                $_POST['post-principal']
            );

            if(array_key_exists('post-principal-atual', $_POST)) {
                delete_post_meta($_POST['post-principal-atual'], 'is_blog_post_featured');
            }
        } else {
            delete_post_meta($post_id, 'is_blog_post_featured');
        }
    }
 
    public static function html($post)
    {
        $value = get_post_meta($post->ID, 'is_blog_post_featured', true);
        $value = ($value != null) ? true : false ;

        $query = new WP_Query(array(
            'post_type' => 'post',
            'meta_query'=> array(
                array(
                    'key' => 'is_blog_post_featured', // this key will change!
                    'compare' => '=',
                    'value' =>  'true',
                )
            ),
            'meta_key' => 'is_blog_post_featured',
        ));

        $post_principal_atual_ID = 0;
        $post_principal_atual_titulo = "";

        if($query->posts != null && count($query->posts) > 0) {
            foreach($query->posts as $p) { 
                $post_principal_atual_ID = $p->ID; 
                $post_principal_atual_titulo = $p->post_title;
            }
        }

        ?>
            <?php if($post_principal_atual_ID != 0 && $post_principal_atual_ID != null && $post_principal_atual_ID != $post->ID) {
                echo '<style>.meta-box-alert {background: #fff3cd;color: #856404;padding: 16px;line-height: 1.1em; border-radius: 4px; margin-top: 8px;}</style>';
                echo '<div class="meta-box-alert">O post "'. $post_principal_atual_titulo . '" está escolhido atualmente como destaque.</div>';
                echo '<input type="hidden" name="post-principal-atual" value="' . $post_principal_atual_ID . '">';
            } ?>
            <h4>Aparecerá na página principal como post em destaque?</h4>
            <input type="checkbox" name="post-principal" value="true" <?= is_checked($value, true)?>>
            <label for="post-principal">Sim</label>
        <?php
    }
}

function get_first_category($categories) {
    if(!empty($categories) && $categories[0]->cat_name != "Uncategorized") {
        return $categories[0]->cat_name;
    }
    return "Sem Categoria";
}

function is_checked($value, $compared) {
    return ($value === $compared) ? ' checked ' : '';
}

/**
 * If post type is post add views count column
 */
if( !get_current_post_type() == 'post' ){
    function setPostViews( $postID ) {
        $countKey = 'post_views_count';
        $count = get_post_meta($postID, $countKey, true);
        if($count==''){
            $count = 0;
            delete_post_meta($postID, $countKey);
            add_post_meta($postID, $countKey, '0');
        }elseif( is_single($postID) ){
            $count++;
            update_post_meta($postID, $countKey, $count);
            
        }
    }
    function getPostViews( $postID ){
        $countKey   = 'post_views_count';
        $count      = get_post_meta($postID, $countKey, true);
        if( $count == '' ){
            delete_post_meta($postID, $countKey);
            add_post_meta($postID, $countKey, '0');
            return "0 Vizualizações";
        }
        return $count." Vizualizações";
    }
    function posts_column_views( $defaults ) {
        $defaults['post_views'] = __( 'Visualizações' );
        return $defaults;
    }
    function posts_custom_column_views( $column_name, $id ) {
        if ( $column_name === 'post_views' ) {
            echo getPostViews( get_the_ID() );
        }
    }
}

/**
 * If post type is post add featured option column
 */
if( !get_current_post_type() == 'post' ) {

    function setPostFeatured(){
    
        if( !isset( $_POST['nonce'] ) ){
    
            wp_die();
    
        }
    
        // Check for nonce security
        $nonce = $_POST['nonce'];
    
        if ( ! wp_verify_nonce( $nonce, 'dft_blog-' ) ) {
    
            wp_die();
    
        } else {
    
            //check action
            if($_POST['action'] != 'setPostFeatured'){
    
                wp_die();
    
            }else{
                
                $preValue   = $_POST['value'];
                $post_id    = $_POST['id'];
                $key        = 'is_blog_post_featured';
                $id         = 0;
    
                if( isset($_POST['value']) && !empty($_POST['value']) ){
                    
                    $value = 'false';
                    delete_post_meta( $post_id, $key );
    
                } else {
    
                    $args = array(
                        'post_type'         => 'post',
                        'post_status'       => 'publish',
                        'order'             => 'ASC',
                        'orderby'           => 'meta_value_num',
                        'meta_key'          => 'is_blog_post_featured',
                        'posts_per_page'    => -1,
                        'meta_query'        => array( array(
                                                    'key'       => 'is_blog_post_featured',
                                                    'value'     => 'true',
                                                    'compare'   => '=',
                                                ) )
                    );
                
                    $featuredPosts  = new WP_Query( $args );
    
                    if( $featuredPosts->have_posts() && $featuredPosts->post_count >= 1 ){
                        $count = 0;
                        while( $featuredPosts->have_posts() ){
                            $featuredPosts->the_post();
                            if( $count == 0 ){                  
                                delete_post_meta( get_the_ID(), $key );
                                $id = get_the_ID();
                            }
                            $count++;
                        }
                    }
                    $value      = 'true';
                    update_post_meta( $post_id, $key, $value );
                }
    
                //return
                echo json_encode(array(
                    'status'    => 'success',
                    'change'    => $value,
                    'id'        => $id
                ));
    
                wp_die();
            }
        }
    }
    
    function getPostFeatured( $postID ){
        
        $key    = 'is_blog_post_featured';
        $value  = get_post_meta( $postID, $key, true ); 
        // delete_post_meta($postID, 'is_blog_post_featured');
    
        if( $value == 'true' ){
            
            return "<label class='switch btn-posts_toggle'>
                        <input name='btnBom' value='true' type='checkbox' checked >
                        <span class='slider round'></span>
                    </label>";
    
        } 
        
        return "<label class='switch btn-posts_toggle'>
                    <input name='btnBom' value='false' type='checkbox' >
                    <span class='slider round'></span>
                </label>";
    
    }

    function posts_column_post( $defaults ) {

        $defaults['posts_toggle'] = __( 'Destacar Post' );
        return $defaults;
    }
    
    function posts_custom_column_post( $column_name, $id ) {
        if ( $column_name === 'posts_toggle' ) {
    
            echo getPostFeatured( get_the_ID() );
    
        }
    }

}


/**
 * Get current post type in admin area
 */
function get_current_post_type() {
    
    global $post, $typenow, $current_screen;
    
    if ($post && $post->post_type) return $post->post_type;
    
    elseif($typenow) return $typenow;
    
    elseif($current_screen && $current_screen->post_type) return $current_screen->post_type;
    
    elseif(isset($_REQUEST['post_type'])) return sanitize_key($_REQUEST['post_type']);
    
    return null;
}


/**
 * 
 *  FIM DAS CONFIGURAÇÕES DO BLOG
 * 
 */

/**
 * Desativa o script wp-embed.min.js
 */
function desativa_embed() {
    wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_enqueue_scripts', 'desativa_embed', 99 );


/**
 * Carrega Scripts e Styles
*/
function dft_enqueue_scripts() {

    // JS
    wp_enqueue_script( 'dft-custom-js', THEME_DIR . '/assets/js/custom.js', array( 'jquery' ), false, true );
    wp_enqueue_script( 'dft-sticky', THEME_DIR . '/assets/js/dft-sticky.js', array( 'jquery' ), false, true );

    // CSS
    wp_enqueue_style( 'dft-main-less', THEME_DIR . '/style.less' );
    wp_enqueue_style( 'dft-main-css', THEME_DIR . '/style.css' );

    /**
     * LIBS
     */
    if ( SLIM_SELECT ) {
        wp_enqueue_style( 'slim-select-css', 'https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.18.6/slimselect.min.css' );
        wp_enqueue_script( 'slim-select-js', 'https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.18.6/slimselect.min.js' );
    }

    if ( MODAL_VIDEO ) {
        wp_enqueue_script( 'dft-modal-video-js', THEME_DIR. '/lib/modal-video/jquery-modal-video.min.js', array( 'jquery' ) );
        wp_enqueue_style( 'dft-modal-video-css', THEME_DIR. '/lib/modal-video/modal-video.min.css' );
    }

    if ( IMAGE_LIGHTBOX || BLOG ) {
        wp_enqueue_script( 'lightbox-js', THEME_DIR . '/lib/lightbox/lightbox.min.js', array( 'jquery' ) );
        wp_enqueue_style( 'lightbox-css', THEME_DIR . '/lib/lightbox/lightbox.min.css' );
    }

    if ( SLICK_SLIDER ) {
        wp_enqueue_style( 'slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css' );
        wp_enqueue_script( 'slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js' );
    }
}

function dft_style_admin() {
    wp_enqueue_style( 'dft-style-admin', THEME_DIR . '/assets/css/style-admin.css' );
}
add_action( 'admin_enqueue_scripts', 'dft_style_admin' );
add_action( 'wp_enqueue_scripts', 'dft_enqueue_scripts' );


if ( is_admin() ) {
    function ordena_post_types_dashboard( $wp_query ) {
        if ( is_admin() && !wp_doing_ajax() &&!isset( $_GET['orderby'] ) && $wp_query->query['post_type'] != 'acf-field' ) {
            $wp_query->set( 'orderby', 'date' );
            $wp_query->set( 'order', 'DESC' );
        }
    }
    add_filter( 'pre_get_posts', 'ordena_post_types_dashboard' );
 }

/* Função para pegar o Youtube para diferentes urls */
function get_the_youtube_id($url) {
    $matches = array();
    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
    return trim(end($matches));
}

