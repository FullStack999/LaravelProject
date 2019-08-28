<div class="card" id="order_registerer">
    <div class="card-header">
        <div class="card-title text-center"><h4>Ընդունել պատվեր</h4></div>
    </div>
    <div class="card-body">
        <form id="upl_form" action="{{route('upload')}}">
        <div class="row flex text-center">
            <div class="col-md-12 box">
                <div class="form-group">
                    <select id="client" class="form-control" name="client">
                        <option value="">Ընտրել պատվիրատու</option>
                        @if(!empty($client))
                            @foreach($client as $item)
                                <option value="{{ $item->id }}" data-status="{{$item->status}}">{{ $item->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <button  id="btn_add_order" type="button"  data-toggle="modal" data-target="#modal_order_message">Հաստատել</button>
                </div>

                <div class="form-group">
                    <label>Ուղարկել պահեստում առկա ապրանքների ցուցակը</label>
                    <button id="btn_send_modal" type="button"   data-toggle="modal" data-target="#send_modal">Ուղարկել</button>
                </div>

                <div class="form-group">
                    <label>Բեռնել պահեստում առկա ապրանքների ցուցակը</label>
                    <button class="form-control btn btn-primary" id="export_excel">Բեռնել</button>
                </div>

                <div class="form-group">
                    <label>Ընդունել պատվեր Excel - ի միջոցով</label>
                    <button type="button"  id="import_excel" data-toggle="modal" data-target="#excel_order_dialog">Ընդունել</button>
                </div>
            </div>
        </div>
        </form>
        <table id="orders_register_table"></table>
    </div>
</div>


<div class="modal fade" id="send_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h4>Ուղարկել Ֆայլ</h4>
                </div>
            </div>
            <div class="modal-body">
                <form  id="send_form" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Էլեկտրոնային փոստ" name="email">
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control" name="file">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="btn_send_email"> Ուղարկել</button>
                    </div>
                </form>
                <h5 class="send_message"></h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Լավ</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade  bd-example-modal-lg" tabindex="-1" id="excel_order_dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="excel_order_title">Օրինակ՛</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ asset('images/example_orders.png') }}" alt="">
                    </div>
                    <div class="col-md-6 text-center">
                        <form id="excel_order_form" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>
                                    <span>Ընտրեք .xls Կամ .xlsx ֆայլ</span>
                                    <input type="file" name="file" id="add_order_in_excel" class="form-control"/>
                                </label>
                            </div>
                            <div class="form-group">
                                <button type="submit" id="btn_add_order_excel" class="btn btn-primary">Հաստատել</button>
                            </div>
                            <p class="upload_message"></p>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button"  class="btn btn-secondary" data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_order_message" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="access_order_message">Պատվիրատուն ընտրված չէ</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Լավ</button>
            </div>
        </div>
    </div>
</div>





