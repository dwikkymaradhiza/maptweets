<!DOCTYPE html>
<html>
    <head>
        <title>Simple Map</title>
        <meta name="viewport" content="initial-scale=1.0">
        <meta charset="utf-8">
        {!! HTML::style('assets/bootstrap/css/bootstrap.min.css') !!} 
        {!! HTML::style('assets/css/style.css') !!} 
    </head>
    <body>
        <div id="loading">
            {!! HTML::image('assets/images/ajax-loader.gif' , 'Loading...' , array('id' => 'loading-image')) !!} 
        </div>
        <div id="map"></div>
        <div class="floating-panel search-title container-fluid">
            <div class="col-md-6 col-md-offset-3"></div>
        </div>
        <div class="floating-panel container-fluid menu">
            <div class="row">
                <div class="col-md-6 col-sm-4 col-xs-12">
                    <input type="text" id="location" placeholder="City name" />
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 search">
                    <button id="search">SEARCH</button>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 history">
                    <button id="history">HISTORY</button>
                </div>
            </div>
        </div>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?sensor=true&v=3&callback=initMap"
        async defer></script>
        <script>var apiUrl = "<?php echo url() ?>/index.php/search/tweets"; </script>
        {!! HTML::script('assets/bootstrap/js/bootstrap.min.js') !!} 
        {!! HTML::script('assets/js/script.js') !!} 
    </body>
</html>