<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller\authorize;
use Illuminate\Auth\Events\PasswordReset;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'store', 'forgotpassword', 'resetpassword', 'handleGoogleCallback', 'redirectToGoogle', 'handleFacebookCallback', 'redirectToFacebook']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Veuillez vérifier votre adresse e-mail ou votre mot de passe.'], 401);
        }

        return $this->respondWithToken($token);
    }

    // Redirection vers Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Callback de Google
    public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if the user already exists
        $user = User::where('email', $googleUser->getEmail())->first();
        
        if (!$user) {
            
            // Create a new user if not found
            $user = new User();
            $user->first_name = $googleUser['given_name'];
            $user->last_name = $googleUser['family_name'];
            $user->email = $googleUser->getEmail();
            $user->google_id = $googleUser->getId();
            $user->role = 'user';
            $user->slug = \Str::slug($user->first_name.'-'.$user->last_name.'-'.$user->email);
            $user->save();
        }

        Auth::login($user);
    if (! $token = auth()->login($user)) {
        return response()->json(['error' => 'Could not authenticate the user.'], 401);
    }

    return redirect()->to('http://localhost:5173/auth/google/callback?token=' . $token);

}
    
public function redirectToFacebook()
{
    return Socialite::driver('facebook')->stateless()->redirect();
}

public function handleFacebookCallback()
{
    $facebookUser = Socialite::driver('facebook')->stateless()->user();
    // Check if the user already exists
    $user = User::where('email', $facebookUser->getEmail())->first();


    if (!$user) {
            
        // Create a new user if not found
        $user = new User();
        $user->first_name = $facebookUser['name'];
        $user->email = $facebookUser->getEmail();
        $user->facebook_id = $facebookUser->getId();
        $user->role = 'user';
        $user->slug = \Str::slug($user->first_name.'-'.$user->last_name.'-'.$user->email);
        $user->save();
    }

    Auth::login($user);
if (! $token = auth()->login($user)) {
    return response()->json(['error' => 'Could not authenticate the user.'], 401);
}

return redirect()->to('http://localhost:5173/auth/facebook/callback?token=' . $token);
}

    
    public function store(StoreUserRequest $request)
    {

        $user = new User();

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->phone_number = $request->input('phone_number');
        $user->address = $request->input('address');
        $user->region = $request->input('region');
        $user->city = $request->input('city');
        $user->country = $request->input('country');
        $user->slug = Str::slug($request->input('first_name').'-'.$request->input('last_name').'-'.$request->input('email'));
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));

        $user->save();

        return new UserResource($user);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return new UserResource(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function forgotpassword(Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __('passwords.sent')], 200)
            : response()->json(['message' => __('passwords.user')], 400);

        /*$emailNotExists = User::where('email', $request->email)->exists();
    if(!$emailNotExists) {
        return response()->json(['emailNotExists' => "Cet email n'existe pas"], 409);
    }

        $request->validate([
            'email' => 'required|email',
        ]);
    
        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        if ($status == Password::RESET_LINK_SENT) {
            return [ 
                'status' => __($status)
            ];
        };
    
        throw ValidationException::withMessages([
            'email' => [trans($status)]
        ]);*/
    }

    public function resetpassword(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __('passwords.reset')], 200)
            : response()->json(['message' => __('passwords.token')], 400);
        /*$request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
    
                $user->tokens()->delete();
    
                event(new PasswordReset($user));
            }
        );
    
        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message'=> 'Réinitialisation du mot de passe réussie'
            ]);
        }
    
        return response([
            'error'=> 'Vérifiez vos informations'
        ], 500);*/
    }
}
