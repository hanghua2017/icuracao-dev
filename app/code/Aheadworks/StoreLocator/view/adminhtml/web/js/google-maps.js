/**
* Copyright 2018 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'jquery/ui',
], function ($) {
    'use strict';

    $.widget('aheadworks.googleMaps', {
        options: {
            messageSelector: '#google-map-message',
            mapSelector: 'google-maps-content',
            zoomSelector: '#location_zoom',
            latitudeSelector: '#location_latitude',
            longitudeSelector: '#location_longitude',
            countrySelector: '#location_country_id',
            regionSelector: '#location_region_id',
            citySelector: '#location_city',
            streetSelector: '#location_street'
        },

        _create: function() {
            this._initMap();
        },

        _initMap: function () {
            this.latlng = new google.maps.LatLng(this.options.latitude, this.options.longitude);
            var mapOptions = {
                zoom: this.options.zoom,
                center: this.latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            this.map = new google.maps.Map(document.getElementById(this.options.mapSelector), mapOptions);

            this.marker = new google.maps.Marker({
                position: this.latlng,
                draggable: true,
                map: this.map
            });

            google.maps.event.addListener(this.marker, 'dragend', function(event) {
                this._updateLatLngFields(event.latLng.lat(), event.latLng.lng());
            }.bind(this));
            google.maps.event.addListener(this.map, 'zoom_changed', function() {
                $(this.options.zoomSelector).val(this.map.getZoom());
            }.bind(this));

            return this;
        },

        mapResize: function () {
            google.maps.event.trigger(this.map, 'resize');
            this.map.setCenter(this.latlng);
        },

        findStoreByAddress: function () {
            if($(this.options.citySelector).val() == '' || $(this.options.streetSelector).val() == '') {
                $(this.options.messageSelector).show();
                $(this.options.messageSelector + '> label').html(this.options.message.defineAddress);
                return;
            }

            var queryList = [];
            queryList.push($(this.options.countrySelector).val());
            if (!$(this.options.regionSelector).is(':disabled')) {
                queryList.push($(this.options.regionSelector).attr('title'));
            }
            queryList.push($(this.options.citySelector).val());
            queryList.push($(this.options.streetSelector).val());

            var request = {
                query: queryList.join(' ')
            };

            this.service = new google.maps.places.PlacesService(this.map);
            this.service.textSearch(request, this._showSearchResult.bind(this));
        },

        _showSearchResult: function (results, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK && results.length > 0) {
                this.latlng = new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());

                this.marker.setPosition(this.latlng);
                this.map.panTo(this.latlng);
                this.map.setZoom(15);
                this.map.setZoom(16);
                this._updateLatLngFields(results[0].geometry.location.lat(), results[0].geometry.location.lng());

                $(this.options.messageSelector).hide();
            } else {
                $(this.options.messageSelector).show();
                $(this.options.messageSelector + '> label').html(this.options.message.noResults);
            }
        },

        _updateLatLngFields: function (latitude, longitude) {
            $(this.options.latitudeSelector).val(latitude);
            $(this.options.longitudeSelector).val(longitude);
        }
    });

    $(document).ready(function () {
        $('#find-store-button').on('click', function () {
            $('#google-maps-content').googleMaps('findStoreByAddress');
        });

        $('#location_tabs_google_map_section').on('click', function () {
            $('#google-maps-content').googleMaps('mapResize');
        });
    });

    return $.aheadworks.googleMaps;
});
