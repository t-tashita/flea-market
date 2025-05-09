<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\CategoryItem;
use App\Models\Comment;
use App\Models\Like;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $query = Item::query();

        if (!empty($keyword)) {
            $query->where('item_name', 'like', "%{$keyword}%");
        }
        $userId = auth()->id();
        if ($userId) {
            $query->where('user_id', '<>', $userId);
        }
        $items = $query->get();
        return view('top', compact('items', 'keyword'));
    }

    public function detail($item_id)
    {
        $item = Item::with(['categories', 'condition', 'likes', 'comments'])->findOrFail($item_id);
        $categories = $item->categories;
        $condition = $item->condition;
        $likes = $item->likes;
        $comments = $item->comments;

        $userId = auth()->id();
        $isLiked = false;
        if ($userId) {
            $isLiked = $item->likes()->where('user_id', $userId)->exists();
        }

        return view('detail', compact('item', 'categories', 'condition', 'likes', 'comments', 'isLiked'));
    }

    public function like(Request $request)
    {
        $itemId = $request->item_id;
        $userId = auth()->id();

        $existingLike = Like::withTrashed()
                            ->where('item_id', $itemId)
                            ->where('user_id', $userId)
                            ->first();

        if ($existingLike && $existingLike->trashed()) {
            $existingLike->restore();
        } elseif (!$existingLike) {
            Like::create([
                'item_id' => $itemId,
                'user_id' => $userId,
            ]);
        } else {
            $existingLike->delete();
        }

        return redirect()->back();
    }

    public function comment($item_id, CommentRequest $request)
    {
        $user = auth()->id();
        $comment = Comment::create([
            'item_id' => $item_id,
            'user_id' => $user,
            'comment' => $request->comment,
        ]);

        return redirect("/item/$item_id");
    }

    public function mylist(Request $request)
    {
        $userId = auth()->id();
        $keyword = $request->query('keyword');
        $items = [];

        if ($userId) {
            $items= Item::with(['likes'])
            ->where('user_id', '<>', $userId)->whereHas('likes', function ($q) use ($userId) {
                $q->where('likes.user_id', '=', $userId);
            });

            if (!empty($keyword)) {
                $items->where(function ($query) use ($keyword) {
                    $query->where('item_name', 'like', '%' . $keyword . '%');
                });
            }
            $items = $items->get();
        }

        return view('top', compact('items', 'keyword'));
    }

    public function buy($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);
        $payment_methods = PaymentMethod::all();
        $address = session('purchase_address');

        return view('buy', compact('user', 'item', 'payment_methods', 'address'));
    }

    public function buyItem($item_id, PurchaseRequest $request)
    {
        $userId = auth()->id();
        $order = Order::create([
            'item_id' => $item_id,
            'payment_method_id' => $request->payment_method,
            'order_postal_code' => $request->order_postal_code,
            'order_address' => $request->order_address,
            'order_building' => $request->order_building,
            'user_id' => $userId,
        ]);
        $request->session()->forget('purchase_address');

        return redirect()->route('mypage.page', ['page' => 'buy']);
    }

    public function change($item_id)
    {
        $user = auth()->user();
        return view('address', compact('user', 'item_id'));
    }

    public function changeAddress($item_id, Request $request)
    {
        $request->session()->put('purchase_address', [
            'postal_code' => $request->input('postal_code'),
            'address'     => $request->input('address'),
            'building'    => $request->input('building'),
        ]);

        return redirect("/purchase/$item_id");
    }

    public function sell()
    {
        $user = auth()->user();
        $categories = Category::all();
        $conditions = Condition::all();
        return view('sell', compact('user', 'categories', 'conditions'));
    }

    public function sellItem(ExhibitionRequest $request)
    {
        $image = $request->file('image');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('', $imageName, 'public');

        $item = Item::create([
            'user_id' => auth()->id(),
            'item_image' => $imageName,
            'condition_id' => $request->condition_id,
            'item_name' => $request->item_name,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        $item->categories()->attach($request->item_category);

        return redirect('/mypage');
    }

    public function profile()
    {
        $user = auth()->user();
        $items = Item::where('user_id', '=', $user->id)->get();
        return view('profile', compact('user', 'items'));
    }

    public function update()
    {
        $user = auth()->user();

        return view('update', compact('user'));
    }

    public function updateProfile(AddressRequest $request)
    {
        $validator = Validator::make($request->all(), (new ProfileRequest())->rules());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'user_postal_code' => $request->postal_code,
            'user_address' => $request->address,
            'user_building' => $request->building,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public', $imageName);
            $data['user_image'] = $imageName;
        }
        auth()->user()->update($data);

        return redirect('/mypage');
    }

    public function deal($page)
    {
            $user = auth()->user();
        if ($page === 'sell') {
            $items = Item::where('user_id', auth()->id())->get();
        } elseif ($page === 'buy') {
            $userId = auth()->id();
            $items= Item::with(['order'])
            ->whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', '=', $userId);
            })
            ->get();
        }

        return view('profile', compact('items', 'user'));
    }
}