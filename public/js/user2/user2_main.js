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
/******/ 	return __webpack_require__(__webpack_require__.s = 19);
/******/ })
/************************************************************************/
/******/ ({

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(20);


/***/ }),

/***/ 20:
/***/ (function(module, exports) {

var log = console.log;

$(document).ready(function () {

    var fn = {
        accessFertilizer: function accessFertilizer() {
            return _accessFertilizer();
        },
        exitFertilizer: function exitFertilizer() {
            return _exitFertilizer();
        },
        fertilizerHistory: function fertilizerHistory() {
            return _fertilizerHistory();
        },
        fertilizers: function fertilizers() {
            return _fertilizers();
        }
    };
    if (sessionStorage.url) {
        var f = getfn(sessionStorage.url);
        fn[f]();
    } else {
        _fertilizers();
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
        $('#fertilizers').off('click').on('click', _fertilizers);
        $('#accessFertilizer').off('click').on('click', _accessFertilizer);
        $('#exitFertilizer').off('click').on('click', _exitFertilizer);
        $('#fertilizerHistory').off('click').on('click', _fertilizerHistory);
    }

    function _fertilizers() {

        $('#fertilizers').off('click');
        var url = 'user2/fertilizers_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response);
            $('#fertilizers').addClass('active');
            sessionStorage.setItem('url', url);
        }).then(function () {
            return fertilizersData();
        }).then(function () {
            return $('#fertilizers').off('click').on('click', _fertilizers);
        }).fail(errors);
    }
    function fertilizersData() {
        $('#fertilizers_table').grid('destroy', true, true);
        var grid = $('#fertilizers_table').grid({
            dataSource: { url: 'user2/fertilizers_data', type: 'post', success: function success(response) {
                    return grid.render(sortByName(response));
                } },
            fontSize: 15,
            responsive: true,
            height: 700,
            fixedHeader: true,
            notFoundText: 'Արդյունք չի գտնվել',
            columns: [{ field: 'good_name', title: 'Անուն <i class="fas fa-sort"></i>' }, { field: 'good_unit', title: 'Միավոր <i class="fas fa-sort"></i>' }, { field: 'good_balance', title: 'Քանակ <i class="fas fa-sort"></i>' }]
        });
    }

    function _accessFertilizer() {
        $('#accessFertilizer').off('click');
        var url = 'user2/accessFertilizer_get';
        $.get(url, function (response) {
            removeActive();
            sessionStorage.setItem('url', url);
            $('#accessFertilizer').addClass('active');
            $('#content').html(response);
        }).then(function () {
            return accessData();
        }).then(function () {
            return $('#accessFertilizer').off('click').on('click', _accessFertilizer);
        }).fail(errors);
    }
    function accessData() {

        $('#access_fertilizer_table').grid('destroy', true, true);
        var grid = $('#access_fertilizer_table').grid({
            dataSource: { url: 'user2/accessFertilizer_data', success: onSuccessFunction },
            primaryKey: 'supplier_id',
            responsive: true,
            detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
            notFoundText: 'Արդյունք չի գտնվել',
            fixedHeader: true,
            fontSize: 15,
            height: 620,
            columns: [{ field: 'supplier_name', title: 'Անուն' }, { field: 'date', title: 'Ամսաթիվ' }, {
                tmpl: '<button class="btn_access_fertilizer" data-products = "{product_keys}" data-id="{supplier_id}">Հաստատել</button><button class="return_to_review" data-paid="{paid}"  data-id="{supplier_id}">Ուղարկել վերանայման</button>',
                align: 'right',
                cssClass: 'fa_button'
            }]
        });

        function onSuccessFunction(response) {

            var record = [];
            var product_keys = [];
            $.each(response, function (k, value) {
                product_keys = [];
                $.each(value, function (v, item) {
                    item.id = item.supplier_id;
                    product_keys.push(item.good_id);
                });
                value[0].product_keys = product_keys.join();
                record.push(value[0]);
            });
            grid.render(record);
            var opacTr = $('#access_fertilizer_table').find('button[data-paid=2]').closest('tr');
            opacTr.css('opacity', '.6').find('*').off();
            opacTr.find('.fa_button div').css('padding', '15px').text('Ուղարկված է վերանայման');

            $('.btn_access_fertilizer').off('click').on('click', function () {
                var id = $(this).data('id'),
                    product_keys = $(this).data('products');
                FadeInModal({
                    body: 'Հաստատել ապրանքի մուտքը',
                    footer: '<button type="button" class="btn btn-default" data-dismiss="modal">Ոչ</button> ' + '<button type="button" id="confirm_access_good" class="btn btn-primary" data-dismiss="modal">Այո</button>',
                    success: function success() {
                        $('#confirm_access_good').off('click').on('click', function () {
                            $.post('user2/confirm_access_fertilizer', { id: id, product_keys: product_keys }, function (response) {
                                if (response.status == 'success') {
                                    grid.reload();
                                }
                            });
                        });
                    }
                });
            });
            $('.return_to_review').off('click').on('click', function () {
                var id = $(this).data('id');
                FadeInModal({
                    body: 'Ուղարկել վերանայման',
                    footer: '<button type="button" id="confirm_access_good" class="btn btn-primary" data-dismiss="modal">Այո</button>' + '<button type="button" class="btn btn-default" data-dismiss="modal">Ոչ</button>',
                    success: function success() {
                        $('#confirm_access_good').off('click').on('click', function () {
                            $.post('user2/return_to_review', { id: id }, function (response) {
                                if (response.status == 'success') {
                                    grid.reload();
                                }
                            });
                        });
                    }
                });
            });
        }

        grid.on('detailExpand', function (e, $detailWrapper, id) {
            var detail = $detailWrapper.find('table').grid({
                dataSource: {
                    url: 'user2/accessFertilizer_data/' + id, success: function success(response) {
                        log(response);
                        detail.render(response);
                    }
                },
                fixedHeader: true,
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                columns: [{ field: 'good_name', title: 'Ապրանք' }, { field: 'good_unit', title: 'Միավոր' }, { field: 'good_amt', title: 'Քանակ' }]
            });
        });
    }

    function _exitFertilizer() {
        var url = 'user2/exitFertilizer_get';
        $.get(url, function (response) {
            removeActive();
            sessionStorage.setItem('url', url);
            $('#content').html(response);
            $('#exitFertilizer').addClass('active');
        }).then(function () {
            return exitData();
        }).then(function () {
            return $('#exitFertilizer').off('click').on('click', _exitFertilizer);
        }).fail(errors);
    }
    function exitData() {

        $('#exit_fertilizer_table').grid('destroy', true, true);
        var grid = $('#exit_fertilizer_table').grid({
            dataSource: { url: 'user2/exitFertilizer_data', type: 'post', success: function success(response) {
                    grid.render(sortByName(response));
                } },
            primaryKey: 'good_id',
            responsive: true,
            fixedHeader: true,
            height: 630,
            fontSize: 15,
            notFoundText: 'Արդյունք չի գտնվել',
            columns: [{ field: 'good_name', title: 'Անուն' }, { field: 'good_unit', title: 'Միավոր' }, { tmpl: '<input class="num_input" data-balance="{good_balance}" data-id="{good_id}"/>', title: 'Քանակ' }, { field: 'good_balance', title: 'Մնացորդ' }]
        });

        $('#btn_exit_fertilizer_table').off('click').on('click', function () {

            var message = $('.exit_fertilizer_message'),
                input_amt = $('#exit_fertilizer_table input'),
                obj = [],
                error_id = [],
                error = false;

            input_amt.removeClass('product_not_enough');
            $.each(input_amt, function () {
                var fertilizer_id = $(this).attr('data-id');
                var amt = $(this).val();
                var balance = $(this).attr('data-balance');

                if (amt !== '' && amt > 0) {
                    obj.push({
                        id: fertilizer_id,
                        amt: -amt
                    });
                    if (balance - amt < 0) {
                        error_id.push(fertilizer_id);
                    }
                }
            });
            if (obj.length == 0) {
                error = true;
                message.text('Լրացրեք քանակ');
            }

            $.each(error_id, function (x) {
                var not = $('#exit_fertilizer_table input[data-id=' + error_id[x] + ']');
                message.text('Լրացրել եք մնացորդից ավել քանակ');
                not.addClass('product_not_enough');
                error = true;
            });
            if (!error) {
                $.ajax({
                    url: 'user2/posts',
                    type: 'post',
                    data: { data: obj },
                    success: function success(response) {
                        if (response.status == 'success') {
                            message.text('Ելքերը հաջողությամբ կատարվեցին');
                            grid.reload();
                        }
                    },
                    error: errors
                });
            }
        });
    }

    function _fertilizerHistory() {
        var url = 'user2/fertilizerHistory_get';
        $('#fertilizerHistory').off('click');
        removeActive();
        sessionStorage.setItem('url', url);
        $.get(url, function (response) {
            $('#content').html(response);
            $('#fertilizerHistory').addClass('active');
        }).then(function () {
            return fertilizerHistoryData();
        }).then(function () {
            return $('#fertilizerHistory').off('click').on('click', _fertilizerHistory);
        }).fail(errors);
    }
    function fertilizerHistoryData() {

        $('#fertilizers_history_table').grid('destroy', true, true);
        var from = $('#history_from').val();
        var to = $('#history_to').val();
        var name = $('#history_search_name').val();
        var select = $('#fertilizer_history_drop_down');

        var selectBy = function selectBy(access, exit, all) {
            switch (select.val()) {
                case 'access':
                    return access;
                case 'exit':
                    return exit;
                default:
                    return all;
            }
        };

        var grid = $('#fertilizers_history_table').grid({
            primaryKey: 'id',
            dataSource: { url: 'user2/fertilizers_history_data', type: 'post', success: onSuccessGridFunction },
            fontSize: 15,
            fixedHeader: true,
            height: 555,
            notFoundText: 'Արդյունք չի գտնվել',
            params: {
                from: from,
                to: to,
                select: select.val(),
                name: name
            },
            detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
            responsive: true,
            columns: [{ field: 'fertilizer_name', title: 'Անուն' }, { field: 'fertilizer_unit', title: 'Միավոր' }, { field: selectBy('access_sum', 'exit_sum', 'access_sum'), title: selectBy('Ընդհանուր մուտքեր', 'Ընդհանուր ելքեր', 'Ընդհանուր մուտքեր') }, { field: selectBy('', '', 'exit_sum'), title: selectBy('', '', 'Ընդհանուր ելքեր') }]
        });

        function onSuccessGridFunction(response) {

            var records = [];
            for (var x in response) {
                var access = 0,
                    exit = 0;
                for (var j in response[x]) {
                    response[x][j].id = response[x][j].fertilizer_id;
                    if (response[x][j].amt > 0) {
                        access += +response[x][j].amt;
                    } else {
                        exit += +response[x][j].amt;
                    }
                }
                response[x][0].access_sum = access;
                response[x][0].exit_sum = Math.abs(exit);
                records.push(response[x][0]);
            }
            grid.render(records);
        }
        grid.on('detailExpand', function (e, $detailWrapper, id) {

            var detail = $detailWrapper.find('table').grid({
                params: {
                    fertilizer_id: id,
                    from: from,
                    to: to,
                    select: select.val()
                },
                dataSource: { url: 'user2/fertilizers_history_data', type: 'post' },
                fixedHeader: true,
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                columns: [{ field: 'date', title: 'Ամսաթիվ' }, { field: selectBy('access', 'exit', 'access'), title: selectBy('Մուտք', 'Ելք', 'Մուտք') }, { field: selectBy('', '', 'exit'), title: selectBy('', '', 'Ելք') }, { field: 'balance', title: 'Մնացորդ' }]
            });
        });

        $('.box input').off('keypress').on('keypress', function (e) {
            if (e.keyCode == 13) {
                return false;
            }
        });
        $('.box input').off('input').on('input', fertilizerHistoryData);
        $('.box select, .box input').off('change').on('change', fertilizerHistoryData);

        $('#btn_exit_history_search_clear').off('click').on('click', _fertilizerHistory);
    }

    function sortByName(obj) {
        var sorter = obj.slice(0);
        sorter.sort(function (a, b) {
            var x = a.good_name.toLowerCase();
            var y = b.good_name.toLowerCase();
            return x < y ? -1 : x > y ? 1 : 0;
        });
        return sorter;
    }
    function FadeInModal(_ref) {
        var body = _ref.body,
            footer = _ref.footer,
            success = _ref.success;

        var modal = $('#notificationModal');
        modal.modal('show');
        modal.find('.modal-body').html(body);
        modal.find('.modal-footer').html(footer);
        success();
    }
    function errors(jqXHR, textStatus, errorThrown) {
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }
});

/***/ })

/******/ });