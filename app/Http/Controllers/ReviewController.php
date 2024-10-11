<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create($requestId)
    {
        $request = \App\Models\Request::find($requestId);


        return view('tables', compact('request'));
    }

    public function store(Request $request, $requestId)
    {

        Review::create([
            'request_id' => $requestId,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);
        $request = \App\Models\Request::find($requestId);


        return view('tables', compact('request'));
    }
}
