<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Category;
use App\Models\Condition;
use App\Models\PaymentMethod;

class FlemaTest extends TestCase
{
    use RefreshDatabase;

    // ゲスト利用時の表示確認
    public function testStatusGuest()
    {
        //表示用アイテム作成
        $item = Item::factory()->create();

        //認証ページ
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response = $this->get('/login');
        $response->assertStatus(200);

        //公開ページ
        $response = $this->get('/');
        $response->assertStatus(200);
        $response = $this->get('/mylist');
        $response->assertStatus(200);
        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        //ユーザ閲覧ページ
        $response = $this->get("/purchase/{$item->id}");
        $response->assertStatus(302);
        $response = $this->get("/purchase/address/{$item->id}");
        $response->assertStatus(302);
        $response = $this->get('/mypage');
        $response->assertStatus(302);
        $response = $this->get('/mypage/sell');
        $response->assertStatus(302);
        $response = $this->get('/mypage/buy');
        $response->assertStatus(302);
        $response = $this->get('/mypage/profile');
        $response->assertStatus(302);
        $response = $this->get('/sell');
        $response->assertStatus(302);

        //未設定route
        $response = $this->get('/no_route');
        $response->assertStatus(404);
    }

    //　ユーザ利用時の表示確認
    public function testStatusUser()
    {
        //ユーザログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        //表示用アイテム作成
        $item = Item::factory()->create();

        //認証ページ
        $response = $this->get('/register');
        $response->assertStatus(302);
        $response = $this->get('/login');
        $response->assertStatus(302);

        //公開ページ
        $response = $this->get('/');
        $response->assertStatus(200);
        $response = $this->get('/mylist');
        $response->assertStatus(200);
        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        //ユーザ閲覧ページ
        $response = $this->get("/purchase/{$item->id}");
        $response->assertStatus(200);
        $response = $this->get("/purchase/address/{$item->id}");
        $response->assertStatus(200);
        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response = $this->get('/mypage/sell');
        $response->assertStatus(200);
        $response = $this->get('/mypage/buy');
        $response->assertStatus(200);
        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);
        $response = $this->get('/sell');
        $response->assertStatus(200);

