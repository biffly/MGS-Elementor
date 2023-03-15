<?php
if( !defined('ABSPATH') ){ exit; }

if( !class_exists('MGS_Post_Rate_Admin') ){
    class MGS_Post_Rate_Admin{
        public function __construct(){
            //post
            add_filter('manage_post_posts_columns', [$this, 'mgs_post_rate_add_admin_column']);
            add_action('manage_post_posts_custom_column' , [$this, 'mgs_post_rate_manage_admin_column'], 10, 2);
            //pages
            add_filter('manage_page_posts_columns', [$this, 'mgs_post_rate_add_admin_column']);
            add_action('manage_page_posts_custom_column' , [$this, 'mgs_post_rate_manage_admin_column'], 10, 2);

            //reset
            add_filter('post_row_actions', [$this, 'mgs_post_rate_post_row_actions'], 10, 2);
            add_filter('page_row_actions', [$this, 'mgs_post_rate_post_row_actions'], 10, 2);
            add_action('wp_ajax_mgs_post_rate_reset', [$this, 'mgs_post_rate_reset']);
            add_action('wp_ajax_mgs_post_rate_send_to_mail', [$this, 'mgs_post_rate_send_to_mail_callback']);
            add_action('wp_ajax_mgs_post_rate_download_csv', [$this, 'mgs_post_rate_download_csv_callback']);
            add_action('wp_ajax_mgs_post_rate_delete', [$this, 'mgs_post_rate_delete_callback']);
            add_action('admin_footer', [$this, 'mgs_post_rate_reset_script']);
        }

        public function mgs_post_rate_delete_callback(){
            global $wpdb;
            $t1 = MGS_Elementor_AddOns::$post_rate_tbl;
            $t2 = MGS_Elementor_AddOns::$post_rate_comments_tbl;
            $wpdb->delete(MGS_Elementor_AddOns::$post_rate_tbl, ['submit_id' => $_POST['submit_id']]);
            $wpdb->delete(MGS_Elementor_AddOns::$post_rate_comments_tbl, ['submit_id' => $_POST['submit_id']]);
            die();
        }

        public function mgs_post_rate_download_csv_callback(){
            global $wpdb;
            $t1 = MGS_Elementor_AddOns::$post_rate_tbl;
            $t2 = MGS_Elementor_AddOns::$post_rate_comments_tbl;
            $out = '';
            $submit_id = $_POST['submit_id'];
            $post_rate = $wpdb->get_row("SELECT * FROM $t1 WHERE submit_id = '$submit_id'");
            $fields = $wpdb->get_results("SELECT * FROM $t2 WHERE submit_id = '$submit_id'");

            $h = [
                '"VALORACION"', 
                '"FECHA"'
            ];
            $v = [
                '"'.$post_rate->post_rate.'"', 
                '"'.$post_rate->created_at_gmt.'"'
            ];

            foreach( $fields as $field ){
                array_push($h, '"'.strtoupper($field->key).'"');
                array_push($v, '"'.$field->value.'"');
            }

            $h = implode(',', $h).PHP_EOL;
            $v = implode(',', $v).PHP_EOL;
            echo $h.$v;
            die();
        }

        public function mgs_post_rate_send_to_mail_callback(){
            global $wpdb;
            $t1 = MGS_Elementor_AddOns::$post_rate_tbl;
            $t2 = MGS_Elementor_AddOns::$post_rate_comments_tbl;
            $out = '';
            $submit_id = $_POST['submit_id'];

            $post_rate = $wpdb->get_row("SELECT * FROM $t1 WHERE submit_id = '$submit_id'");
            $fields = $wpdb->get_results("SELECT * FROM $t2 WHERE submit_id = '$submit_id'");

            $out = '
                <table>
                    <tr>
                        <th>'.__('Valoración', 'mgs_elementor').'</th>
                        <td>'.$post_rate->post_rate.'</td>
                    </tr>
                    <tr>
                        <th>'.__('Fecha', 'mgs_elementor').'</th>
                        <td>'.$post_rate->created_at_gmt.'</td>
                    </tr>
            ';
            foreach( $fields as $field ){
                $out .= '
                    <tr>
                        <th>'.$field->key.'</th>
                        <td>'.$field->value.'</td>
                    </tr>
                ';
            }
            $out .= '
                </table>
            ';
            $headers = ['Content-Type: text/html; charset=UTF-8'];
            wp_mail($_POST['to'], $_POST['asunto'], $out, $headers);
            //echo $out;
            die();
        }

        public static function _post_rate_page_content(){
            global $wpdb;
            $t1 = MGS_Elementor_AddOns::$post_rate_tbl;
            $t2 = MGS_Elementor_AddOns::$post_rate_comments_tbl;
            if( isset($_GET['view']) && $_GET['view']!='' ){
                $post_id = $_GET['view'];
                /*
                $sql_post = "SELECT * FROM $t1 WHERE submit_id = '".$_GET['view']."'";
                $sql_comment = "SELECT * FROM $t2 WHERE submit_id = '".$_GET['view']."'";

                $valoracion = $wpdb->get_row($sql_post);
                $comment = $wpdb->get_results($sql_comment);
                */
                ?>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
                <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js"></script>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
                <div class="addon-paga-container">
                    <div class="inner">
                        <div class="mgs-post-rate-comment-view">
                            <h2><?php _e('Valoración', 'mgs_elementor')?> <small><?php echo get_the_title($_GET['view'])?></small></h2>
                            <a href="?page=mgs_elementor_admin&sec=post-rate&addon=post-rate" class="close_view"><span class="material-symbols-outlined">close</span></a>
                            <table class="display mgs-post-rate-view-comment-table" style="width:100%">
                                <tr>
                                    <th>
                                        <input type="checkbox" class="action-submits_id" value="">
                                    </th>
                                    <td></td>
                                </tr>
                                <?php
                                $sql = "SELECT $t2.submit_id, $t1.created_at_gmt, $t1.post_rate FROM $t1 INNER JOIN $t2 ON $t1.submit_id = $t2.submit_id WHERE $t1.post_id = $post_id GROUP BY $t2.submit_id";
                                $comentarios = $wpdb->get_results($sql);
                                foreach( $comentarios as $comentario ){
                                    $sql_comment = "SELECT * FROM $t2 WHERE submit_id = '".$comentario->submit_id."'";
                                    $fields = $wpdb->get_results($sql_comment);
                                ?>
                                <tr class="row-comment-content row-<?php echo $comentario->submit_id?>">
                                    <th>
                                        <div class="comment_actions">
                                            <input type="checkbox" name="submits_id[]" class="submits_id" value="<?php echo $comentario->submit_id?>">
                                            <a href="<?php echo $comentario->submit_id?>" class="view-details" data-submit_id="<?php echo $comentario->submit_id?>"><?php echo $comentario->created_at_gmt?></a>
                                        </div>
                                        <div class="mgs-post-rate-share-bar">
                                            <a href="#" class="tomail" data-post="<?php echo $_GET['view']?>" data-submit_id="<?php echo $comentario->submit_id?>"><span class="material-symbols-outlined">forward_to_inbox</span></a>
                                            <a href="#" class="tocsv" data-post="<?php echo $_GET['view']?>" data-submit_id="<?php echo $comentario->submit_id?>"><span class="material-symbols-outlined">export_notes</span></a>
                                            <a href="#" class="delete" data-post="<?php echo $_GET['view']?>" data-submit_id="<?php echo $comentario->submit_id?>"><span class="material-symbols-outlined">delete</span></a>
                                        </div>
                                    </th>
                                    <td>
                                        <table class="display mgs-post-rate-view-comment-table-detail" id="wrapper_detail_<?php echo $comentario->submit_id?>" style="width:100%">
                                            <tr>
                                                <th><?php _e('Valoración', 'mgs_elementor')?></th>
                                                <td><?php echo $comentario->post_rate?></td>
                                            </tr>
                                            <?php
                                            foreach( $fields as $field ){
                                            ?>
                                            <tr>
                                                <th><?php echo $field->key?></th>
                                                <td><?php echo $field->value?></td>
                                            </tr>
                                            <?php }?>
                                        </table>
                                    </td>
                                </tr>
                                <?php }?>
                                <tr>
                                    <th>
                                        <div class="mgs-post-rate-share-bar mgs-post-rate-share-bar-all">
                                            <a href="#" class="notwork" data-post="<?php echo $_GET['view']?>"><span class="material-symbols-outlined">forward_to_inbox</span></a>
                                            <a href="#" class="notwork" data-post="<?php echo $_GET['view']?>"><span class="material-symbols-outlined">export_notes</span></a>
                                            <a href="#" class="notwork" data-post="<?php echo $_GET['view']?>"><span class="material-symbols-outlined">delete</span></a>
                                        </div>
                                    </th>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        <script>
                            jQuery(document).ready(function(){

                                var submit_id_hash = window.location.hash;
                                if( submit_id_hash!='' ){
                                    submit_id_hash = submit_id_hash.replace('#', '');
                                    ViewDetails(submit_id_hash);
                                }

                                jQuery('.tomail').on('click', function(e){
                                    e.preventDefault();
                                    var post = jQuery(this).data('post');
                                    var submit_id = jQuery(this).data('submit_id');

                                    bootbox.confirm({
                                        'title'         : 'Enviar por correo electrónico',
                                        'message'       : '<div class="modal_form modal_mail_form">\
                                                                <div class="modal_input_wrapper">\
                                                                    <label for="modal_mail_form_para">Para</label>\
                                                                    <input type="email" id="modal_mail_form_para" />\
                                                                </div>\
                                                                <div class="modal_input_wrapper">\
                                                                    <label for="modal_mail_form_asunto">Asunto</label>\
                                                                    <input type="text" id="modal_mail_form_asunto" />\
                                                                </div>\
                                                          </div>',
                                        'className'     : 'mgs_elementor_bootbox',
                                        'centerVertical': true,
                                        'buttons'       : {
                                            confirm         : {
                                                label           : 'Enviar',
                                                className       : 'btn-success'
                                            },
                                            cancel          : {
                                                label               : 'Cancelar',
                                                className           : 'btn-danger'
                                            }
                                        },
                                        callback: function(result){
                                            var to = jQuery('input#modal_mail_form_para').val();
                                            var asunto = jQuery('input#modal_mail_form_asunto').val();
                                            
                                            jQuery('#modal_mail_form_para, #modal_mail_form_asunto').removeClass('error')

                                            if( result && to!='' && isEmail(to) && asunto!='' ){
                                                //mando
                                                SentToMail(post, submit_id, to, asunto);
                                            }else if( result && (to=='' || !isEmail(to)) ){
                                                //to mal
                                                jQuery('#modal_mail_form_para').addClass('error')
                                                return false;
                                            }else if( result && asunto=='' ){
                                                jQuery('#modal_mail_form_asunto').addClass('error')
                                                return false;
                                                //asunto mal
                                            }else if( result==false ){
                                                //se dio cerrar
                                            }
                                        }
                                    });
                                })

                                jQuery('.tocsv').on('click', function(e){
                                    e.preventDefault();
                                    var post = jQuery(this).data('post');
                                    var submit_id = jQuery(this).data('submit_id');

                                    bootbox.confirm({
                                        'title'         : 'Descargar como CSV',
                                        'message'       : '<div class="modal_form modal_mail_form">\
                                                                <div class="modal_input_wrapper">\
                                                                    <label for="modal_mail_form_name">Nombre del archivo</label>\
                                                                    <input type="text" id="modal_mail_form_name" />\
                                                                    <p><small>Sin extención<small></p>\
                                                                </div>\
                                                          </div>',
                                        'className'     : 'mgs_elementor_bootbox',
                                        'centerVertical': true,
                                        'buttons'       : {
                                            confirm         : {
                                                label           : 'Descargar',
                                                className       : 'btn-success'
                                            },
                                            cancel          : {
                                                label               : 'Cancelar',
                                                className           : 'btn-danger'
                                            }
                                        },
                                        callback: function(result){
                                            var name = jQuery('input#modal_mail_form_name').val();
                                            
                                            jQuery('#modal_mail_form_name').removeClass('error')

                                            if( result && name!='' ){
                                                //mando
                                                DownloadCSV(post, submit_id, name);
                                            }else if( result && name=='' ){
                                                jQuery('#modal_mail_form_name').addClass('error')
                                                return false;
                                            }else if( result==false ){
                                                //se dio cerrar
                                            }
                                        }
                                    });
                                })

                                jQuery('.delete').on('click', function(e){
                                    e.preventDefault();
                                    var post = jQuery(this).data('post');
                                    var submit_id = jQuery(this).data('submit_id');

                                    bootbox.confirm({
                                        'title'         : 'Descargar como CSV',
                                        'message'       : '<div class="modal_form modal_mail_form">\
                                                                <div class="modal_input_wrapper">\
                                                                    <p>Escriba DELETE para confirmar que desea eliminar este registro.</p>\
                                                                    <input type="text" id="modal_mail_form_delete" />\
                                                                </div>\
                                                          </div>',
                                        'className'     : 'mgs_elementor_bootbox',
                                        'centerVertical': true,
                                        'buttons'       : {
                                            confirm         : {
                                                label           : 'Eliminar',
                                                className       : 'btn-success'
                                            },
                                            cancel          : {
                                                label               : 'Cancelar',
                                                className           : 'btn-danger'
                                            }
                                        },
                                        callback: function(result){
                                            var acc = jQuery('input#modal_mail_form_delete').val();
                                            
                                            jQuery('#modal_mail_form_delete').removeClass('error')

                                            if( result && acc=='DELETE' ){
                                                DeleteComment(submit_id);
                                            }else if( result && (acc=='' || acc!='DELETE') ){
                                                jQuery('#modal_mail_form_delete').addClass('error')
                                                return false;
                                            }else if( result==false ){
                                                //se dio cerrar
                                            }
                                        }
                                    });
                                })

                                jQuery('.notwork').on('click', function(e){
                                    e.preventDefault();
                                    bootbox.alert({
                                        message     : 'Falta implementar, paciencia.',
                                        className   : 'mgs_elementor_bootbox'
                                    });
                                })

                                

                                jQuery('.view-details').on('click', function(e){
                                    e.preventDefault();
                                    var id = jQuery(this).data('submit_id');
                                    var th_parent = jQuery(this).closest('th');
                                    ViewDetails(id);
                                });

                                

                                jQuery('input.action-submits_id').on('click', function(){
                                    if( jQuery(this).is(':checked') ){
                                        jQuery('input.submits_id').prop('checked', 'checked')
                                        check_checkboxs()
                                    }else{
                                        jQuery('input.submits_id').prop('checked', '')
                                        check_checkboxs()
                                    }
                                })
                                
                                jQuery('input.submits_id').on('click', function(){
                                    check_checkboxs()
                                })

                                function ViewDetails(id){
                                    if( jQuery('.row-'+id).hasClass('active') ){
                                        jQuery('.row-'+id).removeClass('active')
                                    }else{
                                        jQuery('.row-comment-content').removeClass('active')
                                        jQuery('.row-'+id).addClass('active')
                                        window.location.hash = id;
                                    }
                                }

                                function SentToMail(post, submit_id, to, asunto){
                                    bootbox.dialog({ 
                                        'message'           : '<div class="text-center">Enviando...</div>', 
                                        'centerVertical'    : true,
                                        'closeButton'       : false,
                                        'className'         : 'mgs_elementor_bootbox',
                                    });
                                    var data = {
                                        'action'        : 'mgs_post_rate_send_to_mail',
                                        'submit_id'     : submit_id,
                                        'post'          : post,
                                        'to'            : to,
                                        'asunto'        : asunto
                                    };
                                    jQuery.post('<?php echo admin_url('admin-ajax.php')?>', data, function(response){
                                        bootbox.hideAll();
                                        bootbox.dialog({ 
                                            'message'           : 'Registro enviado con éxito.', 
                                            'centerVertical'    : true,
                                            'className'         : 'mgs_elementor_bootbox',
                                        });
                                    });
                                    //console.log(post, submit_id, to, asunto)
                                }

                                function DownloadCSV(post, submit_id, name){
                                    bootbox.dialog({ 
                                        'message'           : '<div class="text-center">Preparando...</div>', 
                                        'centerVertical'    : true,
                                        'closeButton'       : false,
                                        'className'         : 'mgs_elementor_bootbox',
                                    });
                                    var data = {
                                        'action'        : 'mgs_post_rate_download_csv',
                                        'submit_id'     : submit_id,
                                        'post'          : post,
                                        'to'            : name,
                                    };
                                    jQuery.post('<?php echo admin_url('admin-ajax.php')?>', data, function(response){
                                        bootbox.hideAll();
                                        var downloadLink = document.createElement("a");
                                        var blob = new Blob(["\ufeff", response]);
                                        var url = URL.createObjectURL(blob);
                                        downloadLink.href = url;
                                        downloadLink.download = name+".csv";
                                        document.body.appendChild(downloadLink);
                                        downloadLink.click();
                                        document.body.removeChild(downloadLink);
                                        bootbox.dialog({ 
                                            'message'           : 'Descarga generada con éxito.', 
                                            'centerVertical'    : true,
                                            'className'         : 'mgs_elementor_bootbox',
                                        });
                                    });
                                    //console.log(post, submit_id, to, asunto)
                                }

                                function DeleteComment(submit_id){
                                    bootbox.dialog({ 
                                        'message'           : '<div class="text-center">Eliminando...</div>', 
                                        'centerVertical'    : true,
                                        'closeButton'       : false,
                                        'className'         : 'mgs_elementor_bootbox',
                                    });
                                    var data = {
                                        'action'        : 'mgs_post_rate_delete',
                                        'submit_id'     : submit_id
                                    };
                                    jQuery.post('<?php echo admin_url('admin-ajax.php')?>', data, function(response){
                                        bootbox.hideAll();
                                        jQuery('tr.row-'+submit_id).remove();
                                        bootbox.dialog({ 
                                            'message'           : 'Registro eliminado.', 
                                            'centerVertical'    : true,
                                            'className'         : 'mgs_elementor_bootbox',
                                        });
                                    });
                                    //console.log(post, submit_id, to, asunto)
                                }

                                function check_checkboxs(){
                                    //console.log(jQuery('input.submits_id:checked').length)
                                    if( jQuery('input.submits_id:checked').length > 0 ){
                                        jQuery('.mgs-post-rate-share-bar-all').fadeIn();
                                    }else{
                                        jQuery('.mgs-post-rate-share-bar-all').fadeOut();
                                    }
                                }

                                function isEmail(email) {
                                    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                                    return regex.test(email);
                                }

                            });
                        </script>
                    </div>
                </div>
                <?php
            }else{
                $sql = "SELECT * FROM $t1 INNER JOIN $t2 ON {$t1}.submit_id = {$t2}.submit_id GROUP BY {$t1}.post_id";
                ?>
                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.13.2/b-2.3.4/b-html5-2.3.4/datatables.min.css"/>
                <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.13.2/b-2.3.4/b-html5-2.3.4/datatables.min.js"></script>
    
                <div class="addon-paga-container">
                    <div class="inner">
                        <div class="mgs-post-rate-comments-list">
                            <h2><?php _e('Valoraciones con comentarios', 'mgs_elementor')?></h2>
                            <table id="mgs-post-rate-comments-list" class="display mgs-elementor-datatable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><?php _e('Donde', 'mgs_elementor')?></th>
                                        <th><?php _e('Valoración', 'mgs_elementor')?></th>
                                        <th><?php _e('Comentarios', 'mgs_elementor')?></th>
                                        <th><?php _e('Fecha', 'mgs_elementor')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $rows = $wpdb->get_results($sql);
                                foreach( $rows as $row ){
                                    $current_value = get_post_meta($row->post_id, 'mgs_post_rating_value', true);
                                    $current_veces = get_post_meta($row->post_id, 'mgs_post_rating_veces', true);
                                    $post_id = $row->post_id;
                                    $sql = "SELECT COUNT($t2.submit_id) FROM $t1 INNER JOIN $t2 ON $t1.submit_id = $t2.submit_id WHERE $t1.post_id = $post_id GROUP BY $t2.submit_id";
                                    $comentarios = $wpdb->get_results($sql);


                                    $row_actions = [];
                                    $row_actions[] = [
                                        'link'      => '?page=mgs_elementor_admin&sec=post-rate&addon=post-rate&view='.$row->post_id,
                                        'label'     => __('Ver comentarios', 'mgs_elementor')
                                    ];
                                    $row_actions[] = [
                                        'link'      => 'post.php?post='.$row->post_id.'&action=edit',
                                        'label'     => __('Editar', 'mgs_elementor')
                                    ];
                                    
                                ?>
                                    <tr>
                                        <td class="row_actions">
                                            <a class="main_link"><?php echo get_the_title($row->post_id)?></a>
                                            <?php self::render_row_actions($row_actions)?>
                                        </td>
                                        <td><?php echo $current_value?>/<?php echo $current_veces?></td>
                                        <td><?php echo count($comentarios)?></td>
                                        <td><?php echo $row->created_at_gmt?></td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            <script>
                                jQuery(document).ready(function () {
                                    jQuery('#mgs-post-rate-comments-list').DataTable({
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        private static function render_row_actions($row_actions){
            if( !is_array($row_actions) ) return;

            $links = [];
            foreach( $row_actions as $row_action ){
                $links[] = '<a href="'.$row_action['link'].'">'.$row_action['label'].'</a>';
            }
            echo '
                <div class="actions">
                    '.implode(' | ', $links).'
                </div>
            ';
        }

        public function mgs_post_rate_add_admin_column($columns){
            $columns['mgs_post_rate'] = '<span class="dashicons dashicons-star-filled"></span> Valoraciones';
            return $columns;
        }

        public function mgs_post_rate_manage_admin_column($column, $post_id){
            if( $column=='mgs_post_rate' ){
                $current_value = get_post_meta($post_id, 'mgs_post_rating_value', true);
                $current_veces = get_post_meta($post_id, 'mgs_post_rating_veces', true);
                if( $current_value>0 && $current_veces>0 ){
                    //echo round($current_value/$current_veces, 1);
                    $dd = explode('.', round($current_value/$current_veces, 1));
                    $entero = ( isset($dd[0]) ) ? $dd[0] : 0;
                    $decimal = ( isset($dd[1]) ) ? $dd[1] : 0;
                    //list($entero, $decimal) = explode('.', round($current_value/$current_veces, 1));

                    $put_decimal = false;
                    echo '<div class="mgs-pr-admin-view mgs-pr-admin-view-'.$post_id.'">';
                    for( $i=1; $i<=5; $i++ ){
                        if( $i>$entero){
                            if( $decimal>5 && !$put_decimal ){
                                echo '<span class="dashicons dashicons-star-half"></span>';
                                $put_decimal = true;
                            }else{
                                echo '<span class="dashicons dashicons-star-empty"></span>';
                            }
                        }elseif( $i==$entero ){
                            echo '<span class="dashicons dashicons-star-filled"></span>';
                        }else{
                            echo '<span class="dashicons dashicons-star-filled"></span>';
                        }
                    }
                    echo '<br><strong>Valor:</strong> '.round($current_value/$current_veces, 1).' - <strong>Veces:</strong> '.$current_veces.'</div>';
                }else{
                    echo '<div class="mgs-pr-admin-view mgs-pr-admin-view-'.$post_id.'">'.$this->mgs_post_rate_show_no_rating().'</div>';
                }
            }
        }

        public function mgs_post_rate_post_row_actions($actions, $post){
            if ( $post->post_type=='post' || $post->post_type=='page' ){
                if( get_post_meta($post->ID, 'mgs_post_rating_veces', true)>0 ){
                    $actions['mgs_post_rate_reset'] = '<a href="#" title="Reset" data-post="'.$post->ID.'" class="mgs-post-rate-reset"><span class="dashicons dashicons-image-rotate"></span></a>';
                }
            }
            return $actions;
        }

        public function mgs_post_rate_reset(){
            update_post_meta($_POST['post'], 'mgs_post_rating_value', 0);
            update_post_meta($_POST['post'], 'mgs_post_rating_veces', 0);
            echo $this->mgs_post_rate_show_no_rating();
            die();
        }

        public function mgs_post_rate_reset_script(){
            ?>
            <script>
                jQuery(function($){
                    jQuery('a.mgs-post-rate-reset').on('click', function(e){
                        e.preventDefault();
                        if( confirm('Realmente desea resetear la votación?') ){
                            var post = jQuery(this).data('post');
                            var data = {
                                'action'    : 'mgs_post_rate_reset',
                                'post'      : post
                            };
                            //console.log(mgs_post_ratings_vars.ajaxurl, post);
                            jQuery('.mgs-pr-admin-view-'+post).html('Reseteando...');
                            
                            jQuery.post('<?php echo admin_url('admin-ajax.php')?>', data, function(response){
                                jQuery('.mgs-pr-admin-view-'+post).fadeOut(500, function(){
                                    jQuery('.mgs-pr-admin-view-'+post).html(response).fadeIn()
                                })
                            });
                            jQuery(this).fadeOut();
                        }
                    })
                });
            </script>
            <?php
        }

        private function mgs_post_rate_show_no_rating(){
            return '<span class="dashicons dashicons-star-empty"></span><span class="dashicons dashicons-star-empty"></span><span class="dashicons dashicons-star-empty"></span><span class="dashicons dashicons-star-empty"></span><span class="dashicons dashicons-star-empty"></span><br><strong>Valor:</strong> 0 - <strong>Veces:</strong> 0';
        }
    }

    new MGS_Post_Rate_Admin();
}