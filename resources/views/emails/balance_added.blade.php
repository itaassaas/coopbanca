@component('mail::message')
# Saldo Agregado

Estimado {{ $user->name }},

Se ha agregado {{ $amount }} {{ $currency }} a su cuenta.

Su nuevo saldo es: {{ $user->balance }} {{ $currency }}

Gracias,<br>
{{ config('app.name') }}
@endcomponent