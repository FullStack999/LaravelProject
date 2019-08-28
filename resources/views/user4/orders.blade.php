<div id="orders_page" class="card">
    <div class="card-header">
        <div class="card-title text-center"><h4>Պատվերներ ըստ պատվիրատուների</h4></div>
    </div>
    <div class="card-body">
        <table id="orders_tbl">

        </table>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="ord_dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
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

<!-- Modal -->
<div class="modal fade" id="access_prod_to_client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Դուք հաստատում եք, որ ապրանքը հանձնել եք պատվիրատուին</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Ոչ</button>
                <button id="modal_access_button" type="button" class="btn btn-primary" data-dismiss="modal">Այո</button>
            </div>
        </div>
    </div>
</div>
