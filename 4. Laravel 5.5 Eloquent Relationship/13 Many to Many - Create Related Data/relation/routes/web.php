<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\User; // User Primary Key, hasOne
use App\Profile; // Profile Foreign Key, belongsTo
use App\Post; // Profile Foreign Key, belongsTo

// Test One To One

Route::get('/create_user', function(){
    $user = User::create([
        'name' => 'Azhar',
        'email' => 'azhar@gmail.com',
        'password' => bcrypt('password')
    ]);

    $user = User::create([
        'name' => 'Rasyad',
        'email' => 'rasyad@gmail.com',
        'password' => bcrypt('password')
    ]);

    return $user;
});

Route::get('/create_profile', function(){
    // Cara ke 1
    $profile = Profile::create([
        'user_id' => 1,
        'phone' => '01234567891',
        'address' => 'Jl. Persada No 1'
    ]);

    return $profile;

    // Cara ke 2
    $user = User::find(1);

    $user->profile()->create([
        'phone' => '01234567894',
        'address' => 'Jl. Persada No 4'
    ]);

    return $user;
});

Route::get('/create_user_profile', function(){
    $user = User::find(2);

    $profile = new Profile([
        'phone' => '01234567892',
        'address' => 'Jl. Persada No 2'
    ]);

    $user->profile()->save($profile);
    // profile() diambil dari model User untuk melakukan relasi

    return $user;
});

Route::get('/read_user', function(){
    // Memanggil data profile dari table user

    // User dengan id = 1 dicari
    $user = User::find(1);
    
    // Method profile() dari model User dipanggil    

    // Cara ke 1
    // Kolom phone yaitu sebuah atribut yang dipanggil dengan user id = 1
    return $user->profile->phone;
    return $user->profile->address;

    // Cara ke 2
    // Menggunakan array untuk memanggil beberapa kolom
    $data = [
        'name' => $user->name,
        'phone' => $user->profile->phone,
        'address' => $user->profile->address
    ];

    return $data;
});

Route::get('/read_profile', function(){
    // Memanggil data profile dari table user
    $profile = Profile::where('phone', '01234567891')->first();

    // Cara ke 1
    // user merupakan method yang dipanggil dalam model profile
    return $profile->user->name;

    // Cara ke 2
    $data = [
        'name' => $profile->user->name,
        'email' => $profile->user->email,
        'phone' => $profile->phone
    ];

    return $data;
});

Route::get('/update_profile', function(){
    // Mengupdate data profile berdasarkan id user
    // Table users      Table profiles
    // id               user_id
    $user = User::find(2);

    // Cara ke 1
    $user->profile()->update([
        'phone' => '01234567893',
        'address' => 'Jl. Persada No 3'
    ]);

    // Cara ke 2
    $data = [
        'phone' => '01234567893',
        'address' => 'Jl. Persada No 3'
    ];

    $user->profile()->update($data);

    return $user;
});

Route::get('/delete_profile', function(){
    // Menghapus data profile berdasarkan kolom id table users
    $user = User::find(2);

    $user->profile()->delete();

    return $user;

});

// Test One To Many

Route::get('/create_post', function(){
    // Cara ke 1
    // id yang baru dibuat akan sama dengan user_id
    // contoh id = 1 maka user_id = 1
    $user = User::create([
        'name' => 'Azhar',
        'email' => 'azhar@gmail.com',
        'password' => bcrypt('password')
    ]);

    $user->posts()->create([
        'title' => 'Isi Title Post',
        'body' => 'Hello World! Ini isi dari body table Post'
    ]);

    // Cara ke 2
    // Menggunakan id table users yang sudah ada
    $user = User::findOrFail(1);

    $user->posts()->create([
        'title' => 'Isi Title Post',
        'body' => 'Hello World! Ini isi dari body table Post'
    ]);

    // Cara ke 3
    $user->posts()->create([
        'user_id' => 2,
        'title' => 'Isi Title Post',
        'body' => 'Hello World! Ini isi dari body table Post'
    ]);

    // Cara ke 4
    Post::create([
        'user_id' => 2,
        'title' => 'Isi Title Post',
        'body' => 'Hello World! Ini isi dari body table Post'
    ]);

    return 'Success';
});

Route::get('/read_post', function () {    
    $user = User::find(1);

    // Cara ke 1
    // return $user->posts();
    // Terjadi error HasMany could not be converted to string

    // Cara ke 2
    // dd($user->posts()->get());

    // Cara ke 3
    // get() Mengambil seluruh data
    $posts = $user->posts()->get();

    foreach ($posts as $post) {
        $data[] = [
            'name' => $post->user->name,
            'post_id' => $post->id,
            'title' => $post->title,
            'body' => $post->body
        ];
    }

    return $data;

    // Cara ke 4
    // first() Mengambil data yang pertama dibuat
    $post = $user->posts()->first();

    $data[] = [
        'name' => $post->user->name,
        'post_id' => $post->id,
        'title' => $post->title,
        'body' => $post->body
    ];

    return $data;
});

Route::get('/update_post', function () {
    $user = User::findOrFail(1);

    // Cara ke 1
    // whereKolom(value) sama seperti where(kolom, value)        
    $user->posts()->whereId(1)->update([
        'title' => 'Ini isian title post update',
        'body' => 'Ini isian body post yang sudah diupdate'
    ]);
    
    // Cara ke 2
    $user->posts()->where('id', 2)->update([
        'title' => 'Ini isian title post update',
        'body' => 'Ini isian body post yang sudah diupdate'
    ]);

    // Cara ke 3
    $user->posts()->update([
        'title' => 'Ini isian title post update',
        'body' => 'Ini isian body post yang sudah diupdate'
    ]);

    return 'Success';
});

Route::get('/delete_post', function () {
    // Memilih id = 1 pada tabel users
    $user = User::find(1);

    // Cara ke 1    
    $user->posts()->whereId(1)->delete();

    // whereId juga digunakan untuk validasi jika id yang dimiliki berelasi dengan user_id
    $user->posts()->whereId(100)->delete();

    // Cara ke 2
    $user->posts()->where('id', 2)->delete();

    // Cara ke 3
    $user->posts()->delete();

    // Cara ke 4
    // Model camel case yaitu membaca kolom berdasarkan awal kata
    // Contoh kolom user_id menjadi UserId tanpa menggunakan tanda underscore _
    $user->posts()->whereUserId(2)->delete();

    return 'Success';
});

Route::get('/create_categories', function () {
    // Cara ke 1
    // Data table posts harus ada terlebih dahulu atau sebaliknya
    $post = Post::findOrFail(1);

    // Menginput table category sekaligus table category_post karena method categories()        
    // Untuk table category_post tidak didefinisikan di Model karena laravel sudah otomatis mendeklarasikannya
    // ! Hal yang perlu diperhatikan adalah penamaan table harus tepat
    $post->categories()->create([
        // str_slug() untuk memisahkan kata dengan tanda - dan dalam database kata tersebut menjadi lowercase
        'slug' => str_slug('Belajar Laravel', '-'),
        'category' => 'Belajar Laravel'
    ]);

    return 'Success';

    // Cara ke 2
    $user = User::create([
        'name' => 'Rasyad',
        'email' => 'rasyad@gmail.com',
        'password' => bcrypt('password')
    ]);

    $user->posts()->create([
        'title' => 'New Title',
        'body' => 'New Body Content'
    ])->categories()->create([
        'slug' => str_slug('New Category', '-'),
        'category' => 'New Category'
    ]);

    return 'Success';
});