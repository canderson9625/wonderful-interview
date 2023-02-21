<?php 
add_action('wp_enqueue_scripts', 'load_assets');
function load_assets() {
    wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/assets/css/main.css', null, filemtime(get_stylesheet_directory() . '/assets/css/main.css') );
    wp_enqueue_script( 'main-script', get_stylesheet_directory_uri() . '/assets/js/bundle.js', array(), filemtime(get_stylesheet_directory() . '/assets/js/bundle.js'), true );
    wp_enqueue_script( 'ajax-script', get_stylesheet_directory_uri() . '/assets/js/ajax.js', array(), filemtime(get_stylesheet_directory() . '/assets/js/ajax.js'), true );
    wp_localize_script( 'ajax-script', 'submissionObject',
		array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'file_upload' ),
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

    // $value = get_post_meta( $post->ID, '_submission', true );
    ?>
        <style>
            #airport-details .inside {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .input-group {
                /* display: flex;
                flex-wrap: wrap; */
                /* flex-basis: calc(50% - 15px); */
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

        
        <!-- <div class="input-group">
            <label>ID</label>
            <input type="text" name="submission_airport-id" value="<?php echo get_post_meta($post->ID, "submission_airport-id", true); ?>"></input>
        </div>

        <div class="input-group">
            <label>Airport Name</label>
            <input type="text" name="submission_airport-name" value="<?php echo get_post_meta($post->ID, "submission_airport-name", true); ?>"></input>
        </div>
        
        <div class="input-group">
            <label>City</label>
            <input type="text" name="submission_airport-city" value="<?php echo get_post_meta($post->ID, "submission_airport-city", true); ?>"></input>
        </div>
        
        <div class="input-group">
            <label>Country</label>
            <input type="text" name="submission_airport-country" value="<?php echo get_post_meta($post->ID, "submission_airport-country", true); ?>"></input>
        </div>
        
        <div class="input-group">
            <label>IATA/FAA</label>
            <input type="text" name="submission_airport-iata_faa" value="<?php echo get_post_meta($post->ID, "submission_airport-iata_faa", true); ?>"></input>
        </div>
        
        <div class="input-group">
            <label>ICAO</label>
            <input type="text" name="submission_airport-icao" value="<?php echo get_post_meta($post->ID, "submission_airport-icao", true); ?>"></input>
        </div>
        
        <div class="input-group">
            <label>Latitude</label>
            <input type="text" name="submission_airport-lat" value="<?php echo get_post_meta($post->ID, "submission_airport-lat", true); ?>"></input>
        </div>
        
        <div class="input-group">
            <label>Longitude</label>
            <input type="text" name="submission_airport-lng" value="<?php echo get_post_meta($post->ID, "submission_airport-lng", true); ?>"></input>
        </div>
        
        <div class="input-group">
            <label>Altitude</label>
            <input type="text" name="submission_airport-alt" value="<?php echo get_post_meta($post->ID, "submission_airport-alt", true); ?>"></input>
        </div>
        
        <div class="input-group">
            <label>Timezone</label>
            <input type="text" name="submission_airport-timezone" value="<?php echo get_post_meta($post->ID, "submission_airport-timezone", true); ?>"></input>
        </div> -->

        <div class="input-group">
            <label for="submission_airport-csv">Airports CSV</label>
            <textarea name="submission_airport-csv"><?php echo get_post_meta($post->ID, "submission_airport-csv", true); ?></textarea>
        </div>
    <?php
}

add_action( 'save_post', 'submission_save' );
function submission_save( $post_id ) {

    if (!isset($_POST["submission_nonce"]) || !wp_verify_nonce($_POST["submission_nonce"], 'submission_nonce_data') ) {
        return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'submission' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    }
    else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }


    //if there is data in post, then update the meta
	// if ( array_key_exists( 'submission_airport-id', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-id',
	// 		$_POST['submission_airport-id']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-name', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-name',
	// 		$_POST['submission_airport-name']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-city', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-city',
	// 		$_POST['submission_airport-city']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-country', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-country',
	// 		$_POST['submission_airport-country']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-iata_faa', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-iata_faa',
	// 		$_POST['submission_airport-iata_faa']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-icao', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-icao',
	// 		$_POST['submission_airport-icao']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-lat', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-lat',
	// 		$_POST['submission_airport-lat']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-lng', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-lng',
	// 		$_POST['submission_airport-lng']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-alt', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-alt',
	// 		$_POST['submission_airport-alt']
	// 	);
	// }
    // if ( array_key_exists( 'submission_airport-timezone', $_POST ) ) {
	// 	update_post_meta(
	// 		$post_id,
	// 		'submission_airport-timezone',
	// 		$_POST['submission_airport-timezone']
	// 	);
	// }
    if ( array_key_exists( 'submission_airport-csv', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-csv',
			$_POST['submission_airport-csv']
		);
	}
}

add_action( 'rest_api_init', 'submission_register_rest' );
function submission_register_rest() {

    register_rest_field( 'submission', 'submission_fields', array(
            'get_callback'          => 'get_post_meta_for_api',
            'update_callback'       => 'set_attachment_url' ,
            'show_in_rest'          => true,
            'auth_callback'	        => 'attachment_url_permission_check',
        )
    );
}

function get_post_meta_for_api( $object ) {
    //get the id of the post object array
    $post_id = $object['id'];
    

    //return the post meta
    return get_post_meta( $post_id );
}


//admin ajax for submission post creation
// add_action( 'admin_ajax_submit', 'submission_ajax_handler' );
// function submission_ajax_handler() {


//     // $post_data = array();
//     // //handle your form data here by accessing $_POST

//     // $new_post_ID = wp_insert_post( $post_data );

//     // // send some information back to the javascipt handler
//     // $response = array(
//     //     'status' => '200',
//     //     'message' => 'OK',
//     //     'new_post_ID' => $new_post_ID
//     // );

//     // // normally, the script expects a json respone
//     // header( 'Content-Type: application/json; charset=utf-8' );
//     // echo json_encode( $response );

//     // exit; // important
// }


// $csv= file_get_contents($file);
// $array = array_map("str_getcsv", explode("\n", $csv));
// $json = json_encode($array);
// $login = 'root';
// $password = 'root';
// $response = wp_remote_get(
//     'http://localhost:10089/wp-json/wp/v2/submission',
//     // array(
//     //     'headers' => array(
//     //         'Authorization' => 'Basic ' . base64_encode( "$login:$password" )
//     //     ),
//     //     'body' => array(
//     //         'title'   => 'My test',
//     //         'status'  => 'draft',
//     //     )
//     // )
// );
// var_dump($response);

add_action( 'wp_ajax_nopriv_submission_create', 'submission_create' );
add_action( 'wp_ajax_submission_create', 'submission_create' );
function submission_create() {

    

    // var_dump($_POST);
    // var_dump($_FILES);
    // var_dump($_REQUEST['_wpnonce']);
    // var_dump(file_get_contents($_FILES["submission"]["tmp_name"]));
    if ( wp_verify_nonce($_POST['nonce'], 'file_upload') ) {

        $post_name_cache = array();
        $name = "";
        $max = 999;
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
                echo 'break';
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
            return 'success';
        }
    } 

    // we could also process the csv into a header array then loop over that
    // and store the values for each header

    return 'failed';

    // Create post object
    // $new_pva_post = array(
    //     'post_type'     => 'page',
    //     'post_title'    => $post_title,
    //     'post_status'   => 'publish',
    //     'post_author'   => 1,
    // );

    // Insert the post into the database
    // wp_insert_post( $new_pva_post );


    
};