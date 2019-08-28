try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap/dist/js/bootstrap.bundle.min');
    require('bootstrap-multiselect');
    require('jquery-validation/dist/jquery.validate.min');
    require('jquery.easing/jquery.easing.min');
    require('chart.js/dist/Chart.min');
    require('gijgo');


} catch (e) {}
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});