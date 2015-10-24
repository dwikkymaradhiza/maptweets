/**
 *  Author : Dwikky Maradhiza
 */

$(function () {
    $(window).load(function () {
        $('#loading').hide();
    });
});

var map;
var infoWindows;
var interval = 200;
var markers = [];
var tweetsLatLon = {};
var cityLatLon = {lat: 52.511, lng: 13.447};

var menuRight = document.getElementById('cbp-spmenu-history'),
        showRight = document.getElementById('showRight'),
        hideRight = document.getElementById('hideRight'),
        searchButton = document.getElementById('search'),
        body = document.body;

showRight.onclick = function () {
    classie.toggle(this, 'active');
    classie.toggle(menuRight, 'cbp-spmenu-open');
};

hideRight.onclick = function () {
    classie.toggle(this, 'active');
    classie.toggle(menuRight, 'cbp-spmenu-open');
};

var searchHistory = function () {
    $('.location-history').off();
    $('.location-history').on('click', function () {
        classie.toggle(this, 'active');
        classie.toggle(menuRight, 'cbp-spmenu-open');

        var cityName = $(this).html();

        $("#location").val(cityName);
        $("#search").trigger('click');
    });
}

var addNewHistory = function(locName) {
    var isNew = true;
    var el = document.createElement('a');
    el.href = '#';
    el.className = 'location-history';
    el.textContent = locName.toUpperCase();
    
    $('.location-history').each(function() {
        if($(this).html() == locName.toUpperCase()){
            isNew = false;
        }
    });
    
    if(isNew) {
        var afterDiv = document.getElementById('hideRight').nextSibling;
        if (afterDiv === null) {
            document.getElementById('cbp-spmenu-history').appendChild(el);
        } else {
            document.getElementById('cbp-spmenu-history').insertBefore(el, afterDiv);
        }
        
        searchHistory();
    }
}

searchHistory();

$("#search").on('click', function () {
    var locName = document.getElementById('location').value;
    var geocoder = new google.maps.Geocoder();
    if (locName == "") {
        alert("Please input city name.");
        return false;
    }
    geocoder.geocode({'address': locName}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            $.ajax({
                url: apiUrl,
                data: {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng(), words: locName},
                dataType: 'json',
                type: 'GET',
                beforeSend: function () {
                    $("#loading").show();
                },
                error: function (e) {
                    alert('Something got wrong : [' + e.status + '] ' + e.statusText);
                    return false;
                },
                success: function (e) {
                    if (!e.stat) {
                        alert('Sorry, we can not find ' + locName + ' city.');
                        ;
                    }

                    tweetsLatLon = e.tweets;

                    cityLatLon = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                    map.panTo(cityLatLon);
                    map.setZoom(12);

                    $(".search-title").text("Tweets about " + locName);
                    drop(tweetsLatLon);

                    //add new history element
                    addNewHistory(locName);
                },
                complete: function () {
                    $("#loading").hide();
                }
            });
        } else {
            alert('Sorry, we can not find ' + locName + ' city.');
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

