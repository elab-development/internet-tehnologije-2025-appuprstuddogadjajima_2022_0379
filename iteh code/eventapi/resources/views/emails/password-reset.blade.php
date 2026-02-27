<x-mail::message>
Resetovanje lozinke 
Zdravo {{ $user->name ?? $user->email }},

Kliknite na dugme ispod da resetujete lozinku:

<x-mail::button :url="$resetUrl">
Resetuj lozinku
</x-mail::button>

Tvoj token za resetovanje lozinke je: 
**{{ $token }}**

Ako niste tražili resetovanje lozinke, slobodno ignorišite ovaj mejl.

Hvala,<br>
{{ config('app.name') }}
</x-mail::message>