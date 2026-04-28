<?php

namespace App\Http\Controllers;

use App\Models\UserFace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaceController extends Controller
{
    public function enroll(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|array|size:128',
        ]);

        UserFace::updateOrCreate(
            ['user_id' => Auth::id()],
            ['descriptor' => $request->descriptor]
        );

        return response()->json([
            'message' => 'Face registered',
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|array|size:128',
        ]);

        $face = UserFace::where('user_id', auth()->id())->firstOrFail();

        $distance = $this->euclidean(
            $request->descriptor,
            $face->descriptor
        );

        return response()->json([
            'match' => $distance < 0.5,
            'distance' => $distance,
        ]);
    }

    private function euclidean($a, $b)
    {
        $sum = 0;

        foreach ($a as $i => $v) {
            $sum += pow($v - $b[$i], 2);
        }

        return sqrt($sum);
    }
}
