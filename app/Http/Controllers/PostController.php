<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // =========================================================================
    // 12. Broken Access Control — Policy Defined But Never Applied
    // =========================================================================

    public function index(): View
    {
        $posts = Post::orderBy('id')->get();

        return view('posts', compact('posts'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        // VULNERABILITY: Broken Access Control — App\Policies\PostPolicy defines an
        // update() ability and nothing here ever reaches it: the method makes no
        // authorization call, the controller never wires authorizeResource, and the
        // route carries no can: middleware. Anyone can edit anyone's post.
        // VULNERABILITY: Mass Assignment — $request->all() reaches a $fillable that
        // contains user_id and is_admin, so the same request can also steal the post
        // or flag it as an official admin announcement.
        $post = Post::findOrFail($id);

        $post->update($request->all());

        return back()->with('message', 'Post updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        // VULNERABILITY: Broken Access Control — PostPolicy::delete() exists and is
        // never invoked. No ownership check, no policy call, no middleware: any
        // visitor can delete any post by POSTing its ID.
        $post = Post::findOrFail($id);

        $post->delete();

        return back()->with('message', 'Post deleted.');
    }
}
