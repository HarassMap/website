'use strict';

import mapStyle from "./map.style.json";

export class ReportPageMap {

    constructor(element) {
        let lat = element.dataset.lat,
            lng = element.dataset.lng,
            latInput = document.getElementById(element.dataset.latInput),
            lngInput = document.getElementById(element.dataset.lngInput),
            centre = new google.maps.LatLng(lat, lng);

        this._element = element;

        this.map = new google.maps.Map(element, {
            zoom: 15,
            center: centre,
            styles: mapStyle,
            scrollwheel: false,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false
        });

        this.marker = new google.maps.Marker({
            position: centre,
            draggable: true
        });

        const listener = (event) => {
            $('#marker-moved').val(true);

            if (latInput) {
                latInput.value = event.latLng.lat().toFixed(5);
            }

            if (lngInput) {
                lngInput.value = event.latLng.lng().toFixed(5);
            }
        };

        // add the listeners
        google.maps.event.addListener(this.marker, 'dragend', listener);
        google.maps.event.addListener(this.marker, 'drag', listener);

        // add the marker to the map
        this.marker.setMap(this.map);
    }

    setCenter(position) {
        this.map.setCenter(position);
        this.marker.setPosition(position);
    }

}