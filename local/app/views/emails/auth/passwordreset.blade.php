@extends('emails.layouts.clean')

@section('content')

    <h2>{{ Lang::get('confide.email.password_reset.subject') }}</h2>
    <p class="lead">{{ Lang::get('confide.email.password_reset.greetings', array('name' => $user['username'])) }},</p>
	<p>{{ Lang::get('confide.email.password_reset.body') }}</p>

    <p class="callout">
		<a href="{{ URL::to('/reset_password/' . $token) }}">{{ URL::to('/reset_password/' . $token)  }}</a>
    </p>

<p>{{ Lang::get('confide.email.password_reset.farewell') }}</p>

@stop