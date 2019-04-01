<?php

namespace App\Http\Controllers;

use http\Env\Response;
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
        ->join('users', 'predaje.user_id', '=', 'users.id');


      if ($user->role !== 'admin') {
        $predmets->WHERE('users.id', '=', $user->id);
      }

      $predmets->select('predmet.id', 'predmet.name', 'users.firstName', 'users.lastName');

      $result = $predmets->get();

      return response()->json($result);
    }

    protected function profesorPredaje($predmetId) {
      $user = JWTAuth::parseToken()->authenticate();

      if ($user->role === 'admin') {
        return true;
      }

      $predmets = DB::table('predmet')
        ->join('predaje', 'predmet.id', '=', 'predaje.predmet_id')
        ->join('users', 'predaje.user_id', '=', 'users.id')
        ->WHERE([
          ['users.id', '=', $user->id],
          ['predmet.id', '=', $predmetId],
        ])->select('predmet.id')
        ->get();

      return count($predmets) > 0;
    }

    public function studentiPredmeta($id)
    {
      $predmet = $this->profesorPredaje($id);
      if(!$predmet) {
        return response()->json(['error'=> 'Nema dostupnog predmeta za ovog profesora'], 404);
      }

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
