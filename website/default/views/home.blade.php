@extends('layouts.master')

@section('content')

<!-- Header -->
    <header id="header" class="alt">
        <nav id="nav">
            <ul>
                <li class="special">
                    <a href="#" class="menuToggle"><span>{{ trans('website::global.txt_menu') }}</span></a>
                    <div id="menu">
                        <ul>
                            <li><a href="{{ url('/login') }}">{{ trans('website::global.txt_login') }}</a></li>
                            <li><a href="{{ url('/signup') }}">{{ trans('website::global.txt_create_an_account') }}</a></li>
                            <li><a href="{{ url('/forgot_password') }}">{{ trans('website::global.txt_forgot_password') }}</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

<!-- Banner -->
    <section id="banner">
        <div class="inner">
            <h2>{{ trans('website::global.area_app_title') }}</h2>
            <p>{{ trans('website::global.area_app_title_slogan') }}</p>
            <ul class="actions">
                <li><a href="{{ url('/platform') }}" class="button special">{{ trans('website::global.txt_login') }}</a></li>
                <li><a href="{{ url('/signup') }}" class="button">{{ trans('website::global.txt_create_an_account') }}</a></li>
            </ul>
        </div>
    </section>

@stop