<div class="card" id="deleting_tabs">
    <div class="card-body">
        <ul class="nav nav-tabs" id="tab_cache" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="clients_tab" data-toggle="tab" href="#tab_clients" role="tab" aria-controls="tab_clients" aria-selected="true">Հեռացված հաճախորդներ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="suppliers_tab" data-toggle="tab" href="#tab_suppliers" role="tab" aria-controls="tab_suppliers" aria-selected="false">Հեռացված մատակարարներ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="places_tab" data-toggle="tab" href="#tab_places" role="tab" aria-controls="tab_places" aria-selected="false">Հեռացված ուղություններ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="goods_tab" data-toggle="tab" href="#tab_goods" role="tab" aria-controls="tab_goods" aria-selected="false">Հեռացված ապրանքներ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="products_tab" data-toggle="tab" href="#tab_products" role="tab" aria-controls="tab_products" aria-selected="false">Հեռացված ծաղիկներ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="expenses_tab" data-toggle="tab" href="#tab_expenses" role="tab" aria-controls="tab_expenses" aria-selected="false">Հեռացված ծախսի տեսակ</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab_clients" role="tabpanel" aria-labelledby="clients_tab">
                <div class="card">
                    <div class="card-body">
                        <table id="deleting_clients"></table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab_suppliers" role="tabpanel" aria-labelledby="suppliers_tab">
                <div class="card">
                    <div class="card-body">
                        <table id="deleting_suppliers"></table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab_places" role="tabpanel" aria-labelledby="places_tab">
                <div class="card">
                    <div class="card-body">
                        <table id="deleting_places"></table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab_goods" role="tabpanel" aria-labelledby="goods_tab">
                <div class="card">
                    <div class="card-body">
                        <table id="deleting_goods"></table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab_products" role="tabpanel" aria-labelledby="products_tab">
                <div class="card">
                    <div class="card-body">
                        <table id="deleting_products"></table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab_expenses" role="tabpanel" aria-labelledby="expenses_tab">
                <div class="card">
                    <div class="card-body">
                        <table id="deleting_expenses"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>