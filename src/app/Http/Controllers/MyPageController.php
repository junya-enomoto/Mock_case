<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;

class MyPageController extends Controller
{
    public function edit()
    {
        $user = Auth::user(); 
        return view('mypage_profile', compact('user'));
    }

    public function update(ProfileRequest $request) 
    {
        $user = Auth::user();

        if ($request->hasFile('user_image')) {
            $path = $request->file('user_image')->store('profile_images', 'public');
            $user->user_image = 'storage/' . $path;
        }

        $user->user_name = $request->user_name; 
        $user->postal_code = $request->postal_code; 
        $user->street_address = $request->street_address; 
        $user->building_name = $request->building_name;

        $user->save();

        return redirect()->route('mypage')->with('message', 'プロフィールを更新しました');
    }

    public function index(Request $request)
    {
        $user = Auth::user(); 

        if (!$user) {
            return redirect()->route('login'); 
        }

        $type = $request->query('type', 'sell');

        $items = collect();

        if ($type === 'sell') {
            $items = $user->items()->orderBy('created_at', 'desc')->get();
        } elseif ($type === 'buy') {
            $orders = $user->orders()->with('item')->orderBy('created_at', 'desc')->get();
            $items = $orders->map(function ($order) {
                return $order->item;
            });
        }

        return view('mypage', compact('user', 'items', 'type'));
    }
}

