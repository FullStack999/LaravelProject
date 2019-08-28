@extends('layouts.app')

@section('content')
<!-- login form start -->
<body class="bg-dark">
<div class="container">
    <div class="card card-login mx-auto mt-5 ">
        <div class="card-header justify-content-between">
            <div class="row">
                <div class="col-md-12">
                    Մուտք
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('login') }}" method="POST">
                {{ csrf_field() }}
                <div class="form-group {{ $errors->has('login') ? ' error' : '' }}">
                    @if ($errors->has('login'))
                        <span class="help-block">
                            <strong>{{ $errors->first('login') }}</strong>
                        </span>
                    @endif
                    <input class="form-control" id="login" name="login" type="text" tabindex="1" aria-describedby="loginHelp" placeholder="Մուտքանուն" required autofocus>
                </div>
                <div class="form-group {{ $errors->has('password') ? ' error' : '' }}">
                    <input class="form-control" id="password" name="password" type="password" placeholder="Գախտնաբառ" tabindex="2" required>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" name="remember" type="checkbox" tabindex="3" {{ old('remember') ? 'checked' : '' }}> <small>Հիշել ինձ</small></label>
                    </div>
                </div>
                <input type="submit" tabindex="4" class="btn btn-primary btn-block" value="Մուտք">
            </form>
            <div class="text-center">
                <a class="d-block" href="{{ url('/password/reset') }}" tabindex="5" >Մոռացել եք գախտնաբառը</a>
            </div>
        </div>
        <div class="card-footer">
            <ul class="test_names">
                <li>
                    <span>Կառավարիչ</span>
                    <div>
                        <div>մուտքանուն : admin </div>
                        <div>Գախտնաբառ : secret </div>
                    </div>
                    <hr>
                </li>
                <li>
                    <span>Փաթեթավորում</span>
                    <div>
                        <div>մուտքանուն : account_1</div>
                        <div>Գախտնաբառ : 123456 </div>
                    </div>
                    <hr>
                </li>
                <li>
                    <span>Պարարտանյութեր</span>
                    <div>
                        <div>մուտքանուն : account_2</div>
                        <div>Գախտնաբառ : 123456 </div>
                    </div>
                    <hr>
                </li>
                <li>
                    <span>Թունաքիմիկատներ</span>
                    <div>
                        <div>մուտքանուն : account_3</div>
                        <div>Գախտնաբառ : 123456 </div>
                    </div>
                    <hr>
                </li>
                <li>
                    <span>Պատր.արտ.պահեստ</span>
                    <div>
                        <div>մուտքանուն : account_4</div>
                        <div>Գախտնաբառ : 123456 </div>
                    </div>
                    <hr>
                </li>
                <li>
                    <span>Տնօրեն</span>
                    <div>
                        <div>մուտքանուն : account_5</div>
                        <div>Գախտնաբառ : 123456 </div>
                    </div>
                    <hr>
                </li>
                <li>
                    <span>Պատվեր գրող</span>
                    <div>
                        <div>մուտքանուն : account_6</div>
                        <div>Գախտնաբառ : 123456 </div>
                    </div>
                    <hr>
                </li>
                <li>
                    <span>Հաշվապահ</span>
                    <div>
                        <div>մուտքանուն : account_7</div>
                        <div>Գախտնաբառ : 123456 </div>
                    </div>
                    <hr>
                </li>
            </ul>
        </div>
    </div>
</div>
</body>
<!-- login form end -->


@endsection
