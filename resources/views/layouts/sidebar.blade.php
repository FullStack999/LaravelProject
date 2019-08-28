<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    @if(Auth::check())
        <a class="navbar-brand" href="@if(Auth::user()->type_id == 1)
        {{ route('admin') }}
        @elseif(Auth::user()->type_id == 2)
        {{ route('user1') }}
        @elseif(Auth::user()->type_id == 3)
        {{ route('user2') }}
        @elseif(Auth::user()->type_id == 4)
        {{ route('user3') }}
        @elseif(Auth::user()->type_id == 5)
        {{ route('user4') }}
        @elseif(Auth::user()->type_id == 6)
        {{ route('user5') }}
        @elseif(Auth::user()->type_id == 7)
        {{ route('user6') }}
        @elseif(Auth::user()->type_id == 8)
        {{ route('user7') }}
        @endif">
            <i class="fas fa-user"></i>
            <span>{{ Auth::user()->name }} {{ Auth::user()->last_name }}</span>
        </a>
    @endif
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
        @if(Auth::user()->type_id == 1)
            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                <li class="nav-item" data-placement="right" id="users">
                    <a class="nav-link">
                        <span class="nav-link-text">Աշխատողներ</span>
                    </a>
                </li>
            </ul>

        @elseif(Auth::user()->type_id == 2)

            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                <li class="nav-item" data-placement="right" id="goods" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ապրանքներ</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="accessGood" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ընդունել ապրանք</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="exitGood" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ապրանքի դուրսգրում</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="goodHistory" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ներքին շարժ</span>
                    </a>
                </li>
            </ul>

        @elseif(Auth::user()->type_id == 3)
            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                <li class="nav-item" data-placement="right" id="fertilizers" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ապրանքներ</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="accessFertilizer" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ընդունել ապրանք</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="exitFertilizer" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ապրանքի դուրսգրում</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="fertilizerHistory" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ներքին շարժ</span>
                    </a>
                </li>

            </ul>

        @elseif(Auth::user()->type_id == 4)
            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                <li class="nav-item" data-placement="right" id="pesticides" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ապրանքներ</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="accessPesticide" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ընդունել ապրանք</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="exitPesticide" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ապրանքի դուրսգրում</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="pesticideHistory" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Ներքին շարժ</span>
                    </a>
                </li>
            </ul>
        @elseif(Auth::user()->type_id == 5)

        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

            <li class="nav-item" data-placement="right" id="products" >
                <a class="nav-link collapsed" >
                    <span class="nav-link-text">Ապրանքներ</span>
                </a>
            </li>
            <li class="nav-item" data-placement="right" id="addProductsPage" >
                <a class="nav-link collapsed" >
                    <span class="nav-link-text">Ապրանքի մուտքագրում</span>
                </a>
            </li>
            <li class="nav-item" data-placement="right" id="orders" >
                <a class="nav-link collapsed" >
                    <span class="nav-link-text">Պատվերներ</span>
                </a>
            </li>
            <li class="nav-item" data-placement="right" id="movements" >
                <a class="nav-link collapsed" >
                    <span class="nav-link-text">Ներքին շարժ</span>
                </a>
            </li>
        </ul>

        @elseif(Auth::user()->type_id == 6)
        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
            <li class="nav-item" data-placement="right" id="orders" >
                <a class="nav-link collapsed" >
                    <span class="nav-link-text">Պատվերներ</span>
                </a>
            </li>
            <li class="nav-item" data-placement="right" id="deleting" >
                <a class="nav-link collapsed" >
                    <span class="nav-link-text">Քեշ</span>
                </a>
            </li>
        </ul>
        @elseif(Auth::user()->type_id == 7)

        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
            <li class="nav-item" data-placement="right" id="addOrders">
                <a class="nav-link collapsed">
                    <span class="nav-link-text">Պատվերի գրանցում</span>
                </a>
            </li>
            <li class="nav-item" data-placement="right" id="orders">
                <a class="nav-link collapsed">
                    <span class="nav-link-text">Պատվերներ</span>
                </a>
            </li>
            <li class="nav-item" data-placement="right" id="backFill">
                <a class="nav-link collapsed">
                    <span class="nav-link-text">Պատվերներ որոնք ենթակա են վերանայման</span>
                </a>
            </li>
        </ul>

        @elseif(Auth::user()->type_id == 8)

            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                <li class="nav-item" data-placement="right" id="clients" >
                    <a class="nav-link collapsed" >
                        <span class="nav-link-text">Հաճախորդներ</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="suppliers">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Մատակարարներ</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="goods">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Ապրանքներ</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="products">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Ծաղիկներ</span>
                    </a>
                </li>
                {{--<li class="nav-item" data-placement="right" id="places" >--}}
                    {{--<a class="nav-link" >--}}
                        {{--<span class="nav-link-text">Ուղություններ</span>--}}
                    {{--</a>--}}
                {{--</li>--}}
                <li class="nav-item" data-placement="right" id="newExpenses">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Ծախսերի տեսակներ</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="orders">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Պատվերների պատմություն</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="expenses">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Ապրանքների ընդունման և վճարման բաժին</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="utilities">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Կոմունալ վճարումներ և աշխատավարձ</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="history">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Ծախսերի պատմություն</span>
                    </a>
                </li>
                <li class="nav-item" data-placement="right" id="accept">
                    <a class="nav-link collapsed">
                        <span class="nav-link-text">Ընդունել գումար</span>
                    </a>
                </li>
            </ul>
        @endif
        <!-- header -->
            @include('layouts.header')
    </div>
</nav>


