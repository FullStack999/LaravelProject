<?php


    Route::get('/',['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm'])->middleware(['web','auth']);
    Auth::routes();
    Route::get('/force-logout',function(){
        Auth::logout();
    });
    Route::fallback(function(){
        return view('errors.404');
    })->name('fallback');


    Route::get('setLocale/{lang}',  function ($locale) {
        if (in_array($locale, \Config::get('app.locales'))) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    })->name('setLocale');



//Admin Routes
    Route::group(['middleware' => ['web','auth'],'prefix' => 'admin'],function(){
        Route::get('/',['as'=>'admin','uses'=>'Admin\AdminController@index']);
        Route::get('/users_get',['uses'=>'Admin\AdminController@profileGet']);
        Route::post('/users_data',[ 'uses'=>'Admin\AdminController@usersData']);
        Route::post('/update_user',['uses'=>'Admin\AdminController@userUpdate']);
        Route::delete('/delete_user',['uses'=>'Admin\AdminController@userDelete']);
        Route::get('/types_data',['uses'=>'Admin\AdminController@typesData']);
        Route::get('/register_get',['uses'=>'Admin\AdminController@showRegistrationForm']);
        Route::post('/profile_changePass',['uses' => 'Admin\AdminController@profileChangePassPost']);
        Route::post('/register',['uses'=>'Admin\AdminController@register']);
    });


//User1 Routes
    Route::group(['middleware' => ['web','auth'],'prefix' => 'user1'],function(){
        Route::get('/',['as' => 'user1','uses'=>'Users\User1Controller@index']);
        Route::post('/posts',[ 'uses' => 'Users\User1Controller@posts']);
        Route::get('/goods_get',['uses'=>'Users\User1Controller@getGoods']);
        Route::post('/goods_data',[ 'uses'=>'Users\User1Controller@goodsData']);
        Route::get('/history_get',['uses'=>'Users\User1Controller@getGoodHistory']);
        Route::get('/accessGood_get',['uses'=>'Users\User1Controller@accessGoodGet']);
        Route::post('/accessGood_data',['uses'=>'Users\User1Controller@accessGoodData']);
        Route::post('/receptionGood_data',['uses'=>'Users\User1Controller@receptionGoodData']);
        Route::post('/confirm_access_good',['uses'=>'Users\User1Controller@confirmAccessGood']);
        Route::post('/return_to_review',['uses'=>'Users\User1Controller@returnGoodToReview']);
        Route::get('/exitGood_get',['uses'=>'Users\User1Controller@exitGoodGet']);
        Route::post('/exitGood_post',['uses'=>'Users\User1Controller@exitGoodPost']);
        Route::get('/goodHistory_get',['uses'=>'Users\User1Controller@showGoodsHistoryPage']);
        Route::match(['get', 'post'],'/history_date',['uses'=>'Users\User1Controller@goodsHistoryData']);
    });


//User2 Routes
    Route::group(['middleware' => ['web','auth'],'prefix' => 'user2'],function(){
        Route::get('/',['as' => 'user2','uses'=>'Users\User2Controller@index']);
        Route::post('/posts',[ 'uses' => 'Users\User2Controller@posts']);
        Route::get('/fertilizers_get',['uses'=>'Users\User2Controller@getFertilizers']);
        Route::post('/fertilizers_data',[ 'uses'=>'Users\User2Controller@fertilizersData']);
        Route::get('/accessFertilizer_get',['uses'=>'Users\User2Controller@accessFertilizersGet']);
        Route::get('/accessFertilizer_data/{id?}',['uses'=>'Users\User2Controller@accessFertilizersData']);
        Route::post('/confirm_access_fertilizer',['uses'=>'Users\User2Controller@confirmFertilizer']);
        Route::post('/return_to_review',['uses'=>'Users\User2Controller@returnFertilizerToReview']);
        Route::get('/exitFertilizer_get',['uses'=>'Users\User2Controller@exitFertilizersGet']);
        Route::post('/exitFertilizer_data',['uses'=>'Users\User2Controller@exitFertilizersData']);
        Route::get('/fertilizerHistory_get',['uses'=>'Users\User2Controller@showFertilizersHistoryPage']);
        Route::match(['get', 'post'],'/fertilizers_history_data',['uses'=>'Users\User2Controller@fertilizersHistoryData']);
    });


