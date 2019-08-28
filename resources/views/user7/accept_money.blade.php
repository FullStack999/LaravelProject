<div class="card">
    <div class="card-header text-center">
        <h4>Ընդունել գումար</h4>
    </div>
    <div class="card-body">
        <div class="row flex text-center">
            <div class="col box">
                <div class="form-group">
                    <button class="btn btn-primary" id="btn_accept" data-toggle="modal" data-target="#accept_dialog">Հաստատել</button>
                </div>
            </div>
        </div>
        <table id="accept_table"></table>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="accept_dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="message"></h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Լավ</button>
            </div>
        </div>
    </div>
</div>