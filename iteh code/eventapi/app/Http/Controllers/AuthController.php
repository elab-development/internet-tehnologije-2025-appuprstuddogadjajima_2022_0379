<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyMail;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Illuminate\Support\Facades\Password as FacadesPassword;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str as FacadesStr;
use Illuminate\Support\Facades\RateLimiter as FacadesRateLimiter;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Support\Facades\URL as FacadesURL;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\Password as PasswordFacade;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    //
    //POST /api/register

    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Registracija korisnika",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"firstName","lastName","email","password","password_confirmation"},
 *             @OA\Property(property="firstName", type="string", example="Stefan"),
 *             @OA\Property(property="lastName", type="string", example="Stefanović"),
 *             @OA\Property(property="email", type="string", format="email", example="stefan@gmail.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Uspešna registracija"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Greška validacije"
 *     )
 * )
 */
    public function register(Request $request)
    {
        //

        $validator = Validator::make($request->all(), [
            //'name' => 'required|string|max:255',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
          //  'role' => 'required|string|in:STUDENT,ORGANIZATOR,ADMINISTRATOR',
            //'isActive' => 'required|boolean',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
      $user = User::create([
    'firstName' => $data['firstName'],
    'lastName'  => $data['lastName'],
    'email'     => $data['email'],
    'password'  => bcrypt($data['password']),

    'role'     => 'STUDENT',
    'isActive' => true,
]);

        $url = URL::temporarySignedRoute(
            'verification.verify', 
            now()->addMinutes(60), 
            ['id' => $user->id]
        );
        Mail::to($user->email)->send(new VerifyMail($user, $url));


       //  $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
        'message' => 'Registracija je uspesna',
        'user' => $user,
        //'token' => $token
        ], 201);
      


    }
    //POST /api/login
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Prijava korisnika",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="stefan@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Uspešna prijava",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Uspesna prijava"),
     *             @OA\Property(property="token", type="string", example="1|abc123def456"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="firstName", type="string", example="Stefan"),
     *                 @OA\Property(property="lastName", type="string", example="Stefanović"),
     *                 @OA\Property(property="email", type="string", format="email", example="stefan@gmail.com"),
     *                 @OA\Property(property="role", type="string", example="STUDENT"),
     *                 @OA\Property(property="isActive", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neispravni kredencijali"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Greška validacije"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }



        if(!Auth::attempt($validator->validated())){
            return response()->json([
                'message' => 'Neispravna email ili lozinka'
            ], 401);
        }


        $user = Auth::user();



        //$data = $validator->validated();
        //$user = User::where('email', $data['email'])->first();

       /* if(!$user || !Hash::check($data['password'], $user->password)){
            return response()->json([
                'message' => 'Neispravna email ili lozinka'
            ], 401);
        }*/


        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
            'message' => 'Uspesna prijava',
            'user' => $user,
            'token' => $token
        ], 200);    



    }

    //POST /api/logout
    
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Odjava korisnika",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Uspešna odjava",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Uspesna odjava")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neautorizovan pristup"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Uspesna odjava'
        ], 200);


    }

    //GET /api/me
     /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Podaci o trenutno ulogovanom korisniku",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Podaci o korisniku",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="firstName", type="string", example="Stefan"),
     *                 @OA\Property(property="lastName", type="string", example="Stefanović"),
     *                 @OA\Property(property="email", type="string", format="email", example="stefan@gmail.com"),
     *                 @OA\Property(property="role", type="string", example="STUDENT"),
     *                 @OA\Property(property="isActive", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neautorizovan pristup"
     *     )
     * )
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ], 200);
    }


    
    /**
     * @OA\Get(
     *     path="/api/email/verify/{id}",
     *     summary="Verifikacija email adrese",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID korisnika",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email uspešno verifikovan"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nevažeći verifikacioni link"
     *     )
     * )
     */
    public function verifyEmail(Request $request, $id)
    {

        if(!$request->hasValidSignature()) {
            return response()->json(['message' => 'Nevažeći verifikacioni link.'], 401);
        }

        $user = User::findOrFail($id);
        if($user->email_verified_at) {
            return response()->json(['message' => 'Email već verifikovan.'], 200);
        }
        $user->email_verified_at = now();
        $user->save();
        return response()->json(['message' => 'Email uspešno verifikovan.'], 200);






}
}