//User3 Routes
    Route::group(['middleware' => ['web','auth'],'prefix' => 'user3'],function(){
        Route::get('/',['as' => 'user3','uses'=>'Users\User3Controller@index']);
        Route::post('/posts',[ 'uses' => 'Users\User3Controller@posts']);
        Route::get('/pesticides_get',['uses'=>'Users\User3Controller@getPesticides']);
        Route::post('/pesticides_data',[ 'uses'=>'Users\User3Controller@pesticidesData']);
        Route::get('/accessPesticide_get',['uses'=>'Users\User3Controller@accessPesticidesGet']);
        Route::get('/accessPesticide_data/{id?}',['uses'=>'Users\User3Controller@accessPesticidesData']);
        Route::post('/confirm_access_pesticide',['uses'=>'Users\User3Controller@confirmPesticide']);
        Route::post('/return_to_review',['uses'=>'Users\User3Controller@returnPesticideToReview']);
        Route::get('/exitPesticide_get',['uses'=>'Users\User3Controller@exitPesticidesGet']);
        Route::post('/exitPesticide_data',['uses'=>'Users\User3Controller@exitPesticidesData']);
        Route::get('/pesticideHistory_get',['uses'=>'Users\User3Controller@showPesticidesHistoryPage']);
        Route::match(['get', 'post'],'/pesticides_history_data',['uses'=>'Users\User3Controller@pesticidesHistoryData']);
    });


//User4 Routes
    Route::group(['middleware' => ['web','auth'],'prefix' => 'user4'],function(){
        Route::get('/',['as'=>'user4','uses'=>'Users\User4Controller@index']);
        Route::get('/orders_get',['uses' => 'Users\User4Controller@ordersGet']);
        Route::get('/orders_data',['uses' => 'Users\User4Controller@ordersData']);
        Route::post('/order_detail',['uses' => 'Users\User4Controller@orderDetail']);
        Route::post('/orders_post',['uses' => 'Users\User4Controller@ordersPost']);
        Route::get('/movements_get',['uses' => 'Users\User4Controller@getMovementsHistory']);
        Route::match(['get', 'post'],'/movements_data',['uses' => 'Users\User4Controller@movementsHistoryData']);
        Route::get('/addProductsPage_get',['uses' => 'Users\User4Controller@addProductsPage']);
        Route::post('/product_data',['uses' => 'Users\User4Controller@addProductData']);
        Route::get('/products_get',['uses' => 'Users\User4Controller@getProducts']);
        Route::post('/add_end_products',['uses' => 'Users\User4Controller@addEndProducts']);
        Route::post('/back_fill',['uses' => 'Users\User4Controller@backFill']);
    });


//User5 Routes
    Route::group(['middleware' => ['web','auth'],'prefix' => 'user5'],function(){
        Route::get('/',['as'=>'user5','uses'=>'Users\User5Controller@index']);
        Route::get('/orders_get',['uses' => 'Users\User5Controller@showReceivingOrdersPage']);
        Route::get('/notifications',['uses' => 'Users\User5Controller@notifications']);
        Route::post('/orders_data',['uses' => 'Users\User5Controller@dataReceivingOrder']);
        Route::post('/confirm_order',['uses' => 'Users\User5Controller@confirmOrder']);
        Route::get('/deleting_get',['uses' => 'Users\User5Controller@deleting']);
        Route::post('/deleting_data',['uses' => 'Users\User5Controller@deletingData']);
        Route::post('/removing',['uses' => 'Users\User5Controller@removing']);
    });



