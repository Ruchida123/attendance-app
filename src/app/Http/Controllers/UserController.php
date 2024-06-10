<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function user_list()
    {
        // ユーザー一覧
        $users = User::Paginate(7);

        // ユーザー一覧ページ表示
        return view('user.list', compact('users'));
    }

    public function search(Request $request)
    {
        // ユーザー一覧
        $users = User::KeywordSearch($request->keyword)->Paginate(7);

        // ユーザー一覧ページ表示
        return view('user.list', compact('users'));
    }
}
