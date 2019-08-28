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
/******/ 	return __webpack_require__(__webpack_require__.s = 23);
/******/ })
/************************************************************************/
/******/ ({

/***/ 23:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(24);


/***/ }),

/***/ 24:
/***/ (function(module, exports) {

var log = console.log;
$(document).ready(function () {
    var fn = {
        orders: function orders() {
            return _orders();
        },
        addProductsPage: function addProductsPage() {
            return _addProductsPage();
        },
        movements: function movements() {
            return _movements();
        },
        products: function products() {
            return _products();
        }
    };
    if (sessionStorage.url) {
        var f = getFn(sessionStorage.url);
        fn[f]();
    } else {
        _products();
    }

    function getFn() {
        return sessionStorage.url.split('/').pop().split('_').shift();
    }

    function removeActive() {
        if (sessionStorage.url) {
            $('#' + getFn(sessionStorage.url)).removeClass('active');
            return true;
        }
        return false;
    }

    events();

    function events() {
        $('#addProductsPage').off('click').on('click', _addProductsPage);
        $('#movements').off('click').on('click', _movements);
        $('#products').off('click').on('click', _products);
        $('#orders').off('click').on('click', _orders);
    }

    function _orders() {
        $('#orders').off('click');
        var url = 'user4/orders_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response);
            $('#orders').addClass('active');
            sessionStorage.setItem('url', url);
        }).then(function () {
            return ordersData();
        }).then(function () {
            return $('#orders').off('click').on('click', _orders);
        }).fail(error);
    }

    function ordersData() {
        $('#orders_tbl').grid('destroy', true, true);
        var grid = $('#orders_tbl').grid({
            dataSource: { url: 'user4/orders_data', success: function success(response) {
                    var records = [];
                    for (var x in response.orders) {
                        records.push(response.orders[x][0]);
                    }
                    for (var i = 0; i < records.length; i++) {
                        for (var j = 0; j < response.goods.length; j++) {
                            records[i].good_elem1 = '<input class="num_input" data-id= ' + response.goods[0].id + ' data-name=' + response.goods[0].name + '>';
                            records[i].good_elem2 = '<input class="num_input" data-id= ' + response.goods[1].id + ' data-name=' + response.goods[1].name + '>';
                            records[i].good_elem3 = '<input class="num_input" data-id= ' + response.goods[2].id + ' data-name=' + response.goods[2].name + '>';
                        }
                    }
                    grid.render(records);
                    var back_fill = $('#orders_page button[data-fill=1]');
                    var tr = back_fill.closest('tr');
                    tr.css('opacity', '1').find('*').on('click');
                    tr.css('opacity', '.6').find('*').off();
                    back_fill.after('<p class="message_order_fill">Վերանայվում է</p>');
                    tr.find('button').remove();
                } },
            primaryKey: 'id',
            responsive: true,
            notFoundText: 'Արդյունք չի գտնվել',
            fixedHeader: true,
            height: 680,
            detailTemplate: '<div><table  style="background:#fcf8e3"></div>',
            columns: [{ field: 'client_name', title: 'Հաճախորդ' }, { field: 'date', title: 'Ամսաթիվ' }, { field: 'good_elem1', title: 'Դույլ' }, { field: 'good_elem2', title: 'Կափարիչ' }, { field: 'good_elem3', title: 'Արկղ' }, { tmpl: '<button>Հաստատել</button>', align: 'right', cssClass: 'fa_button', width: 106, events: { 'click': closeOrder } }, { tmpl: '<button data-fill={back_fill}>Ուղարկել ետ լրացման</button>', align: 'center', width: 175, cssClass: 'fa_button', events: { 'click': backFill } }]
        });

        grid.on('detailExpand', function (e, $detailWrapper, id) {
            $detailWrapper.find('table').grid({
                params: { id: id },
                dataSource: { url: 'user4/order_detail', type: 'post' },
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                columns: [{ field: 'product_name', title: 'Ապրանք <i class="fas fa-sort"></i>', sortable: true }, { field: 'product_height', title: 'Բոյ <i class="fas fa-sort"></i>', sortable: true }, { field: 'product_amt', title: 'Քանակ <i class="fas fa-sort"></i>', sortable: true }]
            });
        });

        function closeOrder(e) {
            var bucket = $(this).closest('tr').find('input.num_input');

            var id = e.data.id;
            var client_id = e.data.record.client_id;
            var dialog = $('#ord_dialog');
            var message = dialog.find('.message');

            var data = [];
            for (var i = 0; i < bucket.length; i++) {
                if (bucket.eq(i).val()) {
                    data.push({
                        good_id: bucket.eq(i).data('id'),
                        good_amt: bucket.eq(i).val(),
                        client_id: client_id,
                        id: id
                    });
                }
            }
            if (data.length < 3) {
                dialog.modal('show');
                message.text('Դաշտերը անկանոն են լրացված');
            } else {
                $('#access_prod_to_client').modal('show');
                $('#modal_access_button').off('click').on('click', function () {
                    $.ajax({
                        url: 'user4/orders_post',
                        type: 'post',
                        data: { data: data },
                        success: function success(response) {
                            if (response.status == 'success') {
                                grid.reload();
                            }
                        },
                        error: error
                    });
                });
            }
        }
        function backFill(e) {
            var order_id = e.data.id;
            $.ajax({
                url: 'user4/back_fill',
                type: 'post',
                data: { id: order_id },
                success: function success(response) {
                    if (response.status == 'success') {
                        grid.reload();
                    }
                }
            });
        }
    }

    function _products() {
        var url = 'user4/products_get';
        $.get(url, function (response) {
            removeActive();
            $('#products').addClass('active').closest('ul').collapse();
            sessionStorage.setItem('url', url);
            $('#content').html(response.view);
            $('#end_products_table').grid({
                dataSource: response.end_products,
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                autoLoad: false,
                columns: [{ field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', sortable: true }, { field: 'height', title: 'Բոյ <i class="fas fa-sort"></i>', sortable: { sorter: caseSensitiveSort } }, { field: 'balance', type: 'number', title: 'Քանակ <i class="fas fa-sort"></i>', sortable: { sorter: caseSensitiveSort } }],
                pager: {
                    limit: 10,
                    sizes: [5, 10, 20, 50]
                }
            });
            function caseSensitiveSort(direction, column) {
                return function (recordA, recordB) {
                    var a = +recordA[column.field] || '',
                        b = +recordB[column.field] || '';
                    return direction == 'asc' ? a < b : b < a;
                };
            }
        }).fail(error);
    }

    function _addProductsPage() {
        $('#addProductsPage').off('click');
        var url = 'user4/addProductsPage_get';
        $.get(url, function (response) {
            removeActive();
            $('#addProductsPage').addClass('active').closest('ul').collapse();
            sessionStorage.setItem('url', url);
            $('#content').html(response);
        }).then(function () {
            return addProductData();
        }).then(function () {
            return $('#addProductsPage').off('click').on('click', _addProductsPage);
        }).fail(error);
    }

    function addProductData() {

        var tables = $('.prod_height_detail');
        tables.each(function () {
            $(this).grid({});
        });

        $('#get_product').off('click').on('click', function () {
            var message = $('.access_product_message');
            var dialog = $('#dialog_access_product');
            var inputs = tables.find('input');
            var data = [];
            inputs.each(function () {
                if ($(this).val() != '') {
                    data.push({
                        product_id: $(this).data('id'),
                        product_amt: $(this).val()
                    });
                }
            });
            if (data.length != 0) {
                $.ajax({
                    url: 'user4/add_end_products',
                    type: 'post',
                    data: { data: data },
                    success: function success(response) {
                        if (response.status === 'success') {
                            inputs.val('');
                            message.text('Ավելացված է');
                            dialog.modal('show');
                            dialog.on('hidden.bs.modal', function () {
                                $('.prod_item_header + .collapse ').collapse('hide');
                            });
                        }
                    },
                    error: error
                });
            } else {
                message.text('Բոլոր դաշտերը դատարկ են ');
                dialog.modal('show');
            }
        });
    }

    function _movements() {
        $('#movements').off('click');
        var url = 'user4/movements_get';
        removeActive();
        sessionStorage.setItem('url', url);
        $.get(url, function (response) {
            $('#content').html(response);
            $('#movements').addClass('active');
        }).then(function () {
            return movementsData();
        }).then(function () {
            return $('#movements').off('click').on('click', _movements);
        }).fail(error);
    }

    function movementsData() {
        var from = $('#movements_from');
        var to = $('#movements_to');
        var client = $('#select_clients');
        var select = $('#movements_drop_down');
        var selectBy = function selectBy(access, exit, all) {
            if (select.val() == 'access') return access;else if (select.val() == 'exit' || client.val() != 0) return exit;else return all;
        };

        $('#movements_table').grid('destroy', true, true);

        var grid = $('#movements_table').grid({
            primaryKey: 'id',
            dataSource: { url: 'user4/movements_data', type: 'post', success: onSuccessGridFunction },
            params: { from: from.val(), to: to.val(), select: select.val(), client_id: client.val() },
            detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
            responsive: true,
            fixedHeader: true,
            notFoundText: 'Արդյունք չի գտնվել',
            height: 530,
            columns: [{ field: 'product_name', title: 'Անուն' }, { field: selectBy('access_sum', 'exit_sum', 'access_sum'),
                title: selectBy('Ընդհանուր մուտքեր', 'Ընդհանուր ելքեր', 'Ընդհանուր մուտքեր') }, { field: selectBy('', '', 'exit_sum'), title: selectBy('', '', 'Ընդհանուր ելքեր') }]
        });

        function onSuccessGridFunction(response) {

            var records = [];
            for (var x in response) {
                var access = 0,
                    exit = 0;
                for (var j in response[x]) {
                    response[x][j].id = response[x][j].product_id;
                    if (response[x][j].amt > 0) {
                        access += response[x][j].amt;
                    } else {
                        exit += response[x][j].amt;
                    }
                }
                response[x][0].access_sum = access;
                response[x][0].exit_sum = Math.abs(exit);
                response[x][0].product_name = response[x][0].product_name + ' ' + response[x][0].product_height;
                records.push(response[x][0]);
            }
            grid.render(records);

            select.removeAttr('disabled');
            client.removeAttr('disabled');
            if (client.val() != '') {
                select.attr('disabled', true);
            }
            if (select.val() == 'access') {
                client.attr('disabled', true);
            }
        }

        grid.on('detailExpand', function (e, $detailWrapper, id) {
            var detail = $detailWrapper.find('table').grid({
                dataSource: { url: 'user4/movements_data', type: 'post', success: successDetailFunction },
                params: { product_id: id, from: from.val(), to: to.val(), select: select.val(), client_id: client.val() },
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                columns: [{ field: 'date', title: 'Ամսաթիվ', sortable: true }, { field: selectBy('access', '', 'access'), title: selectBy('Մուտք', '', 'Մուտք'), sortable: true, hidden: selectBy(false, true, false) }, { field: selectBy('', 'exit', 'exit'), title: selectBy('', 'Ելք', 'Ելք'), sortable: true, hidden: selectBy(true, false, false) }, { field: 'balance', title: 'Մնացորդ', sortable: true }]
            });
            function successDetailFunction(response) {
                response.map(function (item, index) {
                    return item.exit = Math.abs(item.exit);
                });
                detail.render(response);
            }
        });

        $('.box input').off('keypress').on('keypress', function (e) {
            if (e.keyCode == 13) return false;
        });

        client.off('change').on('change', movementsData);
        select.off('change').on('change', movementsData);
        $('.box input').off('input').on('input', movementsData);

        $('#btn_movements_search_clear').off('click').on('click', _movements);
    }

    function error(jqXHR, textStatus, errorThrown) {
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }
});

/***/ })

/******/ });