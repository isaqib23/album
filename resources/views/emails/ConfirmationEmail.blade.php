@component('mail::message')
<h1>Hey {{$body['name']}},</h1>
<p>
Welcome to the Secret Albums community!  Looks like you’ve decided it’s a good time to share your private, personal memories with your favourite people only</p>
<p>To confirm your email address, please click on the following button:
@component('mail::button', ['url' => $body['url']])
    Confirm Email
@endcomponent</p>

<p>
Before you login, check out our Quick Tour video to help you get started and take advantage of our Help Site to make sure you use all of the Secret Albums features.
</p>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 20px; width: 100%">
    <tbody>
    <tr>
        <td align="center">
            <table style="width: 100%" role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td style="text-align: center; width: 33%"> <a href="#" target="_blank">Login</a> </td>
                    <td style="text-align: center; width: 33%"> <a href="#" target="_blank">Quick Tour</a> </td>
                    <td style="text-align: center; width: 33%"> <a href="#" target="_blank">Help Site</a> </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>

We love hearing from you so feel free to get in touch with feedback, questions, and support.<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
