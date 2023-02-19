<?php get_header(); ?>
    
<main>
    <header>
        <h1>The Amazing Airport Visualizer</h1>
    </header>

    <section>
        <h2>Share your favorite airports!</h2>
        <p>Upload a CSV document with your favorite airports. We'll put them on a map, and provide a shareable url.</p>

        <div class="modal">
            <input type="file" accept=".csv">Select File</input>
            <p>Drag and drop a CSV file, or select one from your computer.</p>
        </div>
    </section>
</main>

<div id="map">
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBv_GoHe7K6FV_u4CtOybV7ZHceMWVBy68&callback=initMap"></script>
</div>
    
<?php get_footer(); ?>