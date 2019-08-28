<div class="card expenses">
    <div class="card-header">
        <div class="row">
            <div class="col flex">
                <div class="box">
                    <div class="form-group">
                        <label>Սկսած</label>
                        <input type="date" name="from" id="exp_from" class="form-control" width="200" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                    </div>
                    <div class="form-group">
                        <label>Մինչև</label>
                        <input type="date" name="to" id="exp_to" class="form-control" width="200"  value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"/>
                    </div>
                    <div class="form-group">
                        <label for="">Որոնում</label>
                        <input type="text" name="name" class="form-control" id="textSearch" placeholder="Անուն">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn_clear_search">Մաքրել Ընտրածը</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Ապրանքների վճարումների պատմություն</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Կոմունալ վճարումների պատմություն</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="card">
                    <div class="card-body">
                        <table id="grid_history_expenses"></table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="card">
                    <div class="card-body">
                        <table id="grid_history_utilities"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>








