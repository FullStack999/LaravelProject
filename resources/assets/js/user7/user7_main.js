const log = console.log;
$(function () {
    const fn = {
        clients: () => clients(),
        goods: () => goods(),
        products: () => products(),
        orders: () => orders(),
        expenses: () => expenses(),
        history: () => history(),
        accept: () => accept(),
        // places: () => places(),
        suppliers: () => suppliers(),
        newExpenses: () => newExpenses(),
        utilities: () => utilities()
    };
    if (sessionStorage.url) {
        let f = getFn();
        fn[f]();
    } else {
        clients();
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
        $('#clients').off('click').on('click', clients);
        $('#goods').off('click').on('click', goods);
        $('#products').off('click').on('click', products);
        $('#orders').off('click').on('click', orders);
        $('#expenses').off('click').on('click', expenses);
        $('#history').off('click').on('click', history);
        $('#accept').off('click').on('click', accept);
        // $('#places').off('click').on('click', places);
        $('#suppliers').off('click').on('click', suppliers);
        $('#newExpenses').off('click').on('click', newExpenses);
        $('#utilities').off('click').on('click', utilities);

    }

    function utilities() {
        let url = 'user7/utilities_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response.view);
            $('#utilities').addClass('active');
            sessionStorage.setItem('url', url);
            utilitiesData(response);
        })
            .fail(error)
    }

    function utilitiesData(response) {


        let months = '<option value=""> Ընտրել ամիսը</option>';
        for (let x in response['months']) {
            months += `<option value="${response['months'][x].id}">${response['months'][x].name}</option>`;
        }
        $('#utilities_table').grid({
            dataSource: response['utilities'],
            fixedHeader: true,
            fontSize: 15,
            height: 628,
            notFoundText: 'Արդյունք չի գտնվել',
            columns: [
                {field: 'name', title: 'Անուն'},
                {field: 'unit', title: 'Միավոր'},
                {tmpl: '<input class="num_input amt">', title: 'Քանակ'},
                {tmpl: '<input class="num_input price" data-id="{id}" >', title: 'Գումար'},
                {
                    tmpl: '<select class="form-control" id="select_month">' + months + '</select>',
                    title: 'Որ ամսվա համար'
                },
            ]
        });

        $('#utility_btn').off('click').on('click', function () {
            let message = $('.message');
            let modal = $('#expense_modal');
            let input = $('#utilities_table input.price');
            let obj = [];
            $.each(input, function () {
                let inp = $(this);
                let id = inp.data('id');
                let amt = inp.closest('tr').find('.amt');
                let month = inp.closest('tr').find('#select_month');

                if (inp.val() != '' && amt.val() != '' && month.val() != '') {
                    obj.push({
                        id: id,
                        amt: amt.val(),
                        price: inp.val(),
                        month: month.val()
                    });
                }
                else if (amt.val() == '' && inp.val() != '' && month.val() != '') {
                    return obj = false;
                }
                else if (amt.val() == '' && inp.val() == '' && month.val() != '') {
                    return obj = false;
                }
                else if (amt.val() == '' && inp.val() != '' && month.val() == '') {
                    return obj = false;
                }
                else if (amt.val() != '' && inp.val() != '' && month.val() == '') {
                    return obj = false;
                }
                else if (amt.val() != '' && inp.val() == '' && month.val() != '') {
                    return obj = false;
                }
                else if (amt.val() != '' && inp.val() == '' && month.val() == '') {
                    return obj = false;
                }

            });

            if (!obj) {
                modal.modal('show');
                message.text('Ունեք անկանոն լրացրած դաշտ');
            }
            else if (obj.length != 0) {
                $.ajax({
                    url: 'user7/accept_utilities',
                    type: 'post',
                    data: {records: obj},
                    success: function (response) {
                        if (response.status == 'success') {
                            modal.modal('show');
                            message.text('Պատրաստ');
                            input.val('');
                            input.closest('tr').find('.amt').val('');
                            input.closest('tr').find('#select_month').val('')
                        } else {
                            return false
                        }
                    },
                    error: error
                });
            }
            else {
                modal.modal('show');
                message.text('Լրացրեք դաշտերից առնվազը մեկը');
            }
        });

    }

    function expenses() {
        let url = 'user7/expenses_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response.view);
            $('#expenses').addClass('active');
            sessionStorage.setItem('url', url);
            expensesData(response)
        })
            .fail(error);
    }

    function expensesData(data) {

        let modal = $('#expense_modal');

        $('#add_expenses_table').grid('destroy', true, true);
        let grid = $('#add_expenses_table').grid({
            dataSource: {
                success: function () {
                    grid.render(data.goods);

                    $('.check_active').on('change', function () {
                        $(this).closest('tr').find('.p_price').attr('disabled', false).removeClass('disable_amt_inp');
                        let elem = $(this).closest('tr').find('select').empty();
                        let good = elem.data('id');
                        let options = '<option value="" selected>Ընտրել Հաճախորդ</option>';
                        if ($(this).prop('checked')) {
                            $(this).closest('tr').find('.p_price').attr('disabled', true).addClass('disable_amt_inp').val('');
                            $.each(data.clients, function (i, v) {
                                options += `<option value=${v.id}>${v.name}</option>`
                            });
                            elem.append(options)
                        }
                        else {
                            options += '<option value="" selected>Ընտրել մատակարար</option>';
                            $.each(data.goods, function (i, v) {
                                if (v.id == good){
                                    $.each(v.supplier, function (key, item) {
                                        options += `<option value=${item.id}>${item.name}</option>`
                                    });
                                }
                            });
                            elem.append(options) ;
                        }
                    });
                }},
            fixedHeader: true,
            primaryKey: 'id',
            height: 615,
            responsive: true,
            notFoundText: 'Արդյունք չի գտնվել',
            columns: [
                {
                    field: 'name',
                    title: 'Ապրանքի'
                },
                {
                    field: 'unit',
                    title: 'Միավոր'
                },
                {
                    tmpl: '<input class="num_input p_amt" data-id="{id}">',
                    title: 'Քանակ'
                },
                {
                    tmpl: '<input class="num_input p_price">',
                    title: 'Գին'
                },
                {
                    renderer: supplierList,
                    title: 'Մատակարար'
                },
                // {
                //     tmpl: '<input type="checkbox" class="form-control check_active" style="width:18px; height:18px">',
                //     align:'center',
                //     title: 'Վերադարձ',
                //     width: '120px'
                // },
            ]
        });
        function supplierList (value, record, $cell, $displayEl, id, $grid) {
            let sel = `<select class="form-control select" data-id=${id}><option value="" selected>Ընտրել մատակարար</option>`;
            $.each(record.supplier, (i, v) => sel += `<option value="${v.id}">${v.name}</option>`);
            sel += `</select>`;
            return sel;
        }

        $('#expense_btn').off('click').on('click', function () {

            let data = [];
            let err_elem = [];

            $('.p_amt').each(function () {
                let price = $(this).closest('tr').find('.p_price');
                let select = $(this).closest('tr').find('select');
                $(this).closest('tr').css('background', 'initial');

                if ($(this).val() != '' && (price.hasClass('disable_amt_inp') ? true : price.val() != '') && select.val() != '') {
                    let good_id = $(this).data('id');
                    let checkBox = $(this).closest('tr').find('.check_active');
                    data.push({
                        amt: $(this).val(),
                        price: price.val(),
                        [checkBox.prop('checked') ? 'client' : 'supplier']: select.val(),
                        good_id: good_id,
                    })
                }
                else if(
                    ((price.hasClass('disable_amt_inp') ? $(this).val() != '' && select.val() == '' : price.val() == '') && $(this).val() != '' && select.val() == '') ||
                    ((price.hasClass('disable_amt_inp') ? $(this).val() == '' && select.val() == '' : price.val() != '') && $(this).val() == '' && select.val() == '') ||
                    ((price.hasClass('disable_amt_inp') ? $(this).val() == '' && select.val() != '' : price.val() == '') && $(this).val() == '' && select.val() != '') ||
                    ((price.hasClass('disable_amt_inp') ? $(this).val() != '' && select.val() == '' : price.val() != '') && $(this).val() != '' && select.val() == '') ||
                    ((price.hasClass('disable_amt_inp') ? $(this).val() == '' && select.val() != '' : price.val() != '') && $(this).val() == '' && select.val() != '') ||
                    ((price.hasClass('disable_amt_inp') ? $(this).val() != '' && select.val() != '' : price.val() == '') && $(this).val() != '' && select.val() != '')
                    // !$(this).val() || !select.val() || (!price.hasClass('disable_amt_inp') && !price.val())
                ) {
                    FadeInModal({
                        el: modal,
                        title: '',
                        body: '<h5 class="message">Ունեք բաց թողած դաշտ</h5>',
                        footer: '<button type="button" class="btn btn-default" data-dismiss="modal">Լավ</button>',
                    });
                    err_elem.push($(this))
                }
            });

            if (err_elem.length != 0){
                $.each(err_elem, function (e, v) {
                    v.closest('tr').css('background', '#FFD8D8')
                });
                return false;
            }

            if (data.length != 0) {
                    $.post('user7/accept_expenses', {data}, (response) => {
                        if (response.status == 'success') {
                            FadeInModal({
                                el: modal,
                                title: '',
                                body: '<h5 class="message">Ապրանքները հաստատվեցին</h5>',
                                footer: '<button type="button" class="btn btn-default" data-dismiss="modal">Լավ</button>',
                            });
                            grid.reload();
                        }
                    })
                }
            else {
                FadeInModal({
                    el: modal,
                    title: '',
                    body: '<h5 class="message">Չունեք լրացրած դաշտ</h5>',
                    footer: '<button type="button" class="btn btn-default" data-dismiss="modal">Լավ</button>',
                });

            }
        });


        $('#returned_good_table').grid('destroy', true, true);
        let ret_grid = $('#returned_good_table').grid({
            dataSource: {url: 'user7/returned_good_data', type:'post', success: onSuccess},
            fixedHeader: true,
            primaryKey: 'supplier_id',
            height: 600,
            detailTemplate: '<div><table  style="background:#fcf8e3"></div>',
            responsive: true,
            notFoundText: 'Արդյունք չի գտնվել',
            columns: [
                {field: 'supplier_name', title: 'Մատակարար'},
                {tmpl: '<button type="button" class="btn form-control btn-primary accessReturned" data-id="{supplier_id}">Հաստատել</button>', cssClass: 'fa_button', width: '150px'}
            ]
        });

        function onSuccess(response){

            let record = [];
            $.each(response, function (k, item) {
                record.push(item[0])
            });

            ret_grid.render(record);

            $('.accessReturned').on('click', function () {
                let id = $(this).data('id');
                log($(this).closest('tr').next('tr'));
                $.post('user7/access_returned_good', {id}, function (response) {
                    (response.status == 'success') ? ret_grid.reload() : null
                })
            })
        }

        ret_grid.on('detailExpand', function (e, $detailWrapper, id) {
            let detail = $detailWrapper.find('table').grid({
                dataSource: {url: 'user7/returned_good_data', type: 'post'},
                params: {id: id},
                primaryKey: 'ware_id',
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                inlineEditing: { mode: 'command', managementColumn: false},
                fontSize: 15,
                fixedHeader: true,
                columns: [
                    {field: 'good_name', title: 'Ամսաթիվ'},
                    {field: 'good_unit', title: 'Միավոր'},
                    {field: 'amt', title: 'Քանակ', editor: true },
                    { width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button' },
                ],
            });

            detail.on('rowDataChanged', function (e, id, record) {
                $.post('user7/update_returned_good', {record})
            });
            ret_grid.on('detailCollapse', function (e, $detailWrapper, id) {
                $detailWrapper.find('table').grid('destroy', true, true);
            });



            function editManager (value, record, $cell, $displayEl, id, $grid) {
                let $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
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
                    let key = $(this).data('key');
                    FadeInModal({
                        el: modal ,
                        title: '',
                        body: '<h5>Հեռացնել՞</h5>',
                        footer: '<button type="button" id="btn_remove_returned_good_modal" data-dismiss="modal" class="btn btn-primary">Այո</button>\n' +
                            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Ոչ</button>'

                    });
                    $('#btn_remove_returned_good_modal').off('click').on('click', function () {
                        $.ajax({
                            url: 'user7/delete_detail_returned',
                            type: 'delete',
                            data: {id: key},
                            success: function (response) {
                                if (response.status == 'success'){
                                    detail.removeRow(key)
                                }
                            }
                        })
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


        });

        $('#paid_good_table').grid('destroy', true, true);
        let paid_grid = $('#paid_good_table').grid({
            dataSource: {url: 'user7/pay_good_data', success: function (response) {
                log(response);
                    let record = [];
                    for (let x in response){
                        let sum = 0;
                        for (let j in response[x]){
                            sum += +response[x][j].cost
                        }
                        response[x][0].sum = numberWithCommas(sum);
                        record.push(response[x][0])
                    }
                    paid_grid.render(record);

                    $('.pay_good_button').off('click').on('click', function () {
                        let id = $(this).data('id');

                        FadeInModal({
                            el: modal,
                            title: '',
                            body: '<h5 class="message">Կատարել Վ</h5>',
                            footer: '<button type="button" class="btn btn-primary confirm_pay_good" data-dismiss="modal">Այո</button> <button type="button" class="btn btn-default" data-dismiss="modal">Ոչ</button>',
                        });
                        $('.confirm_pay_good').on('click', function () {
                            $.post('user7/access_pay_good', {id}, function (response) {
                                if (response.status == 'success'){
                                    paid_grid.reload();
                                }
                            })
                        });

                    })
                }},
            responsive: true,
            primaryKey: 'supplier_id',
            fixedHeader: true,
            height: 600,
            notFoundText: 'Արդյունք չի գտնվել',
            detailTemplate: '<div><table  style="background:#fcf8e3"></div>',
            columns: [
                {field: 'supplier_name', title: 'Մատակարար'},
                {field: 'sum', title: 'Գործարքի գումարը'},
                {tmpl: '<button type="button" class="btn btn-primary pay_good_button" data-id="{supplier_id}">Վճարել</button>', cssClass: 'fa_button', width: 200},
            ],
        });
        paid_grid.on('detailExpand', function (e, $detailWrapper, id) {
            let detail = $detailWrapper.find('table').grid({
                dataSource: {url: 'user7/pay_good_data/'+id},
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                columns: [
                    {field: 'good_name', title: 'Ապրանք', cssClass: 'bold'},
                    {field: 'good_unit', title: 'Միավոր'},
                    {field: 'amt', title: 'Քանակ'},
                    {field: 'good_price', title: 'Գին'},
                    {field: 'cost', title: 'Գումար'},
                    {field: 'date', title: 'Ընդունման ամսաթիվ'},
                ],
            });

        });





    }

    function history() {

        $('#history').off('click');
        let url = 'user7/history_get';
        $.get(url, function (response) {
            $('#history').off('click');
            removeActive();
            $('#content').html(response);
            $('#history').addClass('active');
            sessionStorage.setItem('url', url);
        })
            .then(() => paidExpensesHistory())
            .then(() => $('#history').off('click').on('click', history))
            .fail(error);
    }

    function paidExpensesHistory() {

        let from = $('#exp_from').val();
        let to = $('#exp_to').val();
        let input = $('#textSearch').val();

        $('#grid_history_expenses').grid('destroy', true, true);
        let expenses_grid = $('#grid_history_expenses').grid({
            dataSource: {
                url: 'user7/paid_goods_history', type: 'post', success: function (response) {
                    let records = [];
                    for (let x in response) {
                        let sum = 0;
                        for (let j in response[x]) {
                            sum += +response[x][j].cost;
                            response[x][j].cost = numberWithCommas(+response[x][j].cost);
                        }
                        response[x][0].sum = numberWithCommas(sum);
                        records.push(response[x][0])
                    }
                    expenses_grid.render(records);
                }
            },
            responsive: true,
            primaryKey: 'supplier_id',
            fixedHeader: true,
            height: 600,
            notFoundText: 'Արդյունք չի գտնվել',
            detailTemplate: '<div><table  style="background:#fcf8e3"></div>',
            params: {name: input, from: from, to: to},
            columns: [
                {field: 'supplier_name', title: 'Մատակարար'},
                {field: 'sum', title: 'Գործարքի գումարը '},
            ],
        });
        expenses_grid.on('detailExpand', function (e, $detailWrapper, id) {
            let detail = $detailWrapper.find('table').grid({
                dataSource: {url: 'user7/paid_goods_history', type: 'post', success: onSuccessDetailFunction},
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                params: {name: input, from: from, to: to, id: id},
                columns: [
                    {field: 'good_name', title: 'Ապրանք', cssClass: 'bold'},
                    {field: 'good_unit', title: 'Միավոր'},
                    {field: 'amt', title: 'Քանակ'},
                    {field: 'good_price', title: 'Գին'},
                    {field: 'cost', title: 'Գումար'},
                    //{field: 'date_of_created', title: 'Ընդունման ամսաթիվ'},
                    {field: 'date_of_updated', title: 'Վճարման ամսաթիվ'},
                ],
            });

            function onSuccessDetailFunction(response) {
                log(response)
                for (let x in response){
                    response[x].cost = numberWithCommas(response[x].cost);
                    response[x].good_price = numberWithCommas(response[x].good_price);
                }
                detail.render(response)
            }
        });

        $('#grid_history_utilities').grid('destroy', true, true);
        let utilities_grid = $('#grid_history_utilities').grid({
            dataSource: {
                url: 'user7/paid_utilities_history', type: 'post', success: function (response) {
                    let records = [];
                    for (let x in response) {
                        let sum = 0;
                        for (let j in response[x]) {
                            sum += response[x][j].balance
                        }
                        response[x][0].sum = numberWithCommas(sum);
                        records.push(response[x][0])
                    }
                    utilities_grid.render(records);
                }
            },
            responsive: true,
            primaryKey: 'month_id',
            notFoundText: 'Արդյունք չի գտնվել',
            height: 560,
            fixedHeader: true,
            detailTemplate: '<div><table  style="background:#fcf8e3"></div>',
            params: {name: input, from: from, to: to},
            columns: [
                {field: 'month_name', title: 'Ամիս'},
                {field: 'sum', title: 'Ամսվա գործարքի ընհանուր գումարը '},
            ],
        });
        utilities_grid.on('detailExpand', function (e, $detailWrapper, id) {
            let detail = $detailWrapper.find('table').grid({
                dataSource: {url: 'user7/paid_utilities_history', type: 'post', success: function (response) {
                        for (let x in response){
                            response[x].balance = numberWithCommas(response[x].balance);
                        }
                        detail.render(response)
                    }},
                responsive: true,
                fixedHeader: true,
                notFoundText: 'Արդյունք չի գտնվել',
                params: {name: input, from: from, to: to, month: id},
                columns: [
                    {field: 'expense_name', title: 'Ծախսի տեսակ', cssClass: 'bold'},
                    {field: 'amt', title: 'Քանակ'},
                    {field: 'balance', title: 'Գումարը'},
                    {field: 'date', title: 'Վճարման ամսաթիվ'},
                ],
            });
        });


        $('.box input').off('keypress').on('keypress', function (e) {
            if (e.keyCode == 13) return false
        });
        $('.box input').off('input').on('input', paidExpensesHistory);
        $('.btn_clear_search').off('click').on('click', paidExpensesHistory);

    }

    function orders() {
        $('#orders').off('click');
        let url = 'user7/orders_get';
        $.get(url, function (response) {
            removeActive();
            $('#orders').addClass('active');
            $('#content').html(response);
            sessionStorage.setItem('url', url);
        })
            .then((response) => getOrdersInfo(response))
            .then(() => $('#orders').off('click').on('click', orders))
            .fail(error);
    }

    function getOrdersInfo() {
        let groupBy = function (client, product) {
            return (group_by.val() == 'client_id') ? client : product;
        };
        let from = $('#order_from').val();
        let to = $('#order_to').val();
        let name = $('#receiving_order_search').val();
        let group_by = $('#group_by');


        $('#orders_table').grid('destroy', true, true);

        let grid = $('#orders_table').grid({
            primaryKey: 'id',
            dataSource: {url: 'user7/orders_data', type: 'post', success: gridDataFunction},
            params: {from: from, to: to, name: name, group_by: group_by.val()},
            detailTemplate: '<div><table  style="background:#fcf8e3"></div>',
            responsive: true,
            fixedHeader: true,
            height: 580,
            fontSize: 15,
            notFoundText: 'Արդյունք չի գտնվել',
            columns: [
                {field: groupBy('client_name', 'product_name'), title: 'Անուն', hidden: false},
                {field: groupBy('', 'total_amt'), title: groupBy('', 'Ընդանուր քանակ'), hidden: groupBy(true, false)},
                {field: 'total_price', title: 'Ընդանուր գումար', hidden: false},
                {field: groupBy('debt', ''), title: groupBy('Պարտք', ''), hidden: groupBy(false, true)},
                {field: groupBy('bucket', ''), title: groupBy('Դույլ', ''), hidden: groupBy(false, true)},
                {field: groupBy('lid', ''), title: groupBy('Կրիշկա', ''), hidden: groupBy(false, true)},
            ],
        });

        function gridDataFunction(response) {
            for (let x in response) {
                response[x].debt = numberWithCommas(response[x].debt);
                response[x].total_price = numberWithCommas(response[x].total_price);
                response[x].product_name = numberWithCommas(response[x].product_name + ' ' + response[x].product_height);
                response[x].id = groupBy(response[x].client_id, response[x].product_id);
            }
            grid.render(response);
        }

        grid.on('detailExpand', function (e, $detailWrapper, id) {
            let detail = $detailWrapper.find('table').grid({
                dataSource: {url: 'user7/orders_data', type: 'post', success: detailFunction},
                params: {id: id, from: from, to: to, name: name, group_by: group_by.val()},
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                fontSize: 15,
                fixedHeader: true,
                columns: [
                    {field: 'date', title: 'Ամսաթիվ <i class="fas fa-sort"></i>', sortable: true},
                    {
                        field: groupBy('product_name', 'client_name'),
                        title: groupBy('Ապրանք ', 'Հաճախորդ ') + '<i class="fas fa-sort"></i>',
                        sortable: true
                    },
                    {field: 'product_amt', title: 'Քանակ <i class="fas fa-sort"></i>', sortable: true},
                    {field: 'order_price', title: 'Գումար <i class="fas fa-sort"></i>', sortable: true},
                ],
            });

            function detailFunction(response) {
                for (let x in response) {
                    response[x].product_name = response[x].product_name + ' ' + response[x].product_height;
                    response[x].order_price = numberWithCommas(response[x].order_price);
                }
                detail.render(response);
            }
        });

        group_by.off('change').on('change', getOrdersInfo);
        $('.box input').off('keypress').on('keypress', function (e) {
            if (e.keyCode == 13) {
                return false
            }
        });
        $('.box input').off('input').on('input', getOrdersInfo);
        $('#btn_search_receiving_order_clear').off().on('click', orders);
    }

    function clients() {
        $('#clients').off('click');
        let url = 'user7/clients_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response);
            $("#clients").addClass('active');
            sessionStorage.setItem('url', url);
            $('#client_status').dropdown();
        })
            .then((response) => getClientsInfo(response))
            .then(() => $('#clients').off('click').on('click', clients))
            .fail(error);
    }

    function getClientsInfo() {
        let modal_title = $('#client_dialog .client_title');
        let error_message = $('#client_dialog .error_message');
        let status_values = [{value: 0, text: "Տեղական"}, {value: 1, text: "Արտահանում"}];
        let grid = $('#client_table').grid({
            dataSource: {
                url: 'user7/client_data', type: 'post', success: (response) => {
                    $.each(response.records, function (k, v) {
                        (v.status == 0) ? v.status_name = 'Տեղական' : v.status_name = 'Արտահանում';
                    });
                    grid.render(response)
                }
            },
            primaryKey: 'id',
            fontSize: 15,
            autoLoad: false,
            notFoundText: 'Արդյունք չի գտնվել',
            inlineEditing: {mode: 'command', managementColumn: false},
            pager: {limit: 10, sizes: [5, 10, 20, 50]},
            columns: [
                {field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {field: 'address', title: 'Հասցե <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {field: 'phone', title: 'Հեռախոս', editor: true,},
                {
                    field: 'status_name', title: 'Կարգավիճակ', type: 'dropdown',
                    editField: 'status', editor: {dataSource: status_values, valueField: 'value'}
                },
                {width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button'},
            ],
        });

        grid.on('rowDataChanged', function (e, id, record) {
            let data = $.extend(true, {}, record);

            $.ajax({
                url: 'user7/update_client',
                data: {record: data},
                method: 'post',
                error: error
            })
        });

        $('#add_new_client').off().on('click', function () {
            modal_title.text('Ավելացնել նոր Հաճախորդ');
            error_message.text('');
            $('#btn_client').off().on('click', function () {
                let client_name = $('#new_client_name');
                let client_address = $('#new_client_height');
                let client_phone = $('#new_client_price');
                let client_status = $('#client_status');
                let record = {
                    name: client_name.val(),
                    address: client_address.val(),
                    phone: client_phone.val(),
                    status: client_status.val(),
                };
                if (client_name.val() == '') {
                    return error_message.text('Անունը պարտադիր դաշտ է');
                }
                else if (client_status.val() == '') {
                    return error_message.text('Կարգավիճակը պարտադիր դաշտ է');
                }
                else {
                    $.ajax({
                        url: 'user7/add_client',
                        data: {record: record},
                        method: 'post',
                        success: () => {
                            grid.reload();
                            client_name.val('');
                            client_address.val('');
                            client_phone.val('');
                            client_status.val('');
                            $('#client_dialog ').modal('hide')
                        },
                        error: error
                    });
                }
            })
        });


        function editManager(value, record, $cell, $displayEl, id, $grid) {
            let $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
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
                let id = $(this).data('key');
                $('#remove_client_confirm_modal').modal('show');

                $('#btn_remove_client').off('click').on('click', function () {
                    $.ajax({
                        url: 'user7/delete_client',
                        data: {id: id},
                        method: 'delete',
                        success: function () {
                            $grid.removeRow($(this).data('key'));
                        },
                        error: error
                    })
                })
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
    }

    // function places() {
    //     $('#places').off('click');
    //     let url = 'user7/places_get';
    //     $.get(url, function (response) {
    //
    //         removeActive();
    //         $("#content").html(response.view);
    //         $("#places").addClass('active');
    //         sessionStorage.setItem('url', url);
    //     })
    //         .then((response) => getPlacesInfo(response))
    //         .then(() => $('#places').off('click').on('click', places))
    //         .fail(error);
    // }

    // function getPlacesInfo(data) {
    //
    //     let modal_title = $('#place_dialog .place_title');
    //     let error_message = $('#place_dialog .error_message');
    //     let defSuppliers = data.subdivisions;
    //
    //     $('#place_modal_select_section').multiselect({
    //         buttonWidth: '100%',
    //         templates: {
    //             li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>',
    //         },
    //         buttonText: function(options, select) {
    //             if (options.length === 0) {
    //                 return 'Ընտրել Պահեստը';
    //             }
    //             else if (options.length > 2) {
    //                 return options.length + ' Պահեստ';
    //             }
    //             else {
    //                 let labels = [];
    //                 options.each(function() {
    //                     if ($(this).attr('label') !== undefined) {
    //                         labels.push($(this).attr('label'));
    //                     }
    //                     else {
    //                         labels.push($(this).html());
    //                     }
    //                 });
    //                 return labels.join(', ') + '';
    //             }
    //         }
    //     });
    //
    //     let grid = $('#internal_destination_table').grid({
    //         dataSource: {url: 'user7/place_data', type: 'post', success: function (response) {
    //             grid.render(response);
    //             let multiSelect = $('.sub_select').multiselect({
    //                 buttonWidth: '100%',
    //                 templates: {
    //                     li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>',
    //                 },
    //                 buttonText: function(options, select) {
    //                     if (options.length === 0) {
    //                         return 'Պահեստի չի պատկանում';
    //                     }
    //                     else if (options.length > 2) {
    //                         return options.length + ' Պահեստներ';
    //                     }
    //                     else {
    //                         let labels = [];
    //                         options.each(function() {
    //                             if ($(this).attr('label') !== undefined) {
    //                                 labels.push($(this).attr('label'));
    //                             }
    //                             else {
    //                                 labels.push($(this).html());
    //                             }
    //                         });
    //                         return labels.join(', ') + '';
    //                     }
    //                 }
    //             });
    //             multiSelect.multiselect('disable');
    //             $.each(response.records, function (k, v) {
    //                 let elem = $('.sub_select[data-id='+v.id+']');
    //                 let selected_id = JSON.parse(elem.attr('data-selected-id'));
    //                 elem.multiselect('select', selected_id)
    //             });
    //         }},
    //         primaryKey: 'id',
    //         fontSize: 15,
    //         autoLoad: false,
    //         notFoundText: 'Արդյունք չի գտնվել',
    //         inlineEditing: {mode: 'command', managementColumn: false},
    //         columns: [
    //             {field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', editor: true, sortable: true},
    //             {renderer: multiSelects, title: 'Պահեստ', width: 350},
    //             {width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button'},
    //         ],
    //         pager: {limit: 10, sizes: [5, 10, 20, 50]}
    //     });
    //
    //     grid.on('rowDataChanged', function (e, id, record) {
    //         record.subdivision = $(this).find('button[data-key = '+id+']').closest('tr').find('.sub_select').val();
    //         $.ajax({
    //             url: 'user7/update_place',
    //             data: {record: record},
    //             method: 'post',
    //             error: error
    //         })
    //     });
    //
    //     function multiSelects(value, record, $cell, $displayEl, id, $grid) {
    //         let ids = JSON.stringify(record.subdivision.map(item => item.id));
    //         let multiSelectValue = `<select class="custom-select sub_select" multiple data-id=${id} data-selected-id=${ids}>`;
    //         for(let i = 0; i < defSuppliers.length; i++) {
    //             multiSelectValue += `<option value=${defSuppliers[i].id}>${defSuppliers[i].name}</option>`;
    //         }
    //         multiSelectValue += `</select>`;
    //
    //         $displayEl.empty().append(multiSelectValue);
    //     }
    //
    //     $('#add_new_place').off('click').on('click', function () {
    //         modal_title.text('Ավելացնել նոր ուղություն');
    //         error_message.text('');
    //         $('#btn_place').off().on('click', function () {
    //             let place_input = $('#new_place_name');
    //             let subdivisions = $('#place_modal_select_section');
    //             let record = {
    //                 name: place_input.val(),
    //                 subdivisions : subdivisions.val(),
    //             };
    //             if (place_input.val() != '') {
    //                 $.ajax({
    //                     url: 'user7/add_places',
    //                     data: {record: record},
    //                     method: 'post',
    //                     success: () => {
    //                         place_input.val('');
    //                         subdivisions.val('');
    //                         $('#place_dialog ').modal('hide');
    //                         grid.reload()
    //                     },
    //                     error: error
    //                 });
    //             } else {
    //                 error_message.text('Լրացրեք նոր ուղություն')
    //             }
    //
    //         })
    //     });
    //
    //
    //     function editManager(value, record, $cell, $displayEl, id, $grid) {
    //         let $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
    //             $delete = $('<button><i class="far fa-trash-alt"></i></button>').attr('data-key', id),
    //             $update = $('<button><i class="far fa-save"></i></button>').attr('data-key', id).hide().css('background', '#610d0d'),
    //             $cancel = $('<button><i class="fas fa-ban"></i></button>').attr('data-key', id).hide().css('background', '#610d0d');
    //         $edit.off().on('click', function () {
    //             $(this).closest('tr').find('.sub_select').multiselect('enable');
    //             $grid.edit($(this).data('key'));
    //             $edit.hide();
    //             $delete.hide();
    //             $update.show();
    //             $cancel.show();
    //         });
    //         $delete.on('click', function () {
    //             let id = $(this).data('key');
    //             $('#remove_place_confirm_modal').modal('show');
    //
    //             $('#btn_remove_place').off('click').on('click', function () {
    //                 $.ajax({
    //                     url: 'user7/delete_place',
    //                     data: {id: id},
    //                     method: 'delete',
    //                     success: function () {
    //                         $grid.removeRow($(this).data('key'));
    //                     },
    //                     error: error
    //                 })
    //             })
    //         });
    //         $update.on('click', function () {
    //             $(this).closest('tr').find('.sub_select').multiselect('disable');
    //             $grid.update($(this).data('key'));
    //             $edit.show();
    //             $delete.show();
    //             $update.hide();
    //             $cancel.hide();
    //         });
    //         $cancel.on('click', function () {
    //             $(this).closest('tr').find('.sub_select').multiselect('disable');
    //             $grid.cancel($(this).data('key'));
    //             $edit.show();
    //             $delete.show();
    //             $update.hide();
    //             $cancel.hide();
    //         });
    //         $displayEl.empty().append($edit).append($delete).append($update).append($cancel);
    //     }
    //
    // }

    function suppliers() {
        $('#suppliers').off('click');
        let url = 'user7/suppliers_get';
        $.get(url, function (response) {
            removeActive();
            $("#content").html(response);
            $("#suppliers").addClass('active');
            sessionStorage.setItem('url', url);
        })
            .then(() => getSuppliersInfo())
            .then(() => $('#suppliers').off('click').on('click', suppliers))
            .fail(error);
    }

    function getSuppliersInfo() {
        let modal_title = $('#suppliers_dialog .place_title');
        let error_message = $('#suppliers_dialog .error_message');
        let grid = $('#suppliers_table').grid({
            dataSource: {url: 'user7/suppliers_data', type: 'post'},
            primaryKey: 'id',
            fontSize: 15,
            autoLoad: false,
            notFoundText: 'Արդյունք չի գտնվել',
            inlineEditing: {mode: 'command', managementColumn: false},
            columns: [
                {field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button'},
            ],
            pager: {limit: 10, sizes: [5, 10, 20, 50]}
        });

        grid.on('rowDataChanged', function (e, id, record) {
            let data = $.extend(true, {}, record);
            $.ajax({
                url: 'user7/update_supplier',
                data: {record: data},
                method: 'post',
                error: error
            })
        });

        $('#add_new_suppliers').off('click').on('click', function () {
            modal_title.text('Ավելացնել նոր ուղություն');
            error_message.text('');
            $('#btn_suppliers').off().on('click', function () {
                let suppliers_input = $('#new_suppliers_name');
                let record = {
                    name: suppliers_input.val(),
                };
                if (suppliers_input.val() != '') {
                    $.ajax({
                        url: 'user7/add_supplier',
                        data: {record: record},
                        method: 'post',
                        success: () => {
                            suppliers_input.val('');
                            $('#suppliers_dialog ').modal('hide');
                            grid.reload()
                        },
                        error: error
                    });
                } else {
                    error_message.text('Լրացրեք նոր ուղություն')
                }

            })
        });


        function editManager(value, record, $cell, $displayEl, id, $grid) {
            let $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
                $delete = $('<button><i class="far fa-trash-alt"></i></button>').attr('data-key', id),
                $update = $('<button><i class="far fa-save"></i></button>').attr('data-key', id).hide().css('background', '#610d0d'),
                $cancel = $('<button><i class="fas fa-ban"></i></button>').attr('data-key', id).hide().css('background', '#610d0d');
            $edit.off().on('click', function () {
                $grid.edit($(this).data('key'));
                $edit.hide();
                $delete.hide();
                $update.show();
                $cancel.show();
            });
            $delete.on('click', function () {
                let id = $(this).data('key');
                $('#remove_suppliers_confirm_modal').modal('show');

                $('#btn_remove_suppliers').off('click').on('click', function () {
                    $.ajax({
                        url: 'user7/delete_supplier',
                        data: {id: id},
                        method: 'delete',
                        success: function () {
                            $grid.removeRow($(this).data('key'));
                        },
                        error: error
                    })
                })
            });
            $update.off().on('click', function () {
                $grid.update($(this).data('key'));
                $edit.show();
                $delete.show();
                $update.hide();
                $cancel.hide();
            });
            $cancel.off().on('click', function () {
                $grid.cancel($(this).data('key'));
                $edit.show();
                $delete.show();
                $update.hide();
                $cancel.hide();
            });
            $displayEl.empty().append($edit, $delete, $update, $cancel);
        }

    }

    function newExpenses() {

        $('#newExpenses').off('click');
        let url = 'user7/newExpenses_get';
        $.get(url, function (response) {
            removeActive();
            $("#content").html(response);
            $("#newExpenses").addClass('active');
            sessionStorage.setItem('url', url);
        })
            .then(() => getExpensesInfo())
            .then(() => $('#newExpenses').off('click').on('click', newExpenses))
            .fail(error);
    }

    function getExpensesInfo() {

        let error_message = $('#expense_dialog .error_message');
        let grid = $('#expenses_table').grid({
            dataSource: {url: 'user7/expense_data', type: 'post'},
            primaryKey: 'id',
            fontSize: 15,
            autoLoad: false,
            inlineEditing: {mode: 'command', managementColumn: false},
            columns: [
                {field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {field: 'unit', title: 'Միավոր<i class="fas fa-sort"></i>', editor: true, sortable: true},
                {width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button'},
            ],
            pager: {limit: 10, sizes: [5, 10, 20, 50]}
        });

        grid.on('rowDataChanged', function (e, id, record) {
            let data = $.extend(true, {}, record);
            $.ajax({
                url: 'user7/update_expense',
                data: {record: data},
                method: 'post',
                error: error
            })
        });

        $('#add_new_expense').off('click').on('click', function () {
            error_message.text('');
            $('#btn_expense').off('click').on('click', function () {
                let expense_input = $('#new_expense_name');
                let expense_unit = $('#new_expense_unit');
                if (expense_input.val() != '' && expense_unit.val() != '') {
                    $.ajax({
                        url: 'user7/add_new_expense',
                        data: {name: expense_input.val(), unit: expense_unit.val()},
                        method: 'post',
                        success: () => {
                            expense_input.val('');
                            expense_unit.val('');
                            error_message.text('Ավելացված է');
                            grid.reload()
                        },
                        error: error
                    });
                } else {
                    error_message.text('Բոլոր դաշտերը պարտադիր են')
                }

            })
        });

        function editManager(value, record, $cell, $displayEl, id, $grid) {
            let $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
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
                let id = $(this).data('key');
                $('#remove_expense_confirm_modal').modal('show');

                $('#btn_remove_expense').off('click').on('click', function () {
                    $.ajax({
                        url: 'user7/delete_expense',
                        data: {id: id},
                        method: 'delete',
                        success: function () {
                            $grid.removeRow($(this).data('key'));
                        },
                        error: error
                    })
                })
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
    }

    function goods() {
        $('#goods').off('click');
        let url = 'user7/goods_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response.view);
            $("#goods").addClass('active');
            sessionStorage.setItem('url', url);
        })
            .then((response) => getGoodsInfo(response))
            .then(() => $('#goods').off('click').on('click', goods))
            .fail(error);
    }

    function getGoodsInfo(response) {
        let modal_title = $('#good_dialog .good_title');
        let error_message = $('#good_dialog .error_message');
        let multiSelect;
        let defSuppliers = response.suppliers;

        $('#good_modal_select_section').dropdown({
            dataSource: response.subdivision,
            placeholder: 'Ընտրել Պահեստը',

        });

        $('#good_modal_select_supplier').multiselect({
            buttonWidth: '100%',
            templates: {
                li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>',
            },
            buttonText: function(options) {
                if (options.length === 0) {
                    return 'Ընտրել մատակարար';
                }
                else if (options.length > 2) {
                    return options.length + ' Մատակարար';
                }
                else {
                    let labels = [];
                    options.each(function() {
                        if ($(this).attr('label') !== undefined) {
                            labels.push($(this).attr('label'));
                        }
                        else {
                            labels.push($(this).html());
                        }
                    });
                    return labels.join(', ') + '';
                }
            }
        });



        let grid = $('#good_table').grid({
            dataSource: {url: 'user7/good_data', type: 'post', success: function (response) {
                for (let x in response){
                    response[x].returnable = true
                }

                grid.render(response);

                    multiSelect = $('.basic').multiselect({
                        buttonWidth: '100%',
                        templates: {
                            li: '<li><a href="javascript:void(0);"><label class="pl-2"></label></a></li>',
                        },
                        buttonText: function(options) {
                            if (options.length === 0) {
                                return 'Չունի մատակարար';
                            }
                            else if (options.length > 2) {
                                return options.length + ' Մատակարար';
                            }
                            else {
                                let labels = [];
                                options.each(function() {
                                    if ($(this).attr('label') !== undefined) {
                                        labels.push($(this).attr('label'));
                                    }
                                    else {
                                        labels.push($(this).html());
                                    }
                                });
                                return labels.join(', ') + '';
                            }
                        }
                    });
                    multiSelect.multiselect('disable');
                    $.each(response.records, function (k, v) {
                        let elem = $('.basic[data-id='+v.id+']');
                        let selected_id = JSON.parse(elem.attr('data-selected-id'));
                        elem.multiselect('select', selected_id)
                    });
                }},
            primaryKey: 'id',
            fontSize: 15,
            autoLoad: false,
            notFoundText: 'Արդյունք չի գտնվել',
            inlineEditing: {mode: 'command', managementColumn: false},
            columns: [
                {field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {field: 'unit', title: 'Միավոր <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {renderer: multiSelects, title: 'Մատակարար'},
                {
                     title: 'Վերադարձող', field: 'returnable', width: 130, type: 'checkbox', align:'center',
                    sortable: true, editor:true
                },
                {
                    field: 'subdivision_name', title: 'Պահեստը', type: 'dropdown', editField: 'subdivision_id',
                    editor: {dataSource: response.subdivisions, valueField: 'id', textField: 'name'}
                },
                {width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button'},
            ],
            pager: {limit: 10, sizes: [5, 10, 20, 50]}
        });

        grid.on('rowDataChanged', function (e, id, record) {

            record.supplier = $(this).find('button[data-key = '+id+']').closest('tr').find('.basic').val();

            $.ajax({
                url: 'user7/update_good',
                data: {record: record},
                method: 'post',
                error: error
            })
        });

        function multiSelects(value, record, $cell, $displayEl, id, $grid) {

            let ids = JSON.stringify(record.supplier.map(item => item.id));

            let multiSelectValue = document.createElement('select');
            multiSelectValue.className = 'custom-select basic';
            multiSelectValue.setAttribute('data-id', id);
            multiSelectValue.setAttribute('multiple', 'multiple');
            multiSelectValue.setAttribute('data-selected-id', ids);

            for(let i = 0; i < defSuppliers.length; i++) {
                let multiOptionValue = document.createElement('option');
                multiOptionValue.value = defSuppliers[i].id;
                multiOptionValue.innerHTML = defSuppliers[i].name;
                multiSelectValue.append(multiOptionValue);
            }

            $displayEl.empty().append(multiSelectValue.outerHTML);
        }

        function editManager(value, record, $cell, $displayEl, id, $grid) {

            let $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
                $delete = $('<button><i class="far fa-trash-alt"></i></button>').attr('data-key', id),
                $update = $('<button><i class="far fa-save"></i></button>').attr('data-key', id).hide().css('background', '#610d0d'),
                $cancel = $('<button><i class="fas fa-ban"></i></button>').attr('data-key', id).hide().css('background', '#610d0d');
            $edit.on('click', function () {
                $(this).closest('tr').find('.basic').multiselect('enable');
                $grid.edit($(this).data('key'));
                $edit.hide();
                $delete.hide();
                $update.show();
                $cancel.show();
            });
            $delete.on('click', function () {
                let id = $(this).data('key');
                $('#remove_good_confirm_modal').modal('show');

                $('#btn_remove_good').off('click').on('click', function () {
                    $.ajax({
                        url: 'user7/delete_good',
                        data: {id: id},
                        method: 'delete',
                        success: function () {
                            $grid.removeRow($(this).data('key'));
                        },
                        error: error
                    })
                })
            });
            $update.on('click', function () {
                $(this).closest('tr').find('.basic').multiselect('disable');
                $grid.update($(this).data('key'));
                $edit.show();
                $delete.show();
                $update.hide();
                $cancel.hide();
            });
            $cancel.on('click', function () {
                $(this).closest('tr').find('.basic').multiselect('disable');
                $grid.cancel($(this).data('key'));
                $edit.show();
                $delete.show();
                $update.hide();
                $cancel.hide();
            });
            $displayEl.empty().append([$edit, $delete, $update, $cancel]);
        }

        $('#add_new_good').off('click').on('click', function () {
            modal_title.text('Ավելացնել նոր ապրանք');
            error_message.text('');

            $('#btn_good').off('click').on('click', function () {

                let good_name = $('#new_good_name');
                let good_unit = $('#new_good_unit');
                let good_price = $('#new_good_price');
                let sub_id = $('#good_modal_select_section');
                let supplier_id = $('#good_modal_select_supplier');
                let record = {
                    name: good_name.val(),
                    unit: good_unit.val(),
                    price: good_price.val(),
                    sub_id: sub_id.val(),
                    supplier_id: supplier_id.val(),
                };

                if (good_name.val() != '' && supplier_id.val().length != 0 && good_unit.val() != '') {
                    log(record);
                    $.ajax({
                        url: 'user7/add_good',
                        data: {record: record},
                        method: 'post',
                        success: () => {
                            $('#good_dialog ').modal('hide');
                            grid.reload()
                        },
                        error: error
                    });
                }
                else {
                    error_message.text('Ունեք բաց թողած դաշտ')
                }
            })
        });
    }

    function excelValidateAndProductAdd(e) {
        let formData = new FormData();
        let file_input = $('#exc_file');
        let file = file_input[0].files[0];
        let message = $('.message');
        formData.append('excel', file);
        if (file) {
            if (file.type != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type != 'application/vnd.ms-excel') {
                return message.text('Սխալ ֆայլ');
            }
            else {
                $.ajax({
                    url: 'user7/add_product_excel',
                    type: 'post',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#excel_product_dialog').modal('hide');
                            file_input.val('');
                            e.data.grid.reload();
                        }
                        else {
                            message.text('Սխալ ֆայլ');
                        }
                    },
                    error: function () {
                        message.text('Սխալ ֆայլ');
                    }
                })
            }
        } else {
            return message.text('Ընտրեք ֆայլ');
        }
    }

    function productInputsValidate(e) {

        let tr = $('.add_new_product_table tr');
        let product_name, product_height, local_price, export_price, record = [], error = false;
        let error_message = $('#product_dialog .error_message');

        $.each(tr, function () {
            product_name = $(this).find('.new_product_name');
            product_height = $(this).find('.new_product_height');
            local_price = $(this).find('.new_local_price');
            export_price = $(this).find('.new_export_price');

            if (product_name.val() == '' || product_height.val() == '' || local_price.val() == '' || export_price.val() == '') {
                error_message.text('Ունեք բաց թողած դաշտ');
                return error = true
            }
            else if (product_height.val() == /[^1-9'"]+/ || local_price.val() == /[^1-9'"]+/ || export_price.val() == /[^1-9'"]+/) {
                error_message.text('Ունեք սխալ լրացրած դաշտ');
                return error = true
            }
            record.push({
                name: product_name.val(),
                height: product_height.val(),
                local_price: local_price.val(),
                export_price: export_price.val()
            })
        });

        if (!error) {
            $.ajax({
                url: 'user7/add_product',
                data: {record: record},
                method: 'post',
                success: function () {
                    e.data.grid.reload();
                    $('#product_dialog ').modal('hide');
                    $('.new_product_name').val('');
                    $('.new_product_height').val('');
                    $('.new_product_price').val('');
                    $('.add_new_product_table tr:not(:first)').remove()

                },
                error: error
            });
        }
    }

    function products() {
        $('#products').off('click');
        let url = 'user7/products_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response);
            $("#products").addClass('active');
            sessionStorage.setItem('url', url);
            $('#add_new_product_table').grid({});
        })
            .then(() => getProductInfo())
            .then(() => $('#products').off('click').on('click', products))
            .fail(error);
    }

    function getProductInfo() {

        $('#btnAddRow').off().on('click', function () {

            $('.add_new_product_table').append('<tr>\n' +
                '<td><input class="gj-textbox-md modal_input new_product_name"></td>' +
                '<td><input class="gj-textbox-md modal_input new_product_height"></td>' +
                '<td><input class="gj-textbox-md modal_input new_local_price"></td>' +
                '<td><input class="gj-textbox-md modal_input new_export_price"></td>' +
                '<td><button class="remove_row"><i class="fas fa-minus-circle"></i></button></td>' +
                '</tr>');

            $('.remove_row').off().on('click', function () {
                $(this).closest('tr').remove()
            })
        });


        let grid = $('#product_table').grid({
            dataSource: {url: 'user7/product_data', type: 'post'},
            primaryKey: 'id',
            autoLoad: false,
            responsive: true,
            fontSize: 15,
            notFoundText: 'Արդյունք չի գտնվել',
            inlineEditing: {mode: 'command', managementColumn: false},
            columns: [
                {field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {field: 'height', title: 'Բոյ <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {
                    field: 'local_price',
                    title: 'Արտահանման գին <i class="fas fa-sort"></i>',
                    editor: true,
                    sortable: true
                },
                {field: 'export_price', title: 'Տեղական գին <i class="fas fa-sort"></i>', editor: true, sortable: true},
                {width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button'},
            ],
            pager: {limit: 10}
        });

        grid.on('rowDataChanged', function (e, id, record) {
            let data = $.extend(true, {}, record);
            $.ajax({
                url: 'user7/update_product',
                data: {record: data},
                method: 'post',
                error: error
            })
        });

        function editManager(value, record, $cell, $displayEl, id, $grid) {
            let $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
                $delete = $('<button><i class="far fa-trash-alt"></i></button>').attr('data-key', id),
                $update = $('<button><i class="far fa-save"></i></button>').attr('data-key', id).hide().css('background', '#610d0d'),
                $cancel = $('<button><i class="fas fa-ban"></i></button>').attr('data-key', id).hide().css('background', '#610d0d');
            $edit.off().on('click', function () {
                $grid.edit($(this).data('key'));
                $edit.hide();
                $delete.hide();
                $update.show();
                $cancel.show();
            });
            $delete.on('click', function () {
                let id = $(this).data('key');
                $('#remove_product_confirm_modal').modal('show');

                $('#btn_remove_product').off('click').on('click', function () {
                    $.ajax({
                        url: 'user7/delete_product',
                        data: {id: id},
                        method: 'delete',
                        success: function () {
                            $grid.removeRow($(this).data('key'));
                        },
                        error: error
                    })
                })
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

        $('#btn_add_product_excel').off().click({grid: grid}, excelValidateAndProductAdd);
        $('#btn_product').off().click({grid: grid}, productInputsValidate);

    }

    function accept() {
        $('#accept').off('click');
        let url = 'user7/accept_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response);
            $('#accept').addClass('active');
            sessionStorage.setItem('url', url);
        })
            .then(() => getAcceptInfo())
            .then(() => $('#accept').off('click').on('click', accept))
            .fail(error);
    }

    function getAcceptInfo() {
        let table = $('#accept_table');
        table.grid({
            primaryKey: 'id',
            dataSource: { url: 'user7/get_clients', method: 'post' },
            responsive: true,
            notFoundText: 'Արդյունք չի գտնվել',
            fixedHeader: true,
            height: 630,
            fontSize: 15,
            columns: [
                {field: 'name', title: 'Անուն '},
                {field: 'address', title: 'Հասցե'},
                {field: 'phone', title: 'Հեռախոս'},
                {title: 'Գումար', tmpl: '<input class="num_input paid" data-id="{id}">'},
            ]
        });
        $('#btn_accept').off('click').on('click', function () {
            let input = table.find('input.paid');
            let modal = $('#accept_dialog');
            let data = [];
            $.each(input, function () {

                if ($(this).val() != '') {
                    data.push({
                        paid: $(this).val(),
                        id: $(this).attr('data-id')
                    })
                }
            });
            if (data.length != '') {
                $.ajax({
                    url: 'user7/accept_post',
                    type: 'post',
                    data: {data: data},
                    success: function (response) {
                        if (response.status == 'success') {
                            table.find('input').val('');
                            FadeInModal({
                                el: modal,
                                title: '',
                                body: '<h5 class="message">Գումարները ընդունվեց</h5>',
                                footer: '<button type="button" class="btn btn-default" data-dismiss="modal">Լավ</button>',
                            })
                        }
                    },
                    error: error
                })
            } else {
                FadeInModal({
                    el: modal,
                    title: '',
                    body: '<h5 class="message">Չունեք լրացրած դաշտ</h5>',
                    footer: '<button type="button" class="btn btn-default" data-dismiss="modal">Լավ</button>',
                })
            }
        });


        //
        //
        // let grid = $('#accept_from_customer_table').grid({
        //     dataSource: {url: 'user7/accept_from_customer', success: function (response) {
        //             grid.reload(response)
        //         }},
        //     fixedHeader: true,
        //     primaryKey: 'supplier_id',
        //     height: 600,
        //     detailTemplate: '<div><table  style="background:#fcf8e3"></div>',
        //     responsive: true,
        //     notFoundText: 'Արդյունք չի գտնվել',
        //     columns: [
        //         {field: 'client_name', title: 'Հաճախորդ'},
        //         {field: 'good_name', title: 'Ապրանք'},
        //         {field: 'good_unit', title: 'Միավոր'},
        //         {field: 'amt', title: 'Քանակ'},
        //         {tmpl: '<button type="button" class="btn form-control btn-primary accept_from_customer_btn" data-id="{client_id}">Հաստատել</button>', cssClass: 'fa_button', width: '150px'}
        //         ]
        // });

    }

    function error(jqXHR, textStatus, errorThrown) {
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }

    function FadeInModal({el, title, body, footer}) {
        el.modal('show');
        el.find('.modal-title').html(title);
        el.find('.modal-body').html(body);
        el.find('.modal-footer').html(footer);
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }


});