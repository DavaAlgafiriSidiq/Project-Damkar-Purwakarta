<?php

// Tambahkan baris ini ke routes/api.php

use App\Http\Controllers\HidranController;

Route::get('/hidran', [HidranController::class, 'apiIndex']);
Route::get('/hidran/nearest', [HidranController::class, 'nearest']);