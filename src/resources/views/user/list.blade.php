@extends('layouts.app')

@section('css')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css')  }}" >
<link rel="stylesheet" href="{{ asset('css/user/list.css') }}" />
@endsection

@section('content')
<div class="list__content">
  <div class="section__title">
    <h2>ユーザー一覧</h2>
  </div>
  <form class="search-form" action="/search" method="get">
    @csrf
    <div class="search-form__item">
      <input class="search-form__item-keyword" type="text" name="keyword"
        value="{{ old('keyword') }}" placeholder="名前やメールアドレスを入力してください"/>
    </div>
    <div class="search-form__button">
      <button class="search-form__button-submit" type="submit">検索</button>
    </div>
  </form>
  <div class="admin-table">
    <table class="admin-table__inner">
      <tr class="admin-table__row">
        <th class="admin-table__header">
          <span class="admin-table__header-span">お名前</span>
        </th>
        <th class="admin-table__header">
          <span class="admin-table__header-span">メールアドレス</span>
        </th>
        <th class="admin-table__header">
          <span></span>
        </th>
      </tr>

      @foreach ($users as $user)
      <tr class="admin-table__row">
        <td class="admin-table__item">
          <p class="admin-form__itme-p">{{ $user['name'] }}</p>
          <input type="hidden" name="id" value="{{ $user['id'] }}">
        </td>
        <td class="admin-table__item">
          <p class="admin-form__itme-p">{{ $user['email'] }}</p>
        </td>
        <td class="admin-table__item">
          <form class="detail-form" action="/user_attendance" method="post">
            @csrf
            <div class="detail-form__button">
              <button class="detail-form__button-submit" type="submit" >勤怠表</button>
              <input type="hidden" name="user_name" value="{{ $user['name'] }}">
              <input type="hidden" name="user_email" value="{{ $user['email'] }}">
            </div>
          </form>
        </td>
      </tr>
      @endforeach
    </table>
    <div class="admin-table__pagination">
      {{ $users->links('vendor.pagination.attendance') }}
    </div>
  </div>
</div>
@endsection