<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Cours_formations;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Cours::all();
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
        $cours =  Cours::create($request->all());
        if($request->idformation){
            Cours_formations::where('cour_id',$cours->id)->delete();
            foreach ($request->idformation as $key => $value) {
                DB::insert("INSERT INTO `cours_formations`( `cour_id`, `formation_id`) VALUES (?,?)",[$cours->id,$value]);
            }
        }
        return $cours;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cours  $cours
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Cours::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cours  $cours
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
     * @param  \App\Models\Cours  $cours
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cours = Cours::findOrFail($id);
        $cours->update($request->all());
        if($request->idformation){
            Cours_formations::where('cour_id',$cours->id)->delete();
            foreach ($request->idformation as $key => $value) {
                DB::insert("INSERT INTO `cours_formations`( `cour_id`, `formation_id`) VALUES (?,?)",[$cours->id,$value]);
            }
        }
        return $cours;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cours  $cours
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Cours_formations::where('cour_id',$id)->count() == 0) {
            $cours = Cours::findOrFail($id);
            $cours->delete();

            return 204;
        }
        else{
            return response()->json(['error' => 'Ce cour est lié à des formations'], 401);
        }
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
        $mesformations= DB::table('cours')
        ->select('cours.*');
        $suite = '';
        if ($request->nom) {
            $mesformations = $mesformations->where("nom","like",'%'.$request->nom.'%');
        }
        if (strlen($request->statut) != 0) {
            $mesformations = $mesformations->where("actif",'=',$request->statut);
        }
        if ($request->idutilisateur) {
            $mesformations = $mesformations->whereIn('id', function($query) use($request){
                                $query->select('cour_id')
                                ->from(with(new Inscription)->getTable())
                                ->where('user_id', $request->idutilisateur);
                            });
        }
        if ($request->idformation) {
            $mesformations = $mesformations->whereIn('id', function($query) use($request){
                                $query->select('cour_id')
                                ->from(with(new Cours_formations)->getTable())
                                ->where('formation_id', $request->idformation);
                            });
        }
        $mesformations = $mesformations->get();
        foreach ($mesformations as $value) {
            $value->inscrits = DB::table('users')
                                ->join('inscriptions', 'users.id', '=', 'inscriptions.user_id')
                                ->select('users.*', 'inscriptions.created_at as dateinscription')
                                ->where('inscriptions.cours_id',$value->id)
                                ->get();
            $value->formation = DB::table('formations')
                            ->join('cours_formations', 'formations.id', '=', 'cours_formations.formation_id')
                            ->select('formations.*','cours_formations.id as idcoursformation')
                            ->where('cours_formations.cour_id',$value->id)
                            ->get();
        }
        return $mesformations;
    }
}
