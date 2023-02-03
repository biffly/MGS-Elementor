<?php

if( !class_exists('MGS_Dummy_Content') ){
    class MGS_Dummy_Content{
        public function __construct(){
            if( get_option('mgs-elementor-addon-state-dummy_content')=='on' ){
                add_action('admin_enqueue_scripts', [$this, 'mgs_dummy_content_admin_enqueue_scripts']);
                add_action('wp_ajax_mgs_dummy_content_generate', [$this, 'mgs_dummy_content_generate_callback']);
                add_action('wp_ajax_mgs_dummy_content_delete', [$this, 'mgs_dummy_content_delete_callback']);

                add_filter('display_post_states', [$this, 'display_dummy_status']);
                add_action('manage_category_custom_column', [$this, 'display_dummy_status_cats'], 999, 3);
                add_filter('manage_edit-category_columns', [$this, 'add_column_dummy_status_cats']);
            }
        }

        public function add_column_dummy_status_cats($columns){
            $columns['dummy'] = 'Tipo';
            return $columns;
        }

        public function display_dummy_status_cats($string, $columns, $term_id){
            if( $columns=='dummy' ){
                if( get_term_meta($term_id, 'mgs_dummy_content', true)=='on' ){
                    echo '<span class="post-state">Dummy</span>';
                }
            }
        }

        public function display_dummy_status(){
            global $post;
            $dummy = ( get_post_meta($post->ID, 'mgs_dummy_content', true)=='on' ) ? true : false;
            if( $dummy ){
                return [__('Dummy', 'mgs_elementor')];
            }

            return;
        }

        public function mgs_dummy_content_admin_enqueue_scripts($hook){
            //if( $hook=='settings_page_'.MGS_Elementor_AddOns::slug_admin ){
                wp_enqueue_script('jquery');
                wp_register_script('mgs-dummy-content-admin-js', MGS_ELEMENTOR_PLUGIN_DIR_URL.'/assets/js/dummy-content-admin.js', ['jquery']);
   				wp_localize_script('mgs-dummy-content-admin-js', 'mgs_dummy_content', [
                    //'ajaxurl'   => admin_url('admin-ajax.php'),
                    'cant_error'        => __('La cantidad debe ser mayor a uno.', 'mgs_elementor'),
                    'parrafos_error'    => __('Los parrafos deben ser mayor o igual a uno.', 'mgs_elementor'),
                    'delete_text'       => __('Eliminar contenido autogenerado', 'mgs_elementor'),
                    'deleting_text'     => __('Eliminando...', 'mgs_elementor'),
                ]);        
   				wp_enqueue_script('mgs-dummy-content-admin-js');
   				wp_enqueue_script('mgs-dummy-content-admin-js-google-apis', 'https://apis.google.com/js/api.js', ['mgs-dummy-content-admin-js']);
            //}
        }

        public function mgs_dummy_content_delete_callback(){
            $pps = get_posts([
                'numberposts'   => -1,
                'meta_query'    => [
                    'key'   => 'mgs_dummy_content',
                    'value' => 'on'
                ]
            ]);
            $deletes = [];
            if( $pps && count($pps)>0 ){
                foreach( $pps as $post ){
                    $attach_id = get_post_meta($post->ID, 'mgs_dummy_content_img', true);
                    wp_delete_attachment($attach_id, true);
                    wp_delete_post($post->ID, true);
                    $deletes[] = [
                        'post_id'   => $post->ID,
                        'attach_id' => $attach_id
                    ];
                }
            }

            //delete terms
            $deletes_terms = [];
            $terms = get_terms([
                'taxonomy'      => 'category',
                'hide_empty'    => false
            ]);
            foreach( $terms as $term ){
                if( get_term_meta($term->term_id, 'mgs_dummy_content', true)=='on' ){
                    wp_delete_term($term->term_id, 'category');
                    $deletes_terms[] = $term->term_id;
                }
            }


            //sleep(5);
            echo json_encode([
                'status'    => 200,
                'posts'     => $deletes,
                'terms'     => $deletes_terms
            ]);
            die();
        }

        public function mgs_dummy_content_generate_callback(){
            //https://stackoverflow.com/questions/20633310/how-to-get-random-text-from-lorem-ipsum-in-php
            //https://rapidapi.com/contextualwebsearch/api/web-search?utm_source=google&utm_medium=cpc&utm_campaign=Beta&utm_term=%2Bimage%20%2Bsearch%20%2Bapi_b&gclid=CjwKCAiAwc-dBhA7EiwAxPRylNE0Kp8TItfO71ISglXGCtQ0Kur4B6ojozaohC3GD2HhZ41CzAZUbBoCZ50QAvD_BwE

            

            $cant = $_POST['cant'];
            $parrafos = $_POST['parrafos'];
            $parrafos_zise = $_POST['parrafos_zise'];
            $parrafos_content = $_POST['parrafos_content'];
            
            $cat_cant_new = $_POST['cat_cant_new'];
            $cat_asignar = $_POST['cat_asignar'];
            $cat_asignar_random = ( $_POST['cat_asignar_random']=='on' ) ? true : false;
            
            $get_imagen = ( $_POST['imagen']=='on' ) ? true : false;
            $gen_cats = ( $_POST['tipo_cat']=='news' ) ? true : false;
            
            $posts = [];
            
            //obtengo la cantidad de imagenes necesarias
            $imagenes = [];
            if( $get_imagen ){
                //$response = $this->GetImagesFromRapidapi($cant, $_POST['search']);
                $imagenes = $this->SearchImages($cant, $_POST['search']);
            }

            //genero categorias
            $cats = [];
            if( $gen_cats && $cat_cant_new>0 ){
                //categorias nuevas
                for($c=0; $c<$cat_cant_new; $c++){
                    $nt = wp_insert_term($this->GetTitle(20), 'category');
                    $cats[] = $nt['term_id'];
                    add_term_meta($nt['term_id'], 'mgs_dummy_content', 'on');
                }
            }elseif( !$gen_cats ){
                //uso las categorias actuales
                $terms = get_terms([
                    'taxonomy'      => 'category',
                    'hide_empty'    => false
                ]);
                foreach( $terms as $term ){
                    $cats[] = $term->term_id;
                }
            }



            //genero el array con la cantidad de post a generar
            for($p=0; $p<$cant; $p++){
                $title = $this->GetTitle();
                $content = $this->GetParrafos($parrafos, $parrafos_zise, $parrafos_content);

                $img_url = '';
                if( $get_imagen ) $img_url = $this->CleanURLImage($imagenes[$p]);

                $posts['query'] = [
                    'cant'          => $cant,
                    'parrafos'      => $parrafos,
                    'content_zise'  => $parrafos_zise,
                    'content_ops'   => $parrafos_content,
                    'img_search'    => $_POST['search']
                ];
                $posts['items'][$p] = [
                    'title'         => $title,
                    'content'       => $content,
                    'img'           => [
                        'url'           => $img_url,
                        'title'         => $imagenes[$p]->title,
                    ]
                ];

                //asigno categorias al post
                if( $cat_asignar_random ) shuffle($cats);
                if( $cat_asignar>count($cats) ){
                    //se solicito asignar mas categorias que las disponibles, se asignan las disponibles
                    $posts['items'][$p]['cats'] = $cats;
                }else{
                    $_cats = [];
                    for($i=0; $i<$cat_asignar; $i++){
                        $_cats[] = $cats[$i];
                    }
                    $posts['items'][$p]['cats'] = $_cats;
                }
            }

            //echo json_encode($posts);
            //die();

            //cargo los posts
            foreach( $posts['items'] as $k=>$post ){
                
                //inserto post
                $id_post = wp_insert_post([
                    'post_content'      => $post['content'],
                    'post_title'        => ( $post['title']=='' ) ? $this->GetTitle() : $post['title'],
                    'post_status'       => 'publish'
                ]);
                if( !is_wp_error($id_post) ){
                    update_post_meta($id_post, 'mgs_dummy_content', 'on');
                    wp_set_post_terms($id_post, $post['cats'], 'category');
                    $posts['items'][$k]['status'] = 200;

                    if( $post['img']['url']!='' && $get_imagen ){
                        //agrego imagen a la libreria
                        $posts['items'][$k]['img']['status'] = $this->UploadImagen($post['img']['url'], $post['img']['title'], $id_post);
                    }
                }else{
                    $posts['items'][$k]['status'] = 500;
                }
            }

            echo json_encode($posts);
            die();
        }

        public static function config(){
            if( get_option('mgs-elementor-addon-state-dummy_content')=='on' && is_admin() ){
            ?>
            <div class="mgs_elementor_config mgs_elementor_dummy_content_config">
                <?php if( get_option('mgs-elementor-addon-dummy_content_google_api_key')=='' ){?>
                    <p>Para poder generar imagenes destacadas para sus entradas debera completar estas opciones.</p>
                    <ol>
                        <li>Ingrese a <a href="https://console.cloud.google.com/apis/dashboard" target="_blank" alt="Google Cloud Console">Google Cloud Console <span class="material-symbols-outlined">open_in_new</span></a>.</li>
                        <li>En la opcion de <code>API y servicios habilitados</code> agregue y habilite <code>Custom Search API</code>.</li>
                        <li>En <code>Credenciales</code> cree y configure una <code>Clave de API</code></li>
                    </ol>
                <?php }?>
                <div class="mgs-elementor-fake-form">
                    <div class="mgs-elementor-field-wrapper fw">
                        <label for="mgs_dummy_content_clave_api"><?php _e('Clave API Custom Search API', 'mgs_elementor')?></label>
                        <input type="text" class="mgs_elementor_input" name="mgs_dummy_content_clave_api" id="mgs_dummy_content_clave_api" value="<?php echo get_option('mgs-elementor-addon-dummy_content_google_api_key')?>">
                    </div>
                    <?php if( get_option('mgs-elementor-addon-dummy_content_google_api_key')=='' ){?>
                        <div class="mgs-elementor-field-wrapper aling-bottom fw">
                            <button type="button" id="cmd_mgs_dummy_content_save_api" class="mgs_elementor_cmd">Verificar y guardar</button>
                        </div>
                    <?php }else{?>
                        <div class="mgs-elementor-field-wrapper aling-bottom fw">
                            <button type="button" id="cmd_mgs_dummy_content_delete_api" class="mgs_elementor_cmd">Eliminar clave API</button>
                        </div>
                    <?php }?>
                </div>
                <div class="dummy_content_api_messages"></div>
            </div>
            <?php
            }
        }

        public static function run(){
            if( get_option('mgs-elementor-addon-state-dummy_content')=='on' && is_admin() ){
                $pps = get_posts([
                    'numberposts'   => -1,
                    'meta_query'    => [
                        'key'   => 'mgs_dummy_content',
                        'value' => 'on'
                    ]
                ]);
                $delete_wrapper_class = 'hidden';
                if( $pps && count($pps)>0 ){
                    $delete_wrapper_class = '';
                }

                //if API search?
                if( get_option('mgs-elementor-addon-dummy_content_google_api_key')!='' ){
                    $img_generate_checked = 'checked';
                    $img_generate_state = '';
                }else{
                    $img_generate_checked = '';
                    $img_generate_state = 'disabled';
                }

                //terms
                $terms = get_terms([
                    'taxonomy'      => 'category',
                    'hide_empty'    => false
                ]);

                ?>
                <div class="mgs_elementor_run mgs_elementor_dummy_content_run delete_wrapper <?php echo $delete_wrapper_class?>">
                    <div class="mgs-elementor-field-wrapper mt-1">
                        <button type="button" class="mgs_elementor_cmd icon_text small" id="cmd_mgs_dummy_content_delete"><span class="material-symbols-outlined">delete</span> <?php _e('Eliminar contenido autogenerado', 'mgs_elementor')?> (<?php echo count($pps)?>) </button>
                    </div>
                </div>
                <div class="mgs_elementor_run mgs_elementor_dummy_content_run">
                    <p><?php _e('Complete las opciones para generar su contenido ficcticio.', 'mgs_elementor')?></p>
                    <?php if( get_option('mgs-elementor-addon-dummy_content_google_api_key')=='' ){?>
                    <p><small><?php _e('Debera configurar completar la configuración antes de poder generar imagenes..', 'mgs_elementor')?></small></p>
                    <?php }?>

                    <!--Opciones basicas-->
                    <div class="mgs-elementor-fake-form">
                        <div class="mgs-elementor-field-wrapper">
                            <label for="mgs_dummy_content_cant"><?php _e('Cantidad', 'mgs_elementor')?></label>
                            <input type="number" class="mgs_elementor_input" min=1 max=10 name="cant" id="mgs_dummy_content_cant" value=4>
                        </div>
                        <div class="mgs-elementor-field-wrapper">
                            <label for="mgs_dummy_content_parrafos"><?php _e('Parrafos', 'mgs_elementor')?></label>
                            <input type="number" class="mgs_elementor_input" min=1 max=10 name="parrafos" id="mgs_dummy_content_parrafos" value=2>
                        </div>
                        <div class="mgs-elementor-field-wrapper aling-bottom checkbox">
                            <label for="mgs_dummy_content_feactured_img">
                                <input type="checkbox" name="feactured_img" id="mgs_dummy_content_feactured_img" value="on" <?php echo $img_generate_checked?> <?php echo $img_generate_state?>> 
                                <?php _e('Imagen destacada?', 'mgs_elementor')?>
                            </label>
                        </div>
                        <div class="mgs-elementor-field-wrapper aling-bottom">
                            <button type="button" id="cmd_mgs_dummy_content_generate" class="mgs_elementor_cmd iconed"><span class="material-symbols-outlined">play_arrow</span></button>
                        </div>
                    </div>
                    <!--ajax response-->
                    <div class="alert-resume"></div>
                    <!--Opciones busqueda imgs-->
                    <div class="mgs-elementor-fake-form mgs_dummy_content_img_search_wrapper hidden">
                        <div class="mgs-elementor-field-wrapper">
                            <label for="mgs_dummy_content_img_search"><?php _e('Buscar imagenes, tema.', 'mgs_elementor')?></label>
                            <input type="text" class="mgs_elementor_input" id="mgs_dummy_content_img_search" value="Natural forest">
                        </div>
                    </div>
                    
                    <!--OPCIONES-->
                    <h2><?php _e('Opciones', 'mgs_elementor')?></h2>
                    
                    <!-- Categorias-->
                    <h3><?php _e('Categorías', 'mgs_elementor')?></h3>
                    <div class="mgs-elementor-fake-form list">
                        <?php if( count($terms)>0 ){?>
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="radio" class="cat_action" name="cat_action" id="cat_action_actuales" value="actuales" checked>
                            <label for="cat_action_actuales"><?php _e('Usar categorías actuales.', 'mgs_elementor')?></label>
                        </div>
                        <?php }?>
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="radio" class="cat_action" name="cat_action" id="cat_action_news" value="news">
                            <label for="cat_action_news"><?php _e('Crear categorías nuevas.', 'mgs_elementor')?></label>
                        </div>
                    </div>
                    <div class="mgs-elementor-fake-form aling-top list mb-2">
                        <div class="mgs-elementor-field-wrapper hidden" id="new_cat_div">
                            <label for="mgs_dummy_content_cat_cant_new"><?php _e('Crear', 'mgs_elementor')?></label>
                            <input type="number" min=1 max=10 class="mgs_elementor_input" id="mgs_dummy_content_cat_cant_new" value=4>
                        </div>
                        <div class="mgs-elementor-field-wrapper">
                            <label for="mgs_dummy_content_cat_asignar"><?php _e('Asignar', 'mgs_elementor')?></label>
                            <input type="number" min=1 max=10 class="mgs_elementor_input" id="mgs_dummy_content_cat_asignar" value=2>
                            <p class="desc"><?php _e('Cantidad de categorias que se asignaran a cada entrada.', 'mgs_elementor')?></p>
                        </div>
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_zise" name="cat_random" id="mgs_dummy_content_cat_random" value="on">
                            <label for="mgs_dummy_content_cat_random"><?php _e('Asignar aleatorio?', 'mgs_elementor')?></label>
                        </div>
                    </div>

                    <!-- Parrafos-->
                    <h3><?php _e('Parrafos, tamaño.', 'mgs_elementor')?></h3>
                    <div class="mgs-elementor-fake-form">
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_zise" name="parrafos_zise[]" id="parrafos_zise_short" value="short">
                            <label for="parrafos_zise_short"><?php _e('Corto.', 'mgs_elementor')?></label>
                        </div>
                        
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_zise" name="parrafos_zise[]" id="parrafos_zise_madiun" value="mediun" checked>
                            <label for="parrafos_zise_madiun"><?php _e('Mediano.', 'mgs_elementor')?></label>
                        </div>        
                        
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_zise" name="parrafos_zise[]" id="parrafos_zise_long" value="long">
                            <label for="parrafos_zise_long"><?php _e('Largo.', 'mgs_elementor')?></label>
                        </div>    
                        
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_zise" name="parrafos_zise[]" id="parrafos_zise_verylong" value="verylong">
                            <label for="parrafos_zise_verylong"><?php _e('Muy largo.', 'mgs_elementor')?></label>
                        </div>
                    </div>
                    <h3><?php _e('Parrafos, contenido.', 'mgs_elementor')?></h3>
                    <div class="mgs-elementor-fake-form list">
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_content" name="parrafos_content[]" id="parrafos_content_decorate" value="decorate">
                            <label for="parrafos_content_decorate"><?php _e('Texto en negrita, cursiva y marcado..', 'mgs_elementor')?></label>
                        </div>
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_content" name="parrafos_content[]" id="parrafos_content_link" value="link">
                            <label for="parrafos_content_link"><?php _e('Links', 'mgs_elementor')?></label>
                        </div>
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_content" name="parrafos_content[]" id="parrafos_content_ul" value="ul">
                            <label for="parrafos_content_ul"><?php _e('Listas desordenadas', 'mgs_elementor')?></label>
                        </div>
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_content" name="parrafos_content[]" id="parrafos_content_ol" value="ol">
                            <label for="parrafos_content_ol"><?php _e('Listas ordenadas', 'mgs_elementor')?></label>
                        </div>
                        <div class="mgs-elementor-field-wrapper checkbox">
                            <input type="checkbox" class="parrafos_content" name="parrafos_content[]" id="parrafos_content_headers" value="headers">
                            <label for="parrafos_content_headers"><?php _e('Cabeceras, titulos.', 'mgs_elementor')?></label>
                        </div>
                    </div>
                </div>
                <?php
            }else{
                echo '<h2 class="error">Opciones no disponibles</h2>';
            }
        }

        private function SearchImages($cant, $search){
            //https://www.googleapis.com/customsearch/v1?key=AIzaSyC2qilqUj5H4kLu4QfT6YplkisWFkTV0Mk&searchType=image&q=monta%C3%B1as&cx=46f45935b43ac4273&imgSize=xxlarge&rights=cc_publicdomain&num=10
            if( !class_exists('WP_Http') ){
                include_once(ABSPATH.WPINC.'/class-http.php');
            }
            $http = new WP_Http();

            $url_search = 'https://www.googleapis.com/customsearch/v1?key='.get_option('mgs-elementor-addon-dummy_content_google_api_key').'&searchType=image&q='.urlencode($search).'&cx=46f45935b43ac4273&imgSize=xxlarge&rights=cc_publicdomain&num='.$cant;
            $search_result = wp_remote_get($url_search);
            $search_code = wp_remote_retrieve_response_code($search_result);
            $search_body = json_decode(wp_remote_retrieve_body($search_result));
            return $search_body->items;
        }

        //DEPRECATED, ahora uso Custom Search JSON API de google
        private function GetImagesFromRapidapi($cant, $search){
            if( !class_exists('WP_Http') ){
                include_once(ABSPATH.WPINC.'/class-http.php');
            }
            $http = new WP_Http();

            $response = wp_remote_get('https://contextualwebsearch-websearch-v1.p.rapidapi.com/api/Search/ImageSearchAPI?q='.urlencode($search).'&pageNumber=1&pageSize='.$cant.'&autoCorrect=true', [
                'headers'   => [
                    'X-RapidAPI-Key'    => 'bSQIGI9FsKmshTf5DCZBsJ9oZ8wGp1UukRejsnie9n9zCmcVdy',
                    'X-RapidAPI-Host'   => 'contextualwebsearch-websearch-v1.p.rapidapi.com'
                ]
            ]);
            $img_code = wp_remote_retrieve_response_code($response);
            $response = wp_remote_retrieve_body($response);
            $response = json_decode($response, true);
            return $response;
        }

        private function _shuffle_words($sentence){
            $words = explode(' ', $sentence);
            shuffle($words);
            $words = strtolower(implode(' ', $words));
            $words = preg_replace_callback('/([.!?])\s*(\w)/', function($matches){
                return strtoupper($matches[1].' '.$matches[2]);
            }, ucfirst(strtolower($words)));
            return $words;
        }

        private function GetTitle($long=90){
            $title = wp_remote_post('https://loripsum.net/api/1/long/plaintext');
            $title = wp_remote_retrieve_body($title);
            $title = $this->_shuffle_words($title);
            $title = ( strlen($title)>$long ) ? substr($title, 0, $long) : $string;
            return $title;
        }

        private function GetParrafos($cant, $parrafos_zise, $parrafos_content){
            if( is_array($parrafos_zise) ){
                shuffle($parrafos_zise);
                $_parrafos_zise = '/'.$parrafos_zise[0];
            }else{
                $_parrafos_zise = '';
            }
            if( is_array($parrafos_content) ){
                shuffle($parrafos_content);
                $_parrafos_content = '/'.implode('/', $parrafos_content);
            }else{
                $_parrafos_content = '';
            }

            $get_parrafos_url = 'https://loripsum.net/api/'.$cant.$_parrafos_zise.$_parrafos_content;
            $content = wp_remote_post($get_parrafos_url);
            $content = wp_remote_retrieve_body($content);
            return $content;
        }

        private function CleanURLImage($url){
            if( str_contains($url->link, '?') ){
                $img_url = substr($url->link, 0, strpos($url->link, '?'));
            }else{
                $img_url = $url->link;
            }
            return $img_url;
        }

        private function UploadImagen($url, $title='', $id_post){
            set_time_limit(300);
            ini_set('memory_limit', '128M');
            if( $url=='' ){
                return [
                    'code'      => '500',
                    'error'     => 'URL no valida.'
                ];
            }
            if( $id_post=='' ){
                return [
                    'code'      => '500',
                    'error'     => 'ID post no valido.'
                ];
            }


            if( !class_exists('WP_Http') ){
                include_once(ABSPATH.WPINC.'/class-http.php');
            }
            require_once(ABSPATH.'wp-admin/includes/image.php' );

            $http = new WP_Http();

            $response = $http->request($url);
            if( $response['response']['code']!=200 ){
                return [
                    'code'      => '500',
                    'error'     => 'No se encontro la imagen.'
                ];
            }

            //cargo la imgen a la libreria
            $upload = wp_upload_bits(basename($url), null, $response['body']);
            if( !empty($upload['error']) ){
                return [
                    'code'      => '500',
                    'error'     => $upload['error']
                ];
            }else{
                $file_path = $upload['file'];
                $file_name = basename($file_path);
                $file_type = wp_check_filetype($file_name, null);
                $attachment_title = ( $title!='' ) ? sanitize_file_name($title) : sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));
                $wp_upload_dir = wp_upload_dir();
                $post_info = [
                    'guid'           => $wp_upload_dir['url'].'/'.$file_name,
                    'post_mime_type' => $file_type['type'],
                    'post_title'     => $attachment_title,
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];
                $attach_id = wp_insert_attachment($post_info, $file_path, $id_post);
                $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
                wp_update_attachment_metadata($attach_id,  $attach_data);
                set_post_thumbnail($id_post, $attach_id);
                update_post_meta($id_post, 'mgs_dummy_content_img', $attach_id);
    
                return [
                    'code'      => '200',
                    'post_info' => $post_info,
                    'file_path' => $file_path,
                    'attach_id' => $attach_id
                ];
            }
        }
    }
}
new MGS_Dummy_Content();