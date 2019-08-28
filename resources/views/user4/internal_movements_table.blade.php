<div class="card">
    <div class="card-header">
        <div class="card-title">
            <div class="card-title text-center"><h4>Ներքին շարժ</h4></div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{url('user4/movements_data')}}" method="get">
            <div class="row text-center">

                <div class="col flex">
                    <div class="box">
                        <div class="form-group">
                            <label>Սկսած</label>
                            <input type="date" name="from" id="movements_from" class="form-control" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>        </div>
                        <div class="form-group">
                            <label>Մինչև</label>
                            <input type="date" name="to" id="movements_to" class="form-control"  value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                        </div>
                        <div class="form-group">
                            <label>Մուտք/Ելք</label>
                            <select id="movements_drop_down" class="form-control" name="select">
                                <option value="">Բոլորը</option>
                                <option value="access">Մուտքեր</option>
                                <option value="exit">Ելքեր</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Ընտրեք Հաճախորդին</label>
                            <select id="select_clients" class="form-control">
                                <option value="">Բոլորը</option>
                                @if(!empty($clients))
                                    @foreach($clients as $client)
                                        <option value="{{$client->client_id}}">{{$client->client_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="box">
                        <div class="form-group">
                            <button id="btn_movements_search_clear" type="button">Մաքրել Ընտրածը</button>
                        </div>
                        <div class="form-group">
                            <button id="export_movements">Բեռնել Արդյունքը</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <table id="movements_table"></table>
    </div>
</div>











