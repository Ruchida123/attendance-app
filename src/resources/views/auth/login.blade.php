@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('header')
<button class="register__button" onclick="location.href='/register'">register</button>
<style>
.header__inner {
  display:flex;
  justify-content: center;
  position: relative;
}
</style>
@endsection

@section('content')
<div class="login-form">
  <div class="login-form__heading">
    <h2 class="login-form__title">ログイン</h2>
  </div>
  <div class="login-form__content">
    <form class="form" action="/login" method="post">
      @csrf
      <div class="form__group">
        <div class="form__group-content">
          <div class="form__input--text">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス" />
          </div>
          <div class="form__error">
            @error('email')
            {{ $message }}
            @enderror
          </div>
        </div>
      </div>
      <div class="form__group">
        <div class="form__group-content">
          <div class="form__input--text">
            <input type="password" name="password" placeholder="パスワード" />
          </div>
          <div class="form__error">
            @error('password')
            {{ $message }}
            @enderror
          </div>
        </div>
      </div>
      <div class="form__button">
        <button class="form__button-submit" type="submit">ログイン</button>
      </div>
    </form>
    <div class="register__link">
      <span class="register__link-text">アカウントをお持ちでない方はこちらから</span>
      <br />
      <a class="register__button-submit" href="/register">会員登録</a>
    </div>
  </div>
</div>
@endsection