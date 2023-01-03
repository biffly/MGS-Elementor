<?php
add_shortcode('mgs_excerpt', 'mgs_get_the_excerpt');

class mgs_compare{
    static function is($op1, $op2, $c){
        $metodos = [
            '==='   => 'igual',
            '=='    => 'igual',
            '!=='   => 'distinto',
            '<'     => 'menor',
            '>'     => 'mayor',
        ];
        if( $metodo=$metodos[$c] ){
            return self::$metodo($op1,$op2);
        }
        return null;
    }

    private static function igual($op1,$op2){
        //return $op1.'==='.$op2;
        return $op1 === $op2;
    }
    private function distinto($op1,$op2){
        return $op1 != $op2;
    }
    private function menor($op1,$op2){
        return $op1 < $op2;
    }
    private function mayor($op1,$op2){
        return $op1 > $op2;
    }
}

function mgs_get_the_excerpt($args=[], $content=null){
    $sc_id = 'mgs_get_the_excerpt_'.uniqid();
    $args = array_change_key_case((array)$args, CASE_LOWER);
    $atts = shortcode_atts(
        [
            'line'      => '',
            'tag'       => 'div'
        ]
        , $args
	);

    $out = '';
    //$args['line'] = ( isset($args['line']) ) ? $args['line'] : '';
    //$args['tag'] = ( isset($args['tag']) ) ? $args['tag'] : 'div';

    
    $out .= '
        <'.$atts['tag'].' class="mgs_get_the_excerpt '.$sc_id.'">
            '.get_the_excerpt().'
        </'.$atts['tag'].'>
    ';

    if( $atts['line']!='' ){
        $out .= '
            <style>
                .'.$sc_id.'{
                    display: -webkit-box;
                    -webkit-line-clamp: '.$atts['line'].';
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
            </style>
        ';
    }
    return $out;
}