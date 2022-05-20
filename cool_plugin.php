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




// HEJ JOHN! Vi flyttar över hela vår kod som gäller custom post types från functions.php under themes
// och så gör vi såhär istället :) fråga så ska jag försöka förklara!
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


// Här nedan testar vi lite shortcodes, sådana som vi hitta i content som såg ut typ: [wcm_travels type=travel_cup]
// Skapa en post, exempelvis match och skriv sedan [cool_latest_matches type='travel_camp,travel_cup'] i content editorn

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

/**
 * Här skapar vi en ny meny i admin.
 */
add_action('admin_menu', 'wcm_admin_menu');
function wcm_admin_menu()
{
    add_menu_page(
        'Cool Menu',
        'Your Github Repos',
        'manage_options',
        'wcm_menu',
        'wcm_admin_menu_page',
        'dashicons-learn-more',
        20
    );
}

/* Vi har extraherat view koden för meny-sidan och inkludera det här */
function wcm_admin_menu_page()
{
    include plugin_dir_path(__FILE__) . 'admin/cool_menu_page.php';
}




/**
 * Lägg till settings på vår nya admin sida
 */
function wcm_setttings_init()
{

    register_setting('wcm_menu', 'wcm_setting_name');
    register_setting('wcm_menu', 'wcm_setting_user');


    /* Skapar en settings sektion */
    add_settings_section(
        'wcm_main_settings',
        'WCM Inställningar',
        'wcm_settings_sections_html',
        'wcm_menu'
    );

    /* Skapar fält för settings */
    add_settings_field(
        'wcm_settings_field',
        'API Nyckel',
        'wcm_api_field_html',
        'wcm_menu',
        'wcm_main_settings'
    );



    add_settings_field(
        'wcm_user_field',
        'Användarnamn',
        'wcm_username_field_html',
        'wcm_menu',
        'wcm_main_settings'

    );
}
add_action('admin_init', 'wcm_setttings_init');



function wcm_settings_sections_html()
{
    echo '<p>Fyll i din api-nyckel och användarnamn</p>';
}


function wcm_username_field_html(){

    $user_key = get_option('wcm_setting_user');


    $putout = '<input type="text" name="wcm_setting_user" value="';
    $putout .= isset($user_key) ? esc_attr($user_key) : '';
    $putout .= '" />';

    echo $putout;
}

function wcm_api_field_html()
{
    $api_key = get_option('wcm_setting_name');


    $output = '<input type="text" name="wcm_setting_name" value="';
    $output .= isset($api_key) ? esc_attr($api_key) : '';
    $output .= '" />';


    echo $output;
}

function get_github_user_data(){

    wp_remote_get('https://api.github.com/users/carljohansson1992');
}

function get_github_repos(){

    $args = array(
        'headers' => array(
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(get_option('wcm_setting_user') . ":" . get_option('wcm_setting_name'))
        )
        );

    $githubUserRepos = get_transient('cool_github_userdata');
    $inputUser = get_option('wcm_setting_user');

    if($githubUserRepos == false){

    return $githubUser = wp_remote_get('https://api.github.com/users/' . $inputUser . '/repos', $args);
    $githubUserRepos = wp_remote_retrieve_body($githubUser);
    set_transient('cool_github_userdata', $githubUserRepos, 60*60);
    }
    delete_transient('cool_github_userdata');

    return json_decode($githubUserRepos, true);
}

function plugin_deactivated(){

}