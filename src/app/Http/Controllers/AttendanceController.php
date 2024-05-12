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
        $attendance = Attendance::UserDateSearch($user->id)->first();
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
        $attendance = Attendance::UserDateSearch($user->id)->first();
        if (isset($attendance)) {
            // 既に勤務終了している場合エラーとする
            $end_attendance = Attendance::EndWorkSearch($attendance->id)->first();
            if (isset($end_attendance)) {
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
            'start_work_time' => Carbon::now('Asia/Tokyo')
        ]);
        $attendance = Attendance::UserDateSearch($user->id)->first();
        $message = "勤務を開始しました";
        return redirect('/')->with(compact('user', 'attendance', 'message'));
    }

    public function work_end()
    {
        // ユーザー情報
        $user = Auth::user();
        // 勤怠情報
        $attendance = Attendance::UserDateSearch($user->id)->first();

        // 勤務開始していない場合はエラーとする
        if (empty($attendance)) {
            $error = "本日の勤務を開始されていません";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 勤怠情報のID
        $attendance_id = $attendance->id;

        // 休憩終了時間が更新されていない場合、現在の時刻で更新する。
        $rest = Rest::StartRestAtteSearch($attendance_id)->first();
        if (isset($rest)) {
            Rest::find($rest->id)->update([
                'end_rest_time' => Carbon::now('Asia/Tokyo')
            ]);
        };

        // 既に勤務終了している場合エラーとする
        $attendance = Attendance::EndWorkSearch($attendance_id)->first();
        if (isset($attendance)) {
            $error = "本日は既に勤務終了済みです";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 勤務終了時間の更新
        Attendance::find($attendance_id)->update([
            'end_work_time' => Carbon::now('Asia/Tokyo')
        ]);
        $attendance = Attendance::find($attendance_id);
        $message = "勤務を終了しました";
        return redirect('/')->with(compact('user', 'attendance', 'message'));
    }

    public function date()
    {
        // 日付別勤怠ページ表示
        return view('date');
    }
}
