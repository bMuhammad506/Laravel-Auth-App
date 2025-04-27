<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function check(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => "Hey {$user->name} ({$user->email}) — congratulations, your precious little token actually worked! 🎉😂",
        ], 200);
    }
}
