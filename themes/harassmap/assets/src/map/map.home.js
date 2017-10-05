'use strict';

import debounce from "debounce";
import Handlebars from "handlebars";
import _ from "lodash";
import moment from "moment";
import { CENTER_MAP, emitter, FILTER_MAP, REFRESH_MAP } from '../utils/events';
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

        this.infowindow = new google.maps.InfoWindow({
            content: '',
            maxHeight: 200
        });

        this.markers = [];
        this.filters = {};

        this.markerCluster = new MarkerClusterer(
            this.map,
            [],
            {
                imagePath: '/themes/harassmap/assets/img/markers/m',
                imageExtension: 'svg',
                maxZoom: 22
            }
        );

        this.markerCluster.styles_ = _.map(this.markerCluster.styles_, (style) => {
            return {
                ...style,
                textColor: "#FFFFFF"
            }
        });

        google.maps.event.addListener(this.markerCluster, 'clusterclick', (cluster) => {
            console.debug(this.map.getZoom());
            if (this.map.getZoom() >= this.markerCluster.getMaxZoom()) {
                let markers = cluster.getMarkers();
                let content = '<div class="cluster-info">';

                _.forEach(markers, (marker) => {
                    content += marker.info;
                });

                content += '</div>';

                this.infowindow.close();
                this.infowindow.setContent(content);
                this.infowindow.setPosition(cluster.getCenter());
                this.infowindow.open(this.map);

            }
        });

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

        console.time("HOME_MAP_REQUEST");

        $.request('onGetReports', {
            data: {
                bounds,
                filters: this.filters
            },
            success: (data) => {
                console.timeEnd("HOME_MAP_REQUEST");
                console.time("MARKERS_ADD");
                this.addMarkers(data);
            }
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
        this.markers = _.filter(this.markers, (marker) => !marker.remove);

        // add all the markers that need to be added
        let new_markers = [];
        _.forEach(data, (report) => {
            if (_.indexOf(old_ids, report.public_id) === -1) {
                new_markers.push(this.addMarker(report));
            }
        });
        this.markerCluster.addMarkers(new_markers);

        console.timeEnd("MARKERS_ADD");
    }

    addMarker(report) {
        let centre = new google.maps.LatLng(report.location.lat, report.location.lng);
        let icon = '/themes/harassmap/assets/img/map/' + (report.intervention ? 'intervention' : 'incident') + '.svg';
        let date = moment(report.date);
        let location = report.location;
        let address = location.address + ', ' + location.city;
        let source = $("#info-template").html();
        let template = Handlebars.compile(source);

        let marker = new google.maps.Marker({
            position: centre,
            icon: icon,
            id: report.public_id,
            remove: false,
            info: template({
                time: date.format("L, LT"),
                address: _.truncate(address),
                link: this.link.replace('REPORT_ID', report.public_id)
            })
        });

        google.maps.event.addListener(marker, 'click', () => {
            this.infowindow.close();
            this.infowindow.setContent(marker.info);
            this.infowindow.open(this.map, marker);
        });

        this.markers.push(marker);

        return marker;
    }

}