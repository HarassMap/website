'use strict';

import debounce from "debounce";
import Handlebars from "handlebars";
import _ from "lodash";
import moment from "moment";
import { emitter, FILTER_MAP, REFRESH_MAP, CENTER_MAP } from '../utils/events';
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
        this.filters = {};

        this.markerCluster = new MarkerClusterer(
            this.map,
            [],
            {
                imagePath: '/themes/harassmap/assets/img/markers/m'
            }
        );

        google.maps.event.addListener(this.map, 'bounds_changed', debounce(() => {
            this.center = this.map.getCenter();

            this.getReports();
        }, 500));

        google.maps.event.addDomListener(window, 'resize', () => {
            this.map.setCenter(this.center);
        });

        emitter.on(REFRESH_MAP, () => google.maps.event.trigger(this.map, 'resize'));
        emitter.on(FILTER_MAP, (filters) => this.getReports(filters));
        emitter.on(CENTER_MAP, (center) => this.map.setCenter(center));
    }

    /**
     *
     */
    getReports(filters = {}) {
        let bounds = this.map.getBounds().toJSON();

        this.filters = {
            ...this.filters,
            ...filters
        };

        $.request('onGetReports', {
            data: {
                bounds,
                filters: this.filters
            },
            success: (data) => this.addMarkers(data)
        });
    }

    /**
     *
     * @param data
     */
    addMarkers(data) {
        let new_ids = _.map(data, 'public_id'),
            old_ids = _.map(this.markers, 'id'),
            remove_ids = _.difference(old_ids, new_ids);

        // first we remove the markers we don't need
        let old_markers = [];
        _.forEach(this.markers, (marker) => {
            if (_.indexOf(remove_ids, marker.id) !== -1) {
                marker.remove = true;
                old_markers.push(marker);
            }
        });
        this.markerCluster.removeMarkers(old_markers);

        // remove the marker/windows that have been removed from the map
        this.windows = _.filter(this.windows, (window) => _.indexOf(remove_ids, window.id) === -1);
        this.markers = _.filter(this.markers, (marker) => !marker.remove);

        // add all the markers that need to be added
        let new_markers = [];
        _.forEach(data, (report) => {
            if (_.indexOf(old_ids, report.public_id) === -1) {
                new_markers.push(this.addMarker(report));
            }
        });
        this.markerCluster.addMarkers(new_markers);
    }

    addMarker(report) {
        let centre = new google.maps.LatLng(report.location.lat, report.location.lng);
        let icon = '/themes/harassmap/assets/img/map/' + (report.intervention ? 'intervention' : 'incident') + '.svg';
        let date = moment(report.date);
        let location = report.location;
        let address = location.address + ', ' + location.city;
        let source = $("#info-template").html();
        let template = Handlebars.compile(source);

        let infowindow = new google.maps.InfoWindow({
            id: report.public_id,
            content: template({
                time: date.format("L, LT"),
                address: _.truncate(address),
                link: this.link.replace('REPORT_ID', report.public_id)
            })
        });

        let marker = new google.maps.Marker({
            position: centre,
            icon: icon,
            id: report.public_id,
            remove: false
        });

        marker.addListener('click', () => {
            this.closeWindows();
            infowindow.open(this.map, marker);
        });

        this.windows.push(infowindow);
        this.markers.push(marker);

        return marker;
    }

    closeWindows() {
        _.forEach(this.windows, (window) => {
            window.close();
        });
    }

}