'use strict';

import debounce from "debounce";
import _ from "lodash";
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
            fullscreenControl: true
        });

        this.markers = [];

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
        this.clearMarkers();

        _.forEach(data, (report) => this.addMarker(report));
    }

    addMarker(report) {
        let centre = new google.maps.LatLng(report.location.lat, report.location.lng);
        let icon = '/themes/harassmap/assets/img/map/' + (report.intervention ? 'intervention' : 'incident') + '.svg';

        let infowindow = new google.maps.InfoWindow({
            content: '<div class="infowindow">THIS IS A TEST</div>'
        });

        let marker = new google.maps.Marker({
            position: centre,
            map: this.map,
            icon: icon
        });

        marker.addListener('click', () => {
            infowindow.open(this.map, marker);
        });

        this.markers.push(marker);
    }

    clearMarkers() {
        _.forEach(this.markers, (marker) => {
            marker.setMap(null);
        });

        this.markers = [];
    }

}