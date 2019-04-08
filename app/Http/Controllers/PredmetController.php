<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use PhpParser\Node\Expr\Cast\Object_;

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

    // Return studente za predmet
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
        ->select('users.id','users.firstName', 'users.lastName','predmet.name as predmet', 'pohadja.id as pohadjaId')
        ->get();

      return response()->json($studenti);
    }

    // Ocene ucenika po predmetu

    // upis ocena za predmet
    public function getOcene($pohadjaId)
    {
      $ocene = DB::table('users')
        ->join('pohadja', 'users.id', '=', 'pohadja.user_id')
        ->join('predmet', 'pohadja.predmet_id', '=' ,'predmet.id')
        ->join('ocene', 'pohadja.predmet_id', '=','ocene.pohadja_id')
        ->WHERE('pohadja.id', '=', 2)
        ->select('users.firstName', 'users.lastName', 'predmet.name as predmet', 'ocene.ocena')
        ->get();
      
      $result['firstName'] = $ocene[0]->firstName;
      $result['lastName'] = $ocene[0]->lastName;
      $result['predmet'] = $ocene[0]->predmet;
      $result['ocene'] = [];
      foreach($ocene as $key => $value) {
        array_push($result['ocene'], $value->ocena);
      }
      return response()->json($result);
    }
}
