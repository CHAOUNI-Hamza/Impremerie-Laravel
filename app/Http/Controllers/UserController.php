<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', User::class);
        $query = $request->input('search'); 

        $users = User::orderBy('created_at', 'desc')
        ->where( 'last_name' , 'like' , '%'. $query . '%' )
            ->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {

        $user = new User($request->except(['password']));
        $user->slug = Str::slug($request->input('first_name') . '-' . $request->input('last_name') . '-' . $request->input('email'));
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $user)
    {
        try {
            $user = User::findOrFail($user);
            $user->fill($request->except(['password']));

            if ($request->filled('password')) {
                $user->password = bcrypt($request->input('password'));
            }

            $user->save();

            return new UserResource($user);

        } catch (ModelNotFoundException $e) {
            // L'utilisateur avec l'ID spécifié n'a pas été trouvé
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                // Code d'erreur spécifique pour la violation de la contrainte unique
                return response()->json(['messageCode' => $errorCode], 422);
            }
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        $user->restore();
        return new UserResource($user);
    }
}
