<div class="card">
    <div class="card-header text-center">
        <h4>Պատրաստի ապրանքի բաժին</h4>
    </div>
    <div class="card-body">
        <div class="row text-center flex">
            <div class="col-md-12 box">
                <div class="form-group">
                    <button id="add_new_product" type="button"  data-toggle="modal" data-target="#product_dialog">Ավելացնել ծաղիկ</button>
                </div>
                <div class="form-group">
                    <button id="add_new_excel" type="button"  data-toggle="modal" data-target="#excel_product_dialog">Ավելացնել ծաղիկ Excel-ի միջոցով</button>
                </div>
            </div>
        </div>
        <table id="product_table"></table>
    </div>
</div>


<div class="modal fade  bd-example-modal-lg" tabindex="-1" id="product_dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="product_title">Ավելացնել նոր ապրանք</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body product_modal_content text-center">
                <button id="btnAddRow" class="btn btn-default">Ավելացնել տող</button>
                    <table id="add_new_product_table">
                        <thead>
                        <tr>
                            <th>Անուն</th>
                            <th>Բոյ</th>
                            <th>Տեղ. գին</th>
                            <th>Արտ. գին</th>
                            <th></th>
                        </tr>
                        <tbody class="add_new_product_table">
                        <tr>
                            <td><input class="gj-textbox-md modal_input new_product_name"></td>
                            <td><input class="gj-textbox-md modal_input new_product_height"></td>
                            <td><input class="gj-textbox-md modal_input new_local_price"></td>
                            <td><input class="gj-textbox-md modal_input new_export_price"></td>
                            <td><button class="remove_row"><i class="fas fa-minus-circle"></i></button></td>
                        </tr>
                        </tbody>
                        </thead>
                    </table>
                <p class="error_message text-danger"></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_product" class="btn btn-primary">Հաստատել</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade  bd-example-modal-lg" tabindex="-1" id="excel_product_dialog" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="excel_product_title">Օրինակ՛</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ asset('images/example_products.png') }}" alt="">
                    </div>
                    <div class="col-md-6 text-center">
                        <form id="excel_product_form">
                            <div class="form-group">
                                <label>Ընտրեք Excel ֆայլ</label>
                                <input type="file" id="exc_file" name="file"/>
                            </div>
                            <div class="form-group">
                                <button type="button" id="btn_add_product_excel" class="btn btn-primary">Հաստատել</button>
                            </div>
                            <p class="message text-danger"></p>
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
<div class="modal fade" tabindex="-1" id="remove_product_confirm_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Պատրաստվում եք հեռացնել Ծաղիկը</h5>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_remove_product" data-dismiss="modal" class="btn btn-primary">Հեռացնել</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Փակել</button>
            </div>
        </div>
    </div>
</div>

