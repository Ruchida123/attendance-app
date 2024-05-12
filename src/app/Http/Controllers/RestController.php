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
        $attendance = Attendance::UserDateSearch($user->id)->first();

        // 勤怠情報が取得できなかった場合エラー
        if (empty($attendance)) {
            $error = "本日の勤務を開始されていません";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 既に勤務終了している場合はエラー
        $end_attendance = Attendance::EndWorkSearch($attendance->id)->first();
        if (isset($end_attendance)) {
            $error = "本日は既に勤務終了済みです";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 既に休憩開始している場合はエラー
        $rest = Rest::StartRestAtteSearch($attendance->id)->first();
        if (isset($rest)) {
            $error = "既に休憩開始済みです";
            return redirect('/')->with(compact('user', 'attendance', 'rest', 'error'));
        };
        // 休憩開始時刻登録
        Rest::create([
            'attendance_id' => $attendance->id,
            'date' => Carbon::today('Asia/Tokyo'),
            'start_rest_time' => Carbon::now('Asia/Tokyo')
        ]);
        $rest = Rest::StartRestAtteSearch($attendance->id)->first();
        $message = "休憩を開始しました";
        return redirect('/')->with(compact('user', 'attendance', 'rest', 'message'));
    }

    public function rest_end(Request $request)
    {
        $user = Auth::user();
        // 勤怠情報
        $attendance = Attendance::UserDateSearch($user->id)->first();
        // 休憩情報のID
        $rest_id = $request->rest_id;

        // 勤怠情報が取得できなかった場合エラー
        if (empty($attendance)) {
            $error = "本日の勤務を開始されていません";
            return redirect('/')->with(compact('user', 'attendance', 'error'));
        };

        // 既に勤務終了している場合はエラー
        $end_attendance = Attendance::EndWorkSearch($attendance->id)->first();
        if (isset($end_attendance)) {
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

        // 休憩終了時刻更新
        Rest::find($rest_id)->update([
            'end_rest_time' => Carbon::now('Asia/Tokyo')
        ]);
        $rest = Rest::find($rest_id);
        $message = "休憩を終了しました";
        return redirect('/')->with(compact('user', 'attendance', 'rest', 'message'));
    }
}
