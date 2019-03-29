<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class PredmetController extends Controller
{
    public function predaje()
    {
      $user = JWTAuth::parseToken()->authenticate();

      $predmets = DB::table('predmet')
        ->join('predaje', 'predmet.id', '=', 'predaje.predmet_id')
        ->join('users', 'predaje.user_id', '=', 'users.id')
        ->WHERE('users.id', '=', 3)
        ->select('predmet.id', 'predmet.name')
        ->get();

      return response()->json($predmets);
    }

    public function studentiPredmeta($id)
    {
      $studenti = DB::table('users')
        ->join('pohadja', 'users.id', '=', 'pohadja.user_id')
        ->join('predmet', 'pohadja.predmet_id', '=' ,'predmet.id')
        ->WHERE([
          ['users.role', '=', 'student'],
          ['predmet.id', '=', $id],
        ])
        ->get();

      return response()->json($studenti);
    }
}
