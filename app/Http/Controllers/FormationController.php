<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Formation::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // print_r($request->all());
        return Formation::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Formation  $formation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Formation::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Formation  $formation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Formation  $formation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $formation = Formation::findOrFail($id);
        $formation->update($request->all());
        return $formation;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Formation  $formation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Inscription::where('formation_id',$id)->count() == 0) {
            $formation = Formation::findOrFail($id);
            $formation->delete();

            return 204;
        }
        else{
            return response()->json(['error' => 'Des utilisateurs sont inscrits a cette formation'], 401);
        }
    }

    /**
     * Searh  specific resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Formation  $formation
     * @return \Illuminate\Http\Response
     */
    public function Search(Request $request)
    {
        $mesformations= DB::table('formations')
        ->select('formations.*');
        $suite = '';
        if ($request->nom) {
            $mesformations = $mesformations->where("nom","like",'%'.$request->nom.'%');
        }
        if ($request->niveau) {
            $mesformations = $mesformations->where("niveau",$request->niveau);
        }
        if (strlen($request->statut) != 0) {
            $mesformations = $mesformations->where("statut",$request->statut);
        }
        if ($request->idutilisateur) {
            $mesformations = $mesformations->whereIn('id', function($query) use($request){
                                $query->select('formation_id')
                                ->from(with(new Inscription)->getTable())
                                ->where('user_id', $request->idutilisateur);
                            });
        }
        if ($request->date) {
            $mesformations = $mesformations->where(DB::raw("(STR_TO_DATE(date_debut,'%M %d %Y'))"), ">=", $request->date)->where(DB::raw("(STR_TO_DATE(date_fin,'%M %d %Y'))"), "<=", $request->date);
        }
        $mesformations = $mesformations->get();
        foreach ($mesformations as $value) {
            $value->inscrits = DB::table('users')
                                ->join('inscriptions', 'users.id', '=', 'inscriptions.user_id')
                                ->select('users.*', 'inscriptions.created_at as dateinscription','inscriptions.is_valider')
                                ->where('inscriptions.formation_id',$value->id)
                                ->get();
            $value->cours = DB::table('cours')
                            ->join('cours_formations', 'cours.id', '=', 'cours_formations.cour_id')
                            ->select('cours.*','cours_formations.id as idcoursformation')
                            ->where('cours_formations.formation_id',$value->id)
                            ->where('cours.actif',1)
                            ->get();
        }
        return $mesformations;
    }
}