//User6 Routes
    Route::group(['middleware' => ['web','auth'],'prefix' => 'user6'],function(){
        Route::get('/',['as'=>'user6','uses'=>'Users\User6Controller@index']);
        Route::get('/addOrders_get', ['uses' => 'Users\User6Controller@addOrdersPage']);
        Route::post('/get_order_data',['uses' => 'Users\User6Controller@getOrderData']);
        Route::post('/add_orders', ['uses' => 'Users\User6Controller@addOrders']);
        Route::post('/add_order_excel', ['uses' => 'Users\User6Controller@addOrderExcel']);
        Route::post('/send_excel_to_email', ['uses' => 'Users\User6Controller@sendExcelFile']);
        Route::get('/upload_excel_file', ['as' => 'upload','uses' => 'Users\User6Controller@uploadExcelFile']);
        Route::get('/orders_get',['uses' => 'Users\User6Controller@OrdersPage']);
        Route::match(['get', 'post'],'/orders_data',['uses' => 'Users\User6Controller@ordersdata']);
        Route::get('/backFill_get',['uses' => 'Users\User6Controller@backFillOrders']);
        Route::post('/backFill_data',['uses' => 'Users\User6Controller@backFillOrdersData']);
        Route::post('/backFill_new_product',['uses' => 'Users\User6Controller@backFillNewProduct']);
        Route::post('/update_detail_order',['uses' => 'Users\User6Controller@updateDetailOrder']);
        Route::delete('/delete_detail_order',['uses' => 'Users\User6Controller@deleteDetailOrder']);
        Route::post('/update_order',['uses' => 'Users\User6Controller@updateBackFill']);
        Route::delete('/delete_order',['uses' => 'Users\User6Controller@deleteOrder']);
    });

