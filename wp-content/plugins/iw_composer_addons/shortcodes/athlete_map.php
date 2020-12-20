<?php

/*
 * @package Inwave Athlete
 * @version 1.0.0
 * @created Mar 30, 2015
 * @author Inwavethemes
 * @email inwavethemes@gmail.com
 * @website http://inwavethemes.com
 * @support Ticket https://inwave.ticksy.com/
 * @copyright Copyright (c) 2015 Inwavethemes. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

/**
 * Description of athlete_map
 *
 * @Developer duongca
 */
if (!class_exists('Athlete_Map')) {

    class Athlete_Map {

        private $google_api;

        function __construct() {
            add_action('vc_before_init', array($this, 'heading_init'));
            add_action('wp_enqueue_scripts', array($this, 'athlete_map_scripts'));
            add_shortcode('athlete_map', array($this, 'athlete_map_shortcode'));
        }

        function heading_init() {

            // Add banner addon
            vc_map(array(
                'name' => 'Map',
                'description' => __('Display a Google Map', 'inwavethemes'),
                'base' => 'athlete_map',
                // 'icon' => 'icon-wpb-inwavethemes',
                'category' => 'InwaveThemes',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        "holder" => "div",
                        "heading" => __("Title", "inwavethemes"),
                        "value" => "Map",
                        "param_name" => "title",
                        "description" => __('Title of map block.', "inwavethemes")
                    ),
                    array(
                        "type" => "textfield",
                        "heading" => __("Google API", "inwavethemes"),
                        "param_name" => "google_api",
                        "value" => "",
                    ),
                    array(
                        "type" => "textfield",
                        "heading" => __("Latitude", "inwavethemes"),
                        "param_name" => "latitude",
                        "value" => "40.6700",
                    ),
                    array(
                        "type" => "textfield",
                        "heading" => __("Longitude", "inwavethemes"),
                        "param_name" => "longitude",
                        "value" => "-73.9400",
                    ),
                    array(
                        "type" => "textfield",
                        "heading" => __("Zoom", "inwavethemes"),
                        "param_name" => "zoom",
                        "value" => "11",
                    ),
                    array(
                        "type" => "textfield",
                        "group" => "Marker",
                        "heading" => __("Title", "inwavethemes"),
                        "param_name" => "marker_title",
                        'value' => 'Inwavethemes'
                    ),
                    array(
                        "type" => "attach_image",
                        "group" => "Marker",
                        "heading" => __("Image", "inwavethemes"),
                        "param_name" => "marker_image",
                    ),
                    array(
                        "type" => "textarea",
                        "group" => "Marker",
                        "heading" => __("Info", "inwavethemes"),
                        "param_name" => "info",
                    ),
                    array(
                        "type" => "textfield",
                        "group" => "Marker",
                        "heading" => __("Marker width", "inwavethemes"),
                        "param_name" => "mrk_width",
                        "value" => '108'
                    ),
                    array(
                        "type" => "textfield",
                        "group" => "Marker",
                        "heading" => __("Marker Height", "inwavethemes"),
                        "param_name" => "mrk_height",
                        "value" => '49'
                    ),
                    array(
                        "type" => "dropdown",
                        "group" => "Marker",
                        "heading" => __("Marker Position", "inwavethemes"),
                        "param_name" => "mrk_position",
                        "value" => array(
                            __("Left", 'inwavethemes') => 'left',
                            __("Center", 'inwavethemes') => 'center',
                            __("Right", 'inwavethemes') => 'right',
                        )
                    ),
                    array(
                        "type" => "textfield",
                        "heading" => __("Extra Class", "inwavethemes"),
                        "param_name" => "class",
                        "description" => __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', "inwavethemes")
                    )
                )
            ));
        }

        function athlete_map_scripts() {
            global $smof_data;
            $theme_info = wp_get_theme();
            wp_enqueue_script('athlete-map-script', get_template_directory_uri() . '/js/athlete_map.js', array(), $theme_info->get('Version'), true);
            wp_deregister_script('evcal_gmaps');
            wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js' . (isset($smof_data['google_api']) ? '?key=' . $smof_data['google_api'] : ''), array(), $theme_info->get('Version'), true);
        }

        // Shortcode handler function for list Icon
        function athlete_map_shortcode($atts, $content = null) {
            extract(shortcode_atts(array(
                'title' => '',
                'google_api' => '',
                'latitude' => '40.6700',
                'longitude' => '-73.9400',
                'marker_title' => 'Inwavethemes',
                'marker_image' => '',
                'zoom' => '11',
                'info' => '',
                'mrk_width' => '108',
                'mrk_height' => '49',
                'mrk_position' => 'left',
                'class' => ''
                            ), $atts));
            $this->google_api = $google_api;
            $img = wp_get_attachment_image_src($marker_image, 'large');
            if (is_array($img) && count($img)) {
                $img = $img[0];
            } else {
                $img = $marker_image;
            }
            $html = '';
            $html = '<div class="contact-map ' . $class . '">';
            $html .= '<div class="map-contain" data-mrk-position="' . $mrk_position . '" data-mrk-width="' . $mrk_width . '" data-mrk-height="' . $mrk_height . '" data-title="' . $marker_title . '" data-image="' . $img . '" data-lat="' . $latitude . '" data-long="' . $longitude . '" data-zoom="' . $zoom . '" data-info="' . $info . '"><div class="map-view map-frame"></div></div>';
            $html .= '<script type="text/javascript">';
            $html .= 'jQuery(document).ready(function(){';
            $html .= 'jQuery("' . ($class ? '.' . $class . ' ' : '') . '.map-contain").iwMap();';
            $html .= '});';
            $html .= '</script>';
            $html .='</div>';
            return $html;
        }

    }

}

new Athlete_Map();
if (class_exists('WPBakeryShortCode')) {

    class WPBakeryShortCode_Athlete_Map extends WPBakeryShortCode {
        
    }

}