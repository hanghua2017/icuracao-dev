/*!
 * Hamburger Menu For Desktop
*/
require(['jquery'], function ($) {

    //perform this operation only after DOM Loaded.
    $(function () {
        var sidebar = document.getElementById('sidebar'),
            sidebarOverlay = document.getElementsByClassName('sidebar-overlay')[0],
            sidebarBtnClose = document.getElementById('sidebarBtnClose'),
            sidebarBtnOpen = document.getElementById('sidebarBtnOpen'),

            openSidebar = function () {
                sidebarOverlay.style.left = '0';
                sidebar.style.left = '0';
            },

            closeSidebar = function (e) {
                e.cancelBubble = true;
                sidebarOverlay.style.left = '-100%';
                sidebar.style.left = '-250px';
            };

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        if (sidebarBtnClose) {
            sidebarBtnClose.addEventListener('click', closeSidebar);
        }

        if (sidebarBtnOpen) {
            sidebarBtnOpen.addEventListener('click', openSidebar);
        }
    });
});
