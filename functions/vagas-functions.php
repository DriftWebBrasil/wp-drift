<?php

abstract class Vagas_Configuration {

    public static function init() {

        add_action("init", [self::class, 'createPostType']);

        // Adicionar a Metabox para preencher as descrições da vaga
        add_action('add_meta_boxes', [ 'Vagas_Meta_Box', 'addMetaBox' ]);
        add_action('save_post_vagas', [ 'Vagas_Meta_Box', 'saveMetabox']);   
        
        if( class_exists("GFAPI") ) {
            $form_id = absint(get_option('vagas_form_id'));
            $exists = GFAPI::get_form($form_id);
    
            if( !$exists ) {
                add_action("init", [ self::class, 'createForm' ]);
            } else {
                add_filter( 'gform_pre_render_' . $form_id, [ self::class, 'populateVagasSelect']  );
                add_filter( 'gform_pre_validation_' . $form_id, [ self::class, 'populateVagasSelect']  );
                add_filter( 'gform_pre_submission_filter_' . $form_id, [ self::class, 'populateVagasSelect']  );
                add_filter( 'gform_admin_pre_render_' . $form_id, [ self::class, 'populateVagasSelect'] );     
                add_action( 'wp_enqueue_scripts', [self::class, 'enqueueStaticFiles' ]);
            }
    
            // Mudar o input de upload de arquivo
            add_filter( 'gform_field_content', [self::class, 'changeFileInputLabel'], 10, 2 );    
        }
    }

    public static function createForm() {

        $fields = array(
            array(
                'id'                => 1,
                'label'             => 'Vaga desejada',
                'type'              => 'select',
                'isRequired'        => true,
                'cssClass'          => 'select-vagas',
                'allowsPrepopulate' => true
            ),
            array(
                'id'            => 2,
                'label'         => 'Nome',
                'type'          => 'text',
                'isRequired'    => true,
            ),
            array(
                'id'            => 3,
                'label'         => 'E-mail',
                'type'          => 'text',
                'isRequired'    => true,
                'cssClass'      => 'email'
            ),
            array(
                'id'            => 4,
                'label'         => 'Telefone',
                'type'          => 'text',
                'isRequired'    => true,
                'cssClass'      => 'email'
            ),
            array(
                'id'            => 5,
                'label'         => 'Anexar Currículo',
                'type'          => 'fileupload',
                'isRequired'    => true,
            ),
            array(
                'id'            => 6,
                'label'         => 'Bloco HTML',
                'content'       => 'Formatos permitidos: pdf, doc, docx, jpg, png.',
                'type'          => 'html',
            ),
            array(
                'id'            => 7,
                'label'         => 'Mensagem',
                'type'          => 'textarea',
                'isRequired'    => false,
            ),
        );

        $form = array(
            'title'                 => 'Vagas Disponíveis',
            'description'           => 'Para fazer parte do processo seletivo preencha os campos',
            'labelPlacement'        => 'top_label',
            'descriptionPlacement'  => 'above',
            'button'                => array(
                'type'      => 'text',
                'text'      => 'Enviar',
                'imageUrl'  => ''
            ),
            'fields'                => $fields
        );
        
        $id =  GFAPI::add_form($form);
        update_option( 'vagas_form_id', $id );

        add_filter( 'gform_pre_render_' . $id, [ self::class, 'populateVagasSelect']  );
        add_filter( 'gform_pre_validation_' . $id, [ self::class, 'populateVagasSelect']  );
        add_filter( 'gform_pre_submission_filter_' . $id, [ self::class, 'populateVagasSelect']  );
        add_filter( 'gform_admin_pre_render_' . $id, [ self::class, 'populateVagasSelect'] );                

    }

    public static function createPostType() {
        $labels_vagas = array(
            'name'               => __( 'Vagas de Trabalho' ),
            'singular_name'      => __( 'Vaga de Trabalho' ),
            'name_admin_bar'     => __( 'Vagas de Trabalho'),
            'add_new'            => __( 'Adicionar Vagas'),
            'add_new_item'       => __( 'Adicionar nova Vaga' ),
            'new_item'           => __( 'Nova Vaga' ),
            'edit_item'          => __( 'Editar Vaga' ),
            'view_item'          => __( 'Ver Vaga' ),
            'all_items'          => __( 'Todos' ),
        );

        $args_vagas = array(
            'labels'               => $labels_vagas,
            'public'               => true,
            'has_archive'          => false,
            'menu_position'        => 2,
            'menu_icon'            => 'dashicons-id-alt',
            'capability_type'      => 'page',
            'hierachical'          => true,
            'description'          => 'Adicione suas vagas aqui',
            'supports'             => array( 'title', 'custom-fields', 'page-attributes'),
            'rewrite'              => array('slug' => 'vagas'),
        );

        register_post_type( 'vagas', $args_vagas);
    }

    public static function changeFileInputLabel( $field_content, $field ) {
        if ( $field->type == 'fileupload' ) {

            return str_replace( '</label>', "<div class='full-label'><a class='label-btn'>Anexar</a><span class='nome-arquivo'>Nenhum arquivo selecionado</span></div></label>", $field_content );
        }
        return $field_content;
    }

    public static function populateVagasSelect( $form ) {

        foreach ( $form['fields'] as &$field ) {
    
            if ( $field->type != 'select' || strpos( $field->cssClass, 'select-vagas' ) === false ) {
                continue;
            }
    
            $query_vagas = new WP_Query(array(
                "post_type" => 'vagas',
                'post_status'   => 'publish',
                'posts_per_page'    => -1
            ));
    
            $choices = array();

            foreach ( $query_vagas->posts as $post ) {
                $choices[] = array( 'text' => $post->post_title, 'value' => $post->post_title );
            }
    
            $field->placeholder = 'Selecione uma vaga';
            $field->choices = $choices;
        }
    
        return $form;
    }   

    public static function enqueueStaticFiles($form_id) {
        $form_id = absint(get_option('vagas_form_id'));
        $exists = GFAPI::get_form($form_id);

        if( $exists && is_page_template( 'templates/trabalhe-conosco.php')  ) {
            wp_enqueue_style('dft-trabalhe-conosco-style', THEME_DIR . '/templates/trabalhe-conosco.less');
            wp_enqueue_script('dft-trabalhe-conosco-js', THEME_DIR . '/templates/trabalhe-conosco.js', array('jquery') );
            wp_localize_script( 'dft-trabalhe-conosco-js', 'site_info', array( 'form_id' => $form_id ) );
        }
    }
}

abstract class Vagas_Meta_Box {


    public static function addMetaBox() {
        add_meta_box(
            'informacoes_vaga',   
            'Informações da Vaga',     
            array('Vagas_Meta_Box', 'htmlMetabox'),  
            'vagas',
            'normal'              
        );
    }

    public static function saveMetabox($post_id) {

        if (array_key_exists('descricao', $_POST)) {

            update_post_meta(
                $post_id,
                'descricao_vaga',
                $_POST['descricao']
            );

        }
    }

    public static function htmlMetabox($post) {

        $descricao = get_post_meta($post->ID, 'descricao_vaga', true);

        ?>
            <label for="descricao">Descrição</label><br>
            <textarea rows="4" maxlength="300" name="descricao" style="width: 100%"><?= $descricao ?></textarea>
        <?php
    }

}

Vagas_Configuration::init();