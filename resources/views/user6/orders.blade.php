<div class="card">
    <div class="card-header text-center">
        <h4>Պատվերներ</h4>
    </div>
    <div class="card-body">
            <form action="{{url('user5/orders_data')}}" method="get">
                 <div class="row flex text-center">
                     <div class="col box">
                         <div class="form-group">
                             <label for="">Սկսած</label>
                             <input type="date" id="order_from" name="from" width="200" class="form-control" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                         </div>
                         <div class="form-group">
                             <label for="">Մինչև</label>
                             <input type="date" id="order_to" name="to" width="200" class="form-control"  value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                         </div>
                         <div class="form-group">
                             <label for="">Որոնել</label>
                             <input id="receiving_order_search" type="text" name="name" placeholder="Անուն" class="form-control"/>
                         </div>
                         <div class="form-group">
                             <button id="btn_search_receiving_order_clear" type="button" class="btn btn-primary">Մաքրել Ընտրածը</button>
                         </div>
                     </div>
                 </div>
                <hr>
                <div class="row text-center">
                    <div class="col-md-12">

                    </div>
                </div>
            </form>
           <table id="orders_table"></table>
    </div>
</div>

<div class="modal fade" id="remove_order_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Պատրաստվում եք հեռացնել պատվերը</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_remove_order" data-dismiss="modal">Հեռացնել</button>
                <button type="button" class="btn btn-secondary"  data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="remove_product_order_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Պատրաստվում եք պատվերից հեռացնել ապրանք</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_remove_product_order_modal" data-dismiss="modal">Հեռացնել</button>
                <button type="button" class="btn btn-secondary"  data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>