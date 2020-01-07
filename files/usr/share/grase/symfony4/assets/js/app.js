const $ = require('jquery');
// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap');
require('admin-lte');
require('jquery.json-viewer/json-viewer/jquery.json-viewer.js');
var dt = require( 'datatables.net-bs4' );

/* Currently we load this on each page, so we don't have to worry about generating the routes file with the correct
   hostname etc
// Get our routing into JS world (if we want it in Webpack apps)
const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
Routing.setRoutingData(routes);
// Make it available outside of webpack
global.Routing = Routing;
*/

//import 'datatables.net-bs4/css/jquery.datatables.css';
require('../css/global.scss');
// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();

    // Load datatables
    $('.dataTable').DataTable();
    // Load inline JS from page
    if (typeof pageJs === 'function') {
        pageJs($);
    }

    // Load any JSON-viewer views
    $('.json-renderer').each(function() {
        var jsondata = $(this).data("json")
        if (jsondata.length !== 0) {
            $(this).jsonViewer(jsondata, {collapsed: true});
        }
    })

});