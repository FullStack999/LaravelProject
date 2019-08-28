<div class="card ware_history">
    <div class="card-header text-center">
        <div class="card-title">
            <h4>Ներքին շարժ</h4>
        </div>
    </div>
    <div class="card-body">
        <form action="{{url('user1/history_date')}}">
            <div class="row text-center">
                <div class="row">
                    <div class="col flex">
                        <div class="box">
                            <div class="form-group">
                                <label>Սկսած</label>
                                <input type="date" name="from" id="history_from" class="form-control" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                            </div>
                            <div class="form-group">
                                <label>Մինչև</label>
                                <input type="date" name="to" id="history_to" class="form-control col-sm"  value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                            </div>
                            <div class="form-group">
                                <label>Մուտք / Ելք</label>
                                <select id="goods_history_drop_down" name="select" class="form-control">
                                    <option value="">Բոլորը</option>
                                    <option value="access">Մուտքեր</option>
                                    <option value="exit">Ելքեր</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Որոնել Ապրանք</label>
                                <input id="history_search_name" name="name" type="text" placeholder="Անուն" class="form-control"/>
                            </div>
                        </div>
                        <div class="box">
                            <div class="form-group">
                                <button id="btn_exit_history_search_clear" type="button" class="form-control btn btn-primary">Մաքրել Ընտրածը</button>
                            </div>
                            <div class="form-group">
                                <button class="form-control btn btn-primary">Բեռնել</button>
                            </div>
                        </div>
                    </div>

                </div>
                <table id="exit_history_table"></table>

            </div>
        </form>
    </div>
</div>