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

    protected function ifProfesorPredaje($predmetId) {
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
    public function studentiPredmeta($predmetId)
    {
      $predmet = $this->ifProfesorPredaje($predmetId);
      if(!$predmet) {
        return response()->json(['error'=> 'Nema dostupnog predmeta za ovog profesora'], 404);
      }

      $studenti = DB::table('users')
        ->join('pohadja', 'users.id', '=', 'pohadja.user_id')
        ->join('predmet', 'pohadja.predmet_id', '=' ,'predmet.id')
        ->WHERE([
          ['users.role', '=', 'student'],
          ['predmet.id', '=', $predmetId],
        ])
        ->select('users.id as uid','users.firstName', 'users.lastName','predmet.name as predmet', 'pohadja.id as pohadjaId')
        ->get();

      return response()->json($studenti);
    }

    // upis ocena za predmet
    public function getOcene($pohadjaId)
    {
      $ocene = DB::table('users')
        ->join('pohadja', 'users.id', '=', 'pohadja.user_id')
        ->join('ocene', 'pohadja.id', '=','ocene.pohadja_id')
        ->join('predmet', 'pohadja.predmet_id', '=' ,'predmet.id')
        ->WHERE('pohadja.id', '=', $pohadjaId)
        ->select('users.id as uid', 'users.firstName', 'users.lastName', 'predmet.name as predmet', 'ocene.ocena', 'pohadja.predmet_id')
        ->get();

      if(count($ocene) === 0) {
        return response()->json([], 204);
      }

      $result['firstName'] = $ocene[0]->firstName;
      $result['lastName'] = $ocene[0]->lastName;
      $result['predmet'] = $ocene[0]->predmet;
      $result['uid'] = $ocene[0]->uid;
      $result['ocene'] = [];
      foreach($ocene as $key => $value) {
        array_push($result['ocene'], $value->ocena);
      }
      return response()->json($result);
    }

    public function getPredmeteOcena($studentId)
    {
      $ocene = DB::table('users')
        ->join('pohadja', 'users.id', '=', 'pohadja.user_id')
        ->join('ocene', 'pohadja.id', '=','ocene.pohadja_id')
        ->join('predmet', 'pohadja.predmet_id', '=' ,'predmet.id')
        ->WHERE('users.id', '=', $studentId)
        ->select('users.firstName', 'users.lastName', 'predmet.name as predmet', 'ocene.ocena')
        ->get();

      if(count($ocene) === 0) {
        return response()->json([], 204);
      }


      $result['firstName'] = $ocene[0]->firstName;
      $result['lastName'] = $ocene[0]->lastName;
      $result['predmeti'] = [];

      foreach($ocene as $key => $value) {

        if (isset($result['predmeti'][$value->predmet])) {
          array_push($result['predmeti'][$value->predmet], $value->ocena);
        } else {
          $result['predmeti'][$value->predmet] = [];
          array_push($result['predmeti'][$value->predmet], $value->ocena);
        }
      }
      return response()->json($result);
    }

    public function isStudentPohadja($uid, $pohadja_id) {
      $userPohadja = DB::table('users')
        ->join('pohadja', 'users.id', '=', 'pohadja.user_id')
        ->join('predmet', 'pohadja.predmet_id', '=' ,'predmet.id')
        ->WHERE([
          ['pohadja.id','=', $pohadja_id],
          ['users.id', '=', $uid],
        ])
        ->select('users.id as uid, users.firstName', 'users.lastName', 'predmet.name as predmet')
        ->get();
      return $userPohadja;

//      return count($userPohadja) > 0;
    }

    public function oceni(Request $request)
    {
        $enumOcene = [1,2,3,4,5];

        $uid = $request->get('uid');
        $pohadjaId = $request->get('pohadja_id');
        $ocena = $request->get('ocena');

        // validacija ocene
        if (!in_array($ocena, $enumOcene)) {
          return response()->json(['error' => 'ocena mora da bude izmedju 1 i 5'], 400);
        }

        // validacija da li student slusa predmet
        $ifPohadja = $this->isStudentPohadja($uid,$pohadjaId);

        if (count($ifPohadja) === 0) {
          return response()->json(['error' => 'student ne pohadja predmet'], 403);
        }

        $result = DB::table('ocene')->insert([
          'pohadja_id' => $pohadjaId,
          'ocena' => $ocena,
        ]);

        if ($result) {
          $out['firstName'] = $ifPohadja[0]->firstName;
          $out['lastName'] = $ifPohadja[0]->lastName;
          $out['predmet'] = $ifPohadja[0]->predmet;
          $out['ocena'] = $ocena;
          return response()->json($out, 201);
        } else {
          return response()->json(['error' => 'Doslo je do serverske greske'], 500);
        }
    }
}
