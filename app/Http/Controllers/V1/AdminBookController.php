<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Book;
use Illuminate\Http\Request;

class AdminBookController extends Controller
{
    private function authorizeAdmin()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $admin = Admin::where('user_id', $user->id)->first();

        if (!$admin) {
            return response()->json(['message' => 'Only admins can access this resource'], 403);
        }

        return null;
    }

    /**
     * إضافة كتاب جديد
     */
    public function store(Request $request)
    {
        if ($error = $this->authorizeAdmin()) return $error;

        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'book_url' => 'required|string',
            'cover_image_url' => 'nullable|string',
            'material_id' => 'nullable|exists:materials,id',
        ]);

        $book = Book::create([
            'title' => $request->title,
            'description' => $request->description,
            'book_url' => $request->book_url,
            'cover_image_url' => $request->cover_image_url,
            'material_id' => $request->material_id,
        ]);

        return response()->json([
            'message' => 'Book created successfully',
            'data' => $book
        ], 201);
    }

    
    public function update(Request $request, $id)
    {
        if ($error = $this->authorizeAdmin()) return $error;

        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'book_url' => 'sometimes|string',
            'cover_image_url' => 'nullable|string',
            'material_id' => 'nullable|exists:materials,id',
        ]);

        $book->update($request->only([
            'title',
            'description',
            'book_url',
            'cover_image_url',
            'material_id'
        ]));

        return response()->json([
            'message' => 'Book updated successfully',
            'data' => $book
        ]);
    }

   
    public function destroy($id)
    {
        if ($error = $this->authorizeAdmin()) return $error;

        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }
}