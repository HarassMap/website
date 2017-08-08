'use strict';

import { CENTER_MAP, emitter, MOVE_MARKER } from '../utils/events';
import mapStyle from "./map.style.json";

export class ReportPageMap {

    constructor(element) {
        let lat = element.dataset.lat,
            lng = element.dataset.lng,
            latInput = document.getElementById(element.dataset.latInput),
            lngInput = document.getElementById(element.dataset.lngInput);

        this.center = new google.maps.LatLng(lat, lng);

        this._element = element;

        this.map = new google.maps.Map(element, {
            zoom: 15,
            center: this.center,
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
            position: this.center,
            draggable: true
        });

        const listener = (event) => {
            $('#marker-moved').val(true);

            if (latInput && lngInput) {

                latInput.value = event.latLng.lat().toFixed(5);
                lngInput.value = event.latLng.lng().toFixed(5);

                this.center = new google.maps.LatLng(latInput.value, lngInput.value);
            }

        };

        // add the listeners
        google.maps.event.addListener(this.marker, 'dragend', (event) => {
            listener(event);

            emitter.emit(MOVE_MARKER, this.center);
        });

        google.maps.event.addListener(this.marker, 'drag', listener);

        // add the marker to the map
        this.marker.setMap(this.map);

        emitter.on(CENTER_MAP, (center) => this.setCenter(center));
        emitter.on(MOVE_MARKER, (center) => this.marker.setPosition(center));
    }

    setCenter(position) {
        this.map.setCenter(position);
        this.marker.setPosition(position);
    }

}