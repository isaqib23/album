@component('mail::message')
<h1>Hey {{$body['name']}},</h1>
<p>
We understand you would like to reset the password for your Secret Albums account.
</p>
<p>Click the button below to reset it:
@component('mail::button', ['url' => $body['url']])
Reset Password
@endcomponent</p>

<p>
If you did not request a password reset, please ignore this email or reply to let us know.
</p>
<p>
P.S. We love hearing from you so feel free to get in touch with feedback, questions, and support.
</p>

<p>Try this link if the password reset button isn’t working:
<a href="{{$body['url']}}" target="_blank">{{$body['url']}}</a>
</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
