@extends('../app.layouts.partial')

@section('content')
  <ul class="breadcrumb breadcrumb-page">
    <div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
    <li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
    <li>{{ trans('global.settings') }}</li>
    <li class="active">{{ trans('global.apps') }}</li>
  </ul>

  <div class="page-header">
  <div class="row">
    <h1 class="col-xs-12 text-center text-left-sm"><i class="fa fa-plug page-header-icon"></i> {{ trans('global.connected_apps') }}</h1>
  </div>
  </div>

  <div class="row">
<?php if (count($apps) > 0) { ?>
<?php foreach ($apps as $app) { ?>
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
    <div class="panel panel-default panel-body-colorful widget-profile widget-profile-centered widget-profile-default">
      <div class="panel-heading" style="height:210px;">
        <img src="{{ $app['img'] }}" alt="$app['name']" class="widget-profile-avatar" style="width:auto; height:120px; max-width:180px;">
        <div class="widget-profile-header">
          <span class="ellipsis-oneline">{{ $app['name'] }}</span>
          <br>
          <div class="ellipsis-oneline-small" style="font-weight:bold">{{ $app['accountname'] }}</div>
        </div>
      </div>
      <div class="widget-profile-counters">
        <div class="col-xs-12">
<?php if ($app['connected']) { ?>
          <a href="javascript:void(0);" data-href="{{ $app['connect_url'] }}" data-name="{{ $app['name'] }}" data-data-name="{{ $app['dataName'] }}" class="disconnect-app btn btn-danger btn-xs"><i class="fa fa-chain-broken fa-1x"></i> {{ trans('global.disconnect_account'); }}</a>
<?php } else { ?>
          <a href="{{ $app['connect_url'] }}" class="btn btn-primary btn-xs" target="_blank"><i class="fa fa-chain fa-1x"></i> {{ trans('global.connect_account'); }}</a>
<?php } ?>
<?php if (isset($app['info'])) { ?>
          <a href="javascript:void(0);" class="btn btn-default btn-xs" data-tooltip="{{ $app['info'] }}"><i class="fa fa-question fa-1x" aria-hidden="true"></i></i></a>
<?php } ?>


        </div>
      </div>
      <br style="clear:both">
    </div>
  </div>
<?php } ?>
<?php } else { ?>
  <div class="col-xs-12">
    <h1>{{ trans('global.no_apps_installed') }}</h1>
  </div>
<?php } ?>
</div>

<script>
function widgetOAuthCallback() {
  document.location = '/platform#/oauth?' + new Date().getTime();
}

$('.disconnect-app').on('click', function() {

    var disconnect_url = $(this).attr('data-href');
    var name = $(this).attr('data-name');
    var dataName = $(this).attr('data-data-name');

    swal({
      title: "{{ trans('global.disconnect_account') }}",
      text: "" + name + "\n{{ trans('global.confirm_disconnect') }}",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      confirmButtonText: "{{ trans('global.disconnect') }}",
      cancelButtonText: "{{ trans('global.cancel') }}",
      closeOnConfirm: true,
      closeOnCancel: true
    },
    function(isConfirm)
    {
      if(isConfirm)
      {
        blockUI();
        var request = $.ajax({
          url: "{{ url('/api/v1/oauth/disconnect/') }}/" + dataName,
          type: 'GET',
          dataType: 'json'
        });

        request.done(function(json) {
            document.location = '/platform#/oauth?' + new Date().getTime();
            unblockUI();
        });

        request.fail(function(jqXHR, textStatus) {
            alert('Request failed, please try again (' + textStatus, ')');
            unblockUI();
        });
      }
    });
});
</script>
@stop