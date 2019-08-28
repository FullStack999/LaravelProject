<div class="card access_good_page50">
    <div class="card-header">
        <div class="row text-center">
            <div class="col-md-12">
                <h4>Ապրանքների ընդունման և վճարման բաժին</h4>
            </div>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="access_good_tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="accessGood-tab" data-toggle="tab" href="#tbl2" role="tab" aria-controls="tbl2" aria-selected="false">Ապրանքների Ընդունում</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="returnedGood-tab" data-toggle="tab" href="#tbl1" role="tab" aria-controls="tbl1" aria-selected="true">Ուղարկված է վերանայման</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="paidGood-tab" data-toggle="tab" href="#tbl3" role="tab" aria-controls="tbl1" aria-selected="true">կատարել վճարում</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tbl2" role="tabpanel" aria-labelledby="home-tab">
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center flex">
                            <div class="col-md-12 box">
                                <div class="form-group">
                                    <button type="button" class="btn form-control btn-primary" id="expense_btn">Հաստատել</button>
                                </div>
                            </div>
                        </div>
                        <table id="add_expenses_table"></table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="tbl1" role="tabpanel" aria-labelledby="home-tab">
                <div class="card">
                    <div class="card-body">
                        <table id="returned_good_table"></table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="tbl3" role="tabpanel" aria-labelledby="home-tab">
                <div class="card">
                    <div class="card-body">
                        <table id="paid_good_table"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="expense_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>


