@extends('../app.layouts.partial')

@section('content')

	<div id="head-wrapper">
		<ul class="breadcrumb breadcrumb-page">
			<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
			<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
			<li class="active">{{ trans('global.edit_page') }}</li>
		</ul>

		<div class="page-header">
			
			<div class="row">
				<h1 class="col-xs-12 col-sm-6 text-center text-left-sm"><i class="fa fa-laptop page-header-icon"></i> <span id="bs-x-campaign" data-type="select" data-value="{{ $site->campaign->id }}" data-pk="{{ $sl }}">{{ $site->campaign->name }}</span> <small>\</small> <small id="site_name" class="bs-x-text" data-type="text" data-clear="false" data-mode="inline" data-pk="{{ $sl }}">{{ $site->name }}</small></h1>

				<div class="col-xs-12 col-sm-6 text-right">
							<button type="button" class="btn btn-default" onClick="document.getElementById('site-preview').contentWindow.editorTogglePreview()" id="app-toggle-buttons"><i class="fa fa-eye"></i> {{ trans('global.preview') }}</button>
							<button type="button" class="btn btn-default" onClick="document.getElementById('site-preview').contentWindow.editorSavePage()">{{ trans('global.save') }}</button>

							<div class="btn-group">
<?php if (! $publish) { ?>
								<button type="button" class="btn btn-primary" id="btnUpgradeAccount">{{ trans('global.publish') }}</button>
<?php } else { ?>
								<button type="button" class="btn btn-primary" onClick="document.getElementById('site-preview').contentWindow.editorPublishPage()">{{ trans('global.publish') }}</button>
<?php } ?>
								<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right" role="menu">
									<li><a href="javascript:void(0);" onClick="document.getElementById('site-preview').contentWindow.editorUnPublishPage()">{{ trans('global.unpublish') }}</a></li>
									<li class="divider"></li>
									<li><a href="{{ $site->domain() }}?published" target="_blank"><i class="fa fa-external-link"></i> &nbsp; {{ trans('global.view_published_version') }}</a></li>
								</ul>
							</div>

							<div class="btn-group">
								<button class="btn dropdown-toggle" type="button" data-toggle="dropdown" tooltip="{{ trans('global.options') }}"><i class="icon fa fa-bars"></i> <i class="fa fa-caret-down"></i></button>
								<ul class="dropdown-menu dropdown-menu-right" role="menu">
<?php /*									<li><a href="{{ url('/web/' . $site->local_domain) }}" tabindex="-1" target="_blank"><i class="fa fa-external-link"></i> &nbsp; {{ trans('global.open_new_window') }}</a></li>*/ ?>
									<li><a href="javascript:void(0);" data-toggle="modal" data-target="#qrModal" tabindex="-1"><i class="fa fa-qrcode"></i> &nbsp; {{ trans('global.qr_code') }}</a></li>
									<li class="divider"></li>
									<li><a href="javascript:void(0);" data-modal="{{ url('/app/modal/web/site-settings?sl=' . $sl) }}" tabindex="-1"><i class="fa fa-cogs"></i> &nbsp; {{ trans('global.website_settings') }}</a></li>
									<li class="divider"></li>
									<li><a href="javascript:void(0);" tabindex="-1" id="btnDeleteSite"><i class="fa fa-trash-o"></i> &nbsp; {{ trans('global.delete_website') }}</a></li>

<?php /*									<li class="divider"></li>
									<li><a href="javascript:void(0);" tabindex="-1" onClick="document.getElementById('site-preview').contentWindow.appLaunchEditorTour()"><i class="fa fa-info-circle"></i> &nbsp; {{ trans('global.help') }}</a></li>*/ ?>
<?php
if(\Auth::user()->can('system_management') && 1==2)
{
?>
									<li class="divider"></li>
									<li><a href="javascript:void(0);" tabindex="-1" onClick="document.getElementById('site-preview').contentWindow.editorGetTemplateJson()"><i class="fa fa-code"></i> &nbsp; Get JSON</a></li>
<?php
}
?>
								</ul>
							</div>
						</div>
			</div>
		</div>
	</div>

