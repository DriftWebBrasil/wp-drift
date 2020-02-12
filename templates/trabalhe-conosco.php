<?php 
/**
 * Template Name: Trabalhe Conosco - Padrão
 */
get_header(); 

if( class_exists("GFAPI") ) :

    $form_id  = absint( get_option( 'vagas_form_id' ) );
    $exists = GFAPI::get_form($form_id);

    if( $exists ) : 
        if(have_posts()) :
            while(have_posts()) : the_post();

            $args_vagas = array(
                'post_type' => 'vagas',
                'post_status' => 'publish',
                'orderby'   => 'DATE',
                'order'     => 'desc'
            );

            $query_vagas = new WP_Query($args_vagas);
            $possui_vagas = ($query_vagas->have_posts() == true); 

            $titulo = "Vagas disponíveis";
    ?>

    <div class="trabalhe-content">
        <div class="main-grid">
            <div class="trabalhe-wrapper">
                <section id="vagas" class="<?= $possui_vagas ? '' : 'sem-vagas' ?>">
                    <div class="vagas-title">
                        <h3><?= $titulo ?></h3>
                    </div>
                    <?php                         
                        if($query_vagas->have_posts()) : 
                            echo '<ul class="vagas-list">';

                            while($query_vagas->have_posts()) : $query_vagas->the_post();

                                $nome = get_the_title();
                                $desc = get_post_meta(get_the_ID(), "descricao_vaga", true);
                            ?>
                                <li>
                                    <div class="accordion">
                                        <div class="accordion-wrapper">
                                            <a class="accordion-clickable" >
                                                <p class="nome-da-vaga"><?= $nome; ?></p>
                                                <p>    
                                                    <span class="data"><?= get_the_date("d/m/Y"); ?></span>
                                                    <button type="button" class="vagas-btn" onclick="jQuery('#input_' + <?= $form_id ?>  +  '_1').val('<?= $nome ?>')">Quero me canditar</button>
                                                </p>
                                                
                                            </a>
                                            <div class="accordion-hidden">
                                                <p><?= $desc ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php
                            endwhile;

                            echo '</ul>';
                            wp_reset_postdata(  );
                        else : 
                            ?>
                            <div class="vagas-list">
                                <div class="sem-vagas-alert">
                                    <p>Sem vagas em aberto</p>
                                </div>
                            </div>

                            <?php

                        endif;
                    ?>
                </section>
                <section id="formulario">
                    <?= do_shortcode( '[gravityforms id=' . $form_id . ' ajax="true" description="true" title="false"]') ?>
                </section>
            </div>
        </div>
    </div>

    <?php
            endwhile;
        endif;
    else : 
        echo "<div style='padding: 16px; font-size: 20px; line-height: 22px'>Erro: O formulário de Vagas não existe</div>";
    endif;
else : 
    echo "<div style='padding: 16px; font-size: 20px; line-height: 22px'>Você precisa instalar o GravityForms para executar essa página</div>";
endif;

get_footer();