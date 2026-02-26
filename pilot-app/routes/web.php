<?php

use App\Http\Controllers\PilotAuctionController;
use Illuminate\Support\Facades\Route;

Route::get('/_pilot/auction/health', [PilotAuctionController::class, 'health']);
Route::get('/_pilot/auction/admin/json_provider/get_initial_data/{idLelang}/{idBarang}', [PilotAuctionController::class, 'shadowGetInitialData']);
Route::get('/_pilot/auction/admin/json_provider/get_chart_update/{idLelang}', [PilotAuctionController::class, 'shadowGetChartUpdate']);
Route::get('/auction/admin/json_provider/get_barang/{idLelang}', [PilotAuctionController::class, 'getBarang']);
Route::get('/auction/admin/json_provider/get_peserta/{idLelang}', [PilotAuctionController::class, 'getPeserta']);
Route::get('/auction/admin/json_provider/get_initial_data/{idLelang}/{idBarang}', [PilotAuctionController::class, 'getInitialData']);
Route::get('/auction/admin/json_provider/get_chart_update/{idLelang}', [PilotAuctionController::class, 'getChartUpdate']);
Route::get('/_pilot/auction/{path?}', [PilotAuctionController::class, 'fallback'])->where('path', '.*');
