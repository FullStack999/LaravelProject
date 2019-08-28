const log = console.log;
$(function(){
    const fn = {
        addOrders:()=>addOrders(),
        orders:()=>orders(),
        backFill:()=>backFill(),
    };
    if(sessionStorage.url){
        let f = getfn(sessionStorage.url);
        fn[f]();
    }else{
        addOrders();
    }

    function getfn(){
        return sessionStorage.url.split('/').pop().split('_').shift();
    }
    function error(jqXHR, textStatus, errorThrown) {
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }

    function removeActive() {
        if(sessionStorage.url) {
            $('#' + getfn(sessionStorage.url)).removeClass('active');
            return true;
        }
        return false;
    }
    events();
    function events(){
        $('#addOrders').off('click').on('click', addOrders);
        $('#orders').off('click').on('click', orders);
        $('#backFill').off('click').on('click', backFill);
    }

    function sendEmailFunction() {

        $("#send_form").validate({
            rules: {
                file: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                file: {
                    required: "Խնդրում ենք ընտրել ֆայլը",
                },
                email: {
                    required: "Խնդրում ենք նշել հաճախորդի էլփոստի հասցեն",
                    email: "Ձեր էլփոստի հասցեն պետք է լինի name@domain.com ձևաչափով"
                }
            },
            submitHandler: function() {

                let message = $('.send_message');
                let form = $('#send_form');
                let file = form.find('input[type=file]');
                let email = form.find('input[type=email]');
                let formData = new FormData();
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
                    beforeSend: function(){
                        message.text('Ուղարկվում է').removeClass('text-success text-danger').addClass('text-warning')
                    },
                    success: function (response) {
                        if (response.status == 'success'){
                            message.text('Հաջողությամբ ուղարկված է').removeClass('text-warning text-danger').addClass('text-success');
                            email.val('');
                            form.find('input[type=file]').val('')
                        }else{
                            alert('error')
                        }

                    },
                    error: error
                });
                $('#btn_send_modal').off('click').on('click', () => { message.text('')})
            }
        });
    }

    function addOrders(){
        let url = 'user6/addOrders_get';
        $.get(url, function(response){

            removeActive();
            $('#content').html(response);
            $('#addOrders').addClass('active');
            sessionStorage.setItem('url',url);
            function hasClient(){
                let client = $('#client').val();
                if (client == ''){
                    $('#modal_order_message').modal('show');
                    return false
                }
                return client
            }
            $('#export_excel').off('click').on('click', hasClient);
            $('#import_excel').off('click').on('click', hasClient);
            $('#btn_send_modal').off('click').on('click', hasClient);

            $('#client').off('change').on('change', function () {
                let option = $(this).find('option:selected');
                $('#orders_register_table').grid('destroy', true, true);
                if (option.val() != ''){
                    let grid = $('#orders_register_table').grid({
                        primaryKey: 'product_id',
                        dataSource: {url: 'user6/get_order_data', type: 'post', success:(response)=>{
                                let records = [];
                                for (let x in response){
                                    if (option.data('status') == 0){
                                        response[x]['price'] = response[x].local_price;
                                    }
                                    else if (option.data('status') == 1){
                                        response[x]['price'] = response[x].export_price;
                                    }
                                    records.push(response[x]);
                                }
                                grid.render(records);
                            }},
                        params: {client_id: $(this).val()},
                        responsive: true,
                        fixedHeader: true,
                        height: 545,
                        notFoundText: 'Արդյունք չի գտնվել',
                        columns: [
                            {field: 'name', title: 'Անուն'},
                            {field: 'height', title: 'Բոյ'},
                            {title: 'Գին', tmpl: '<input min="1" class="num_input price" data-price="{price}" value="{price}">'},
                            {title: 'Քանակ', tmpl: '<input min="1" class="num_input amt" data-id="{product_id}">'},
                            {field: 'balance', title: 'Մնացորդ', value: '{balance}', cssClass: 'balance'}
                        ]
                    });
                    $('#btn_add_order').off('click').click({grid: grid}, successOrder);
                    $('#btn_add_order_excel').off('click').click({grid: grid}, successOrderExcel);
                    $('#btn_send_email').off('click').on('click', sendEmailFunction);
                }
            });
        }).fail(error);
    }

    function successOrder(e){
        let e_error = false;
        let message = $('.access_order_message');
        let product = $('#orders_register_table .amt');
        let client = $('#client');
        let modal = $('#modal_order_message');
        let data = [];
        $.each(product, function () {
            let product_id = $(this).attr('data-id');
            let amt = $(this).val();
            let price =  $(this).closest('tr').find('input.price');
            if (amt != '' && amt > 0 && price.val() != '' && price.val() > 0){
                data.push({
                    id: product_id,
                    amt: amt,
                    price: price.val(),
                    old_price: price.attr('data-price'),
                    client_id: client.val(),
                });
            }
        });



        if (client.val() == ''){
            message.text('Պատվիրատուն ընտրված չէ');
            e_error = true;
        }

        else if (data.length == 0){
            modal.modal('show');
            message.text('Լրացնել քանակ');
            e_error = true;
        }
    if (!e_error){
        $.ajax({
            url: 'user6/add_orders',
            type: 'post',
            data: {data: data},
            success: function(response){
                if (response.status === 'success'){
                    product.val('');
                    message.text('Պատվերները Հաջողությամբ ընդունված են');
                    e.data.grid.reload();
                }
            },
            error: error
        })
        }
    }

    function successOrderExcel(e) {

        $('#excel_order_form').validate({

            rules: {
                file: {required: true,},
            },
            messages: {
                file: {required: "Ընտրել ֆայլը",},
            },
            submitHandler: function () {
                let form_data = new FormData();
                let client = $('#client').val();
                let file = $('#add_order_in_excel');
                let message = $('.upload_message');
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
                    success: function (response) {
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
                    error:function () {
                        message.text('Սխալ ֆայլ');
                    }
                });
            }
        });

    }

    function orders(){
        $('#orders').off('click');
        let url = 'user6/orders_get';
        $.get(url, function(response){
            removeActive();
            $('#orders').addClass('active');
            $('#content').html(response);
            sessionStorage.setItem('url',url);
        })
            .then((response)=> getOrdersInfo(response))
            .then(()=> $('#orders').off('click').on('click', orders))
            .fail(error);
    }

    function getOrdersInfo() {
        let from = $('#order_from').val();
        let to = $('#order_to').val();
        let name = $('#receiving_order_search').val();


        $('#orders_table').grid('destroy', true, true);

        let grid = $('#orders_table').grid({
            primaryKey: 'id',
            dataSource: {url: 'user6/orders_data', type: 'post', success: gridDataFunction},
            params: { from: from, to: to, name: name},
            detailTemplate: '<div><table  style="background: #fcf8e3"></div>',
            responsive: true,
            fontSize: 15,
            notFoundText: 'Արդյունք չի գտնվել',
            fixedHeader: true,
            height: 575,
            columns: [
                {field: 'client_name', title: 'Անուն'},
                {field: 'date', title: 'Ամսաթիվ'},
                {field: 'order_price', title: 'Ընդհանուր գումար'},
                {tmpl: '<span class="{class}">{status}</span>', title: 'Կարգավիճակ'},
                {tmpl: '<button><i class="fas fa-trash-alt"></i></button>', cssClass: 'fa_button', align: 'right', events: {'click': removeOrder}},
            ],

        });
        function removeOrder(e) {
            $('#remove_order_modal').modal('show');

            $('#btn_remove_order').off('click').on('click', function () {
                $.ajax({
                    url: 'user6/delete_order',
                    type: 'delete',
                    data: {id: e.data.id},
                    success: function (response) {
                        if (response.status == 'success'){
                            grid.removeRow(e.data.id);
                        }
                    }

                })
            });
        }
        function gridDataFunction (response) {
            let records = [];
            for (let x in response){
                let sum = 0;
                for (let j in response[x]){
                    sum += response[x][j].order_price;
                    response[x][0].order_price = numberWithCommas(sum);

                    if (response[x][0].confirmed == 1 && response[x][0].not_enough == null && response[x][0].exit_ware_status == 1){
                        response[x][0].status = 'Պատվերը հանձնված է հաճախորդին';
                        response[x][0].class = 'green';

                    }
                    else if(response[x][0].confirmed == 0 || response[x][0].not_enough != null){
                        response[x][0].status = 'Պատվերը դեռ հաստատված չէ';
                        response[x][0].class = 'red';

                    }
                    else{
                        response[x][0].status = 'Պատվերը ենթակա է հանձման';
                        response[x][0].class = 'orange';
                    }
                }
                records.unshift(response[x][0])
            }
            grid.render(records);
            $('.green').closest('tr').find('.fa_button').empty();
        }
        grid.on('detailExpand', function (e, $detailWrapper, id) {

            let detail = $detailWrapper.find('table').grid({
                dataSource: { url: 'user6/orders_data', type: 'post', success: detailFunction },
                primaryKey: 'order_id',
                params: { id: id, from: from, to: to, name: name},
                responsive: true,
                fixedHeader: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                inlineEditing: { mode: 'command', managementColumn: false},
                columns: [
                    { field: 'product_name', title: 'Ապրանք'},
                    { field: 'price', title: 'Նախնական գին'},
                    { field: 'discount_price', editField: 'discount_price', title: 'Զեղչված գին', editor: true},
                    { field: 'product_amt', editField: 'product_amt', title: 'Քանակ', editor: true},
                    { width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button' },
                ],
            });



            detail.on('rowDataChanged', function (e, id, record) {
                $.ajax({
                    url: 'user6/update_detail_order',
                    data: record,
                    method: 'post',
                    error: error
                })
            });
            grid.on('detailCollapse', function (e, $detailWrapper, id) {
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
                    $('#remove_product_order_modal').modal('show');
                    $('#btn_remove_product_order_modal').off('click').on('click', function () {
                        $.ajax({
                            url: 'user6/delete_detail_order',
                            type: 'delete',
                            data: {id: key},
                            success: function (response) {
                                if (response.status == 'success'){
                                    detail.removeRow(key);
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
            function detailFunction (response) {

                for (let x in response){
                    response[x].product_name = response[x].product_name +' '+ response[x].product_height;
                    response[x].discount_price = numberWithCommas(+response[x].discount_price);
                    if (response[x].client_status == 0){
                        response[x].price = numberWithCommas(+response[x].local_price);
                    }
                    else if(response[x].client_status == 1){
                        response[x].price = numberWithCommas(+response[x].export_price );
                    }
                }
                detail.render(response);
                $('.green').closest('tr').next('tr[data-role="details"]').find('.fa_button').empty();
            }
        });

        $('.box input').off('keypress').on('keypress', function(e){
            if(e.keyCode == 13){
                return false;
            }
        });
        $('.box input').off('input').on('input', getOrdersInfo);
        $('#btn_search_receiving_order_clear').off('click').on('click',orders);
    }

    function backFill(){
        let url = 'user6/backFill_get';
        $.get(url, function (response) {
            removeActive();
            $('#content').html(response);
            $('#backFill').addClass('active');
            sessionStorage.setItem('url',url);
            backFillData();
        })
    }
    
    function backFillData(){

        $('#f_client').on('change', function () {
            let id = $(this).val();
            let client_id = $(this).find(':selected').data('client');
            $('#back_fill_table').grid('destroy', true, true);
            let grid = $('#back_fill_table').grid({
                dataSource: {url: 'user6/backFill_data', type: 'post', success: onSuccess},
                params: {id: id},
                responsive: true,
                fixedHeader: true,
                height: 629,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                columns: [
                    {field: 'name', title: 'Անուն'},
                    {title: 'Գին', tmpl: '<input class="num_input price" data-price="{price}" value="{price}">'},
                    {title: 'Քանակ', tmpl: '<input class="num_input amt" data-id="{product_id}" value="{product_amt}">'},
                ]
            });

            function onSuccess(response){

                let records = [];
                let prod = '<option value="">Ընտրել ծաղիկ</option>';
                $.each(response.data, function (k, item) {
                    item.price = item.order_price / item.product_amt;
                    item.name = item.product_name +' '+ item.product_height;
                    records.push(item);
                });
                $.each(response.products, function (k, item) {
                    prod += `<option value="${item.id}">${item.name} ${item.height}</option>`
                });

                grid.render(records);

                $('#add_new_row').off('click').on('click', function () {
                    grid.addRow({
                        name: `<select class="select_prod form-control">${prod}</select>`,
                    });
                    $('.select_prod').off('change').on('change', function(){
                        let this_select = $(this);
                        if (this_select.val() != ''){
                            $.ajax({
                                url: 'user6/backFill_new_product',
                                type: 'post',
                                data: {
                                    prod_id: $(this).val(),
                                    client_id: client_id
                                },
                                success: function (response) {
                                    this_select.closest('tr').find('.price').val(response.price).attr('data-price', response.price);
                                    this_select.closest('tr').find('.amt').attr('data-id', response.prod_id)
                                }
                            })
                        }
                    });
                });
            }

            $('#back_fill_success').off('click').click({id: id, client_id: client_id}, updateOrder);
            $('#back_fill_delete').off('click').click({id: id, client_id: client_id}, deleteOrder);

        });

    }
    
    function updateOrder(e) {

        let modal = $('#back_fill_messages');
        let product = $('#back_fill_table .amt');
        let data = [];
        $.each(product, function () {
            let price = $(this).closest('tr').find('input.price');
            let prod_name =  $(this).closest('tr').find('select');
            let amt = $(this);
            if (amt.val() != '' && prod_name.val() != '' && $.isNumeric(amt.val()) && price.val() != '' && $.isNumeric(price.val())){
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
            data: {records: data},
            success: function (response) {
                if (response.status == 'success'){
                    modal.modal('show');
                    modal.on('hidden.bs.modal', backFill);
                }
            }
        })

    }

    function deleteOrder(e) {
        let modal = $('#delete_order_modal');
        modal.modal('show');
        $('.confirm_delete_order').off('click').on('click', function () {
            $.ajax({
                url: 'user6/delete_order',
                type: 'delete',
                data: {id: e.data.id},
                success: function (response) {
                    if (response.status == 'success'){
                        backFill();
                    }
                }
            })
        })

    }

    function numberWithCommas(x){
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

});
