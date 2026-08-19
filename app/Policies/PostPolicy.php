<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

// The policy itself is correct. The VULNERABILITY is that nothing ever calls it:
// PostController::update() and PostController::destroy() do not call
// $this->authorize(), the controller never calls $this->authorizeResource(),
// and the routes in routes/web.php carry no `can:` middleware.
// Declaring a policy does not enforce it — Laravel only runs it when it is wired
// up. A policy that is never applied protects nothing.
class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || (bool) $user->is_admin;
    }
}