<div class="modal fade" id="qrModal" data-ng-app="monospaced.qrcode">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans('global.qr_code') }}</h4>
            </div>
            <div class="modal-body" data-ng-init="url='{{ $site->domain() }}';v=6;e='M';s=256;" style="padding-bottom:0">

                <qrcode version="@{{v}}" error-correction-level="@{{e}}" size="@{{s}}" data="@{{url}}" download id="qrcode"></qrcode>

                <div class="form-group">
                    <label for="url">{{ trans('global.url') }}</label>
                    <textarea id="url" class="form-control" data-ng-model="url" maxlength="2953">{{ $site->domain() }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="size">{{ trans('global.size') }}</label>
							<div class="input-group">
	                            <input id="size" class="form-control" type="number" data-ng-model="s">
								<div class="input-group-addon">px</div>
							</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="version">{{ trans('global.version') }}</label>
                            <input id="version" class="form-control" type="number" data-ng-model="v" min="1" max="40">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="level">{{ trans('global.level') }}</label>
                            <select class="form-control" id="level" data-ng-model="e" data-ng-options="option.version as option.name for option in [{name:'{{ trans('global.low') }}', version:'L'},{name:'{{ trans('global.medium') }}', version:'M'},{name:'{{ trans('global.quartile') }}', version:'Q'},{name:'{{ trans('global.high') }}', version:'H'}]">
                            </select>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-primary" onclick="downloadQr(this, '.qrcode', 'qr.png');">{{ trans('global.download') }}</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
function downloadQr(link, canvasClass, filename) {
    link.href = $(canvasClass).get(0).toDataURL();
    link.download = filename;
}
</script>

	<div style="position:absolute; z-index:-1; text-align:center; width:100%; display:none" id="throbber">
		<div class="small-throbber main-throbber"> </div>
	</div>

<script>
$('#throbber').css('top', (parseInt($(window).outerHeight()) / 2) + 0 + 'px');
$('#throbber').fadeIn();
</script>

	<iframe src="{{ url('/web/' . $site->local_domain) }}" id="site-preview" style="z-index:-1"></iframe>

<script type="text/javascript">

$('#site_name').editable({
	url: '{{ url('/api/v1/site-edit/site-title') }}',
    ajaxOptions: {
        type: 'post'
    },
	success: function(response, newValue) {
        if(response.status == 'error') return response.msg;
    }
});

$('#bs-x-campaign').editable({
	url: '{{ url('/api/v1/site-edit/site-campaign') }}',
    ajaxOptions: {
        type: 'post'
    },
	success: function(response, newValue) {
		/* Empty cache */
		angular.element($('#content-wrapper')).injector().get("$templateCache").removeAll();
        if(response.status == 'error') return response.msg;
    },
	source: [
<?php
$site_campaigns = '';
foreach($campaigns as $campaign) {
	$site_campaigns .= '{value: ' . $campaign->id . ', text: "' . $campaign->name . '"},';
}
$site_campaigns = trim($site_campaigns, ',');
echo $site_campaigns;
?>
	]
});

previewSiteResize();

$(window).resize($.debounce(100, previewSiteResize));

function previewSiteResize()
{
	$('#site-preview').css({ 'height' : (parseInt($(window).outerHeight()) - 150) + 'px'});
}

$('#btnDeleteSite').on('click', function() {
    swal({
      title: "{{ trans('global.are_you_sure') }}",
      text: "{{ trans('global.confirm_delete_website') }}",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      confirmButtonText: "{{ trans('global.delete_website') }}",
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
          url: "{{ url('/api/v1/site/delete') }}",
          type: 'GET',
          data: {data : "{{ $sl }}"},
          dataType: 'json'
        });

        request.done(function(json) {

            /* Decrement count */
            var count = parseInt($('#count_sites').text());
            $('#count_sites').text(count-1);

            /* Open site overview */
            document.location = '#/web?' + new Date().getTime();
            unblockUI();
        });

        request.fail(function(jqXHR, textStatus) {
            alert('Request failed, please try again (' + textStatus, ')');
            unblockUI();
        });
      }
    });
});

$('#btnUpgradeAccount').on('click', function() {
    swal({
      title: "{{ trans('admin.upgrade_title') }}",
      text: "{{ trans('admin.upgrade_msg_feature') }}",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-success",
      confirmButtonText: "{{ trans('admin.upgrade') }}",
      cancelButtonText: "{{ trans('global.ok') }}",
      closeOnConfirm: true,
      closeOnCancel: true
    },
    function(isConfirm)
    {
      if(isConfirm)
      {
        var isDirty = document.getElementById('site-preview').contentWindow.isDirty();

        if (isDirty) {
          var message = 'You have unsaved changes. If you leave they will be lost. ' +
                'To save them click \'Cancel\', and then click ' +
                'the \'Save\' button.';
          if (confirm(message)) {
            document.location = '#/account';
          }
        } else {
          document.location = '#/account';
        }
      }
    });
});
</script>
@stop