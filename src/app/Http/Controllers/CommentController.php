<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Item; 
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:500'],
        ]);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'comment' => $validated['comment'],
        ]);

        return redirect()
            ->route('items.show', $item->id)
            ->with('status', 'コメントを投稿しました');
    }
}