        //未設定route
        $response = $this->get('/no_route');
        $response->assertStatus(404);

    }

    // 会員登録機能
    // 名前が入力されていない場合、バリデーションメッセージが表示される
    public function testRegisterNoName()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('name');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('お名前を入力してください。', $errors->first('name'));

    }

    // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function testRegisterNoEmail()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田 太郎',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('メールアドレスを入力してください。', $errors->first('email'));
    }

    // パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function testRegisterNoPassword()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田 太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('パスワードを入力してください。', $errors->first('password'));
    }

    // パスワードが7文字以下の場合、バリデーションメッセージが表示される
    public function testRegister8LessPassword()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田 太郎',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors('password');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('パスワードは8文字以上で入力してください。', $errors->first('password'));
    }

    // パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
    public function testRegisterDifferentPassword()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田 太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => '12345678',
        ]);

        $response->assertSessionHasErrors('password');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('パスワードと一致しません。', $errors->first('password'));
    }

    // 全ての項目が入力されている場合、会員情報が登録され、ログイン画面に遷移される
    public function testRegister()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田 太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => '山田 太郎',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/login');
    }

    // ログイン機能
    // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function testLoginNoEmail()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('メールアドレスを入力してください。', $errors->first('email'));
    }

    // パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function testLoginNoPassword()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('パスワードを入力してください。', $errors->first('password'));
    }

    // パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function testLoginWrong()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => '666666666',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('ログイン情報が登録されていません', $errors->first('email'));
    }

    // 正しい情報が入力された場合、ログイン処理が実行される
    public function testLogin()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    //ログアウト機能
    // ログアウトができる
    public function testLogout()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create([]);
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
    }

    // 商品一覧取得
    // 全商品を取得できる
    public function testTop()
    {
        $this->seed();

        $response = $this->get('/');
        $response->assertStatus(200);

        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->item_name);
        }
    }

    // 購入済み商品は「Sold」と表示される
    public function testTopSold()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        Order::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('class="item-link sold"', false);

    }

    // 自分が出品した商品は表示されない
    public function testTopMySell()
    {
        $user = User::factory()->create();
        $myItem = Item::factory()->create(['user_id' => $user->id, 'item_name' => 'りんごジュース',]);

        $this->actingAs($user);
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertDontSee($myItem->item_name);

    }

    // マイリスト一覧取得
    // いいねした商品だけが表示される
    public function testMylist()
    {
        $user = User::factory()->create();
        $likedItem = Item::factory()->create(['item_name' => 'りんごジュース',]);
        $unlikedItem = Item::factory()->create(['item_name' => 'みかんジュース',]);
        $likedItem->likes()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get('/mylist');
        $response->assertStatus(200);

        $response->assertSee($likedItem->item_name);
        $response->assertDontSee($unlikedItem->item_name);
    }

    // 購入済み商品は「Sold」と表示される
    public function testMylistSold()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        Order::factory()->create([
            'item_id' => $item->id,
        ]);

        $item->likes()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get('/mylist');
        $response->assertStatus(200);

        $response->assertSee($item->item_name);
        $response->assertSee('class="item-link sold"', false);
    }

    // 自分が出品した商品は表示されない
    public function testMylistMySell()
    {
        $user = User::factory()->create();
        $likedItem = Item::factory()->create();
        $likedMySellItem = Item::factory()->create(['user_id' => $user->id, 'item_name' => 'りんごジュース',]);

        $likedItem->likes()->create(['user_id' => $user->id]);
        $likedMySellItem->likes()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get('/mylist');
        $response->assertStatus(200);

        $response->assertSee($likedItem->item_name);
        $response->assertDontSee($likedMySellItem->item_name);
    }

    // 未認証の場合は何も表示されない
    public function testMylistGuest()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['item_name' => 'りんごジュース',]);
        $item->likes()->create(['user_id' => $user->id]);

        $response = $this->get('/mylist');
        $response->assertStatus(200);

        $response->assertDontSee($item->item_name);
    }

    // 商品検索機能
    // 「商品名」で部分一致検索ができる
        public function testSearch()
    {
        $matchingItem = Item::factory()->create(['item_name' => 'りんごジュース',]);
        $nonMatchingItem = Item::factory()->create(['item_name' => 'みかんジュース',]);

        $keyword = 'りんご';
        $response = $this->get('/?keyword=' . $keyword);

        $response->assertSee($matchingItem -> item_name);
        $response->assertDontSee($nonMatchingItem -> item_name);
    }

    // 検索状態がマイリストでも保持されている
    public function testSearchMylist()
    {
        $matchingItem = Item::factory()->create(['item_name' => 'りんごジュース',]);
        $nonMatchingItem = Item::factory()->create(['item_name' => 'みかんジュース',]);

        $keyword = 'りんご';
        $response = $this->get('/?keyword=' . $keyword);

        $response->assertSee($matchingItem -> item_name);
        $response->assertDontSee($nonMatchingItem -> item_name);
        $response->assertSee($keyword);

        $user = User::factory()->create();
        $matchingItem->likes()->create(['user_id' => $user->id]);
        $nonMatchingItem->likes()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get('/mylist?keyword=' . $keyword);
        $response->assertSee($matchingItem -> item_name);
        $response->assertDontSee($nonMatchingItem -> item_name);
        $response->assertSee($keyword);

    }

    // 商品詳細情報取得
    // 必要な情報が表示される（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報（カテゴリ、商品の状態）、コメント数、コメントしたユーザー情報、コメント内容）
    public function testItemDetail()
    {
        // ユーザーとカテゴリ、状態を作成
        $user = User::factory()->create();
        $commentUser = User::factory()->create();
        $category = Category::factory()->create();
        $item = Item::factory()->create([
            'item_name' => 'エアマックス',
            'price' => 15000,
            'item_image' => 'default_profile.jpg',
            'brand' => 'ナイキ',
            'description' => '人気のスニーカーです。',
            'user_id' => $user->id,
        ]);
        $item->categories()->attach($category->id);

        // いいねとコメントを作成
        $item->likes()->create(['user_id' => $user->id]);
        $item->likes()->create(['user_id' => $commentUser->id]);
        $item->comments()->create([
            'user_id' => $commentUser->id,
            'comment' => 'かっこいいですね！',
        ]);

        $likesCount = $item->likes()->count();
        $comments = $item->comments()->with('user')->get();
        $commentsCount = $comments->count();
        $firstComment = $comments->first();

        // 商品詳細ページにアクセス
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // 商品の各種情報を検証
        $response->assertSee($item->item_name); // 商品名
        $response->assertSee((string)number_format($item->price)); // 価格
        $response->assertSee($item->item_image); // 商品画像
        $response->assertSee($item->condition->name); // 商品の状態
        $response->assertSee($item->brand); // ブランド名
        $response->assertSee($item->description); // 商品説明
        $response->assertSee($category->category_name); // カテゴリ名
        $response->assertSee((string) $likesCount); // いいね数
        $response->assertSee((string) $commentsCount); // コメント数
        $response->assertSee($firstComment->user->name); // コメントユーザー
        $response->assertSee($firstComment->comment); // コメント内容
    }

    // 複数選択されたカテゴリが表示されているか
    public function testItemCategories()
    {
        // ユーザーとカテゴリ、状態を作成
        $user = User::factory()->create();
        $categories = Category::factory()->count(3)->create();
        $item = Item::factory()->create();
        foreach ($categories as $category) {
            $item->categories()->attach($category->id);
        }

        // 商品詳細ページにアクセス
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // カテゴリー情報を検証
        foreach ($categories as $category) {
            $response->assertSee($category->category_name);
        }

    }

    // いいね機能
    // いいねアイコンを押下することによって、いいねした商品として登録することができる。
    public function testLike()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->assertEquals(0, $item->likes()->count());

        $this->actingAs($user);

        // 商品詳細ページにアクセス
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // いいね実行
        $response = $this->post('/item/' . $item->id . '/like');
        $response->assertRedirect();

        // DBに登録されたか確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'deleted_at' => null,
        ]);
        $updatedItem = $item->fresh();
        $this->assertEquals(1, $updatedItem->likes()->count());
    }

    // 追加済みのアイコンは色が変化する
    public function testLikeIcon()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $this->post('/item/' . $item->id . '/like');

        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('item-likes__icon liked');
    }

    // 再度いいねアイコンを押下することによって、いいねを解除することができる。
    public function testLikeDelete()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $item->likes()->create(['user_id' => $user->id]);
        $this->assertEquals(1, $item->likes()->count());

        $this->post('/item/' . $item->id . '/like');

        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertDontSee('item-likes__icon liked');
        $this->assertEquals(0, $item->likes()->count());
    }

    // コメント送信機能
    // ログイン済みのユーザーはコメントを送信できる
    public function testCommentUser()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->assertEquals(0, $item->comments()->count());

        $this->actingAs($user);

        $commentText = 'これはテストコメントです。';
        $response = $this->post('/item/' . $item->id . '/comment', ['comment' => $commentText, 'item_id' => $item->id,]);

        $response->assertRedirect('/item/' . $item->id);
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => $commentText,
        ]);

        // コメントが画面に表示されるか確認
        $updatedItem = $item->fresh();
        $this->assertEquals(1, $updatedItem->comments()->count());
    }

    // ログイン前のユーザーはコメントを送信できない
    public function testCommentGuest()
    {
        $item = Item::factory()->create();
        $commentText = 'これはテストコメントです';

        $response = $this->post('/item/' . $item->id . '/comment', [
            'comment' => $commentText,
            'item_id' => $item->id,
        ]);

        // ログイン画面へリダイレクトされることを確認
        $response->assertRedirect('/login');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => $commentText,
        ]);
    }

    // コメントが入力されていない場合、バリデーションメッセージが表示される
    public function testCommentNoComment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/item/' . $item->id . '/comment', [
            'comment' => '',
            'item_id' => $item->id,
        ]);

        $response->assertSessionHasErrors('comment');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('コメントを入力してください', $errors->first('comment'));

    }

    // コメントが255字以上の場合、バリデーションメッセージが表示される
    public function testComment256()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $commentText = str_repeat('a', 256);

        $response = $this->post('/item/' . $item->id . '/comment', [
            'comment' => $commentText,
            'item_id' => $item->id,
        ]);

        $response->assertSessionHasErrors('comment');
        $errors = $response->getSession()->get('errors');
        $this->assertEquals('255文字以下で入力してください', $errors->first('comment'));
    }

    // 商品購入機能
    // 「購入する」ボタンを押下すると購入が完了する
    public function testBuy()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actingAs($user);

        $response = $this->get("/purchase/{$item->id}");
        $response->assertStatus(200);

        $postData = [
            'payment_method' => $paymentMethod->id,
            'order_postal_code' => '123-4567',
            'order_address' => '東京都品川区1-1-1',
            'order_building' => 'テストビル',
        ];

        $response = $this->post("/purchase/{$item->id}", $postData);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method_id' => $paymentMethod->id,
            'order_postal_code' => '123-4567',
            'order_address' => '東京都品川区1-1-1',
            'order_building' => 'テストビル',
        ]);

        $response->assertRedirect('/mypage/buy');
    }

    // 購入した商品は商品一覧画面にて「sold」と表示される
    public function testBuySold()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actingAs($user);

        $response = $this->get("/purchase/{$item->id}");
        $response->assertStatus(200);

        $postData = [
            'payment_method' => $paymentMethod->id,
            'order_postal_code' => '123-4567',
            'order_address' => '東京都品川区1-1-1',
            'order_building' => 'テストビル',
        ];

        $response = $this->post("/purchase/{$item->id}", $postData);

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee($item->item_name);
        $response->assertSee('class="item-link sold"', false);

    }

    // 「プロフィール/購入した商品一覧」に追加されている
    public function testBuyMylist()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actingAs($user);

        $response = $this->get("/purchase/{$item->id}");
        $response->assertStatus(200);

        $postData = [
            'payment_method' => $paymentMethod->id,
            'order_postal_code' => '123-4567',
            'order_address' => '東京都品川区1-1-1',
            'order_building' => 'テストビル',
        ];

        $response = $this->post("/purchase/{$item->id}", $postData);

        $response = $this->get('/mypage/buy');
        $response->assertStatus(200);

        $response->assertSee($item->item_name);

    }

    // 支払い方法選択機能
    // 小計画面で変更が即時反映される（jsでの実装のため正しくデータ保存されていることを確認する）
    public function testSelectPaymentMethod()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actingAs($user);

        $postData = [
            'payment_method' => $paymentMethod->id,
            'order_postal_code' => '123-4567',
            'order_address' => '東京都渋谷区1-1-1',
            'order_building' => '渋谷ヒカリエ',
        ];

        $response = $this->post("/purchase/{$item->id}", $postData);
        $response->assertRedirect('/mypage/buy');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method_id' => $paymentMethod->id,
        ]);
    }

    // 配送先変更機能
    // 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    public function testChangeAddress()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actingAs($user);

        $response = $this->get("/purchase/address/{$item->id}");
        $response->assertStatus(200);

        $postData = [
            'postal_code' => '123-4567',
            'address' => '東京都品川区1-1-1',
            'building' => 'テストビル',
        ];

        $response = $this->post("/purchase/address/{$item->id}", $postData);

        $response->assertRedirect("/purchase/{$item->id}");

        $this->assertEquals(session('purchase_address'), [
            'postal_code' => '123-4567',
            'address'     => '東京都品川区1-1-1',
            'building'    => 'テストビル',
        ]);

        $response = $this->get("/purchase/{$item->id}");
        $response->assertSee('123-4567');
        $response->assertSee('東京都品川区1-1-1');
        $response->assertSee('テストビル');
    }

    // 購入した商品に送付先住所が紐づいて登録される
    public function testChangeAddressBuy()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        $this->actingAs($user);

        $response = $this->get("/purchase/address/{$item->id}");
        $response->assertStatus(200);

        $postDataAddress = [
            'postal_code' => '111-1111',
            'address' => '東京都品川区1-1-1',
            'building' => 'テストビル',
        ];

        $response = $this->post("/purchase/address/{$item->id}", $postDataAddress);
        $response->assertRedirect("/purchase/{$item->id}");

        $postData = [
            'payment_method' => $paymentMethod->id,
            'order_postal_code' => session('purchase_address')['postal_code'],
            'order_address' => session('purchase_address')['address'],
            'order_building' => session('purchase_address')['building'],
        ];

        $response = $this->post("/purchase/{$item->id}", $postData);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method_id' => $paymentMethod->id,
            'order_postal_code' => '111-1111',
            'order_address' => '東京都品川区1-1-1',
            'order_building' => 'テストビル',
        ]);
    }

    // ユーザー情報取得
    // 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
    public function testUserProfile()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // ユーザーが出品した商品を作成
        $sellItem = Item::factory()->create([
            'user_id' => $user->id,
            'item_name' => '出品商品',
            'price' => 1000,
        ]);

        // ユーザーが購入した商品を作成
        $buyItem = Item::factory()->create([
            'item_name' => '購入商品',
            'price' => 3000,
        ]);
        $paymentMethod = PaymentMethod::factory()->create();
        Order::create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
            'payment_method_id' => $paymentMethod -> id,
            'order_postal_code' => $user->user_postal_code,
            'order_address' => $user->user_address,
            'order_building' => $user->user_building,
        ]);

        // ログイン
        $this->actingAs($user);
        $response = $this->get('/mypage/sell');

        // プロフィール画像が正しく表示されるか確認
        $response->assertSee('src="' . asset('storage/profile_default.png') . '" alt="プロフィール画像" >',false);
        // ユーザー名が正しく表示されるか確認
        $response->assertSee($user->name);
        $response->assertSee($sellItem->item_name);

        $response = $this->get('/mypage/buy');
        $response->assertSee($user->name);
        $response->assertSee($buyItem->item_name);
    }
    // ユーザー情報変更
    // 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
    public function testUserProfileUpdate()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);
        $response = $this->get('/mypage/profile');

        // ユーザー情報の初期値が正しく表示されるか確認
        $response->assertSee('src="' . asset('storage/profile_default.png') . '" alt="プロフィール画像" >',false);
        $response->assertSee($user->name);
        $response->assertSee($user->user_postal_code);
        $response->assertSee($user->user_address);
        $response->assertSee($user->user_building);
    }

    // 出品商品情報登録
    // 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、商品の説明、販売価格）
    public function testSell()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $category = Category::factory()->create();
        $file = UploadedFile::fake()->create('test.jpg', 100);

        $this->actingAs($user);
        $response = $this->get('/sell');
        $response->assertStatus(200);

        $response = $this->post('/sell', [
            'image' => $file,
            'condition_id' => $condition->id,
            'item_name' => 'テスト商品',
            'brand' => 'ブランド名',
            'description' => '商品の説明',
            'price' => 1234,
            'item_category' => [$category->id],
        ]);

        // itemsテーブルに登録されているか
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'item_name' => 'テスト商品',
            'brand' => 'ブランド名',
            'description' => '商品の説明',
            'price' => 1234,
        ]);

        Storage::disk('public')->assertExists('test.jpg');

        // カテゴリとの関連も確認
        $this->assertDatabaseHas('category_item', [
            'category_id' => $category->id,
        ]);
    }
}