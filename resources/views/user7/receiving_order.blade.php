<div class="card">
    <div class="card-header text-center">
        <h4>Պատվերների պատմություն</h4>
    </div>
    <div class="card-body">
            <form action="{{url('user7/orders_data')}}" method="get">
                <div class="row">
                    <div class="col flex">
                        <div class="box">
                            <div class="form-group">
                                <label>Սկսած</label>
                                <input type="date" id="order_from" name="from" width="200" class="form-control" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                            </div>
                            <div class="form-group">
                                <label>Մինչև</label>
                                <input type="date" id="order_to" name="to" width="200" class="form-control"  value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                            </div>
                            <div class="form-group">
                                <label>Խնբավորել</label>
                                <select id="group_by" class="form-control" name="group_by">
                                    <option value="client_id">Ըստ Հաճախորդների</option>
                                    <option value="product_id">Ըստ Ապրանքների</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="name" class="form-control" id="receiving_order_search" placeholder="Անուն">
                            </div>
                        </div>
                        <div class="box">
                            <div class="form-group">
                                <button class="export_data btn btn-primary">Բեռնել արդյունքը</button>
                            </div>
                            <div class="form-group">
                                <button id="btn_search_receiving_order_clear" type="button" class="btn btn-primary">Մաքրել Ընտրածը</button>
                                <input type="hidden" name="export">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <div class="row">
            <table id="orders_table"></table>
        </div>
    </div>
</div>


