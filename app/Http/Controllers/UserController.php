<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return success($request->user());
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['sometimes', 'min:2'],
            'password' => ['sometimes', 'string', 'min:6', 'confirmed'],
        ]);
        $request->user()->update($request->only([
            'name'
        ]));

        if ($request->password) {
            $request->user()->password = bcrypt($request->password);
            $request->user()->save();
        }

        return success($request->user()->refresh());
    }
}
