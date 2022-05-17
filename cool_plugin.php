<?php

/**
* Plugin Name: New Cool Plugin
* Version: 1.0.0
* Requires php: 7.4
* Author: Carl Johansson
* Description: Ett coolt plugin som löser alla dina problem
*/

register_activation_hook(__FILE__, 'plugin_activated');

// register_deactivation_hook(__FILE__, 'plugin_deactivated');


function cool_plugin_setup_post_type(){

    register_post_type('wcm_travel', ['public' => true, 'label'  => 'WCM Travels', 'supports' => ['title', 'editor', 'thumbnail', 'custom-fields']]);
    register_post_type('travel_matches', ['public' => true, 'label'  => 'Matcher', 'supports' => ['title', 'editor', 'thumbnail', 'custom-fields']]);
    register_post_type('travel_cup', ['public' => true, 'label'  => 'Kupper', 'supports' => ['title', 'editor', 'thumbnail', 'custom-fields']]);
    register_post_type('travel_camp', ['public' => true, 'label'  => 'Läger', 'supports' => ['title', 'editor', 'thumbnail', 'custom-fields']]);
    register_post_type('travel_soccer', ['public' => true, 'label'  => 'Fotboll', 'supports' => ['title', 'editor', 'thumbnail', 'custom-fields']]);
    register_post_type('netr_team', ['public' => true, 'label'  => 'Lag', 'supports' => ['title', 'editor', 'thumbnail', 'custom-fields']]);

}


add_action('init', 'cool_plugin_setup_post_type');

function plugin_activated(){
    //Skapa custom post types

    cool_plugin_setup_post_type();

    create_sport_tax();

    flush_rewrite_rules();
}

function create_sport_tax(){

    register_taxonomy(
        'travel_age',
        array('wcm_travel', 'travel_camp', 'travel_cup', 'page'),
        array(
            'label' => 'Tid',
            'description' => 'En cool beskrivning',
            'hierarchical' => true,
        )
    );

    register_taxonomy(
        'travel_country',
        array('wcm_travel', 'travel_camp', 'travel_cup', 'travel_soccer', 'page'),
        array(
            'label' => 'Land',
            'hierarchical' => true,
        )
    );

    register_taxonomy(
        'travel_sport_league',
        array('wcm_travel', 'travel_soccer', 'travel_matches', 'page'),
        array(
            'label' => 'Liga',
            'hierarchical' => true,
        )
    );

    register_taxonomy(
        'travel_sport_type',
        array('wcm_travel', 'travel_camp', 'travel_cup', 'travel_soccer', 'page'),
        array(
            'label' => 'Sporttyp',
            'hierarchical' => true,
        )
    );

    register_taxonomy(
        'travel_type',
        array('wcm_travel', 'travel_camp', 'travel_cup', 'travel_matches', 'page'),
        array(
            'label' => 'Resetyp',
            'hierarchical' => true,
        )
    );
}

add_shortcode('cool_latest_matches', 'cool_show_latest_matches');

function cool_show_latest_matches($atts=[], $content = ""){

/**
 * array['namn' => 'hej']
 */
    $hej = explode(",", $atts['type']);
    $loop = new WP_Query(array('post_type' => $hej, 'orderby' => 'date', 'order' => 'ASC', 'posts_per_page' => 4));
    while ($loop->have_posts()) : $loop->the_post();

       $content .=  "<h1>" .  get_the_title() . "</h1>";



    endwhile;

    return $content;

}


function plugin_deactivated(){

}