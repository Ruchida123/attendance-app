@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
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
  <div class="attendance__alert--danger">
    {{ session('error') }}
  </div>
</div>
@endif

@php
  $startWorkBtnDisabled = false;
  $endWorkBtnDisabled = true;
  $startRestBtnDisabled = true;
  $endRestBtnDisabled = true;

  if (isset($attendance) and $attendance['start_work_time'] != null) {
    $startWorkBtnDisabled = true;
    $startRestBtnDisabled = false;
    if ($attendance['end_work_time'] != null) {
      $startRestBtnDisabled = true;
    };
  };
  if ($startWorkBtnDisabled and $attendance['end_work_time'] == null) {
    $endWorkBtnDisabled = false;
  };
  if (isset($rest)) {
    $startRestBtnDisabled = true;
    $endRestBtnDisabled = false;
  }
@endphp

<div class="attendance__user">
  {{ $user['name'] }}さんお疲れ様です！
</div>
<div class="attendance__content">
  <div class="attendance__panel">
    <div class="attendance__panel-work">
      <form class="attendance__button" action="/work" method="post">
        @csrf
        @if ($startWorkBtnDisabled)
          <button class="attendance__button-disabled" disabled>勤務開始</button>
        @else
          <button class="attendance__button-submit" type="submit">勤務開始</button>
        @endif
      </form>
      <form class="attendance__button" action="/work" method="post">
        @method('PATCH')
        @csrf
        @if ($endWorkBtnDisabled)
          <button class="attendance__button-disabled" disabled>勤務終了</button>
        @else
          <button class="attendance__button-submit" type="submit">勤務終了</button>
        @endif
      </form>
    </div>
    <div class="attendance__panel-rest">
      <form class="attendance__button" action="/rest" method="post">
        @csrf
        @if ($startRestBtnDisabled)
          <button class="attendance__button-disabled" disabled>休憩開始</button>
        @else
          <button class="attendance__button-submit" type="submit">休憩開始</button>
        @endif
      </form>
      <form class="attendance__button" action="/rest" method="post">
        @method('PATCH')
        @csrf
        @if ($endRestBtnDisabled)
          <button class="attendance__button-disabled" disabled>休憩終了</button>
        @else
          <button class="attendance__button-submit" type="submit">休憩終了</button>
        @endif
        @if (isset($rest))
          <input type="hidden" name="rest_id" value="{{ $rest['id'] }}">
        @endif
      </form>
    </div>
  </div>
</div>
@endsection