//User7 Routes

    Route::group(['middleware' => ['web','auth'],'prefix' => 'user7'],function(){

        /**
         * Routes for user 7 home page
         */
        Route::get('/',['as'=>'user7','uses'=>'Users\User7Controller@index']);


        /**
         * Routes for user 7 clients page
         */
        Route::get('/clients_get',['uses'=>'Users\User7Controller@showClientPage']);
        Route::post('/client_data',['uses'=>'Users\User7Controller@clientData']);
        Route::post('/add_client',['uses'=>'Users\User7Controller@addClient']);
        Route::post('/update_client',['uses'=>'Users\User7Controller@updateClient']);
        Route::delete('/delete_client',['uses'=>'Users\User7Controller@deleteClient']);

        /**
         * Routes for user 7 goods page
         */
        Route::get('/goods_get',['uses'=>'Users\User7Controller@showGoodPage']);
        Route::post('/good_data',['uses'=>'Users\User7Controller@goodData']);
        Route::post('/add_good',['uses'=>'Users\User7Controller@addGood']);
        Route::post('/update_good',['uses'=>'Users\User7Controller@updateGood']);
        Route::delete('/delete_good',['uses'=>'Users\User7Controller@deleteGood']);

        /**
         * Routes for user 7 products page
         */
        Route::get('/products_get',['uses'=>'Users\User7Controller@showProductPage']);
        Route::post('/product_data',['uses' => 'Users\User7Controller@productData']);
        Route::post('/add_product',['uses'=>'Users\User7Controller@addProduct']);
        Route::post('/update_product',['uses'=>'Users\User7Controller@updateProduct']);
        Route::delete('/delete_product',['uses'=>'Users\User7Controller@deleteProduct']);
        Route::post('/add_product_excel',['uses'=>'Users\User7Controller@addProductExcel']);

//        /**
//         * Routes for user 7 place page
//         */
//        Route::get('/places_get',['uses' => 'Users\User7Controller@showPlacePage']);
//        Route::post('/place_data',['uses' => 'Users\User7Controller@placeData']);
//        Route::post('/add_places',['uses' => 'Users\User7Controller@addPlaces']);
//        Route::post('/update_place',['uses' => 'Users\User7Controller@updatePlace']);
//        Route::delete('/delete_place',['uses' => 'Users\User7Controller@deletePlace']);

        /**
         * Routes for user 7 expenses page
         */
        Route::get('/newExpenses_get',['uses' => 'Users\User7Controller@showExpensesPage']);
        Route::post('/expense_data',['uses' => 'Users\User7Controller@expenseData']);
        Route::post('/add_new_expense',['uses' => 'Users\User7Controller@addNewExpense']);
        Route::post('/update_expense',['uses' => 'Users\User7Controller@updateExpense']);
        Route::delete('/delete_expense',['uses' => 'Users\User7Controller@deleteExpense']);


        /**
         * Routes for user 7 receiving order page
         */
        Route::post('/get_clients',['uses' => 'Users\User7Controller@getClients']);
        Route::get('/orders_get',['uses' => 'Users\User7Controller@showReceivingOrdersPage']);
        Route::match(['get', 'post'],'/orders_data',['uses' => 'Users\User7Controller@getDataReceivingOrder']);


        /**
         * Routes for user 7 suppliers page
         */
        Route::get('/suppliers_get',['uses' => 'Users\User7Controller@showSuppliersPage']);
        Route::post('/suppliers_data',['uses' => 'Users\User7Controller@suppliersData']);
        Route::post('/add_supplier',['uses' => 'Users\User7Controller@addSuppliers']);
        Route::post('/update_supplier',['uses' => 'Users\User7Controller@updateSuppliers']);
        Route::delete('/delete_supplier',['uses' => 'Users\User7Controller@deleteSuppliers']);

        /**
         * Routes for user 7 expenses history page
         */
        Route::get('/history_get',['uses' => 'Users\User7Controller@showexpensesHistoryPage']);
        Route::match(['get', 'post'], '/paid_goods_history',['uses' => 'Users\User7Controller@paidGoodsHistory']);
        Route::match(['get', 'post'], '/paid_utilities_history',['uses' => 'Users\User7Controller@paidUtilitiesHistory']);

        /**
         * Routes for user 7 accept expenses page
         */
        Route::get('/expenses_get',['uses' => 'Users\User7Controller@showAddExpensesPage']);
        Route::post('/add_expenses_data',['uses' => 'Users\User7Controller@addExpensesData']);
        Route::post('/accept_expenses',['uses' => 'Users\User7Controller@acceptExpenses']);
        Route::get('/pay_good_data/{id?}',['uses' => 'Users\User7Controller@payGoodData']);
        Route::post('/access_pay_good',['uses' => 'Users\User7Controller@accessPayGood']);
        Route::post('/returned_good_data',['uses' => 'Users\User7Controller@returnedGoodData']);
        Route::post('/update_returned_good',['uses' => 'Users\User7Controller@updateReturnedGood']);
        Route::delete('/delete_detail_returned',['uses' => 'Users\User7Controller@deleteReturnedGoodDetail']);
        Route::post('/access_returned_good',['uses' => 'Users\User7Controller@accessReturnedGood']);

        /**
         * Routes for user 7 accept utilities page
         */
        Route::get('/utilities_get',['uses' => 'Users\User7Controller@showAddUtilitiesPage']);
        Route::post('/add_utilities_data',['uses' => 'Users\User7Controller@addUtilitiesData']);
        Route::post('/accept_utilities',['uses' => 'Users\User7Controller@acceptUtilities']);
        /**
         * Routes for user 7 accept money page
         */
        Route::get('/accept_get',['uses' => 'Users\User7Controller@showAcceptMoneyPage']);
        Route::post('/accept_post',['uses' => 'Users\User7Controller@AcceptMoneyPost']);
        Route::get('/accept_from_customer',['uses' => 'Users\User7Controller@acceptFromCustomer']);

    });
