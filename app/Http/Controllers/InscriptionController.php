<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inscription::all();
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
        if (Inscription::where('user_id',$request->user_id)->where('formation_id',$request->formation_id)->count() != 0) {
            return response()->json(['error' => 'Une demande existe déjà pour cette formation pour cet utilisateur'], 403);
        }
        elseif (Inscription::where('user_id',$request->user_id)->where('cours_id',$request->cours_id)->count() != 0) {
            return response()->json(['error' => 'Une demande existe déjà pour ce cours pour cet utilisateur'], 403);
        }
        else{
            return Inscription::create($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inscription  $inscription
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Inscription::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inscription  $inscription
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
     * @param  \App\Models\Inscription  $inscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $inscription = Inscription::findOrFail($id);
        $inscription->update($request->all());
        return $inscription;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inscription  $inscription
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inscription = Inscription::findOrFail($id);
        $inscription->delete();

        return 204;
    }
    /**
     * Searh  specific resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cours  $cours
     * @return \Illuminate\Http\Response
     */
    public function Search(Request $request)
    {
        $mesformations= DB::table('users')
                                ->join('inscriptions','users.id','inscriptions.user_id')
                                ->join('formations','formations.id','inscriptions.formation_id')
                                ->select('users.*','users.id as idetudiant','formations.id as idformation',
                                'formations.nom as titreformation','inscriptions.id as idinscription','inscriptions.is_valider');
        $suite = '';
        if ($request->nom) {
            $mesformations = $mesformations->where("users.nom","like",'%'.$request->nom.'%');
        }
        if ($request->idformation) {
            $mesformations = $mesformations->where("formations.id",$request->idformation);
        }
        if (strlen($request->statut) != 0) {
            $mesformations = $mesformations->where("is_valider",'=',$request->statut);
        }
        $mesformations = $mesformations->get();
        return $mesformations;
    }
}
