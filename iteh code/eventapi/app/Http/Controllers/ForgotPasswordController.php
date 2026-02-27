<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $validator->validated()['email'];

        // Ne otkrivamo da li user postoji (security), ali ipak samo ako postoji šaljemo mail
        $user = User::where('email', $email)->first();
if ($user) {
            // 1) generiši token
            $token = Str::random(60);

            // 2) upiši/overwrite u bazu
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now(),
                ]
            );

            // 3) url za frontend
            $resetUrl = config('app.frontend_url') . '/password-reset?token=' . $token . '&email=' . urlencode($email);

            // 4) pošalji mail
            Mail::to($email)->send(new PasswordResetMail($user, $resetUrl, $token));
        }

        return response()->json([
            'message' => 'Ako nalog postoji, poslaćemo email sa linkom za resetovanje lozinke.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (!$record || !Hash::check($data['token'], $record->token)) {
            return response()->json(['message' => 'Nevažeći token.'], 400);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'Token je istekao.'], 400);
        }

        $user = User::where('email', $data['email'])->firstOrFail();
        $user->password = Hash::make($data['password']);
        $user->save();
DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return response()->json(['message' => 'Lozinka je uspešno resetovana.'], 200);
    }
}