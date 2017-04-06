<?php
/*
Plugin Name: AIESEC COP Registration 
Description: Plugin based on gis_curl_registration script by Dan Laush upgraded to Wordpress plugin
Version: 0.2
Author: Enrique Suarez

License: GPL 
*/
wp_enqueue_script('jquery');
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

// [expa-form program="gt"]


///GENERAL
function cop_form( $atts ) {
    $a = shortcode_atts( array(
        'program' => '',
    ), $atts );
    
    $configs = include('config.php');
        
    $form = file_get_contents('form.html',TRUE);
    
    //company type lists
    $company_type_json = plugins_url('company_type.json', __FILE__ );
    $json = file_get_contents($company_type_json, false, stream_context_create($arrContextOptions)); 
    $types = json_decode($json); 
    $option_list = "";

    //cities list
    $cities_json = plugins_url('cities.json', __FILE__ );
    $c_json = file_get_contents($cities_json, false, stream_context_create($arrContextOptions)); 
    $cities = json_decode($c_json); 
    $cities_list = "";
    
    foreach($cities as $key => $value){
        $cities_list = $cities_list.'<option value="'.$key.'">'.$key.'</option>'."\n";//var_dump($lead->);    
    }
    foreach($types as $key => $value){
        $option_list = $option_list.'<option value="'.$value.'">'.$key.'</option>'."\n";//var_dump($lead->);    
    }
    $form = str_replace("{path-gis_reg_process}",plugins_url('gis_reg_process.php', __FILE__ ),$form);
    $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $form = str_replace("{website_url}",$actual_link,$form);
    $form = str_replace("{name}",$configs["name"],$form);
    $form = str_replace("{company-type-list}",$option_list,$form);
    $form = str_replace("{cities-list}",$cities_list,$form);
    $form = str_replace("{surname}",$configs["surname"],$form);
    $form = str_replace("{e-mail}",$configs["e-mail"],$form);
    $form = str_replace("{phone}",$configs["phone"],$form);
    
    
    if($_GET["thank_you"]==="true"){
        return $configs["thank-you-message"]; 
    } elseif ($_GET["error"]!=""){
        
        $form = str_replace('<div id="error" class="error"><p></p></div>','<div id="error" class="error"><p>'.$_GET["error"].'</p></div>',$form);
        return $form;    
    }
    //var_dump( plugins_url('gis_reg_process.php', __FILE__ ));
    return $form;
}
add_shortcode( 'cop-form', 'cop_form' );






?>
