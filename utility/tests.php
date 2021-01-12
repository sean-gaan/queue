<?php

use Laravel\Sanctum\Sanctum;
use App\Models\User;

function signIn($user = null)
{
    if (is_null($user)) {
        $user = User::factory()->create();
    }

    Sanctum::actingAs(
        $user,
        ['*']
    );

    return $user;
}
