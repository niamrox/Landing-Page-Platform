@extends('emails.layouts.clean')

@section('content')

    <h2>{{ Lang::get('confide.email.account_created.subject') }}</h2>
    <p class="lead">{{ Lang::get('confide.email.account_created.greetings', array('name' => $name)) }},</p>
	<p>{{ Lang::get('confide.email.account_created.body') }}</p>
	<p>{{ Lang::get('confide.email.account_created.login_credentials', array('username' => $username, 'email' => $email, 'password' => $password)) }}</p>

    <p class="callout">
      <a href="{{{ URL::to("login") }}}">{{{ URL::to("login") }}}</a>
    </p>

	<p>{{ Lang::get('confide.email.account_created.farewell') }}</p>

@stop