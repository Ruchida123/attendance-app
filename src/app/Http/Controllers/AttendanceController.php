<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        // ユーザー情報
        $user = Auth::user();
        // 勤怠情報
        $attendance = Attendance::UserTodaySearch($user->id)->first();
        // 休憩情報
        $rest = null;
        if (isset($attendance)) {
            $rest = Rest::StartRestAtteSearch($attendance->id)->first();
        };
        // 打刻ページ表示
        return view('index', compact('user', 'attendance', 'rest'));
    }

    public function work_start()
    {
        // ユーザー情報
        $user = Auth::user();

        // 既に勤務開始している場合はエラーとする
        $attendance = Attendance::UserTodaySearch($user->id)->first();
        if (isset($attendance)) {
            // 既に勤務終了している場合エラーとする
            if ($attendance->state == '勤務終了') {
                $error = "本日は既に勤務終了済みです";
                return redirect('/')->with(compact('user', 'attendance', 'error'));
            };
            $error = "本日は既に勤務開始済みです";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 勤務開始時間の登録
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today('Asia/Tokyo'),
            'start_work_time' => Carbon::now('Asia/Tokyo'),
            'state' => '勤務中',
        ]);
        $attendance = Attendance::UserTodaySearch($user->id)->first();
        $message = "勤務を開始しました";
        return redirect('/')->with(compact('user', 'attendance', 'message'));
    }

    public function work_end()
    {
        // ユーザー情報
        $user = Auth::user();
        // 勤怠情報
        $attendance = Attendance::UserTodaySearch($user->id)->first();

        // 勤務開始していない場合はエラーとする
        if (empty($attendance)) {
            $error = "本日の勤務を開始されていません";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 勤怠情報のID
        $attendance_id = $attendance->id;
        // 現在日時
        $datetime_now = Carbon::now('Asia/Tokyo');

        // 休憩終了時間が更新されていない場合、現在の時刻で更新する。
        $rest = Rest::StartRestAtteSearch($attendance_id)->first();
        if (isset($rest)) {
            // 現在時刻と休憩開始時刻の差分を取得
            $start_time = Carbon::createFromTimeString($rest->start_rest_time, 'Asia/Tokyo');
            $interval = $start_time->diff($datetime_now);
            $interval_time = Carbon::createFromTime($interval->format('%h'), $interval->format('%i'), $interval->format('%s'), 'Asia/Tokyo');

            // 休憩終了時間の更新
            Rest::find($rest->id)->update([
                'end_rest_time' => $datetime_now,
                'total_rest_time' => $interval_time,
            ]);
        };

        // 既に勤務終了している場合エラーとする
        if ($attendance->state == '勤務終了') {
            $error = "本日は既に勤務終了済みです";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 現在時刻と勤務開始時刻の差分を取得
        $start_time = Carbon::createFromTimeString($attendance->start_work_time, 'Asia/Tokyo');
        $interval = $start_time->diff($datetime_now);
        $interval_time = Carbon::createFromTime($interval->format('%h'), $interval->format('%i'), $interval->format('%s'), 'Asia/Tokyo');

        // 勤務終了時間の更新
        Attendance::find($attendance_id)->update([
            'end_work_time' => $datetime_now,
            'total_work_time' => $interval_time,
            'state' => '勤務終了',
        ]);
        $attendance = Attendance::find($attendance_id);
        $message = "勤務を終了しました";
        return redirect('/')->with(compact('user', 'attendance', 'message'));
    }

    public function date()
    {
        // 日付
        $today = Carbon::now('Asia/Tokyo')->format('Y-m-d');

        // 勤怠情報
        $attendances = Attendance::with('user', 'rests')->DateSearch($today)->Paginate(5);

        // 日付別勤怠ページ表示
        return view('date', compact('attendances', 'today'));
    }
}
