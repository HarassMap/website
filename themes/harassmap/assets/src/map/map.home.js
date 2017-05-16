'use strict';

import mapStyle from "./map.style.json";

export class HomePageMap {

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
    }

}