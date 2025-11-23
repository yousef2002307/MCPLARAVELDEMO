<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\PostRequest;
use App\Http\Middleware\AcceptLanguageMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post("/s",function(PostRequest $request){
    return response()->json([
        "message"=>"Post created successfully",
    ]);
})->middleware(AcceptLanguageMiddleware::class);