<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class RestController extends Controller
{
    public function rest_start()
    {
        $user = Auth::user();
        // 勤怠情報
        $attendance = Attendance::UserTodaySearch($user->id)->first();

        // 勤怠情報が取得できなかった場合エラー
        if (empty($attendance)) {
            $error = "本日の勤務を開始されていません";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 既に勤務終了している場合はエラー
        if ($attendance->state == '勤務終了') {
            $error = "本日は既に勤務終了済みです";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 既に休憩開始している場合はエラー
        if ($attendance->state == '休憩中') {
            $error = "既に休憩開始済みです";
            return redirect('/')->with(compact('user', 'attendance', 'rest', 'error'));
        };

        // 休憩開始時刻登録
        Rest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'date' => Carbon::today('Asia/Tokyo'),
            'start_rest_time' => Carbon::now('Asia/Tokyo')
        ]);
        // 状態を休憩中に更新
        Attendance::find($attendance->id)->update([
            'state' => '休憩中',
        ]);
        $rest = Rest::StartRestAtteSearch($attendance->id)->first();
        $message = "休憩を開始しました";
        return redirect('/')->with(compact('user', 'attendance', 'rest', 'message'));
    }

    public function rest_end(Request $request)
    {
        $user = Auth::user();
        // 勤怠情報
        $attendance = Attendance::UserTodaySearch($user->id)->first();
        // 休憩情報のID
        $rest_id = $request->rest_id;

        // 勤怠情報が取得できなかった場合エラー
        if (empty($attendance)) {
            $error = "本日の勤務を開始されていません";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 既に勤務終了している場合はエラー
        if ($attendance->state == '勤務終了') {
            $error = "本日は既に勤務終了済みです";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 休憩開始していない場合はエラーとする
        if (empty($rest_id)) {
            $error = "休憩を開始されていません";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 既に休憩終了している場合はエラーとする
        $rest = Rest::StartRestSearch($rest_id)->first();
        if (empty($rest)) {
            $error = "既に休憩終了済みです";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 現在時刻と休憩開始時刻の差分を取得
        $datetime_now = Carbon::now('Asia/Tokyo');
        $start_time = Carbon::createFromTimeString($rest->start_rest_time, 'Asia/Tokyo');
        $interval = $start_time->diff($datetime_now);
        $interval_time = Carbon::createFromTime($interval->format('%h'), $interval->format('%i'), $interval->format('%s'), 'Asia/Tokyo');

        // 休憩終了時刻更新
        Rest::find($rest_id)->update([
            'end_rest_time' => $datetime_now,
            'total_rest_time' => $interval_time,
        ]);
        // 状態を勤務中に更新
        Attendance::find($attendance->id)->update([
            'state' => '勤務中',
        ]);
        $rest = Rest::find($rest_id);
        $message = "休憩を終了しました";
        return redirect('/')->with(compact('user', 'attendance', 'rest', 'message'));
    }
}
