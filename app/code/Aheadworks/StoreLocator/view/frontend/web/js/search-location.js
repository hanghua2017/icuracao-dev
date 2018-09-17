/**
* Copyright 2018 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'jquery/ui',
], function ($) {
    'use strict';

    $.widget('aheadworks.searchLocation', {
        options: {
            addressSelector: '#street',
            latitudeSelector: '#latitude',
            longitudeSelector: '#longitude',
            radiusSelector: '#aw-storelocator-search-block-radius',
            measurementSelector: '#aw-storelocator-search-block-measurement',
            buttonSelector: '#aw-find-location',
        },

        _create: function() {
            this._toggleSearchSelect();
            this._bindAddressAutocomplete();

            this.element.find(this.options.buttonSelector).on('click', this._findCurrentLocation.bind(this));
            this.element.find(this.options.addressSelector).on('keyup', this._toggleSearchSelect.bind(this));
        },

        _findCurrentLocation: function () {
            if (navigator.geolocation) {
                var locationTimeout = setTimeout(this.searchError, 10000);

                navigator.geolocation.getCurrentPosition(function(position) {
                    var geocoder = new google.maps.Geocoder();
                    var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

                    geocoder.geocode({'latLng': latlng}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                this.element.find(this.options.addressSelector).val(results[1].formatted_address);
                                this._toggleSearchSelect();
                            } else {
                                alert('No results found');
                            }
                        } else {
                            alert('Geocoder failed due to: ' + status);
                        }
                    }.bind(this));

                    this.element.find(this.options.latitudeSelector).val(position.coords.latitude);
                    this.element.find(this.options.longitudeSelector).val(position.coords.longitude);

                    clearTimeout(locationTimeout);
                }.bind(this), function(error) {
                    clearTimeout(locationTimeout);
                    this.searchError;
                }.bind(this));
            } else {
                this.searchError;
            }

            return this;
        },

        searchError: function () {
            alert('Error: The Geolocation service failed. Please try again.');
        },

        _toggleSearchSelect: function () {
            var addressValue  = !this.element.find(this.options.addressSelector).val();
            $.merge(this.element.find(this.options.radiusSelector), this.element.find(this.options.measurementSelector)).prop('disabled', addressValue);
        },

        _bindAddressAutocomplete: function () {
            var address = this.element.find(this.options.addressSelector)[0];
            var autocomplete = new google.maps.places.Autocomplete(address);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();

                this.element.find(this.options.latitudeSelector).val(place.geometry.location.lat());
                this.element.find(this.options.longitudeSelector).val(place.geometry.location.lng());
            }.bind(this));
        }
    });

    return $.aheadworks.searchLocation;
});