/*!
 * GRT Youtube Popup - jQuery Plugin
 * Version: 1.0
 * Author: GRT107
 *
 * Copyright (c) 2017 GRT107
 * Released under the MIT license
*/
require([
			'jquery',
	], function($) {
    (function() {
      var sidebar         = document.getElementById('sidebar');
      var sidebarOverlay  = document.getElementsByClassName('sidebar-overlay')[0];
      var container       = document.getElementsByClassName('container')[0];
      var sidebarBtnClose = document.getElementById('sidebarBtnClose');
      var sidebarBtnOpen  = document.getElementById('sidebarBtnOpen');

      var openSidebar = function() {
        sidebarOverlay.style.left = '0';
        sidebar.style.left = '0';
      }

      var closeSidebar = function(e) {
        e.cancelBubble = true;
        sidebarOverlay.style.left = '-100%';
        sidebar.style.left = '-250px';
      }

      sidebarOverlay.addEventListener('click', closeSidebar);
      sidebarBtnClose.addEventListener('click', closeSidebar);
      sidebarBtnOpen.addEventListener('click', openSidebar);
      // sidebarOverlay.click(function() {
      //   closeSidebar();
      // });
      // sidebarBtnClose.click(function() {
      //   closeSidebar();
      // });
      // sidebarBtnOpen.click(function() {
      //   openSidebar();
      // });
    })()
});
