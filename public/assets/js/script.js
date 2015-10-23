/**
 *  Author : Dwikky Maradhiza
 */

(function ($) {
    $(window).load(function () {
        $('#loading').hide();
    });
})(jQuery);

var map;
var infoWindows;
var interval = 200;
var markers = [];
var tweetsLatLon = {};
var cityLatLon = {lat: 52.511, lng: 13.447};

$("#search").click(function () {
    var locName = document.getElementById('location').value;
    var geocoder = new google.maps.Geocoder();
    if(locName == ""){
        alert("Please input city name.");
        return false;
    }
    geocoder.geocode({'address': locName}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            $.ajax({
                url: 'http://localhost/map/public/index.php/search/tweets',
                data: {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng(), words: locName},
                dataType: 'json',
                type: 'GET',
                beforeSend: function(){
                    $("#loading").show();
                },
                error: function (e) {
                    alert('Something got wrong : [' + e.status + '] ' + e.statusText);
                    return false;
                },
                success: function (e) {
                    if (!e.stat) {
                        alert('Sorry, we can not find '+ locName +' city.');
                        ;
                    }

                    tweetsLatLon = e.tweets;

                    cityLatLon = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                    map.panTo(cityLatLon);
                    map.setZoom(12);

                    $(".search-title").text("Tweets about " + locName);
                    drop(tweetsLatLon);
                },
                complete: function(){
                    $("#loading").hide();
                }
            });
        } else {
            alert('Sorry, we can not find '+ locName +' city.');
        }
    });
});

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: cityLatLon,
        zoom: 12,
        mapTypeControlOptions: {
            position: google.maps.ControlPosition.TOP_RIGHT
        },
        streetViewControlOptions: {
            position: google.maps.ControlPosition.TOP_LEFT,
        },
        zoomControlOptions: {
            position: google.maps.ControlPosition.TOP_LEFT,
        }
    });
}

function drop(tweetsLatLon) {
    clearMarkers();
    for (var i = 0; i < tweetsLatLon.length; i++) {
        addMarkerWithTimeout(tweetsLatLon[i], i * interval);
    }
}

function addMarkerWithTimeout(markerData, timeout) {
    var i = timeout / interval;
    window.setTimeout(function () {
        markers.push(new google.maps.Marker({
            position: new google.maps.LatLng(markerData.position.lat, markerData.position.lng),
            map: map,
            icon: markerData.icon,
            animation: google.maps.Animation.DROP
        }));

        markers[i].addListener('click', function () {
            if (infoWindows) {
                infoWindows.close();
            }
            infoWindows = new google.maps.InfoWindow({content: markerData.tweet});
            infoWindows.open(map, markers[i]);
        });

    }, timeout);
}

function clearMarkers() {
    if (infoWindows) {
        infoWindows.close();
    }

    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }

    markers = [];
}

