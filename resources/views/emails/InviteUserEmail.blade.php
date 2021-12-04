@component('mail::message')
<h1>Hey!</h1>
<p>
<Strong>{{$body['name']}}</Strong> has invited to join the Secret Albums community and contribute to their Secret Album. Secret Albums is a mobile app that allows you to share your private and personal photo memories with your favourite people.
</p>
<p>
Before you Sign Up, learn more about Secret Albums by checking out our Quick Tour video and take advantage of our Help Site to make sure you use all of the Secret Albums features.
</p>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 20px; width: 100%">
<tbody>
<tr>
<td align="center">
<table style="width: 100%" role="presentation" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td style="text-align: center; width: 33%"> <a href="#" target="_blank">Sign up</a> </td>
<td style="text-align: center; width: 33%"> <a href="#" target="_blank">Quick Tour</a> </td>
<td style="text-align: center; width: 33%"> <a href="#" target="_blank">Help Site</a> </td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

<p>
We love hearing from you, so get in touch if you have any trouble signing up!
</p>
<p>
P.S. If you received more than one of these invitations to join a Secret Album, then you must be pretty special and important to people.
</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
