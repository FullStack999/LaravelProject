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
/******/ 	return __webpack_require__(__webpack_require__.s = 25);
/******/ })
/************************************************************************/
/******/ ({

/***/ 25:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(26);


/***/ }),

/***/ 26:
/***/ (function(module, exports) {

var log = console.log;
$(function () {
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    var fn = {
        orders: function orders() {
            return _orders();
        },
        deleting: function deleting() {
            return _deleting();
        }
    };
    if (sessionStorage.url) {
        var f = getfn();
        fn[f]();
    } else {
        _orders();
    }

    function getfn() {
        return sessionStorage.url.split('/').pop().split('_').shift();
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
        $('#orders').off('click').on('click', _orders);
        $('#deleting').off('click').on('click', _deleting);
    }

    function _orders() {
        $('#orders').off('click');
        var url = 'user5/orders_get';
        $.get(url, function (response) {

            removeActive();
            $('#orders').addClass('active').closest('ul').collapse();
            $('#content').html(response);
            sessionStorage.setItem('url', url);
        }).then(function () {
            return getOrderInfo();
        }).then(function () {
            return $('#orders').off('click').on('click', _orders);
        }).fail(error);
    }

    function getOrderInfo() {

        $('#detail_orders_table').grid('destroy', true, true);
        var from = $('#order_from').val();
        var to = $('#order_to').val();
        var name = $('#receiving_order_search').val();

        var grid = $('#detail_orders_table').grid({
            primaryKey: 'id',
            dataSource: { url: 'user5/orders_data', type: 'post', success: gridDataFunction },
            params: { from: from, to: to, name: name },
            detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
            responsive: true,
            fontSize: 15,
            notFoundText: 'Արդյունք չի գտնվել',
            fixedHeader: true,
            height: 650,
            columns: [{ field: 'client_name', title: 'Անուն' }, { field: 'date', title: 'Ամսաթիվ' }, { field: 'order_price', title: 'Ընդհանուր գումար' }, { field: 'desc', title: ' ' }, { tmpl: '<button class="confirm_order " data-id="{id}" data-enough="{not_enough}" data-confirm="{confirmed}">Հաստատել</button>', cssClass: 'fa_button', width: 180 }]

        });

        function gridDataFunction(response) {
            var records = [];
            for (var x in response) {
                var sum = 0;
                for (var j in response[x]) {
                    sum += response[x][j].order_price;
                }
                response[x][0].order_price = numberWithCommas(sum);
                if (response[x][0].not_enough != null) {
                    response[x][0].desc = 'Պատվերում կա ապրանք, որ պահեստում մնացորդը բավարար չէ';
                } else if (response[x][0].confirmed == 0) {
                    response[x][0].desc = 'Պատվերում կա զեղչված ապրանք';
                }
                if (response[x][0].not_enough != null && response[x][0].confirmed == 0) {
                    response[x][0].desc = 'Պատվերում կա զեղչված ապրանք և ապրանք, որ պահեստում մնացորդը բավարար չէ';
                }
                records.push(response[x][0]);
            }
            grid.render(records);
            var button = $('.confirm_order');
            button.each(function () {
                if ($(this).data('confirm') == 1 && $(this).data('enough') == '') {
                    $(this).parent().empty().css('color', 'green').html('Հաստատված է &nbsp&nbsp<i class="fas fa-check"></i>');
                } else {
                    $(this).closest('tr');
                }
            });
            button.off('click').on('click', function () {
                var order_id = $(this).data('id');
                $.ajax({
                    url: 'user5/confirm_order',
                    type: 'post',
                    data: { id: order_id },
                    success: function success(response) {
                        if (response.status == 'success') {
                            grid.reload();
                        }
                    },
                    error: error
                });
            });
        }
        grid.on('detailExpand', function (e, $detailWrapper, id) {

            var detail = $detailWrapper.find('table').grid({
                dataSource: { url: 'user5/orders_data', type: 'post', success: detailFunction },
                params: { id: id, from: from, to: to, name: name },
                responsive: true,
                fixedHeader: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                columns: [{ field: 'product_name', title: 'Ապրանք' }, { field: 'product_amt', title: 'Քանակ' }, { field: 'price', title: 'Նախնական գին' }, { field: 'discount_price', title: 'Զեղչված գին' }, { tmpl: '<input type="hidden" data-id="{product_id}" data-enough="{not_enough}" class="not_enough_input">', width: 1 }]
            });
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
                var val = $('.not_enough_input').data('enough').toString().split(',');
                $.each(val, function (k, v) {
                    $('.not_enough_input[data-id="' + v + '"]').closest('tr').css('background', '#FFDEDE');
                });
            }
        });
        $('.box input').off('keypress').on('keypress', function (e) {
            if (e.keyCode == 13) {
                return false;
            }
        });

        $('.box input').off('input').on('input', getOrderInfo);
        $('#btn_search_receiving_order_clear').off('click').on('click', _orders);
    }

    function _deleting() {
        var url = 'user5/deleting_get';
        $.get(url, function (response) {
            removeActive();
            $('#deleting').addClass('active').closest('ul').collapse();
            $('#content').html(response);
            sessionStorage.setItem('url', url);
        }).then(function () {
            return deletingData();
        });
    }
    function deletingData() {
        $('#clients_tab').off('click').on('click', deletingClients);
        $('#suppliers_tab').off('click').on('click', deletingSuppliers);
        $('#places_tab').off('click').on('click', deletingPlaces);
        $('#goods_tab').off('click').on('click', deletingGoods);
        $('#products_tab').off('click').on('click', deletingProducts);
        $('#expenses_tab').off('click').on('click', deletingExpenses);

        deletingClients();
        function deletingClients() {
            $('#deleting_clients').grid('destroy', true, true);
            var grid = $('#deleting_clients').grid({
                primaryKey: 'id',
                dataSource: { url: 'user5/deleting_data', type: 'post' },
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                height: 650,
                params: { table: 'clients' },
                columns: [{ field: 'name', title: 'Անուն' }, { field: 'address', title: 'Հասցե' }, { field: 'phone', title: 'Հեռախոս' }, { field: 'deleted_at', title: 'Հեռացվել է' }, { tmpl: '<button data-id="{id}"><i class="far fa-trash-alt"></i></button>', cssClass: 'fa_button', events: { 'click': removeClient } }]
            });

            function removeClient(e) {
                if (confirm()) {
                    $.ajax({
                        url: 'user5/removing',
                        type: 'post',
                        data: {
                            id: e.data.id,
                            table: 'client'
                        },
                        success: function success(response) {
                            if (response.status == 'success') {
                                grid.removeRow(e.data.id);
                            }
                        },
                        error: error
                    });
                }
            }
        }

        function deletingSuppliers() {
            $('#deleting_suppliers').grid('destroy', true, true);
            var grid = $('#deleting_suppliers').grid({
                primaryKey: 'id',
                dataSource: { url: 'user5/deleting_data', type: 'post' },
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                height: 650,
                params: { table: 'suppliers' },
                columns: [{ field: 'name', title: 'Անուն' }, { field: 'deleted_at', title: 'Հեռացվել է' }, { tmpl: '<button data-id="{id}"><i class="far fa-trash-alt"></i></button>', cssClass: 'fa_button', events: { 'click': removeSupplier } }]
            });

            function removeSupplier(e) {
                if (confirm('Հեռացնելուց հետո ջնջվում է նաև իր ամբողջ պատմությունը')) {
                    $.ajax({
                        url: 'user5/removing',
                        type: 'post',
                        data: {
                            id: e.data.id,
                            table: 'supplier'
                        },
                        success: function success(response) {
                            if (response.status == 'success') {
                                grid.removeRow(e.data.id);
                            }
                        },
                        error: error
                    });
                }
            }
        }
        function deletingPlaces() {
            $('#deleting_places').grid('destroy', true, true);
            var grid = $('#deleting_places').grid({
                primaryKey: 'id',
                dataSource: { url: 'user5/deleting_data', type: 'post' },
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                height: 650,
                params: { table: 'places' },
                columns: [{ field: 'name', title: 'Անուն' }, { field: 'deleted_at', title: 'Հեռացվել է' }, { tmpl: '<button data-id="{id}"><i class="far fa-trash-alt"></i></button>', cssClass: 'fa_button', events: { 'click': removePlaces } }]
            });

            function removePlaces(e) {
                if (confirm('Հեռացնելուց հետո ջնջվում է նաև իր ամբողջ պատմությունը')) {
                    $.ajax({
                        url: 'user5/removing',
                        type: 'post',
                        data: {
                            id: e.data.id,
                            table: 'place'
                        },
                        success: function success(response) {
                            if (response.status == 'success') {
                                grid.removeRow(e.data.id);
                            }
                        },
                        error: error
                    });
                }
            }
        }
        function deletingGoods() {
            $('#deleting_goods').grid('destroy', true, true);
            var grid = $('#deleting_goods').grid({
                primaryKey: 'id',
                dataSource: { url: 'user5/deleting_data', type: 'post' },
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                height: 650,
                params: { table: 'goods' },
                columns: [{ field: 'name', title: 'Անուն' }, { field: 'unit', title: 'Միավոր' }, { field: 'price', title: 'գին' }, { field: 'deleted_at', title: 'Հեռացվել է' }, { tmpl: '<button data-id="{id}"><i class="far fa-trash-alt"></i></button>', cssClass: 'fa_button', events: { 'click': removeGood } }]
            });

            function removeGood(e) {
                if (confirm('Հեռացնելուց հետո ջնջվում է նաև իր ամբողջ պատմությունը')) {
                    $.ajax({
                        url: 'user5/removing',
                        type: 'post',
                        data: {
                            id: e.data.id,
                            table: 'good'
                        },
                        success: function success(response) {
                            if (response.status == 'success') {
                                grid.removeRow(e.data.id);
                            }
                        },
                        error: error
                    });
                }
            }
        }
        function deletingProducts() {
            $('#deleting_products').grid('destroy', true, true);
            var grid = $('#deleting_products').grid({
                primaryKey: 'id',
                dataSource: { url: 'user5/deleting_data', type: 'post' },
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                height: 650,
                params: { table: 'products' },
                columns: [{ field: 'name', title: 'Անուն' }, { field: 'height', title: 'Բոյ' }, { field: 'local_price', title: 'Արտահանման գին' }, { field: 'export_price', title: 'Տեղական գին' }, { field: 'deleted_at', title: 'Հեռացվել է' }, { tmpl: '<button data-id="{id}"><i class="far fa-trash-alt"></i></button>', cssClass: 'fa_button', events: { 'click': removeProduct } }]
            });

            function removeProduct(e) {
                if (confirm('Հեռացնելուց հետո ջնջվում է նաև իր ամբողջ պատմությունը')) {
                    $.ajax({
                        url: 'user5/removing',
                        type: 'post',
                        data: {
                            id: e.data.id,
                            table: 'product'
                        },
                        success: function success(response) {
                            if (response.status == 'success') {
                                grid.removeRow(e.data.id);
                            }
                        },
                        error: error
                    });
                }
            }
        }
        function deletingExpenses() {
            $('#deleting_expenses').grid('destroy', true, true);
            var grid = $('#deleting_expenses').grid({
                primaryKey: 'id',
                dataSource: { url: 'user5/deleting_data', type: 'post' },
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                height: 650,
                params: { table: 'expenses' },
                columns: [{ field: 'name', title: 'Անուն' }, { field: 'unit', title: 'Միավոր' }, { field: 'deleted_at', title: 'Հեռացվել է' }, { tmpl: '<button data-id="{id}"><i class="far fa-trash-alt"></i></button>', cssClass: 'fa_button', events: { 'click': removeExpense } }]
            });

            function removeExpense(e) {
                if (confirm('Հեռացնելուց հետո ջնջվում է նաև իր ամբողջ պատմությունը')) {
                    $.ajax({
                        url: 'user5/removing',
                        type: 'post',
                        data: {
                            id: e.data.id,
                            table: 'expense'
                        },
                        success: function success(response) {
                            if (response.status == 'success') {
                                grid.removeRow(e.data.id);
                            }
                        },
                        error: error
                    });
                }
            }
        }
    }

    function error(jqXHR, textStatus, errorThrown) {
        alert('error');
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }
});

/***/ })

/******/ });