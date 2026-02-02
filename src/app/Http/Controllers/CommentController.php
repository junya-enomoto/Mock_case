<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;


class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function store(CommentRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id); 

        Comment::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'comment' => $request->content, 
        ]);

        return redirect()->route('item.detail', ['item_id' => $item->id]);
    }
}
