<x-mail::message>
Verifikacija naloga

Zdravo {{ $user->firstName }},

Hvala ti na registraciji na {{ config('app.name') }} 
Da bi aktivirao svoj nalog, potrebno je da potvrdiš email adresu klikom na dugme ispod.

<x-mail::button :url="$verificationUrl">
Potvrdi email
</x-mail::button>

Ako nisi napravio nalog, slobodno ignoriši ovaj mejl.

Pozdrav,<br>
{{ config('app.name') }}
</x-mail::message>