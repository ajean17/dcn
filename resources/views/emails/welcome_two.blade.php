@component('mail::message')
# Welcome to The Dream Catcher Network!

{{$user->name}}, We are so glad you joined the network.  You are well on your
way to find the connection that realized your business dream!

Click the Link below to get started.
@component('mail::button', ['url' => 'http://127.0.0.1:8000//'])
To Your Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
