<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Teacher;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * 📖 عرض الكتب (للجميع)
     */
    public function index(Request $request)
    {
        $books = Book::with('material');

        if ($request->filled('material_id')) {
            $books->where('material_id', $request->material_id);
        }

        return response()->json([
            'books' => $books->get()
        ]);
    }

    /**
     * ➕ إضافة كتاب (فقط للأستاذ الحقيقي)
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();

        // 🔐 التحقق أنه أستاذ فعلي
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

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
        ]);
    }

    /**
     * ✏️ تعديل كتاب (فقط للأستاذ الحقيقي)
     */
    public function update(Request $request, $id)
    {
        $user = auth('api')->user();

        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $book = Book::findOrFail($id);

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

    /**
     * ❌ حذف كتاب (فقط للأستاذ الحقيقي)
     */
    public function destroy($id)
    {
        $user = auth('api')->user();

        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully'
        ]);
    }

    /**
     * 📘 عرض كتاب واحد
     */
    public function show($id)
    {
        $book = Book::with('material')->findOrFail($id);

        return response()->json([
            'book' => $book
        ]);
    }
}