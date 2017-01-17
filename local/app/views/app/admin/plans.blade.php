@extends('../app.layouts.partial')

@section('content')
    <ul class="breadcrumb breadcrumb-page">
        <div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
        <li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
        <li class="active">{{ trans('admin.user_plans') }}</li>
    </ul>

    <div class="page-header">
        <div class="row">
            <h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-trophy page-header-icon"></i> {{ trans('admin.user_plans') }}</h1>
            <div class="col-xs-12 col-sm-8">
                <div class="row">
                    <hr class="visible-xs no-grid-gutter-h">

                    <div class="pull-right col-xs-12 col-sm-auto"><a href="#/admin/plan/" class="btn btn-primary btn-labeled" style="width: 100%;"><span class="btn-label icon fa fa-plus"></span> {{ trans('admin.add_plan') }}</a></div>
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
foreach($plans as $plan)
{
	$sl = \App\Core\Secure::array2string(array('plan_id' => $plan->id));

	$settings = $plan->settings;
	if ($settings != '') $settings = json_decode($settings);

	$apps = (isset($settings->max_sites)) ? $settings->max_sites : 0;
	if ($apps == 0) $apps = trans('admin.unlimited');

	$monthly = (isset($settings->monthly)) ? $settings->monthly : 0;
	$annual = (isset($settings->annual)) ? $settings->annual : 0;
	$annual = str_replace('.00', '', number_format($annual * 12, 2));

	$currency = (isset($settings->currency)) ? $settings->currency : 'USD';
	$currencies = trans('currencies');
	$currency_symbol = $currencies[$currency][1];

    if($plan->undeletable == 1)
    {
        $class = 'panel-success';
    }
    else
    {
        $class = 'panel-info';
    }
?>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 item" data-id="{{ $plan->id }}">
            <div class="panel panel-dark widget-profile {{ $class }}">
                <div class="panel-heading">
                    <div class="widget-profile-bg-icon"><i class="fa fa-trophy"></i></div>
                    <div class="widget-profile-header" style="margin-top:0">
						<div class="drag-handle-vert"></div>
                        <span class="ellipsis-oneline" style="margin-top:4px;">{{ $plan->name }}</span><br>
                    </div>
                </div>
                <div class="widget-profile-counters">
                    <div class="col-xs-4"><span>{{ $currency_symbol . $monthly }}</span><br>{{ trans('admin.monthly') }}</div>
                    <div class="col-xs-4"><span>{{ $currency_symbol . $annual }}</span><br>{{ trans('admin.annual') }}</div>
                    <div class="col-xs-4"><div>
                        <a href="#/admin/plan/{{ $sl }}" class="btn btn-default btn-xs" data-toggle="tooltip" title="{{ trans('admin.edit_plan') }}"><i class="fa fa-pencil fa-1x"></i></a>
<?php if($plan->undeletable == 0) { ?>
                        <a href="javascript:void(0);" onclick="_confirm('{{ url('/api/v1/admin/delete-plan') }}', '{{ $sl }}', 'GET', planDeleted);" class="btn btn-danger btn-xs" data-toggle="tooltip" title="{{ trans('admin.delete_plan') }}"><i class="fa fa-trash fa-1x"></i></a>
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

$('#grid').sortable({
	items: '.item',
	handle: '.drag-handle-vert',
    tolerance: 'pointer', /* intersect */
    scrollSensitivity: 100,
    update: function (event, ui) {
        blockUI();
		var node = $(ui.item).attr('data-id');
		var node_prev = $(ui.item).prev('.item').attr('data-id');
		var node_next = $(ui.item).next('.item').attr('data-id');
        var sort = $(this).sortable('toArray', {attribute: 'data-id'});

        $.ajax({
            data: {node: node, node_prev: node_prev, node_next: node_next},
            type: 'POST',
            url: app_root + "/api/v1/admin/plan-sort"
        })
        .done(function(data) {
			showSaved();
        })
        .always(function() {
            unblockUI();
        });
    },
    /*
    containment: 'parent',*/
    distance: 5,
 	placeholder: {
        element: function(currentItem) {
			var width = parseInt($(currentItem).width()) - 5;
			var height = parseInt($(currentItem).height()) - 22;
			console.log(width + ','+ height);
            return $('<div class="el-placeholder col-xs-12 col-sm-6 col-md-6 col-lg-4"><div class="panel" style="width:' + width + 'px;height:' + height + 'px;"></div></div>')[0];
        },
        update: function(container, p) {
            return;
        }
    },
    helper: function(e, tr)
    {
        var $originals = tr.children();
        var $helper = tr.clone();
		$helper.addClass('el-dragging');
        $helper.children().each(function(index)
        {
			$(this).width(parseInt($originals.eq(index).width()));
			$(this).height($originals.eq(index).height());
        });
        return $helper;
    }
});


function planDeleted(arg1, arg2, data)
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