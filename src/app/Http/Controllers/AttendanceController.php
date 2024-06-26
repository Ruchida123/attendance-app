<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\User;
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
        // 翌日ボタン非活性フラグ
        $after_button_disabled = true;
        // 現在日付
        $current_date = Carbon::now('Asia/Tokyo')->format('Y-m-d');

        // 勤怠情報
        $attendances = Attendance::with('user', 'rests')->DateSearch($current_date)->Paginate(5);

        // 日付別勤怠ページ表示
        return view('date', compact('attendances', 'current_date', 'after_button_disabled'));
    }

    public function before_date(Request $request)
    {
        // 翌日ボタン非活性フラグ
        $after_button_disabled = false;
        // 元の日付
        $origin_datetime = new Carbon($request->req_date.' 00:00:00');
        // 現在の日付
        $current_date = $origin_datetime->subDays(1)->format('Y-m-d');
        // 今日の日付
        $today = Carbon::today('Asia/Tokyo')->format('Y-m-d');

        // 現在の日付が今日の日付以上の場合、翌日ボタン非活性
        if (Carbon::parse($current_date)->gte(Carbon::parse($today))) {
            $after_button_disabled = true;
        }

        // 勤怠情報
        $attendances = Attendance::with('user', 'rests')->DateSearch($current_date)->Paginate(5);
        // ページ遷移の際にクエリパラメータを付与する。
        $attendances->withPath('/before?req_date='.$request->req_date);

        // 日付別勤怠ページ表示
        return view('date', compact('attendances', 'current_date', 'after_button_disabled'));
    }

    public function after_date(Request $request)
    {
        // 翌日ボタン非活性フラグ
        $after_button_disabled = false;
        // 元の日付
        $origin_datetime = new Carbon($request->req_date.' 00:00:00');
        // 今日の日付
        $today = Carbon::today('Asia/Tokyo')->format('Y-m-d');
        // 現在の日付
        $current_date = (Carbon::parse($origin_datetime)->gte(Carbon::parse($today))) ? $origin_datetime->format('Y-m-d') : $origin_datetime->addDays(1)->format('Y-m-d');

        // 現在の日付が今日の日付以上の場合、翌日ボタン非活性
        if (Carbon::parse($current_date)->gte(Carbon::parse($today))) {
            $after_button_disabled = true;
        }

        // 勤怠情報
        $attendances = Attendance::with('user', 'rests')->DateSearch($current_date)->Paginate(5);
        // ページ遷移の際にクエリパラメータを付与する。
        $attendances->withPath('/after?req_date='.$request->req_date);

        // 現在の日付が未来の日付の場合、エラーとする。
        if (Carbon::parse($current_date)->gt(Carbon::parse($today))) {
            $error = "未来のデータは表示できません";
            return redirect('/attendance')->with(compact('attendances', 'current_date', 'after_button_disabled', 'error'));
        };

        // 日付別勤怠ページ表示
        return view('date', compact('attendances', 'current_date', 'after_button_disabled'));
    }

    public function user_attendance(Request $request)
    {
        // ユーザー情報
        $user = User::NameEmailSearch($request->user_name, $request->user_email)->first();
        // 翌月ボタン非活性フラグ
        $after_button_disabled = true;
        // 現在日時
        $current_datetime = Carbon::now('Asia/Tokyo');

        // リクエストチェック
        if ($request->before_years) {
            $current_datetime = new Carbon($request->before_years.' 00:00:00');
        } elseif ($request->after_years && $request->after_years <= $current_datetime->format('Y-m')) {
            $current_datetime = new Carbon($request->after_years.' 00:00:00');
        }

        // 現在年月
        $current_years = $current_datetime->format('Y-m');
        // 現在から一月前の年月
        $before_years = Carbon::parse($current_years)->subMonthNoOverflow()->format('Y-m');
        // 現在から一月後の年月
        $after_years = Carbon::parse($current_years)->addMonthNoOverflow()->format('Y-m');

        // 現在年月が今月未満の場合、翌月ボタンを活性化する
        if ($current_years < (Carbon::now('Asia/Tokyo')->format('Y-m'))) {
            $after_button_disabled = false;
        }

        // 勤怠情報
        $attendances = Attendance::with('user', 'rests')->UserYearsSearch($user->id, $current_years)->Paginate(5);

        // ユーザー勤怠表ページ表示
        return view('user.attendance', compact('user', 'attendances', 'current_years', 'before_years', 'after_years', 'after_button_disabled'));
    }
}
