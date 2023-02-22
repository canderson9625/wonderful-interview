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

    <section class="share-container">
        <p><strong>Nice Airports!</strong> Your Shareable url is:</p>
        <div class="input-wrapper">
            <input type="text" name="share" id="share" value="<?php echo get_permalink(); ?>"></input>
            <div class="svg"><?php echo file_get_contents(__DIR__ . '/assets/img/copy.svg');?></div>
        </div>
    </section>
</main>

<div id="map">
    <!-- Google requires a cb but we need to localize before loading map  -->
    <script>function initMap() {}</script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBv_GoHe7K6FV_u4CtOybV7ZHceMWVBy68&callback=initMap"></script>
</div>

<?php get_footer(); ?>