const $ = require('jquery');
// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap');
var dt = require( 'datatables.net-bs4' );


//import 'datatables.net-bs4/css/jquery.datatables.css';
require('../css/global.scss');
// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();

    // Load datatables
    $('.dataTable').DataTable();
    // Load inline JS from page
    if (typeof pageJs === 'function') {
        pageJs($);
    }
});