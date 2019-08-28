const log = console.log;
$(document).ready(function() {
    let change_pass_button = null;

    const fn = {
        register:()=>register(),
        users:()=>users()
    };
    if(sessionStorage.url){
        let f = getfn(sessionStorage.url);
        fn[f]();
    }else{
        users();
    }
    function getfn(){
        return sessionStorage.url.split('/').pop().split('_').shift();
    }
    function removeActive() {
        if(sessionStorage.url) {
            $('#' + getfn(sessionStorage.url)).removeClass('active');
            return true;
        }
        return false;
    }

    events()
    function users(){
        let url = 'admin/users_get';
        $.get(url,function (response) {
            removeActive();
            $('#content').html(response.view);
            $("#users").addClass('active');
            sessionStorage.setItem('url',url);
            $.each(response.types, function (k, v) {
                v.id = v.id.toString()
            });
            let grid = $('#users_table').grid({
                dataSource: {url: 'admin/users_data', type: 'post'},
                primaryKey: 'id',
                responsive: true,
                fontSize: 15,
                notFoundText: 'Արդյունք չի գտնվել',
                inlineEditing: {mode: 'command', managementColumn: false,},
                columns: [
                    { field: 'name', title: 'Անուն <i class="fas fa-sort"></i>', editor: true, sortable: true },
                    { field: 'last_name', title: 'Ազգանուն <i class="fas fa-sort"></i>', editor: true, sortable: true },
                    { field: 'login', title: 'Մուտքանուն <i class="fas fa-sort"></i>', editor: true, sortable: true },
                    { field: 'type_name', title: 'Կարգավիճակ', type: 'dropdown',
                        editField: 'type_id',
                        editor: {dataSource: response.types, valueField: 'id'}},
                    { field: 'created_at', title: 'Ստեղծվել է <i class="fas fa-sort"></i>',  sortable: true},

                    { width: 200, align: 'center', renderer: editManager, cssClass: 'fa_button' },
                ],
            });
            grid.on('rowDataChanged', function (e, id, record) {
                $.ajax({
                    url: 'admin/update_user',
                    data: record,
                    method: 'post',
                    error: error
                })
            });
            function editManager (value, record, $cell, $displayEl, id, $grid) {
                let $edit = $('<button><i class="far fa-edit"></i></button>').attr('data-key', id),
                    $delete = $('<button data-toggle="modal" data-target="#delete_user"><i class="far fa-trash-alt"></i></button>').attr('data-key', id),
                    $update = $('<button><i class="far fa-save"></i></button>').attr('data-key', id).hide().css('background', '#610d0d'),
                    $cancel = $('<button><i class="fas fa-ban"></i></button>').attr('data-key', id).hide().css('background', '#610d0d'),
                    $change_pass = $('<button data-toggle="modal" data-target="#change_pass_modal"><i class="fas fa-key"></i></button>').attr('data-key', id);
                $edit.on('click', function () {
                    $grid.edit($(this).data('key'));
                    $edit.hide();
                    $delete.hide();
                    $update.show();
                    $cancel.show();
                });
                $update.on('click', function () {
                    $grid.update($(this).data('key'));
                    $edit.show();
                    $delete.show();
                    $update.hide();
                    $cancel.hide();
                });

                $delete.on('click', function () {
                    let key = $(this).data('key');
                    $('#user_delete_confirm').on('click', function () {
                        $.ajax({
                            url: 'admin/delete_user',
                            data: {id: key},
                            method: 'delete',
                            success: () => {
                                $grid.removeRow(key);
                            },
                            error: error
                        });
                    });
                });
                $cancel.on('click', function () {
                    $grid.cancel($(this).data('key'));
                    $edit.show();
                    $delete.show();
                    $update.hide();
                    $cancel.hide();
                });
                $change_pass.off('click').on('click', function () {
                    let key = $(this).data('key');
                    $('#change_user_pass').off('click').on('click', function () {
                        let form = $('#user_pass_form');
                        let password = form .find('input[name=password]').val();
                        let confirm_password = form.find('input[name=confirm_password]').val();
                        let message = $('.message');
                        message.removeClass('text-success').addClass('text-danger');
                        if (password.length < 3){
                            return message.text('Գախտնաբառը պետք է լինի առնվազն 6 նիշ');
                        }
                        else if (password.length > 16){
                            return message.text('Գախտնաբառը պետք է լինի ոչ ավելի, քան 16 նիշ');
                        }
                        else if (password != confirm_password){
                            return message.text('Գաղտնաբառերը չեն համապատասխանում');
                        }
                        let data = {
                            id: key,
                            password: password,
                            confirm_password: confirm_password
                        };

                        $.ajax({
                            url: 'admin/profile_changePass',
                            data: data,
                            method: 'post',
                            success: function (response){
                                form.find('input').val('');
                                if (response.status == 'success'){
                                    message.text('Գախտնաբառը հաջողությամբ փոխվել է')
                                        .removeClass('text-danger')
                                        .addClass('text-success')
                                }else{
                                    message.text('Գախտնաբառը արդեն գոյություն ունի')
                                }
                            },
                            error: error
                        });
                    });
                });
                $displayEl.empty().append([$edit, $delete, $update, $cancel, $change_pass]);
            }
            registerUser(grid);
        }).fail(error);
    }

    function registerUser(grid) {
        $('#reg_form').validate({
            rules: {
                name: {
                    required:true,
                    minlength: 3,
                    maxlength:30,
                },
                last_name: {
                    required:true,
                    maxlength:30,
                    minlength: 3,
                },
                login: {
                    required: true,
                    maxlength:30,
                    minlength: 3
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 16
                },
                confirm_password: {
                    equalTo:"#password"
                },
                type_id: {
                    required: true
                }
            },
            messages: {
                name: {
                    required:"Խնդրում ենք մուտքագրել անուն",
                    minlength: "Անունը պետք է լինի առնվազն 3 նիշ",
                    maxlength: "Անունը պետք է լինի ոչ ավելի, քան 30 նիշ",
                },
                last_name: {
                    required:"Խնդրում ենք մուտքագրել ազգանուն",
                    minlength: "Ազգանունը պետք է լինի առնվազն 3 նիշ",
                    maxlength: "Ազգանունը պետք է լինի ոչ ավելի, քան 30 նիշ",
                },
                login: {
                    required:"Խնդրում ենք մուտքագրել մուտքանուն",
                    minlength: "Մուտքանուն պետք է լինի առնվազն 3 նիշ",
                    maxlength: "Մուտքանուն պետք է լինի ոչ ավելի, քան 30 նիշ"
                },
                password: {
                    required: "Խնդրում ենք մուտքագրել գախտնաբառ",
                    minlength: "Գախտնաբառը պետք է լինի առնվազն 6 նիշ",
                    maxlength: "Գախտնաբառը պետք է լինի ոչ ավելի, քան 16 նիշ"
                },
                confirm_password: {
                    equalTo: "Գաղտնաբառերը չեն համապատասխանում"
                },
                type_id: {
                    required: "Խնդրում ենք ընտրել կարգավիճակը"
                }
            },
            submitHandler:function (form) {
                let data = $(form).serialize();
                $.ajax({
                    url: 'admin/register',
                    type: 'post',
                    data: data,
                    success: function (response) {
                        if (response.status == 'success'){
                            grid.reload();
                            $('#user_dialog').modal('hide');
                            $(form).find('input').val('');
                            $(form).find('select').val('')
                        }
                    },
                    error: error
                });
            }
        });
    }

    function events() {
        $('#change_user_pass').off('click').on('click',function changePass() {
            $('#user_pass_form').validate({
                rules:{
                    password: {
                        required: true,
                        minlength: 6,
                        maxlength: 16
                    },
                    confirm_password: {
                        equalTo:"#change_password"
                    }
                },
                messages: {
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 6 characters long",
                        maxlength: "Your Password must be no more then 16 characters long"
                    },
                    confirm_password: {
                        equalTo:"The Passwords Does Not Match"
                    },
                },
                submitHandler:function () {
                    let user_id = change_pass_button.closest('tr').find('[name = id]').attr('data-id');
                    let pass = $('#user_pass_form').find('[name = "password"]').val();
                    let data = {
                        id:user_id,
                        password:pass,
                    };
                    $.ajax({
                        url: '/admin/profile_changePass',
                        type: 'POST',
                        data:data,
                        success: function (response) {
                            $('.change_pass_modal')
                                .text(response.message)
                                .removeClass(response.remove_message_color)
                                .addClass(response.message_color);
                        },
                        error: error
                    });
                    return false;
                }
            });
        });
        $('#users').off('click').on('click', users);
    }

    function error(jqXHR, textStatus, errorThrown) {
        console.error("error occurred: " + textStatus, errorThrown, jqXHR);
    }

});
