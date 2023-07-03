<x-mail::message>
# Hello<span class="splash">, </span>{{$name}}

You are receiving this e-mail because we received a request to reset your Nonverse account password.
This link will expire in 60 minutes

<x-mail::button :url="$url">
Reset password
</x-mail::button>

If you did not make this request, no further action is required

With love,<br>
Nonverse Studios
</x-mail::message>
