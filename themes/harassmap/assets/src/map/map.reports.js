'use strict';

import mapStyle from "./map.style.json";

export class ReportsPageMap {

    constructor(element) {
        let lat = element.dataset.lat,
            lng = element.dataset.lng,
            type = element.dataset.type,
            centre = new google.maps.LatLng(lat, lng),
            icon = '/themes/harassmap/assets/img/map/' + type + '.svg';

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
            icon: icon,
            map: this.map
        });

        google.maps.event.addDomListener(window, 'resize', () => {
            this.map.setCenter(centre);
        });
    }

}