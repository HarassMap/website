'use strict';

import debounce from "debounce";
import mapStyle from "./map.style.json";

export class HomePageMap {

    constructor(element) {
        let lat = element.dataset.lat,
            lng = element.dataset.lng,
            zoom = element.dataset.zoom,
            centre = new google.maps.LatLng(lat, lng);

        this._element = element;

        this.map = new google.maps.Map(element, {
            zoom: parseInt(zoom),
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

        google.maps.event.addListener(this.map, 'bounds_changed', debounce(() => {
            this.getReports();
        }, 500));
    }

    /**
     *
     */
    getReports() {
        let bounds = this.map.getBounds().toJSON();

        $.request('onGetReports', {
            data: {
                bounds
            },
            success: (data) => {
                this.addMarkers(data);
            }
        });
    }

    /**
     *
     * @param data
     */
    addMarkers(data) {
        console.debug(data);
    }

}