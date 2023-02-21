<?php 
/* 
Template Name: airport
Post Type: submission 
*/ 
    
get_header();
$id = get_the_id();
$post_meta = get_post_meta($id, 'submission_airport-csv');
wp_localize_script('ajax-script', 'post_meta', array(
    "data" => $post_meta
));
?>


<main>
    <header>
        <h1>The Amazing Airport Visualizer</h1>
    </header>
</main>

<div id="map">
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBv_GoHe7K6FV_u4CtOybV7ZHceMWVBy68&callback=initMap"></script>
</div>

<?php get_footer(); ?>