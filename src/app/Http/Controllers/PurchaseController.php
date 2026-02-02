<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest; 
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log; 

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($item_id)
    {
        $item = Item::with('user')->findOrFail($item_id);
        $user = Auth::user();

        if ($item->user_id === $user->id) {
            return redirect()->route('item.detail', ['item_id' => $item->id])
                             ->with('error', 'ご自身の出品物は購入できません。');
        }

        if ($item->is_sold) {
            return redirect()->route('item.detail', ['item_id' => $item->id])
                             ->with('error', 'この商品は売り切れです。');
        }

        return view('purchase', compact('item', 'user'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if ($item->user_id === $user->id || $item->is_sold) {
            return redirect()->route('item.detail', ['item_id' => $item->id])
                             ->with('error', 'この商品は購入できません。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'], 
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'unit_amount' => $item->price, 
                        'product_data' => [
                            'name' => $item->name, 
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success', ['item_id' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}', 
                'cancel_url' => route('purchase.index', ['item_id' => $item->id]),   
                'metadata' => [ 
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'payment_method' => $request->payment_method, 
                ],
            ]);

            return redirect($checkoutSession->url);

        } catch (\Exception $e) {
            Log::error('Stripe Checkout Session creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', '決済処理中にエラーが発生しました。');
        }
    }

    public function success(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if ($item->is_sold) {
            return redirect()->route('mypage')->with('info', 'この商品は既にご購入済みです。');
        }

        DB::transaction(function () use ($request, $item, $user) {
 
            $item->is_sold = true;
            $item->save();
            $paymentMethod = 'card'; 
            if ($request->session_id) {
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    $session = Session::retrieve($request->session_id);
                    $paymentMethod = $session->metadata->payment_method ?? 'card';
                } catch (\Exception $e) {
                    Log::error('Stripe session retrieval failed: ' . $e->getMessage());
                }
            }

            Order::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'payment_method' => $paymentMethod,
                'postal_code' => $user->postal_code,
                'street_address' => $user->street_address,
                'building_name' => $user->building_name,
                'price' => $item->price,
                'status' => 'completed',
            ]);
        });

        return redirect()->route('mypage')
                         ->with('success', '商品を購入しました！');
    }

    public function showPurchaseAddress($item_id)
    {
        $user = Auth::user();
        return view('purchase_address', compact('user', 'item_id'));
    }

    public function updateAddress(AddressRequest $request, $item_id) 
    {
        $user = Auth::user();
        $user->postal_code = $request->postal_code;
        $user->street_address = $request->address;
        $user->building_name = $request->building_name;
        $user->save();

        return redirect()->route('purchase.index', ['item_id' => $item_id])
                         ->with('success', '配送先住所を更新しました。');
    }
}
