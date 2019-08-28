
<div class="card">
    <div class="card-header text-center">
        <h4>Աշխատողներ</h4>
    </div>
    <div class="card-body">
        <div class="row flex text-center">
            <div class="col box">
                <div class="form-group">
                    <button id="add_new_client" type="button" data-toggle="modal" data-target="#user_dialog">Ավելացնել Աշխատող</button>
                </div>
            </div>
        </div>
        <table id="users_table"></table>
    </div>
</div>


<div class="modal fade" tabindex="-1" id="user_dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Ավելացնել աշխատող</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reg_form">
                    <div class="form-group">
                        <input type="text" class="gj-textbox-md modal_input" name="name" placeholder=Անուն>
                    </div>
                    <div class="form-group">
                        <input type="text" class="gj-textbox-md modal_input" name="last_name"  placeholder=Ազգանուն>
                    </div>
                    <div class="form-group">
                        <input type="text" class="gj-textbox-md modal_input" name="login" placeholder=Մուտքանուն>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="gj-textbox-md modal_input" id="password" placeholder="Գախտնաբառ">
                    </div>
                    <div class="form-group">
                        <input type="password" name="confirm_password" class="gj-textbox-md modal_input" placeholder="Կրկնել գախտնաբառը">
                    </div>
                    <div class="form-group">
                        <select name="type_id" class="form-control">
                            <option value="">Ընտրեք կարգավիճակ</option>
                            @if($types)
                                @foreach($types as $type)
                                    <option value="{{$type->id}}">{{$type->text}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" id="btn_client" class="btn btn-primary">Հաստատել</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Փակել</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- change user pass modal --}}
<div class="modal fade" tabindex="-1" role="dialog" id="change_pass_modal">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="change_pass_modal">Փոխել գաղտնաբառը</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="user_pass_form" class="modal-body">
                <div class="form-group">
                    <input id="change_password" type="password" class="gj-textbox-md modal_input" name="password" tabindex="6" placeholder="Գախտնաբառ">
                </div>
                <div class="form-group">
                    <input id="change_password_confirm" type="password" class="gj-textbox-md modal_input" name="confirm_password" tabindex="7" placeholder="Կրկնել գախտնաբառ">
                </div>
                <div class="modal-footer">
                    <button type="button" id="change_user_pass" class="btn btn-primary">Հաստատել</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Փակել</button>
                </div>
                <p class="message text-danger"></p>
            </form>
        </div>
    </div>
</div>
{{-- change user pass modal end --}}

{{-- delete_user_modal --}}
<div class="modal fade" tabindex="-1" role="dialog" id="delete_user">
    <div class="modal-dialog" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <h5 class="user_delete_confirm">Հեռացնել Աշխատողին</h5>
                <div class="modal-footer">
                    <button id="user_delete_confirm" type="button" data-dismiss="modal" class="btn btn-primary">Այո</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Ոչ</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- delete user modal end --}}