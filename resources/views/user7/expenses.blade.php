<div class="card">
    <div class="card-header text-center">
        <h4>Ծախսերի տեսակներ</h4>
    </div>
    <div class="card-body">
        <div class="row flex text-center">
            <div class="col-md-12 box">
                <div class="form-group">
                    <button id="add_new_expense" type="button" class="btn btn-primary" data-toggle="modal" data-target="#expense_dialog">Ավելացնել</button>
                </div>
            </div>
            <table id="expenses_table"></table>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" id="expense_dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="product_title">Ավելացնել նոր ծախս</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="gj-textbox-md " id="new_expense_name" placeholder=Անուն>
                </div>
                <div class="form-group">
                    <input type="text" class="gj-textbox-md " id="new_expense_unit" placeholder=Միավոր>
                </div>
                <p class="error_message text-danger"></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_expense" class="btn btn-primary">Հաստատել</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="remove_expense_confirm_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Պատրաստվում եք հեռացնել Ծախսը </h5>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_remove_expense" data-dismiss="modal" class="btn btn-primary">Հեռացնել</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>



