<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// =============================================================================
// 16. Unverified Webhook Signature
// =============================================================================
//
// TEACHING FIXTURE ONLY — this is not a real integration endpoint. It is wired to
// no provider, it returns a fake payload shape invented for this lab, and it sits
// in the `web` middleware group so Laravel's CSRF guard still rejects outside
// POSTs. Nothing beyond this lab should ever call it.
class WebhookController extends Controller
{
    public function page(): View
    {
        return view('webhook');
    }

    public function receive(Request $request): JsonResponse
    {
        // VULNERABILITY: Unverified Webhook Signature — the payload is trusted and
        // acted on without ever checking the X-Lab-Signature header against a shared
        // secret. There is no hash_hmac() computation and no hash_equals() comparison,
        // so the body is accepted on faith and can promote a post to an official
        // admin announcement.
        //
        // Scope, to match the class docblock and routes/web.php: this route sits in
        // the `web` middleware group, so Laravel's CSRF guard already rejects POSTs
        // originating outside the app. That is what stops a drive-by here — NOT any
        // check in this method. A real webhook endpoint lives outside the `web`
        // group (no session, no CSRF token to present), and there the missing
        // signature check is the only thing standing between an attacker and the
        // model: anyone who can reach the URL forges a body and it is applied.
        // Should be: hash_equals(hash_hmac('sha256', $request->getContent(), $secret),
        // (string) $request->header('X-Lab-Signature')) before touching the model.
        $payload = $request->json()->all();

        $post = Post::findOrFail($payload['post_id'] ?? 0);

        $post->update(['is_admin' => (bool) ($payload['is_admin'] ?? false)]);

        return response()->json([
            'status' => 'applied',
            'post_id' => $post->id,
            'is_admin' => $post->is_admin,
        ]);
    }
}
