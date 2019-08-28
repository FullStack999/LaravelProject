<div class="card">
    <div class="card-header text-center">
        <h4>Հաճախորդներ</h4>
    </div>
    <div class="card-body">
        <div class="row flex text-center">
            <div class="col-md-12 box">
                <div class="form-group">
                    <button id="add_new_client" type="button" data-toggle="modal" data-target="#client_dialog">Ավելացնել Հաճախորդ</button>
                </div>
            </div>
        </div>
            <table id="client_table"></table>
    </div>
</div>


<div class="modal fade" tabindex="-1" id="client_dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="client_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="client_modal_content">
                    <input type="text" class="gj-textbox-md modal_input" id="new_client_name" placeholder=Անուն>
                    <input type="text" class="gj-textbox-md modal_input" id="new_client_height" placeholder=Հասցե>
                    <input type="text" class="gj-textbox-md modal_input" id="new_client_price" placeholder=Հեռախոս>
                    <select id="client_status">
                        <option value="">Ընտրել կարգավիճակը</option>
                        <option value="0">Տեղական</option>
                        <option value="1">Արտահանում</option>
                    </select>
                </p>
                <p class="error_message text-danger"></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_client" class="btn btn-primary">Հաստատել</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="remove_client_confirm_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Պատրաստվում եք հեռացնել գործընկերոջը</h5>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_remove_client" data-dismiss="modal" class="btn btn-primary">Հեռացնել</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>