<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotesController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware('auth')->group(function () {
    Route::get('/notes/index', [NotesController::class, 'index'])->name('notes.index');
    Route::get('/notes/note/{id}', [NotesController::class, 'note'])->name('notes.note');
    Route::delete('/notes/delete/{id}', [NotesController::class, 'delete'])->name('notes.delete');
    // GET form update
    Route::get('/notes/update/{id}', [NotesController::class, 'edit'])->name('notes.edit');
    // update xử lý form
    Route::put('/notes/update/{id}', [NotesController::class, 'update'])->name('notes.update');

    // create note
    Route::get('/notes/create', [NotesController::class, 'create'])->name('notes.create');
    Route::post('/notes/store', [NotesController::class, 'store'])->name('notes.store');
});



require __DIR__.'/auth.php';