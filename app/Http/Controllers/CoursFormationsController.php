<?php

namespace App\Http\Controllers;

use App\Models\Cours_formations;
use Illuminate\Http\Request;

class CoursFormationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Cours_formations::all();
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
        if (Cours_formations::where('cour_id',$request->cour_id)->where('formation_id',$request->formation_id)->count() != 0) {
            return response()->json(['error' => 'Ce cours se trouve déjà dans cette formation'], 403);
        }
        else{
            return Cours_formations::create($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cours_formations  $cours_formations
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cours_formations  $cours_formations
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Cours_formations::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cours_formations  $cours_formations
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cours_formations = Cours_formations::findOrFail($id);
        $cours_formations->update($request->all());
        return $cours_formations;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cours_formations  $cours_formations
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cours_formations = Cours_formations::findOrFail($id);
        $cours_formations->delete();

        return 204;
    }
}
