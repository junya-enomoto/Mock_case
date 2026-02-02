<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store($item_id)
    {
        $item = Item::findOrFail($item_id);
        
        if (!$item->isLikedByUser()) {
            Like::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
            ]);
            $item->increment('likes_count');
        }

        return redirect()->route('item.detail', ['item_id' => $item->id]);
    }

    public function destroy($item_id)
    {
        $item = Item::findOrFail($item_id);
        
        $like = $item->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
            $item->decrement('likes_count');
        }

        return redirect()->route('item.detail', ['item_id' => $item->id]);
    }
}
