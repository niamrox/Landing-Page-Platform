@extends('../app.layouts.partial')

@section('content')
    <ul class="breadcrumb breadcrumb-page">
        <div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
        <li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
        <li>{{ trans('admin.website') }}</li>
        <li class="active">{{ trans('admin.templates') }}</li>
    </ul>

    <div class="page-header">
        <div class="row">
            <h1 class="col-xs-12 col-sm-7 text-center text-left-sm" style="height:32px"><i class="fa fa-paint-brush page-header-icon"></i> {{ trans('admin.templates') }}</h1>
			<div class="col-xs-12 col-sm-5">
				<div class="row">
					<hr class="visible-xs no-grid-gutter-h">

					<div class="pull-right col-xs-12 col-sm-auto">
						<div class="btn-group" style="width:100%;" id="tour-options">
							<button class="btn btn-primary dropdown-toggle" style="width:100%;" type="button" data-toggle="dropdown" tooltip="{{ trans('global.options') }}"><i class="icon fa fa-bars"></i> &nbsp; <i class="fa fa-caret-down"></i></button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu">
								<li><a href="javascript:void(0);" data-modal="{{ url('/app/admin/modal/website-settings') }}" tabindex="-1"><i class="fa fa-cogs"></i> &nbsp; {{ trans('admin.website_settings') }}</a></li>
							</ul>
						</div>
					</div>


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
foreach($templates as $name => $template)
{
	$sl = \App\Core\Secure::array2string(array('template_dir' => $template['dir']));

	$panel_class = ($template['dir'] == $active_template) ? 'panel-success panel-dark': 'panel-default';
	$icon_class = ($template['dir'] == $active_template) ? 'fa-check-square-o text-success': 'fa-square-o text-muted';
?>
	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 item" data-id="{{ $template['dir'] }}">
		<div class="panel {{ $panel_class }} widget-profile widget-profile-centered">
			<div class="panel-heading">
				<a href="#/admin/website/{{ $sl }}">
					<img src="{{ url('/website/' . $template['dir'] . '/assets/images/preview.jpg') }}" alt="{{ $name }}" class="widget-profile-avatar" style="width:100%;height:auto">
				</a>
				<div class="widget-profile-header">
					<span class="ellipsis-oneline">{{ $name }}</span>
				</div>
			</div>
			<div class="widget-profile-counters">
				<div class="col-xs-6 template-selector-container">
					<a href="javascript:void(0);" onclick="_confirm('{{ url('/api/v1/admin/activate-template') }}', '{{ $sl }}', 'GET', templateActivated);" class="template-selector" tooltip="{{ trans('admin.set_as_active') }}"><i class="fa fa-3x {{ $icon_class }}" style="margin-top:4px;"></i></a>
				</div>
				<div class="col-xs-6">
					<a href="#/admin/website/{{ $sl }}" class="btn btn-primary btn-xs" data-toggle="tooltip" title="{{ trans('admin.edit_template') }}"><i class="fa fa-pencil fa-1x"></i></a>
				</div>
			</div>
			<br style="clear:both">

		</div>
	</div>
<?php
}
?>
	</div>
<style type="text/css">
.fa-check-square-o {
	margin-left:4px;
}
.template-selector-container .tooltip.top {
  margin-top: -16px;
}
</style>
<script>
$('#grid').liveFilter('#search_grid', 'div.col-xs-12', {
  filterChildSelector: '.widget-profile-header'
});

$('.template-selector')
  .mouseenter(function() {
	var icon = $(this).find('.fa');
	if (! icon.hasClass('text-success'))
	{
		$(this).find('.fa').removeClass('fa-square-o');
		$(this).find('.fa').addClass('fa-check-square-o icon-changed');
	}
  })
  .mouseleave(function() {
	var icon = $(this).find('.fa');
	if (icon.hasClass('icon-changed'))
	{
    $(this).find('.fa').removeClass('icon-changed fa-check-square-o');
    $(this).find('.fa').addClass('fa-square-o');
	}
  });

function templateActivated(arg1, arg2, data)
{
	if (data.result == 'success')
	{
		angular.element($('#content-wrapper')).injector().get("$route").reload();
	}
	else
	{
		swal(data.msg);
	}
}
</script>
@stop