@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-login mx-auto mt-5">
        <div class="card-header">Վերականգնել գախտնաբառը</div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="text-center mt-4 mb-5">
                <h4>Մոռացել եք գաղտնաբառը</h4>
                <p>Մուտքագրեք ձեր էլփոստի հասցեն և մենք ձեզ կուղարկենք նոր գաղտնաբառ</p>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->any() ? ' has-error' : '' }}">
                    <input name="email" class="form-control" id="email" type="email" aria-describedby="emailHelp" placeholder="էլ.հասցե" value="{{ old('email') }}" required>
                    @if ($errors->any())
                        <span class="help-block">
                            <strong>{{ $errors->first() }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-block">Ուղարկել</button>
            </form>
            <div class="text-center">
                <a class="d-block" href="{{ route('login') }}">Վերադառնալ մուտքի էջ</a>
            </div>
        </div>
    </div>
</div>
@endsection
