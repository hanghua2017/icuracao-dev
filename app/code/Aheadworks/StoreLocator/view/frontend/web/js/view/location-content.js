/**
* Copyright 2018 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'ko',
    'underscore',
    'uiComponent'
], function (ko, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            selected: false,
            listens: {
                '${ $.locationMapName }:selected': 'onItemMapSelected',
                '${ $.locationListName }:selected': 'onItemListSelected'
            },
            modules: {
                locationMap: '${ $.locationMapName }',
                locationList: '${ $.locationListName }'
            }
        },

        onItemMapSelected: function (item) {
            this.locationList().setSelectedLocation(item, true);
        },

        onItemListSelected: function (item) {
            this.locationMap().setSelectedMarker(item, true);
        }
    });
});