<?php

namespace App\Http\Controllers\api;

use App\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class ReviewController extends Controller //Nguyen Quoc Dat
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Review::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

            $request->validate([
                'comment' => 'string|max:255',
                'rating' => 'required|integer|min:1|max:5',
            ],[
                'rating.min' => 'The minimum rating is 1.',
                'rating.max' => 'The maximum rating is 5.',
            ]);
            Review::create($request->all());
            return response()->json(['message' => 'Review added' ]);
        }catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $e->validator->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return response()->json(['review'=>$review]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        //
    }
}
