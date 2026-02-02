<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'recommend'); 
        $keyword = $request->query('keyword'); 

        $query = Item::orderBy('created_at', 'desc');

        if ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        if ($filter === 'mylist') {
            if (Auth::check()) {
                
                $user = Auth::user();
                $likedItemIds = $user->likes()->pluck('item_id');
                $query->whereIn('id', $likedItemIds);
            } else {
                
                $items = collect(); 
                return view('index', compact('items', 'filter', 'keyword'));
                
            }
            } else { 
           
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
        }
        
        $items = $query->get();
        
        return view('index', compact('items', 'filter','keyword'));
    }

    public function detail($item_id)
    {
        $item = Item::with('categories', 'comments.user')->findOrFail($item_id);

        return view('item', compact('item'));
    }

    public function showSell()
    {
        $categories = Category::all();
        return view('sell', compact('categories'));
    }

    public function store(ExhibitionRequest $request) 
    {   
        $imagePath = null;
        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('items', 'public');
        }

        $item = new Item();
        $item->user_id = Auth::id(); 
        $item->name = $request->name;
        $item->description = $request->description;
        $item->condition = $request->condition;
        $item->price = $request->price;
        $item->brand_name = $request->brand_name;
        $item->item_image = 'storage/' . $imagePath; 

        $item->save();

        if (!empty($request->category_ids)) {
            $item->categories()->attach($request->category_ids);
        }

       return redirect()->route('item.index') 
                         ->with('success', '商品が出品されました！');
    }
}

