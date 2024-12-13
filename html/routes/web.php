<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PHPGeneratorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\MysqlController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    for ($i=0; $i < 200; $i++) {
        \Log::warning('heheh');

    }

    return view('welcome');
});

Route::get('/load-test', function () {
    return 1;
});

Route::get('/home', [HomeController::class, 'index'])->name('home.index');
Route::get('/php_generator', [PHPGeneratorController::class, 'index'])->name('php_generator.index');
Route::resource('memory', MemoryController::class);
Route::resource('mysql', MysqlController::class);
Route::get('/ajax', [AjaxController::class, 'index']);
Route::get('/ajax/test-content-type', [AjaxController::class, 'testContentType']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/chat', function() {
    return view('chat.index');
})->middleware('auth');

Route::get('/getUserLogin', function() {
	return Auth::user();
})->middleware('auth');

Route::get('/messages', function() {
    return App\Models\Message::with('user')->get();
})->middleware('auth');

Route::post('/messages', function() {
   $user = Auth::user();

  $message = new App\Models\Message();
  $message->message = request()->get('message', '');
  $message->user_id = $user->id;
  $message->save();

  broadcast(new App\Events\MessagePosted($message, $user))->toOthers();

  return ['message' => $message->load('user')];
})->middleware('auth');

require __DIR__.'/auth.php';

