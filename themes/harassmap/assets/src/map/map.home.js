'use strict';

import debounce from "debounce";
import _ from "lodash";
import moment from "moment";
import mapStyle from "./map.style.json";
import Handlebars from "handlebars";

export class HomePageMap {

    constructor(element) {
        let lat = element.dataset.lat,
            lng = element.dataset.lng,
            zoom = element.dataset.zoom,
            centre = new google.maps.LatLng(lat, lng);

        // get the link for the read more
        this.link = element.dataset.link;

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
        this.windows = [];

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
        let date = moment(report.date);
        let location = report.location;
        let address = location.address + ', ' + location.city + ', ' + location.region;
        let source   = $("#info-template").html();
        let template = Handlebars.compile(source);

        let infowindow = new google.maps.InfoWindow({
            content: template({
                time: date.format("L, LT"),
                address: address,
                link: this.link.replace('REPORT_ID', report.public_id)
            })
        });

        let marker = new google.maps.Marker({
            position: centre,
            map: this.map,
            icon: icon
        });

        marker.addListener('click', () => {
            this.closeWindows();
            infowindow.open(this.map, marker);
        });

        this.windows.push(infowindow);
        this.markers.push(marker);
    }

    closeWindows() {
        _.forEach(this.windows, (window) => {
            window.close();
        });
    }

    clearMarkers() {
        _.forEach(this.markers, (marker) => {
            marker.setMap(null);
        });

        this.windows = [];
        this.markers = [];
    }

}