'use strict';

import mapStyle from "./map.style.json";

export class ReportPageMap {

    constructor(element) {
        let centre = new google.maps.LatLng(30.044420, 31.235712);

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

        let latInput = document.getElementById(element.dataset.lat);
        let lonInput = document.getElementById(element.dataset.lon);

        this.marker = new google.maps.Marker({
            position: centre,
            draggable: true
        });

        const listener = (event) => {
            if (latInput) {
                latInput.value = event.latLng.lat();
            }

            if (lonInput) {
                lonInput.value = event.latLng.lng();
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