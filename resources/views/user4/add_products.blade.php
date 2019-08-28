<div id="accordion">
   <div class="col flex">
       <div class="box">
           <div class="form-group">
               <button id="get_product" type="button">Ավելացնել</button>
           </div>
       </div>
   </div>
   <br>
   @if(!empty($products))
       @foreach($products as $product)
           <div class="card">
               <div class="card-header prod_item_header" id="headingOne{{$product[0]->id}}">
                   <h5 class="mb-0">
                       <button class="btn" data-toggle="collapse" data-target="#collapseOne{{$product[0]->id}}" aria-expanded="true" aria-controls="collapseOne{{$product[0]->id}}">
                           {{$product[0]->name}}
                       </button>
                   </h5>
               </div>

               <div id="collapseOne{{$product[0]->id}}" class="collapse" aria-labelledby="headingOne{{$product[0]->id}}" data-parent="#accordion">
                   <div class="card-body">
                       <table class="prod_height_detail" id="prod_height_detail{{$product[0]->id}}">
                           <thead>
                               <tr>
                                   <th>Բոյ</th>
                                   <th>քանակ</th>
                               </tr>
                           </thead>
                           <tbody>
                           @foreach($product as $item)
                               <tr>
                                   <td>{{$item->height}}</td>
                                   <td><input title="" class="num_input" data-id="{{$item->id}}"></td>
                               </tr>
                           @endforeach
                           </tbody>
                       </table>

                   </div>
               </div>
           </div>
       @endforeach
   @endif
</div>


<div class="modal fade" id="dialog_access_product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="access_product_message"> Ավելացված է</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Լավ</button>
            </div>
        </div>
    </div>
</div>