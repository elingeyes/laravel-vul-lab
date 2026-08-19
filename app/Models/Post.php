<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// VULNERABILITY: Mass Assignment — $fillable exposes an ownership foreign key
// (user_id) and a privilege flag (is_admin). Both are reachable from request
// input through Post::create($request->all()) or $post->update($request->all()),
// so an attacker can reassign someone else's post to themselves or promote a
// post to an official admin announcement by adding one extra field.
//
// Both fields are documented separately below, and the two blocks are kept more
// than five lines apart on purpose: the auditor collapses same-file, same-type
// findings that sit within five lines of each other, so adjacent entries would
// surface as a single finding instead of the two this fixture advertises.
// Keep apostrophes out of the comments INSIDE $fillable — the detector reads the
// array by pairing quote characters, and a stray ' swallows the entries after it.
class Post extends Model
{
    protected $fillable = [
        'title',
        'body',

        // DANGEROUS #1 — ownership foreign key.
        // Because user_id is fillable, a request body carrying user_id=1 rebinds
        // the row to another account. An attacker can reassign their own post to
        // the admin so it reads as official, or claim a post they do not own.
        // Ownership keys belong to the server: set them from the authenticated
        // user, never from request input.
        'user_id',

        // DANGEROUS #2 — privilege flag.
        // Because is_admin is fillable, a request body carrying is_admin=1 turns
        // an ordinary post into an official admin announcement. Privilege flags
        // should never be mass-assignable at all; promote through an explicit,
        // authorized code path so the decision stays auditable.
        'is_admin',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
        ];
    }
}
