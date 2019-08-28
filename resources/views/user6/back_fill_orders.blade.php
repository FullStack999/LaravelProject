<div class="card">
    <div class="card-header text-center">
        <h4>Պատվերներ որոնք ենթակա են վերանայման</h4>
    </div>
    <div class="card-body">
        <div class="row flex text-center">
            <div class="col-md-12 box">
                <div class="form-group">
                    <select id="f_client" class="form-control">
                        <option value="">Ընտրել</option>
                        @if(!empty($order_number))
                            @foreach($order_number as $item)
                                <option value="{{ $item->id }}" data-client="{{ $item->client_id }}" data-status="{{ $item->status }}">{{ $item->client_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <button id="back_fill_success"> Հաստատել</button>
                </div>
                <div class="form-group">
                    <button id="back_fill_delete"> Հեռացնել Պատվերը</button>
                </div>
                <div class="form-group">
                    <button id="add_new_row">Նոր ապրանք</button>
                </div>

            </div>
        </div>
        <table id="back_fill_table"></table>
    </div>
</div>


<!-- Modals -->
<div class="modal fade" id="delete_order_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title" id="exampleModalLabel">Հեռացնել Պատվերը</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_delete_order" data-dismiss="modal">Այո</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Ոչ</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="back_fill_messages" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="message">Պատերը հաստատվեց</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Լավ</button>
            </div>
        </div>
    </div>
</div>