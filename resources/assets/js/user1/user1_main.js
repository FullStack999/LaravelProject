const log = console.log;
$(document).ready(function () {

    const fn = {
        accessGood: () => accessGood(),
        exitGood: () => exitGood(),
        goodHistory: () => goodHistory(),
        goods: () => goods()
    };
    if (sessionStorage.url) {
        let f = getfn(sessionStorage.url);
        fn[f]();
    } else {
        goods();
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
        $('#goods').off('click').on('click', goods);
        $('#accessGood').off('click').on('click', accessGood);
        $('#exitGood').off('click').on('click', exitGood);
        $('#goodHistory').off('click').on('click', goodHistory);
    }

    function accessGood() {
        let url = 'user1/accessGood_get';
        $.get(url, function (response) {
            removeActive();
            sessionStorage.setItem('url', url);
            $('#content').html(response.view);
            $('#accessGood').addClass('active');

            accessData({status: 'reception'});
            $('#supplier-tab').click({status: 'supplier'}, accessData);
            $('#reception-tab').click({status: 'reception'}, accessData);

        }).fail(errors);
    }

    function accessData(e) {

        let status = e.data ? e.data.status : e.status;

        if (status == 'supplier'){

            $('#access_good_table').grid('destroy', true, true);
            let grid = $('#access_good_table').grid({
                dataSource: {url: 'user1/accessGood_data', type: 'post', success: onSuccessFunc},
                primaryKey: 'id',
                responsive: true,
                detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
                notFoundText: 'Արդյունք չի գտնվել',
                //fixedHeader: true,
                fontSize: 15,
                height: 50,
                columns: [
                    {field: 'supplier_name', title: 'Անուն'},
                    {field: 'date_of_created', title: 'Ամսաթիվ'},
                    {
                        tmpl: '<button class="btn_access_good" data-products = "{product_keys}" data-id="{id}">Ընդունել</button><button class="return_to_review" data-paid="{paid}"  data-id="{id}">Ուղարկել վերանայման</button>',
                        align: 'right',
                        title: '',
                        cssClass: 'fa_button'
                    },
                ]
            });
            function onSuccessFunc (response) {

                let records = [];
                let product_keys = [];
                $.each(response, function (k, value) {
                    product_keys = [];
                    $.each(value, function (v, item) {
                        item.id = item.supplier_id;
                        product_keys.push(item.good_id)
                    });
                    value[0].product_keys = product_keys.join();
                    records.push(value[0])
                });

                grid.render(records);

                let opacTr = $('#access_good_table').find('button[data-paid=2]').closest('tr');
                opacTr.css('opacity', '.6').find('*').off();
                opacTr.find('.fa_button div').css({
                    'padding': '15px',
                }).text('Ուղարկված է վերանայման');

                $('.btn_access_good').off('click').on('click', function () {
                    let id = $(this).data('id'), product_keys = $(this).data('products');
                    FadeInModal({
                        body: 'Հաստատել ապրանքի մուտքը',
                        footer: '<button type="button" class="btn btn-default" data-dismiss="modal">Ոչ</button> ' +
                            '<button type="button" id="confirm_access_good" class="btn btn-primary" data-dismiss="modal">Այո</button>',
                        success: () => {
                            $('#confirm_access_good').off('click').on('click', function () {
                                $.post('user1/confirm_access_good', { id, product_keys }, function (response) {
                                    if (response.status == 'success') {
                                        grid.reload();
                                    }
                                })
                            });
                        }
                    });

                });
                $('.return_to_review').off('click').on('click', function () {
                    let id = $(this).data('id');
                    FadeInModal({
                        body: 'Ուղարկել վերանայման',
                        footer: '<button type="button" id="confirm_access_good" class="btn btn-primary" data-dismiss="modal">Այո</button>' +
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Ոչ</button>',
                        success: () => {
                            $('#confirm_access_good').off('click').on('click', function () {
                                $.post('user1/return_to_review', { id }, function (response) {
                                    if (response.status == 'success') {
                                        grid.reload();
                                    }
                                })
                            });
                        }
                    });
                })
            }
            grid.on('detailExpand', function (e, $detailWrapper, id) {
                let detail = $detailWrapper.find('table').grid({
                    dataSource: {
                        url: 'user1/accessGood_data', type: 'post', success: function (response) {
                            detail.render(response);
                        }
                    },
                    params: {id},
                    fixedHeader: true,
                    responsive: true,
                    fontSize: 15,
                    notFoundText: 'Արդյունք չի գտնվել',
                    columns: [
                        {field: 'good_name', title: 'Ապրանք'},
                        {field: 'good_unit', title: 'Միավոր'},
                        {field: 'good_amt', title: 'Քանակ'},
                    ],
                });
            });
            $('#btn_access_good_btn').off('click').on('click', function () {
                let message = $('.access_good_message');
                let input_amt = $('#access_good_table input');
                let obj = [];
                $.each(input_amt, function () {

                    let client = $(this).closest('tr').find('.clients').val();

                    if ($(this).val() != '' && $(this).val() > 0) {
                        obj.push({
                            id: $(this).attr('data-id'),
                            amt: $(this).val(),
                            client_id: (client != '') ? client : null
                        });
                    }
                });

                if (obj.length != 0) {
                    $.ajax({
                        url: 'user1/posts',
                        type: 'POST',
                        dataType: 'json',
                        data: {data: obj},
                        success: function (response) {
                            if (response.status == 'success') {
                                message.text('Մուտքերը հաջողությամբ կատարվեցին');
                                grid.reload();
                            }
                        },
                        error: errors
                    });
                } else {
                    message.text('Լրացնել քանակ')
                }
            })
        }
        else if(status == 'reception'){

            let grid = $('#reception_good_table').grid({
                dataSource: {url: 'user1/receptionGood_data', type: 'post', success: (response)=>{
                    grid.render(response.goods);
                        let options = '<option value="">Ընտրել Հաճախորդ</option>';
                        for (let x in response.clients){
                            options += `<option value=${response.clients[x].id}>${response.clients[x].name}</option>`
                        }
                        $('.client_list').append(options)
                    }},
                primaryKey: 'id',
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                fontSize: 15,
                height: 620,
                columns: [
                    {field: 'name', title: 'Անուն'},
                    {field: 'unit', title: 'Միավոր'},
                    {tmpl: '<input class="num_input" data-id="{id}">', title: 'Քանակ'},
                    {tmpl: '<select class="form-control client_list">', title: 'Հաճախորդ'},
                ],
            });

            $('#btn_access_good_btn').off('click').on('click', function () {
                let message = $('.access_good_message');
                let input_amt = $('#reception_good_table input');
                input_amt.closest('tr').css('background', 'initial');
                let obj = [];
                let errElem = [];
                $.each(input_amt, function () {
                    let id = $(this).attr('data-id');
                    let amt = $(this).val();
                    let client = $(this).closest('tr').find('.client_list').val();
                    if (amt != '' && client != '') {
                        obj.push({id, amt, client, type: 'reception'});
                    }
                    else if (amt != '' && client == '' || amt == '' && client != ''){
                        errElem.push($(this))
                    }
                });
                if (errElem.length != 0){
                    for (let x in errElem){
                        errElem[x].closest('tr').css('background', '#FFD8D8')
                    }
                    return false
                }
                if (obj.length != 0){
                    $.ajax({
                        url: 'user1/posts',
                        type: 'post',
                        dataType: 'json',
                        data: {data: obj},
                        success: function (response) {
                            if (response.status == 'success') {
                                message.text('Մուտքերը հաջողությամբ կատարվեցին');
                                input_amt.val('');
                                input_amt.closest('tr').find('select').val('');
                            }
                        },
                        error: errors
                    });
                }
                else{
                    message.text('Լրացնել քանակ')
                }
            })
        }
    }

    function exitGood() {
        let url = 'user1/exitGood_get';
        $.get(url, function (response) {
            removeActive();
            sessionStorage.setItem('url', url);
            $('#content').html(response);
            $('#exitGood').addClass('active');
        })
            .then(() => exitData())
            .then(() => $('#exitGood').off('click').on('click', exitGood))
            .fail(errors);
    }

    function exitData() {

        $('#exit_good_table').grid('destroy', true, true);
        let grid = $('#exit_good_table').grid({
            dataSource: {url: 'user1/exitGood_post', type: 'post', success: function (response) {
                    grid.render(sortByName(response))
                }},
            primaryKey: 'good_id',
            responsive: true,
            fixedHeader: true,
            height: 630,
            fontSize: 15,
            notFoundText: 'Արդյունք չի գտնվել',
            columns: [
                {field: 'good_name', title: 'Անուն'},
                {field: 'good_unit', title: 'Միավոր'},
                {
                    tmpl: '<input class="num_input" data-balance="{good_balance}" data-id="{good_id}"/>',
                    title: 'Քանակ'
                },
                {field: 'good_balance', title: 'Մնացորդ'}

            ]
        });
        $('#btn_exit_good_table').off('click').on('click', function () {

            let message = $('.exit_good_message'),
                input_amt = $('#exit_good_table input'),
                obj = [],
                error_id = [],
                error = false;

            input_amt.removeClass('product_not_enough');

            $.each(input_amt, function () {
                let product_id = $(this).attr('data-id');
                let amt = $(this).val();
                let balance = $(this).attr('data-balance');

                if (amt !== '' && amt > 0) {
                    obj.push({
                        id: product_id,
                        amt: -amt,
                    });
                    if ((balance - amt) < 0) {
                        error_id.push(product_id);
                    }
                }
            });
            if (obj.length == 0) {
                error = true;
                message.text('Լրացրեք քանակ');
            }

            $.each(error_id, function (x) {
                let not = $('#exit_good_table input[data-id=' + error_id[x] + ']');
                message.text('Լրացրել եք մնացորդից ավել քանակ');
                not.addClass('product_not_enough');
                error = true

            });
            if (!error) {
                $.post('user1/posts', {data: obj},(response) => {
                    if (response.status == 'success') {
                            message.text('Ելքերը հաջողությամբ կատարվեցին');
                            input_amt.val('');
                            grid.reload();
                    }
                }).fail(errors);
            }
        })
    }

    function goodHistory() {
        let url = 'user1/goodHistory_get';
        $('#goodHistory').off('click');
        removeActive();
        sessionStorage.setItem('url', url);
        $.get(url, function (response) {
            $('#content').html(response);
            $('#goodHistory').addClass('active');
        })
            .then(() => historyData())
            .then(() => $('#goodHistory').off('click').on('click', goodHistory))
            .fail(errors);

    }

    function historyData() {

        let from = $('#history_from').val();
        let to = $('#history_to').val();
        let name = $('#history_search_name').val();
        let select = $('#goods_history_drop_down');

        let frontColumns = [
            {field: 'good_name', title: 'Անուն'},
            {field: 'good_unit', title: 'Միավոր'},
            {field: 'access_sum', title: 'Ընդհանուր մուտքեր'},
            {field: 'exit_sum', title: 'Ընդհանուր ելքեր'},
        ];

        switch (select.val()) {
            case 'access' :
                frontColumns.splice(3, 1);
                break;
            case 'exit' :
                frontColumns.splice(2, 1);
                break;
        }


        $('#exit_history_table').grid('destroy', true, true);
        let grid = $('#exit_history_table').grid({
            primaryKey: 'id',
            dataSource: {url: 'user1/history_date', type: 'post', success: onSuccessGridFunction},
            fontSize: 15,
            fixedHeader: true,
            height: 555,
            notFoundText: 'Արդյունք չի գտնվել',
            params: {
                from: from,
                to: to,
                select: select.val(),
                name: name,
            },
            detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
            responsive: true,
            columns: frontColumns
        });


        function onSuccessGridFunction(response) {

            let records = [];
            for (let x in response) {
                let access = 0, exit = 0;
                for (let j in response[x]) {
                    response[x][j].id = response[x][j].good_id;
                    if (response[x][j].amt > 0) {
                        access += +response[x][j].amt
                    } else {
                        exit += +response[x][j].amt
                    }
                }
                response[x][0].access_sum = access;
                response[x][0].exit_sum = Math.abs(exit);
                records.push(response[x][0])
            }
            grid.render(records);
        }

        grid.on('detailExpand', function (e, $detailWrapper, id) {

            let columns = [
                {field: 'date_of_updated', title: 'Ամսաթիվ'},
                {field: 'access', title: 'Մուտք'},
                {field: 'exit', title: 'Ելք'},
                {field: 'supplier', title: 'Ումից եք ստացել'},
                {field: 'balance', title: 'Մնացորդ'},
            ];

            switch (select.val()) {
                case 'access' :
                    columns.splice(2, 1);
                    break;
                case 'exit' :
                    columns.splice(1, 1);
                    columns.splice(2, 1);
                    break;
            }

            $detailWrapper.find('table').grid({
                params: {
                    good_id: id,
                    from: from,
                    to: to,
                    select: select.val(),
                },
                dataSource: {url: 'user1/history_date', type: 'post'},
                fixedHeader: true,
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                columns: columns,
            });
        });

        $('.box input').off('keypress').on('keypress', function (e) {
            if (e.keyCode == 13) {
                return false
            }
        });
        $('.box input').off('input').on('input', historyData);
        $('.box select, .box input').off('change').on('change', historyData);

        $('#btn_exit_history_search_clear').off('click').on('click', goodHistory);
    }

    function goods() {
        $('#goods').off();
        let url = 'user1/goods_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response);
            $('#goods').addClass('active');
            sessionStorage.setItem('url', url);
        })
            .then(() => goodsData())
            .then(() => $('#goods').off('click').on('click', goods))
            .fail(errors);
    }

    function goodsData() {
        $('#goods_table').grid('destroy', true, true);
        let grid = $('#goods_table').grid({
            dataSource: {url: 'user1/goods_data', type: 'post', success: response => grid.render(sortByName(response))},
            fontSize: 15,
            responsive: true,
            height: 700,
            fixedHeader: true,
            notFoundText: 'Արդյունք չի գտնվել',
            columns: [
                {field: 'good_name', title: 'Անուն <i class="fas fa-sort"></i>'},
                {field: 'good_unit', title: 'Միավոր <i class="fas fa-sort"></i>'},
                {field: 'good_balance', title: 'Քանակ <i class="fas fa-sort"></i>'},
            ],
        });
    }

    function sortByName(obj){
        let sorter = obj.slice(0);
        sorter.sort(function(a,b) {
            let x = a.good_name.toLowerCase();
            let y = b.good_name.toLowerCase();
            return x < y ? -1 : x > y ? 1 : 0;
        });
        return sorter;
    }

    function FadeInModal({ body, footer, success }) {
        let modal = $('#notificationModal');
        modal.modal('show');
        modal.find('.modal-body').html(body);
        modal.find('.modal-footer').html(footer);
        success();
    }

    function errors(jqXHR, textStatus, errorThrown) {
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }

});
