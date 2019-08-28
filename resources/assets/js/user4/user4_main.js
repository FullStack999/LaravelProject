const log = console.log;
$(document).ready(function() {
    const fn = {
        orders:()=>orders(),
        addProductsPage:()=>addProductsPage(),
        movements:()=>movements(),
        products:()=>products()
    };
    if(sessionStorage.url){
        let f = getFn(sessionStorage.url);
        fn[f]();
    }else{
        products();
    }

    function getFn(){
        return sessionStorage.url.split('/').pop().split('_').shift();
    }

    function removeActive() {
        if(sessionStorage.url) {
            $('#' + getFn(sessionStorage.url)).removeClass('active');
            return true;
        }
        return false;
    }

    events();

    function events() {
        $('#addProductsPage').off('click').on('click', addProductsPage);
        $('#movements').off('click').on('click',movements);
        $('#products').off('click').on('click', products);
        $('#orders').off('click').on('click', orders);
    }

    function orders(){
        $('#orders').off('click');
        let url = 'user4/orders_get';
        $.get(url, function(response){
            removeActive();
            $('#content').html(response);
            $('#orders').addClass('active');
            sessionStorage.setItem('url',url);
        })
        .then(()=> ordersData())
        .then(()=> $('#orders').off('click').on('click', orders))
        .fail(error);
    }

    function ordersData() {
        $('#orders_tbl').grid('destroy', true, true);
        let grid = $('#orders_tbl').grid({
            dataSource: {url: 'user4/orders_data', success: function (response) {
                let records = [];
                for(let x in response.orders){
                    records.push(response.orders[x][0])
                }
                for (let i = 0; i < records.length; i++){
                    for(let j = 0; j < response.goods.length; j++){
                        records[i].good_elem1 = '<input class="num_input" data-id= ' + response.goods[0].id + ' data-name=' + response.goods[0].name + '>';
                        records[i].good_elem2 = '<input class="num_input" data-id= ' + response.goods[1].id + ' data-name=' + response.goods[1].name + '>';
                        records[i].good_elem3 = '<input class="num_input" data-id= ' + response.goods[2].id + ' data-name=' + response.goods[2].name + '>';
                    }
                }
                grid.render(records);
                let back_fill = $('#orders_page button[data-fill=1]');
                let tr = back_fill.closest('tr');
                tr.css('opacity', '1').find('*').on('click');
                tr.css('opacity', '.6').find('*').off();
                back_fill.after('<p class="message_order_fill">Վերանայվում է</p>');
                tr.find('button').remove();
            }},
            primaryKey: 'id',
            responsive: true,
            notFoundText: 'Արդյունք չի գտնվել',
            fixedHeader: true,
            height: 680,
            detailTemplate: '<div><table  style="background:#fcf8e3"></div>',
            columns:[
                {field: 'client_name', title: 'Հաճախորդ'},
                {field: 'date', title: 'Ամսաթիվ'},
                {field: 'good_elem1', title: 'Դույլ'},
                {field: 'good_elem2', title: 'Կափարիչ'},
                {field: 'good_elem3', title: 'Արկղ'},
                {tmpl: '<button>Հաստատել</button>', align: 'right', cssClass: 'fa_button',  width: 106, events: {'click': closeOrder}},
                {tmpl: '<button data-fill={back_fill}>Ուղարկել ետ լրացման</button>', align: 'center', width: 175, cssClass: 'fa_button', events: {'click': backFill}},
            ],
        });

        grid.on('detailExpand', function (e, $detailWrapper, id) {
            $detailWrapper.find('table').grid({
                params: {id: id},
                dataSource: {url:'user4/order_detail', type: 'post'},
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                columns: [
                    { field: 'product_name', title: 'Ապրանք <i class="fas fa-sort"></i>',  sortable: true },
                    { field: 'product_height', title:'Բոյ <i class="fas fa-sort"></i>', sortable: true },
                    { field: 'product_amt', title: 'Քանակ <i class="fas fa-sort"></i>',  sortable: true },
                ],
            });
        });

        function closeOrder(e){
            let bucket = $(this).closest('tr').find('input.num_input');


            let id = e.data.id;
            let client_id = e.data.record.client_id;
            let dialog = $('#ord_dialog');
            let message = dialog.find('.message');


            let data = [];
            for (let i = 0; i < bucket.length; i++){
                if (bucket.eq(i).val()){
                    data.push({
                        good_id: bucket.eq(i).data('id'),
                        good_amt: bucket.eq(i).val(),
                        client_id,
                        id
                    })
                }

            }
            if (data.length < 3) {
                dialog.modal('show');
                message.text('Դաշտերը անկանոն են լրացված');
            }
            else{
                $('#access_prod_to_client').modal('show');
                $('#modal_access_button').off('click').on('click', function () {
                    $.ajax({
                        url: 'user4/orders_post',
                        type: 'post',
                        data: { data },
                        success: function(response){
                            if (response.status == 'success'){
                                grid.reload();
                            }
                        },
                        error: error
                    })
                });
            }
        }
        function backFill(e){
            let order_id = e.data.id;
            $.ajax({
                url: 'user4/back_fill',
                type: 'post',
                data: {id: order_id},
                success: function (response) {
                   if (response.status == 'success'){
                       grid.reload();
                   }
                }
            })
        }
    }

    function products(){
        let url = 'user4/products_get';
        $.get(url, function(response){
            removeActive();
            $('#products').addClass('active').closest('ul').collapse();
            sessionStorage.setItem('url',url);
            $('#content').html(response.view);
            $('#end_products_table').grid({
                dataSource: response.end_products,
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                autoLoad: false,
                columns: [
                    { field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', sortable: true},
                    { field: 'height', title: 'Բոյ <i class="fas fa-sort"></i>', sortable: {sorter: caseSensitiveSort}},
                    { field: 'balance', type: 'number', title: 'Քանակ <i class="fas fa-sort"></i>', sortable: {sorter: caseSensitiveSort}},
                ],
                pager: {
                    limit: 10,
                    sizes: [5, 10, 20, 50],
                }
            });
            function caseSensitiveSort (direction, column) {
                return function (recordA, recordB) {
                    let a = +recordA[column.field] || '',
                        b = +recordB[column.field] || '';
                    return (direction == 'asc') ? a < b : b < a;
                };
            }

        }).fail(error);
    }

    function addProductsPage(){
        $('#addProductsPage').off('click');
        let url = 'user4/addProductsPage_get';
        $.get(url, function(response){
            removeActive();
            $('#addProductsPage').addClass('active').closest('ul').collapse();
            sessionStorage.setItem('url',url);
            $('#content').html(response);
        })
        .then(()=> addProductData())
        .then(()=> $('#addProductsPage').off('click').on('click', addProductsPage))
        .fail(error);
    }

    function addProductData() {

        let tables = $('.prod_height_detail');
        tables.each(function () {
            $(this).grid({
            });
        });

        $('#get_product').off('click').on('click', function () {
            let message = $('.access_product_message');
            let dialog = $('#dialog_access_product');
            let inputs = tables.find('input');
            let data = [];
            inputs.each(function () {
                if ($(this).val() != ''){
                    data.push({
                        product_id: $(this).data('id'),
                        product_amt: $(this).val(),
                    })
                }
            });
            if (data.length != 0){
                $.ajax({
                    url: 'user4/add_end_products',
                    type: 'post',
                    data: {data: data},
                    success: function(response){
                        if(response.status === 'success'){
                            inputs.val('');
                            message.text('Ավելացված է');
                            dialog.modal('show');
                            dialog.on('hidden.bs.modal', function () {
                                $('.prod_item_header + .collapse ').collapse('hide');
                            })
                        }
                    },
                    error: error
                });
            }else{
                message.text('Բոլոր դաշտերը դատարկ են ');
                dialog.modal('show');
            }

        });
    }

    function movements() {
        $('#movements').off('click');
        let url = 'user4/movements_get';
        removeActive();
        sessionStorage.setItem('url', url);
        $.get(url, function (response) {
            $('#content').html(response);
            $('#movements').addClass('active');
        })
        .then(()=> movementsData())
        .then(()=> $('#movements').off('click').on('click', movements))
        .fail(error);
    }

    function movementsData(){
        let from = $('#movements_from');
        let to = $('#movements_to');
        let client = $('#select_clients');
        let select = $('#movements_drop_down');
        let selectBy = function (access, exit, all) {
            if (select.val() == 'access') return access;
            else if (select.val() == 'exit' || client.val() != 0) return exit;
            else return all;
        };

        $('#movements_table').grid('destroy', true, true);

        let grid = $('#movements_table').grid({
            primaryKey: 'id',
            dataSource: {url: 'user4/movements_data', type: 'post', success: onSuccessGridFunction},
            params: {from: from.val(), to: to.val(), select: select.val(), client_id: client.val()},
            detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
            responsive: true,
            fixedHeader: true,
            notFoundText: 'Արդյունք չի գտնվել',
            height: 530,
            columns: [
                { field: 'product_name', title: 'Անուն' },
                { field: selectBy('access_sum', 'exit_sum', 'access_sum'),
                    title: selectBy('Ընդհանուր մուտքեր', 'Ընդհանուր ելքեր', 'Ընդհանուր մուտքեր')},
                { field: selectBy('', '', 'exit_sum'), title: selectBy('', '', 'Ընդհանուր ելքեր' )},
            ]
        });

        function onSuccessGridFunction(response) {

            let records = [];
            for( let x in response ){
                let access = 0, exit = 0;
                for (let j in response[x]){
                    response[x][j].id = response[x][j].product_id;
                    if (response[x][j].amt > 0){
                        access += response[x][j].amt
                    }else{
                        exit += response[x][j].amt
                    }
                }
                response[x][0].access_sum = access;
                response[x][0].exit_sum = Math.abs(exit);
                response[x][0].product_name = response[x][0].product_name + ' ' + response[x][0].product_height;
                records.push(response[x][0])
            }
            grid.render(records);

            select.removeAttr('disabled');
            client.removeAttr('disabled');
            if (client.val() != ''){
                select.attr('disabled', true);
            }
            if (select.val() == 'access'){
                client.attr('disabled', true)
            }

        }

        grid.on('detailExpand', function (e, $detailWrapper, id) {
            let detail = $detailWrapper.find('table').grid({
                dataSource: { url:'user4/movements_data', type: 'post', success: successDetailFunction },
                params: { product_id: id, from: from.val(), to: to.val(), select: select.val(), client_id: client.val()},
                responsive: true,
                notFoundText: 'Արդյունք չի գտնվել',
                fixedHeader: true,
                columns: [
                    { field: 'date', title: 'Ամսաթիվ', sortable: true},
                    { field: selectBy('access', '', 'access'), title: selectBy('Մուտք', '', 'Մուտք'), sortable: true, hidden: selectBy(false, true, false)},
                    { field: selectBy('', 'exit', 'exit'), title: selectBy('', 'Ելք', 'Ելք'), sortable: true, hidden: selectBy(true, false, false) },
                    { field: 'balance', title: 'Մնացորդ', sortable: true},
                ],
            });
            function successDetailFunction (response) {
                response.map(function (item, index) {
                   return item.exit = Math.abs(item.exit)
                });
                detail.render(response);
            }
        });

        $('.box input').off('keypress').on('keypress', function(e){
            if(e.keyCode == 13) return false;
        });

         client.off('change').on('change', movementsData);
         select.off('change').on('change', movementsData);
        $('.box input').off('input').on('input', movementsData);

        $('#btn_movements_search_clear').off('click').on('click', movements);
    }

    function error(jqXHR, textStatus, errorThrown) {
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }

});