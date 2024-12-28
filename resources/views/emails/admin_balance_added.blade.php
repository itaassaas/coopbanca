@component('mail::message')
# Saldo Agregado a Cuenta de Usuario

Se ha agregado saldo a la siguiente cuenta:

**Usuario:** {{ $user->name }}  
**Email:** {{ $user->email }}  
**Monto:** {{ $amount }} {{ $currency }}  
**Nuevo Saldo:** {{ $user->balance }} {{ $currency }}

Saludos,<br>
{{ config('app.name') }}
@endcomponent