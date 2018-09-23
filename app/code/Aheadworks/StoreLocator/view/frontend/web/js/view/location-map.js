/**
* Copyright 2018 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'ko',
    'underscore',
    'uiComponent',
    'mage/template',
], function (ko, _, Component, mageTemplate) {
    'use strict';

    return Component.extend({
        locationItems: {},
        latlngItems: [],
        markerItems: [],
        customMarkerIcons: [],
        infoWindowItems: [],
        infoWindowTemplate: '<div class ="aw-storelocator-template">'
        + '<div class="aw-storelocator-info-window">'
        + '<div class="aw-storelocator-description">'
        + '<div class="aw-storelocator-title"><%- data.title %></div>'
        + '<div class="aw-storelocator-address">'
        + '<div><%- data.street %>, <%- data.city %></div>'
        + '<div>'
        + '<%- data.country %> <% if(data.region) { %><%- data.region %><% } %> <% if(data.zip) { %><%- data.zip %><% } %>'
        + '</div>'
        + '<div><% if(data.phone) { %><%- data.phone %><% } %></div>'
        + '</div>'
        + '</div>'
        + '<div class="aw-storelocator-store-image"><% if (data.image) { %><img src="<%- data.image %>"><% } %></div>'
        + '<div class="aw-storelocator-full-description">'
        + '<div class="aw-storelocator-desc"><% if(data.description) { %><%- data.description %><% } %></div>'
        + '</div>'
        + '</div>'
        + '</div>',
        initMapConfig: {
            latitude: 0,
            longitude: 0,
            zoom: 2,
            selectedMarker: false
        },
        selectedMarker: false,

        /**
         * Initializes model instance.
         */
        initialize: function () {
            this._super();

            this._initMapConfig();
            this._initMap();

            return this;
        },

        _initMapConfig: function () {
            if (undefined !== this.locationItems && this.locationItems.length > 0) {
                var initialLocation = this.locationItems[0];

                this.initMapConfig.latitude = initialLocation.latitude;
                this.initMapConfig.longitude = initialLocation.longitude;
                this.initMapConfig.zoom = parseInt(initialLocation.zoom);
                this.initMapConfig.selectedMarker = initialLocation;
            }
        },

        setSelectedMarker: function(item, muteEvents) {
            this.selectedMarker = item;
            this.panTo(item);
            if (!muteEvents) {
                this.trigger('selected', this.selectedMarker);
            }
        },

        _initMap: function () {
            var latlng = new google.maps.LatLng(this.initMapConfig.latitude, this.initMapConfig.longitude);
            var mapOptions = {
                zoom: this.initMapConfig.zoom,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            this.map = new google.maps.Map(document.getElementById(this.mapSelector), mapOptions);

            if (this.initMapConfig.selectedMarker) {
                for (var i = 0; i < this.locationItems.length; i++) {
                    this._showMarker(this.locationItems[i]);
                }

                this.setSelectedMarker(this.initMapConfig.selectedMarker);
            }

            return this;
        },

        _addLatLngItem: function(item) {
            return this.latlngItems[item.location_id] = new google.maps.LatLng(item.latitude, item.longitude);
        },

        _addMarkerItem: function(item, config) {
            return this.markerItems[item.location_id] = new google.maps.Marker(config);
        },

        _addCustomMarkerIcon: function(item) {
            return this.customMarkerIcons[item.location_id] = {
                url: item.custom_marker,
                size: new google.maps.Size(20, 32),
                scaledSize: new google.maps.Size(20, 32)
            };
        },

        _addInfoWindowItem: function(item) {
            return this.infoWindowItems[item.location_id] = new google.maps.InfoWindow({
                content: (mageTemplate(this.infoWindowTemplate, {
                    data: item
                })),
                identity: item.location_id,
                maxWidth: 300
            });
        },

        panTo: function (item) {
            this.map.panTo(this.latlngItems[item.location_id]);
            this.map.setZoom(parseInt(item.zoom));
        },

        _showMarker: function (locationItem) {
            var latlng = this._addLatLngItem(locationItem);
            var markerConfig = {
                position: latlng,
                map: this.map
            };
            if (locationItem.custom_marker) {
                var icon = this._addCustomMarkerIcon(locationItem);
                markerConfig.icon = icon;
            }
            var marker = this._addMarkerItem(locationItem, markerConfig);
            var infoWindow = this._addInfoWindowItem(locationItem);

            var self = this;
            google.maps.event.addListener(marker, 'click', function () {
                self.infoWindowItems.each(function (w) {
                    w.close();
                });

                infoWindow.open(self.map, self.markerItems[locationItem.location_id]);

                self.setSelectedMarker(locationItem);
            });
        }
    });
});