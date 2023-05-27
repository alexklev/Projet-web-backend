<?php

namespace App\Http\Controllers;

use App\Models\Connexion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $utilisateur = User::all();
        foreach ($utilisateur as $key => $value) {
            //echo hash('sha256', $value->password);
            $value->password = hash('sha256', $value->password);
            $value->lastconnexion = Connexion::where('user_id',$value->id)->orderBy('created_at', 'desc')->first();
            $value->formations = DB::table('formations')
                                ->join('inscriptions', 'formations.id', '=', 'inscriptions.formation_id')
                                ->select('formations.*', 'inscriptions.created_at as dateinscription')
                                ->where('inscriptions.user_id',$value->id)
                                ->where('inscriptions.is_valider',1)
                                ->get();
        }
        return $utilisateur;
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
        if (User::where('email', '=', $request->email)->first()) {
            return response()->json(['error' => 'Cet email existe déjà'], 403);
        }
        elseif (User::where('identifiant', '=', $request->identifiant)->first()) {
            return response()->json(['error' => 'Cet identifiant existe déjà'], 403);
        }
        else{
            $utilisateur = User::create($request->all());
            DB::table('connexions')->insert(
                array(
                       'user_id' => $utilisateur->id,
                       'date_connexion' => date('Y-m-d H:i:s')
                )
            );
            $utilisateur->lastconnexion = Connexion::where('user_id',$utilisateur->id)->orderBy('created_at', 'desc')->first();
            if ($image = $request->file('photo')) {
                $destinationPath = 'images/users';
                $image->move($destinationPath,$utilisateur->id.'_'. $image->getClientOriginalName());
                $utilisateur->photo = $utilisateur->id.'_'.$image->getClientOriginalName();
                $utilisateur->save();
            }
            $utilisateur->password = hash('sha256', $utilisateur->password);
            return  $utilisateur;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $utilisateur = User::find($id);
        $utilisateur->lastpassword = $utilisateur->password;
        $utilisateur->password = hash('sha256', $utilisateur->password);
        $utilisateur->lastconnexion = Connexion::where('user_id',$utilisateur->id)->orderBy('created_at', 'desc')->first();
        return $utilisateur;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User $user
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
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $utilisateur = User::findOrFail($id);
        // var_dump($request->nom);
        $utilisateur->nom = $request->nom;
        $utilisateur->prenom = $request->prenom;
        $utilisateur->email = $request->email;
        $utilisateur->identifiant = $request->identifiant;
        $utilisateur->telephone = $request->telephone;
        $utilisateur->nationalite = $request->nationalite;
        $utilisateur->date_naissance = $request->date_naissance;
        $utilisateur->password = $request->nationalite;
        $utilisateur->role = $request->role;
        $utilisateur->save();
        if ($image = $request->file('photo')) {
            $destinationPath = 'images/users';
            $image->move($destinationPath,$utilisateur->id.'_'. $image->getClientOriginalName());
            $utilisateur->photo = $utilisateur->id.'_'.$image->getClientOriginalName();
            $utilisateur->save();
        }
        $utilisateur->lastconnexion = Connexion::where('user_id',$utilisateur->id)->orderBy('created_at', 'desc')->first();
        $utilisateur->formations = DB::table('formations')
                                ->join('inscriptions', 'formations.id', '=', 'inscriptions.formation_id')
                                ->select('formations.*', 'inscriptions.created_at as dateinscription')
                                ->where('inscriptions.user_id',$utilisateur->id)
                                ->where('inscriptions.is_valider',1)
                                ->get();
        $utilisateur->lastpassword = $utilisateur->password;
        $utilisateur->password = hash('sha256', $utilisateur->password);
        return $utilisateur;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $utilisateur = User::findOrFail($id);
        $utilisateur->delete();

        return 204;
    }


    /**
     * Show the specified resource with credential put.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('identifiant', 'password');
        $identifiant=$credentials["identifiant"];
        $password=$credentials["password"];
        if($identifiant==null){
            return response()->json(['error' => 'Identifiant obligatoire'], 403);
        }
        elseif($password==null){
            return response()->json(['error' => 'Mot de passe obligatoire'], 403);
        }else{
            $user = User::where('identifiant', '=', $identifiant)->where('password', '=', $password)->first();
            if(!$user){
                return response()->json(['error' => 'Authentification erroné'], 401);
            }
            else{
                if ($user->role == 'banni') {
                    return response()->json(['error' => 'Vous n\'avez plus acces au site'], 401);
                }
                else{
                    DB::table('connexions')->insert(
                        array(
                               'user_id' => $user->id,
                               'date_connexion' => date('Y-m-d H:i:s')
                        )
                    );
                    $user->lastconnexion = Connexion::where('user_id',$user->id)->orderBy('created_at', 'desc')->first();
                    $user->password = hash('sha256', $user->password);
                    $user->formations = DB::table('formations')
                                            ->join('inscriptions', 'formations.id', '=', 'inscriptions.formation_id')
                                            ->select('formations.*', 'inscriptions.created_at as dateinscription')
                                            ->where('inscriptions.user_id',$user->id)
                                            ->where('inscriptions.is_valider',1)
                                            ->get();
                    return  $user;
                }
            }
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
        $mesformations= DB::table('users')
        ->select('users.*');
        $suite = '';
        if ($request->elt) {
            $mesformations = $mesformations->where("nom","like",'%'.$request->elt.'%')
                            ->orWhere("prenom","like",'%'.$request->elt.'%')
                            ->orWhere("nationalite","like",'%'.$request->elt.'%')
                            ->orWhere("email","like",'%'.$request->elt.'%')
                            ->orWhere("telephone","like",'%'.$request->elt.'%')
                            ->orWhere("identifiant","like",'%'.$request->elt.'%');
        }
        if ($request->role) {
            $mesformations = $mesformations->where("role",$request->role);
        }
        $mesformations = $mesformations->get();
        foreach ($mesformations as $value) {
            $value->lastpassword = $value->password;
            $value->password = hash('sha256', $value->password);
            $value->lastconnexion = Connexion::where('user_id',$value->id)->orderBy('created_at', 'desc')->first();
        }
        return $mesformations;
    }
}
