/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 27);
/******/ })
/************************************************************************/
/******/ ({

/***/ 27:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(28);


/***/ }),

/***/ 28:
/***/ (function(module, exports) {

var log = console.log;
$(function () {
    var fn = {
        addOrders: function addOrders() {
            return _addOrders();
        },
        orders: function orders() {
            return _orders();
        },
        backFill: function backFill() {
            return _backFill();
        }
    };
    if (sessionStorage.url) {
        var f = getfn(sessionStorage.url);
        fn[f]();
    } else {
        _addOrders();
    }

    function getfn() {
        return sessionStorage.url.split('/').pop().split('_').shift();
    }
    function error(jqXHR, textStatus, errorThrown) {
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }

    function removeActive() {
        if (sessionStorage.url) {
            $('#' + getfn(sessionStorage.url)).removeClass('active');
            return true;
        }
        return false;
    }
    events();
    function events() {
        $('#addOrders').off('click').on('click', _addOrders);
        $('#orders').off('click').on('click', _orders);
        $('#backFill').off('click').on('click', _backFill);
    }

    function sendEmailFunction() {

        $("#send_form").validate({
            rules: {
                file: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                file: {
                    required: "Խնդրում ենք ընտրել ֆայլը"
                },
                email: {
                    required: "Խնդրում ենք նշել հաճախորդի էլփոստի հասցեն",
                    email: "Ձեր էլփոստի հասցեն պետք է լինի name@domain.com ձևաչափով"
                }
            },
            submitHandler: function submitHandler() {

                var message = $('.send_message');
                var form = $('#send_form');
                var file = form.find('input[type=file]');
                var email = form.find('input[type=email]');
                var formData = new FormData();
                message.removeClass('text-success text-warning').addClass('text-danger');

                formData.append('file', file[0].files[0]);
                formData.append('email', email.val());
                $.ajax({
                    url: 'user6/send_excel_to_email',
                    type: 'post',
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: formData,
                    beforeSend: function beforeSend() {
                        message.text('Ուղարկվում է').removeClass('text-success text-danger').addClass('text-warning');
                    },
                    success: function success(response) {
                        if (response.status == 'success') {
                            message.text('Հաջողությամբ ուղարկված է').removeClass('text-warning text-danger').addClass('text-success');
                            email.val('');
                            form.find('input[type=file]').val('');
                        } else {
                            alert('error');
                        }
                    },
                    error: error
                });
                $('#btn_send_modal').off('click').on('click', function () {
                    message.text('');
                });
            }
        });
    }

    function _addOrders() {
        var url = 'user6/addOrders_get';
        $.get(url, function (response) {

            removeActive();
            $('#content').html(response);
            $('#addOrders').addClass('active');
            sessionStorage.setItem('url', url);
            function hasClient() {
                var client = $('#client').val();
                if (client == '') {
                    $('#modal_order_message').modal('show');
                    return false;
                }
                return client;
            }
            $('#export_excel').off('click').on('click', hasClient);
            $('#import_excel').off('click').on('click', hasClient);
            $('#btn_send_modal').off('click').on('click', hasClient);

            $('#client').off('change').on('change', function () {
                var option = $(this).find('option:selected');
                $('#orders_register_table').grid('destroy', true, true);
                if (option.val() != '') {
                    var grid = $('#orders_register_table').grid({
                        primaryKey: 'product_id',
                        dataSource: { url: 'user6/get_order_data', type: 'post', success: function success(response) {
                                var records = [];
                                for (var x in response) {
                                    if (option.data('status') == 0) {
                                        response[x]['price'] = response[x].local_price;
                                    } else if (option.data('status') == 1) {
                                        response[x]['price'] = response[x].export_price;
                                    }
                                    records.push(response[x]);
                                }
                                grid.render(records);
                            } },
                        params: { client_id: $(this).val() },
                        responsive: true,
                        fixedHeader: true,
                        height: 545,
                        notFoundText: 'Արդյունք չի գտնվել',
                        columns: [{ field: 'name', title: 'Անուն' }, { field: 'height', title: 'Բոյ' }, { title: 'Գին', tmpl: '<input min="1" class="num_input price" data-price="{price}" value="{price}">' }, { title: 'Քանակ', tmpl: '<input min="1" class="num_input amt" data-id="{product_id}">' }, { field: 'balance', title: 'Մնացորդ', value: '{balance}', cssClass: 'balance' }]
                    });
                    $('#btn_add_order').off('click').click({ grid: grid }, successOrder);
                    $('#btn_add_order_excel').off('click').click({ grid: grid }, successOrderExcel);
                    $('#btn_send_email').off('click').on('click', sendEmailFunction);
                }
            });
        }).fail(error);
    }

    function successOrder(e) {
        var e_error = false;
        var message = $('.access_order_message');
        var product = $('#orders_register_table .amt');
        var client = $('#client');
        var modal = $('#modal_order_message');
        var data = [];
        $.each(product, function () {
            var product_id = $(this).attr('data-id');
            var amt = $(this).val();
            var price = $(this).closest('tr').find('input.price');
            if (amt != '' && amt > 0 && price.val() != '' && price.val() > 0) {
                data.push({
                    id: product_id,
                    amt: amt,
                    price: price.val(),
                    old_price: price.attr('data-price'),
                    client_id: client.val()
                });
            }
        });

        if (client.val() == '') {
            message.text('Պատվիրատուն ընտրված չէ');
            e_error = true;
        } else if (data.length == 0) {
            modal.modal('show');
            message.text('Լրացնել քանակ');
            e_error = true;
        }
        if (!e_error) {
            $.ajax({
                url: 'user6/add_orders',
                type: 'post',
                data: { data: data },
                success: function success(response) {
                    if (response.status === 'success') {
                        product.val('');
                        message.text('Պատվերները Հաջողությամբ ընդունված են');
                        e.data.grid.reload();
                    }
                },
                error: error
            });
        }
    }

    function successOrderExcel(e) {

        $('#excel_order_form').validate({

            rules: {
                file: { required: true }
            },
            messages: {
                file: { required: "Ընտրել ֆայլը" }
            },
            submitHandler: function submitHandler() {
                var form_data = new FormData();
                var client = $('#client').val();
                var file = $('#add_order_in_excel');
                var message = $('.upload_message');
                message.removeClass('text-success').addClass('text-danger');

                form_data.append('excel', file[0].files[0]);
                form_data.append('client_id', client);

                $.ajax({
                    url: 'user6/add_order_excel',
                    type: 'post',
                    data: form_data,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function success(response) {
                        switch (response.status) {
                            case 'success':
                                message.text('Պատվերները Հաջողությամբ ընդունված են').addClass('text-success').removeClass('text-danger');
                                file.val('');
                                e.data.grid.reload();
                                break;
                            case 'failed':
                                message.text('Սխալ ֆայլ');
                                break;
                        }
                    },
                    error: function error() {
                        message.text('Սխալ ֆայլ');
                    }
                });
            }
        });
    }

    function _orders() {
        $('#orders').off('click');
        var url = 'user6/orders_get';
        $.get(url, function (response) {
            removeActive();
            $('#orders').addClass('active');
            $('#content').html(response);
            sessionStorage.setItem('url', url);
        }).then(function (response) {
            return getOrdersInfo(response);
        }).then(function () {
            return $('#orders').off('click').on('click', _orders);
        }).fail(error);
    }

    function getOrdersInfo() {
        var from = $('#order_from').val();
        var to = $('#order_to').val();
        var name = $('#receiving_order_search').val();

        $('#orders_table').grid('destroy', true, true);

        var grid = $('#orders_table').grid({
            primaryKey: 'id',
            dataSource: { url: 'user6/orders_data', type: 'post', success: gridDataFunction },
            params: { from: from, to: to, name: name },
            detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
            responsive: true,
            fontSize: 15,
            notFoundText: 'Արդյունք չի գտնվել',
            fixedHeader: true,
            height: 575,
            columns: [{ field: 'client_name', title: 'Անուն' }, { field: 'date', title: 'Ամսաթիվ' }, { field: 'order_price', title: 'Ընդհանուր գումար' }, { tmpl: '<span class="{class}">{status}</span>', title: 'Կարգավիճակ' }, { tmpl: '<button><i class="fas fa-trash-alt"></i></button>', cssClass: 'fa_button', align: 'right', events: { 'click': removeOrder } }]

        });
        function removeOrder(e) {
            $('#remove_order_modal').modal('show');

            $('#btn_remove_order').off('click').on('click', function () {
                $.ajax({
                    url: 'user6/delete_order',
                    type: 'delete',
                    data: { id: e.data.id },
                    success: function success(response) {
                        if (response.status == 'success') {
                            grid.removeRow(e.data.id);
                        }
                    }

                });
            });
        }
        function gridDataFunction(response) {
            var records = [];
            for (var x in response) {
                var sum = 0;
                for (var j in response[x]) {
                    sum += response[x][j].order_price;
                    response[x][0].order_price = numberWithCommas(sum);

                    if (response[x][0].confirmed == 1 && response[x][0].not_enough == null && response[x][0].exit_ware_status == 1) {
                        response[x][0].status = 'Պատվերը հանձնված է հաճախորդին';
                        response[x][0].class = 'green';
                    } else if (response[x][0].confirmed == 0 || response[x][0].not_enough != null) {
                        response[x][0].status = 'Պատվերը դեռ հաստատված չէ';
                        response[x][0].class = 'red';
                    } else {
                        response[x][0].status = 'Պատվերը ենթակա է հանձման';
                        response[x][0].class = 'orange';
                    }
                }
                records.unshift(response[x][0]);
            }
            grid.render(records);
            $('.green').closest('tr').find('.fa_button').empty();
        }
        grid.on('detailExpand', function (e, $detailWrapper, id) {

            var detail = $detailWrapper.find('table').grid({
                dataSource: { url: 'user6/orders_data', type: 'post', success: detailFunction },
                primaryKey: 'order_id',
                params: { id: id, from: from, to: to, name: name },
                responsive: true,
                fixedHeader: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                inlineEditing: { mode: 'command', managementColumn: false },
                columns: [{ field: 'product_name', title: 'Ապրանք' }, { field: 'price', title: 'Նախնական գին' }, { field: 'discount_price', editField: 'discount_price', title: 'Զեղչված գին', editor: true }, { field: 'product_amt', editField: 'product_amt', title: 'Քանակ', editor: true }, { width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button' }]
            });

            detail.on('rowDataChanged', function (e, id, record) {
                $.ajax({
                    url: 'user6/update_detail_order',
                    data: record,
                    method: 'post',
                    error: error
                });
            });
            grid.on('detailCollapse', function (e, $detailWrapper, id) {
                $detailWrapper.find('table').grid('destroy', true, true);
            });

            function editManager(value, record, $cell, $displayEl, id, $grid) {
                var $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
                    $delete = $('<button><i class="far fa-trash-alt"></i></button>').attr('data-key', id),
                    $update = $('<button><i class="far fa-save"></i></button>').attr('data-key', id).hide().css('background', '#610d0d'),
                    $cancel = $('<button><i class="fas fa-ban"></i></button>').attr('data-key', id).hide().css('background', '#610d0d');
                $edit.on('click', function () {
                    $grid.edit($(this).data('key'));
                    $edit.hide();
                    $delete.hide();
                    $update.show();
                    $cancel.show();
                });
                $delete.on('click', function () {
                    var key = $(this).data('key');
                    $('#remove_product_order_modal').modal('show');
                    $('#btn_remove_product_order_modal').off('click').on('click', function () {
                        $.ajax({
                            url: 'user6/delete_detail_order',
                            type: 'delete',
                            data: { id: key },
                            success: function success(response) {
                                if (response.status == 'success') {
                                    detail.removeRow(key);
                                }
                            }
                        });
                    });
                });
                $update.on('click', function () {
                    $grid.update($(this).data('key'));
                    $edit.show();
                    $delete.show();
                    $update.hide();
                    $cancel.hide();
                });

                $cancel.on('click', function () {
                    $grid.cancel($(this).data('key'));
                    $edit.show();
                    $delete.show();
                    $update.hide();
                    $cancel.hide();
                });
                $displayEl.empty().append($edit, $delete, $update, $cancel);
            }
            function detailFunction(response) {

                for (var x in response) {
                    response[x].product_name = response[x].product_name + ' ' + response[x].product_height;
                    response[x].discount_price = numberWithCommas(+response[x].discount_price);
                    if (response[x].client_status == 0) {
                        response[x].price = numberWithCommas(+response[x].local_price);
                    } else if (response[x].client_status == 1) {
                        response[x].price = numberWithCommas(+response[x].export_price);
                    }
                }
                detail.render(response);
                $('.green').closest('tr').next('tr[data-role="details"]').find('.fa_button').empty();
            }
        });

        $('.box input').off('keypress').on('keypress', function (e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
        $('.box input').off('input').on('input', getOrdersInfo);
        $('#btn_search_receiving_order_clear').off('click').on('click', _orders);
    }

    function _backFill() {
        var url = 'user6/backFill_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response);
            $('#backFill').addClass('active');
            sessionStorage.setItem('url', url);
            backFillData();
        });
    }

    function backFillData() {

        $('#f_client').on('change', function () {
            var id = $(this).val();
            var client_id = $(this).find(':selected').data('client');
            $('#back_fill_table').grid('destroy', true, true);
            var grid = $('#back_fill_table').grid({
                dataSource: { url: 'user6/backFill_data', type: 'post', success: onSuccess },
                params: { id: id },
                responsive: true,
                fixedHeader: true,
                height: 629,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                columns: [{ field: 'name', title: 'Անուն' }, { title: 'Գին', tmpl: '<input class="num_input price" data-price="{price}" value="{price}">' }, { title: 'Քանակ', tmpl: '<input class="num_input amt" data-id="{product_id}" value="{product_amt}">' }]
            });

            function onSuccess(response) {

                var records = [];
                var prod = '<option value="">Ընտրել ծաղիկ</option>';
                $.each(response.data, function (k, item) {
                    item.price = item.order_price / item.product_amt;
                    item.name = item.product_name + ' ' + item.product_height;
                    records.push(item);
                });
                $.each(response.products, function (k, item) {
                    prod += '<option value="' + item.id + '">' + item.name + ' ' + item.height + '</option>';
                });

                grid.render(records);

                $('#add_new_row').off('click').on('click', function () {
                    grid.addRow({
                        name: '<select class="select_prod form-control">' + prod + '</select>'
                    });
                    $('.select_prod').off('change').on('change', function () {
                        var this_select = $(this);
                        if (this_select.val() != '') {
                            $.ajax({
                                url: 'user6/backFill_new_product',
                                type: 'post',
                                data: {
                                    prod_id: $(this).val(),
                                    client_id: client_id
                                },
                                success: function success(response) {
                                    this_select.closest('tr').find('.price').val(response.price).attr('data-price', response.price);
                                    this_select.closest('tr').find('.amt').attr('data-id', response.prod_id);
                                }
                            });
                        }
                    });
                });
            }

            $('#back_fill_success').off('click').click({ id: id, client_id: client_id }, updateOrder);
            $('#back_fill_delete').off('click').click({ id: id, client_id: client_id }, deleteOrder);
        });
    }

    function updateOrder(e) {

        var modal = $('#back_fill_messages');
        var product = $('#back_fill_table .amt');
        var data = [];
        $.each(product, function () {
            var price = $(this).closest('tr').find('input.price');
            var prod_name = $(this).closest('tr').find('select');
            var amt = $(this);
            if (amt.val() != '' && prod_name.val() != '' && $.isNumeric(amt.val()) && price.val() != '' && $.isNumeric(price.val())) {
                data.push({
                    id: $(this).attr('data-id'),
                    amt: amt.val(),
                    price: price.val(),
                    old_price: price.attr('data-price'),
                    order_id: e.data.id,
                    client_id: e.data.client_id
                });
            }
        });
        $.ajax({
            url: 'user6/update_order',
            type: 'post',
            data: { records: data },
            success: function success(response) {
                if (response.status == 'success') {
                    modal.modal('show');
                    modal.on('hidden.bs.modal', _backFill);
                }
            }
        });
    }

    function deleteOrder(e) {
        var modal = $('#delete_order_modal');
        modal.modal('show');
        $('.confirm_delete_order').off('click').on('click', function () {
            $.ajax({
                url: 'user6/delete_order',
                type: 'delete',
                data: { id: e.data.id },
                success: function success(response) {
                    if (response.status == 'success') {
                        _backFill();
                    }
                }
            });
        });
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});

/***/ })

/******/ });