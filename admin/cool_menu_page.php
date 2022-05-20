<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg_options"
        settings_fields('wcm_menu');
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections('wcm_menu');
        // output save settings button
        submit_button(__('Save Settings', 'textdomain'));
        ?>
    </form>

    <?php $repos = get_github_repos();

    echo $repos['response']['code'];

    if (isset($repos['message']) == 'Not Found'){
        echo '<h2>User not found</h2>';
    } else {
        echo '<h2>Dina Repon</h2>';
        foreach($repos as $repo){
            echo '<h3>' . $repo['name'] . '</h3>';
            echo '<p>' . 'Skapad: ' . $repo['created_at'] . '</p>';
            echo '<p>' . 'Språk: ' . $repo['language'] . '</p>';
            echo '<p>' . 'Watchers: ' . $repo['watchers'] . '</p>';
            echo '<a href="' . $repo['html_url'] . '" target="_blank">Gå till repot</a>';
            echo '<hr>';
        }
    }


    // echo '<h2>Dina Repon</h2>';

    // if (!empty($repos)){

    //     foreach($repos as $repo){
    //         echo '<h3>' . $repo['name'] . '</h3>';
    //         echo '<p>' . 'Skapad: ' . $repo['created_at'] . '</p>';
    //         echo '<p>' . 'Språk: ' . $repo['language'] . '</p>';
    //         echo '<p>' . 'Watchers: ' . $repo['watchers'] . '</p>';
    //         echo '<a href="' . $repo['html_url'] . '" target="_blank">Gå till repot</a>';
    //         echo '<hr>';
    //     }

    // } else{
    //     echo 'något är fel';
    // }
    ?>




</div>