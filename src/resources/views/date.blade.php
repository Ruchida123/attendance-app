@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/date.css') }}">
@endsection

@section('content')
@if (session('message'))
<div class="attendance__alert">
  <div class="attendance__alert--success">
    {{ session('message') }}
  </div>
</div>
@elseif(session('error'))
<div class="attendance__alert">
  <div class="attendance__alertー-danger">
    {{ session('error') }}
  </div>
</div>
@endif

<div class="attendance__date">
  2024-11-11
</div>
<div class="attendance__content">
  <div class="attendance-table">
    <table class="attendance-table__inner">
      <tr class="attendance-table__row">
        <th class="attendance-table__header">名前</th>
        <th class="attendance-table__header">勤務開始</th>
        <th class="attendance-table__header">勤務終了</th>
        <th class="attendance-table__header">休憩時間</th>
        <th class="attendance-table__header">勤務時間</th>
      </tr>
      <tr class="attendance-table__row">
        <td class="attendance-table__item">サンプル太郎</td>
        <td class="attendance-table__item">サンプル</td>
        <td class="attendance-table__item">サンプル</td>
        <td class="attendance-table__item">サンプル</td>
        <td class="attendance-table__item">サンプル</td>
      </tr>
    </table>
  </div>
</div>
@endsection