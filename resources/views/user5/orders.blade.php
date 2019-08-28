<div class="card">
    <div class="card-header">
        <div class="card-title">
            <h5>Պատվերների պատմություն</h5>
        </div>
    </div>
    <div class="card-body">
        <div class="row flex text-center">
            <div class="col-md-12 box">
                <div class="form-group">
                    <label for="">Սկսած</label>
                    <input type="date" id="order_from"  class="form-control"   value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                </div>
                <div class="form-group">
                    <label for="">Մինչև</label>
                    <input type="date" id="order_to"  class="form-control"  value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                </div>
                <div class="form-group">
                    <label>Որոնել</label>
                    <input id="receiving_order_search" type="text" placeholder="Անուն" class="form-control"/>
                </div>
                <div class="form-group">
                    <button id="btn_search_receiving_order_clear" type="button">Մաքրել Ընտրածը</button>
                </div>
            </div>
        </div>
        <table id="detail_orders_table"></table>
    </div>
</div>


