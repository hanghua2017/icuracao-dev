/**
* Copyright 2018 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko',
        'uiComponent'
    ],
    function($, ko, Component) {
        'use strict';
        return Component.extend({
            listSelector: '#aw-storelocator-navigation',
            locationItems: {},
            selectedLocation: false,

            defaults: {
                template: 'Aheadworks_StoreLocator/location-list'
            },

            /**
             * Initializes model instance.
             */
            initialize: function () {
                this._super();

                this._initListConfig();

                return this;
            },

            initObservable: function () {
                this._super()
                    .observe('selectedLocation');

                return this;
            },

            _initListConfig: function() {
                // set list items `active` property to false
                for (var i in this.locationItems) {
                    this.locationItems[i].active = false;
                }

                if (this.locationItems.length > 0) {
                    this.setSelectedLocation(this.locationItems[0]);
                }
            },

            setSelectedLocation: function(item, muteEvents) {
                this.selectedLocation(item);
                if (muteEvents !== true) this.trigger('selected', item);
            }
        });
    }
);
