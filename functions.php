<?php 
function load_assets() {
    wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/assets/css/main.css', null, filemtime(get_stylesheet_directory() . '/assets/css/main.css') );
    wp_enqueue_script( 'main-script', get_stylesheet_directory_uri() . '/assets/js/bundle.js', array(), filemtime(get_stylesheet_directory() . '/assets/js/bundle.js'), true );
}
add_action('wp_enqueue_scripts', 'load_assets');


//custom submission post type
function submission_cpt() {

    $args = array(
        'label'                => 'Submissions',
        'public'               => true,
        'register_meta_box_cb' => 'submission_cpt_add_meta_box',
        'supports'             => array('title'),
    );

    register_post_type( 'submission', $args );
}
add_action( 'init', 'submission_cpt' );


//we need to store fields for each line of the csv in the one submission
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
add_action( 'add_meta_boxes', 'submission_cpt_add_meta_box');

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
                flex-basis: calc(50% - 15px);
            }

            label {
                display: block;
                margin-top: 15px;
            } 

            input {
                width: 100%;
            }
        </style>

        
        <div class="input-group">
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
        </div>
    <?php
}


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
	if ( array_key_exists( 'submission_airport-id', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-id',
			$_POST['submission_airport-id']
		);
	}
    if ( array_key_exists( 'submission_airport-name', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-name',
			$_POST['submission_airport-name']
		);
	}
    if ( array_key_exists( 'submission_airport-city', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-city',
			$_POST['submission_airport-city']
		);
	}
    if ( array_key_exists( 'submission_airport-country', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-country',
			$_POST['submission_airport-country']
		);
	}
    if ( array_key_exists( 'submission_airport-iata_faa', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-iata_faa',
			$_POST['submission_airport-iata_faa']
		);
	}
    if ( array_key_exists( 'submission_airport-icao', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-icao',
			$_POST['submission_airport-icao']
		);
	}
    if ( array_key_exists( 'submission_airport-lat', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-lat',
			$_POST['submission_airport-lat']
		);
	}
    if ( array_key_exists( 'submission_airport-lng', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-lng',
			$_POST['submission_airport-lng']
		);
	}
    if ( array_key_exists( 'submission_airport-alt', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-alt',
			$_POST['submission_airport-alt']
		);
	}
    if ( array_key_exists( 'submission_airport-timezone', $_POST ) ) {
		update_post_meta(
			$post_id,
			'submission_airport-timezone',
			$_POST['submission_airport-timezone']
		);
	}
}
add_action( 'save_post', 'submission_save' );