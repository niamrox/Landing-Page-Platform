@extends('emails.layouts.clean')

@section('content')

    <h2>{{ Lang::get('confide.email.account_confirmation.subject') }}</h2>
    <p class="lead">{{ Lang::get('confide.email.account_confirmation.greetings', array('name' => $user->username)) }},</p>
	<p>{{ Lang::get('confide.email.account_confirmation.body') }}</p>

    <p class="callout">
      <a href="{{{ URL::to("confirm/{$user->confirmation_code}") }}}">{{{ URL::to("confirm/{$user->confirmation_code}") }}}</a>
    </p>

	<p>{{ Lang::get('confide.email.account_confirmation.farewell') }}</p>

@stop