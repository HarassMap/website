'use strict';

import debounce from "debounce";
import Handlebars from "handlebars";
import _ from "lodash";
import moment from "moment";
import { emitter, FILTER_MAP, REFRESH_MAP } from '../utils/events';
import mapStyle from "./map.style.json";

export class HomePageMap {

    constructor(element) {
        let lat = element.dataset.lat,
            lng = element.dataset.lng,
            zoom = element.dataset.zoom;

        // get the link for the read more
        this.link = element.dataset.link;

        this._element = element;

        this.center = new google.maps.LatLng(lat, lng);

        this.map = new google.maps.Map(element, {
            zoom: parseInt(zoom),
            center: this.center,
            styles: mapStyle,
            scrollwheel: false,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_TOP
            },
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false
        });

        this.markers = [];
        this.windows = [];

        google.maps.event.addListener(this.map, 'bounds_changed', debounce(() => {
            this.center = this.map.getCenter();

            this.getReports();
        }, 500));

        emitter.on(REFRESH_MAP, () => google.maps.event.trigger(this.map, 'resize'));
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
            success: (data) => this.addMarkers(data)
        });
    }

    /**
     *
     * @param data
     */
    addMarkers(data) {
        let new_ids = _.map(data, 'public_id');
        let old_ids = _.map(this.markers, 'id');
        let remove_ids = _.difference(old_ids, new_ids);

        // first we remove the markers we don't need
        _.forEach(this.markers, (marker) => {
            if (_.indexOf(remove_ids, marker.id) !== -1) {
                marker.setMap(null);
            }
        });

        // remove the marker/windows that have been removed from the map
        this.windows = _.filter(this.windows, (window) => _.indexOf(remove_ids, window.id) === -1);
        this.markers = _.filter(this.markers, (marker) => marker.map);

        // add all the markers that need to be added
        _.forEach(data, (report) => {
            if (_.indexOf(old_ids, report.public_id) === -1) {
                this.addMarker(report);
            }
        });
    }

    addMarker(report) {
        let centre = new google.maps.LatLng(report.location.lat, report.location.lng);
        let icon = '/themes/harassmap/assets/img/map/' + (report.intervention ? 'intervention' : 'incident') + '.svg';
        let date = moment(report.date);
        let location = report.location;
        let address = location.address + ', ' + location.city + ', ' + location.region;
        let source = $("#info-template").html();
        let template = Handlebars.compile(source);

        let infowindow = new google.maps.InfoWindow({
            id: report.public_id,
            content: template({
                time: date.format("L, LT"),
                address: address,
                link: this.link.replace('REPORT_ID', report.public_id)
            })
        });

        let marker = new google.maps.Marker({
            position: centre,
            map: this.map,
            icon: icon,
            id: report.public_id
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

}