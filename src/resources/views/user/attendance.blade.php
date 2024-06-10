@extends('layouts.app')

@section('css')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css')  }}" >
<link rel="stylesheet" href="{{ asset('css/user/attendance.css') }}">
@endsection

@section('content')
@if(session('error'))
<div class="attendance__alert">
  <div class="attendance__alert--danger">
    {{ session('error') }}
  </div>
</div>
@endif

<div class="attendance__date">
  <form class="attendance__button-before" action="{{ url('/user_attendance')}}" method="post">
    @csrf
    <button class="attendance__button-submit" type="submit"><</button>
    <input type="hidden" name="user_name" value="{{ $user['name'] }}">
    <input type="hidden" name="user_email" value="{{ $user['email'] }}">
    <input type="hidden" name="before_years" value="{{ $before_years }}">
  </form>
  <span> {{ $current_years }} </span>
  <form class="attendance__button-after" action="{{ url('/user_attendance')}}" method="post">
    @csrf
    @if ($after_button_disabled)
      <button class="attendance__button-disabled" disabled>></button>
    @else
      <button class="attendance__button-submit" type="submit">></button>
    @endif
    <input type="hidden" name="user_name" value="{{ $user['name'] }}">
    <input type="hidden" name="user_email" value="{{ $user['email'] }}">
    <input type="hidden" name="after_years" value="{{ $after_years }}">
  </form>
</div>
<div class="attendance__content">
  <div class="attendance-table">
    <table class="attendance-table__inner">
      <tr class="attendance-table__row">
        <th class="attendance-table__header">名前</th>
        <th class="attendance-table__header">日付</th>
        <th class="attendance-table__header">勤務開始</th>
        <th class="attendance-table__header">勤務終了</th>
        <th class="attendance-table__header">休憩時間</th>
        <th class="attendance-table__header">勤務時間</th>
      </tr>
      @foreach ($attendances as $attendance)
      @php
        $totalWorkTime = '00:00:00';
        $totalRestTime = '00:00:00';

        if (isset($attendance['total_work_time'])) {
          $rests = $attendance->rests;
          if ($rests->isNotEmpty()) {
            $workStr = $attendance['date'] . ' ' . $attendance['total_work_time'];
            $totalRestDatetime = null;

            $i = 0;
            foreach ($rests as $rest) {
              $totalWorkDatetime = new DateTime($workStr);
              $str = $rest['date'] . ' ' . ($rest['total_rest_time'] ?? '00:00:00');
              $date = new DateTime($str);
              $formatted_date = $date->format('Y-m-d H:i:s');

              $totalWorkInterval = $date->diff($totalWorkDatetime);
              $workStr = $attendance['date'] . ' ' . $totalWorkInterval->format('%H:%I:%S');

              if ($i == 0) {
                $totalRestDatetime = $date;
              } else {
                $formatted_rest = $totalRestDatetime->format('Y-m-d H:i:s');
                $hour = $date->format('H');
                $min = $date->format('i');
                $sec = $date->format('s');

                $totalRestDatetime = new DateTime($formatted_rest.' +'.$sec.' seconds +'.$min.' min +'.$hour.' hours');
              };
              $i += 1;
            };
            $totalWorkTime = $totalWorkInterval->format('%H:%I:%S');
            $totalRestTime = $totalRestDatetime->format('H:i:s');
          } else {
            $totalWorkTime = $attendance['total_work_time'];
          };
        };
      @endphp
      <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ $attendance->user['name'] }}</td>
        <td class="attendance-table__item">{{ $attendance['date'] }}</td>
        <td class="attendance-table__item">{{ $attendance['start_work_time'] ?? '-' }}</td>
        <td class="attendance-table__item">{{ $attendance['end_work_time'] ?? '-' }}</td>
        <td class="attendance-table__item">{{ $totalRestTime }}</td>
        <td class="attendance-table__item">{{ $totalWorkTime }}</td>
      </tr>
      @endforeach
    </table>
  </div>
</div>
@endsection