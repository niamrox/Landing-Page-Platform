@extends('../app.layouts.partial')

@section('content')

        <ul class="breadcrumb breadcrumb-page">
            <div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
            <li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
            <li class="active">{{ trans('global.intro') }}</li>
        </ul>

        <div class="page-header">
            <div class="row">
                <h1 class="col-xs-12 col-sm-7 text-center text-left-sm" style="height:32px"><i class="fa fa-info-circle page-header-icon"></i> {{ trans('global.welcome_user', ['name' => $username]) }}</h1>
            </div>
        </div>

@stop