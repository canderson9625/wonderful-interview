<?php 
add_action('wp_enqueue_scripts', 'load_assets');
function load_assets() {
    wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/assets/css/main.css', null, filemtime(get_stylesheet_directory() . '/assets/css/main.css') );
    wp_enqueue_script( 'main-script', get_stylesheet_directory_uri() . '/assets/js/bundle.js', array(), filemtime(get_stylesheet_directory() . '/assets/js/bundle.js'), true );
    wp_enqueue_script( 'ajax-script', get_stylesheet_directory_uri() . '/assets/js/ajax.js', array(), filemtime(get_stylesheet_directory() . '/assets/js/ajax.js'), false );
    wp_localize_script( 'ajax-script', 'submissionObject',
		array( 
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'nonce'      => wp_create_nonce( 'file_upload' ),
            'theme_path' => get_stylesheet_directory_uri(),
		)
	);
}


//custom submission post type
add_action( 'init', 'submission_cpt' );
function submission_cpt() {

    $args = array(
        'label'                => 'Submissions',
        'public'               => true,
        'register_meta_box_cb' => 'submission_cpt_add_meta_box',
        'supports'             => array('title'),
        'show_in_rest'         => true,
    );

    register_post_type( 'submission', $args );
}

//we need to store fields for each line of the csv in the one submission
add_action( 'add_meta_boxes', 'submission_cpt_add_meta_box');
function submission_cpt_add_meta_box( $post_type ) {

    add_meta_box(
        'airport-details',
        __('Airport Details'),
        'submission_cpt_meta_box_markup',
        $post_type,
        'advanced',
        'high',
    );

}
function submission_cpt_meta_box_markup( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'submission_nonce_data', 'submission_nonce' );

    ?>
        <style>
            #airport-details .inside {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .input-group {
                width: 100%;
                flex-basis: 100%;
            }

            label {
                display: block;
                margin-top: 15px;
            } 

            input,
            textarea {
                width: 100%;
            }
        </style>

        <div class="input-group">
            <label for="submission_airport-csv">Airports CSV</label>
            <textarea name="submission_airport-csv"><?php echo get_post_meta($post->ID, "submission_airport-csv", true); ?></textarea>
        </div>
    <?php
}

add_action( 'save_post', 'submission_save' );
function submission_save( $post_id ) {

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if (!isset($_POST["submission_nonce"]) || !wp_verify_nonce($_POST["submission_nonce"], 'submission_nonce_data') ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    //if there is data in post, then update the meta
    if ( array_key_exists( 'submission_airport-csv', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-csv',
			$_POST['submission_airport-csv']
		);
	}
}

add_action( 'wp_ajax_nopriv_submission_create', 'submission_create' );
add_action( 'wp_ajax_submission_create', 'submission_create' );
function submission_create() {

    if ( wp_verify_nonce($_POST['nonce'], 'file_upload') ) {

        $post_name_cache = array();
        $name = "";
        $max = 99999;
        while ( count($post_name_cache) < $max ) {
            $name = random_int(1, $max);
            $name = sprintf("%'05d", $name);
            
            if ( in_array($name, $post_name_cache, true) ) { 
                //check that name has not already been generated before and restart the loop if it has
                continue; 
            }

            $query = new WP_Query([
                "post_type" => 'submission',
                "name" => $name
            ]);
    
            //a post with the name was not found, safe to leave while loop
            if ( $query->have_posts() == false ) {
                break;
            } else {
                array_push($post_name_cache, $name);
                $name = "";
            }
        }
            
        if ($name !== "" && isset($_FILES["submission"]["tmp_name"])) {
            wp_insert_post(array(
                'post_title' => $name,
                'post_content' => "",
                'post_status' => "publish",
                'post_type' => 'submission',
                'meta_input'   => array(
                    'submission_airport-csv' => file_get_contents($_FILES["submission"]["tmp_name"])
                ),
            ));
            echo $name;
            die();
        }
    } 
    echo 'failed';
    return;
};