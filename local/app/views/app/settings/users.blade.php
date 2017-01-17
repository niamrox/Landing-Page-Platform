@extends('../app.layouts.partial')

@section('content')
    <ul class="breadcrumb breadcrumb-page">
        <div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
        <li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('global.settings') }}</li>
        <li class="active">{{ trans('global.team_management') }}</li>
    </ul>

    <div class="page-header">
        <div class="row">
            <h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-users page-header-icon"></i> {{ trans('global.team_members') }}</h1>
            <div class="col-xs-12 col-sm-8">
                <div class="row">
                    <hr class="visible-xs no-grid-gutter-h">

                    <div class="pull-right col-xs-12 col-sm-auto"><a href="#/user/" class="btn btn-primary btn-labeled" style="width: 100%;"><span class="btn-label icon fa fa-plus"></span> {{ trans('global.add_team_member') }}</a></div>
                    <div class="visible-xs clearfix form-group-margin"></div>

                    <form action="" class="pull-right col-xs-12 col-sm-6">
                        <div class="input-group no-margin">
                            <span class="input-group-addon" style="border:none;background: #fff;background: rgba(0,0,0,.05);"><i class="fa fa-search"></i></span>
                            <input type="text" id="search_grid" placeholder="{{ trans('global.search_') }}" class="form-control no-padding-hr" style="border:none;background: #fff;background: rgba(0,0,0,.05);">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="grid">
<?php
foreach($users as $user)
{
	$sl = \App\Core\Secure::array2string(array('user_id' => $user->id));
    $username = ($user->first_name != '' || $user->last_name != '') ? $user->first_name . ' ' . $user->last_name : $user->username;
    $last_login = ($user->last_login != NULL) ? $user->last_login->timezone($user->timezone)->format("Y-m-d H:i:s") : trans('global.never');
	$roles = $user->getRolesString();

    if($user->confirmed == 1)
    {
        if($user->parent_id == NULL)
        {
            $class = 'panel-success';
        }
        else
        {
            $class = 'panel-info';
        }
    }
    else
    {
        $class = 'panel-danger';
    }
?>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
            <div class="panel panel-dark widget-profile {{ $class }}">
                <div class="panel-heading">
                    <a href="#/user/{{ $sl }}">
                        <div class="widget-profile-bg-icon"><i class="fa fa-user"></i></div>
                        <img src="{{ App\Controller\AccountController::getAvatar(128, 'ffffff', $sl) }}" alt="" class="widget-profile-avatar">
                    </a>
                    <div class="widget-profile-header">
                        <span class="ellipsis-oneline">{{ $username }}</span><br>
                        <a href="mailto:{{ $user->email }}" class="ellipsis-oneline">{{ $user->email }}</a>
						<div class="user-role">{{ $roles }}</div>
                    </div>
                </div>
                <div class="widget-profile-counters">
                    <div class="col-xs-4"><span>{{ $user->logins }}</span><br>{{ trans('global.logins') }}</div>
                    <div class="col-xs-4"><span>{{ trans('global.last') }}</span><br><div data-moment="fromNowDateTime">{{ $last_login }}</div></div>
                    <div class="col-xs-4"><div>
                        <a href="#/user/{{ $sl }}" class="btn btn-default btn-xs" data-toggle="tooltip" title="{{ trans('global.edit_user') }}"><i class="fa fa-pencil fa-1x"></i></a>
<?php if($user->parent_id != NULL) { ?>
                        <a href="javascript:void(0);" onclick="_confirm('{{ url('/api/v1/account/delete-user') }}', '{{ $sl }}', 'GET', userDeleted);" class="btn btn-danger btn-xs" data-toggle="tooltip" title="{{ trans('global.delete_user') }}"><i class="fa fa-trash fa-1x"></i></a>
<?php } ?>
                    </div></div>
                </div>
                <br style="clear:both">
            </div>
        </div>
<?php
}
?>
	</div>
<script>
$('#grid').liveFilter('#search_grid', 'div.col-xs-12', {
  filterChildSelector: '.widget-profile-header'
});

function userDeleted()
{
	angular.element($('#content-wrapper')).injector().get("$route").reload();
}
</script>
@stop