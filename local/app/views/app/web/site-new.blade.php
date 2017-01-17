@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li class="active">{{ trans('global.new_page') }}</li>
	</ul>

	<div class="page-header">
		<h1><i class="fa fa-laptop page-header-icon"></i> {{ trans('global.new_page') }}</h1>
	</div>

    <div class="row">
       <div class="col-xs-12 col-md-12">
<?php
echo Former::open()
	->class('form custom-validate')
	->action(url('api/v1/site/new'))
	->method('POST');
?>
		<div class="wizard ui-wizard" id="wizard-form">
			<div class="wizard-wrapper">
				<ul class="wizard-steps">
					<li data-target="#wizz-step1">
						<span class="wizard-step-number">1</span>
						<span class="wizard-step-caption">
							{{ trans('global.name') }}
							<span class="wizard-step-description">{{ trans('global.name_your_page') }}</span>
						</span>
					</li>
                    <li data-target="#wizz-step2" onClick="setTimeout(verticalResizer, 300);"> <!-- ! Remove space between elements by dropping close angle -->
						<span class="wizard-step-number">2</span>
						<span class="wizard-step-caption">
							{{ trans('global.type') }}
							<span class="wizard-step-description">{{ trans('global.choose_a_type') }}</span>
						</span>
					</li>
                    <li data-target="#wizz-step3"> <!-- ! Remove space between elements by dropping close angle -->
						<span class="wizard-step-number">3</span>
						<span class="wizard-step-caption">
							{{ trans('global.design') }}
							<span class="wizard-step-description">{{ trans('global.choose_a_template') }}</span>
						</span>
					</li>
				</ul> <!-- / .wizard-steps -->
			</div> <!-- / .wizard-wrapper -->
			<div class="wizard-content panel">
				<div class="wizard-pane" id="wizz-step1">
<?php
echo Former::text()
    ->name('name')
    ->autocomplete('off')
    ->help(trans('global.website_name_info'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
    ->placeholder(trans('global.untitled_page'))
	->label(trans('global.name'));

echo Former::text()
    ->name('campaign')
    ->useDatalist($campaigns, 'name')
    /*->value('{"id": "1", "text": "Store" }')*/
	->class('select2-datalist form-control')
    ->autocomplete('off')
    ->help(trans('global.campaign_info'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
	->label(trans('global.campaign'));

echo '<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">';

echo Former::select('language')
	->class('select2-required form-control')
    ->name('language')
    ->id('language')
    ->forceValue(Auth::user()->language)
	->options(trans('languages.languages'))
	->label(trans('global.language'));

echo Former::select('timezone')
	->class('select2-required form-control')
    ->name('timezone')
    ->forceValue(Auth::user()->timezone)
	->options(trans('timezones.timezones'))
	->label(trans('global.timezone'));
?>
                    <div class="pull-right wizard-buttons">
                        <a href="#/web" class="btn btn-lg btn-default">{{ trans('global.cancel') }}</a>
                        <button type="button" class="btn btn-lg btn-primary wizard-next-step-btn">{{ trans('global.next') }}</button>
                    </div>
				</div> <!-- / .wizard-pane -->

				<div class="wizard-pane" id="wizz-step2" style="display: none;">
					<input type="hidden" name="site_type_id" id="site_type_id" value="">
					<div class="row-fluid icon-select">
<?php
foreach($site_types as $type)
{
	$name = trans('global.' . $type['name']);
?>
    <div class="col-xs-6 col-md-3 col-lg-2">
        <div class="icon-select-type icon-select-item" data-id="{{ $type['id'] }}">
			<div class="vertical-center">
<?php
if (substr($type['icon'], 0, 4) == '<svg')
{
	echo '<div style="width:' . $type['icon_width'] . 'px;margin:auto">' . $type['icon'] . '</div>';
}
else
{
?>
            <i class="fa {{ $type['icon'] }}"></i>
<?php
}
?>
				<div>
				{{ $name }}
				</div>
			</div>
        </div>
    </div>
<?php
}
?>
					</div>

<br style="clear:both">

                    <div class="pull-right wizard-buttons">
                        <button type="button" class="btn btn-lg wizard-prev-step-btn">{{ trans('global.previous') }}</button>
                        <button type="button" id="wizard_type" class="btn btn-lg btn-primary wizard-next-step-btn">{{ trans('global.next') }}</button>
                    </div>

				</div> <!-- / .wizard-pane -->

				<div class="wizard-pane" id="wizz-step3" style="display: none;">
					<input type="hidden" name="site_template" id="site_template" value="">
					<div class="row-fluid icon-select">
<?php
foreach($site_templates as $template)
{
	if($template['active'])
	{
        $class = '';
        foreach($template['categories'] as $site_type_id)
        {
            $class .= ' type' . $site_type_id;
        }
        $thumb = \App\Core\Thumb::template($template['dir'], 0);
?>
    <div class="col-xs-6 col-md-4 col-lg-3 template-type {{ $class }}">
        <div class="icon-select-template icon-select-item icon-select-bg" data-template="{{ $template['dir'] }}">
			<div>
            	<div class="template-preview-holder">
					<a href="{{ url('/web/view/' . $template['dir']) }}" class="template-preview btn btn-primary lightbox" tooltip="{{ trans('global.preview') }}"><i class="fa fa-search" style="font-size:12px"></i></a>
                </div>
	            <img src="{{ $thumb }}" class="img-responsive">
			</div>
        </div>
    </div>
<?php
    }
}
?>
					</div>

<br style="clear:both">

                    <div class="pull-right wizard-buttons">
                        <button type="button" class="btn btn-lg wizard-prev-step-btn">{{ trans('global.previous') }}</button>
                        <button type="button" id="submit-form" class="btn btn-lg btn-primary">{{ trans('global.create_website') }}</button>
                    </div>

				</div> <!-- / .wizard-pane -->

			</div> <!-- / .wizard-content -->
		</div> <!-- / .wizard -->
<?php
echo Former::close();
?>	
       </div>
    </div>

<script>

$('.icon-select-type').on('click dblclick', function(e) {
	var site_type_id = $(this).attr('data-id');
	$('#site_type_id').val(site_type_id);
    $('.icon-select-type').removeClass('active');
    $(this).addClass('active');

	// Only show designs for type
	$('.template-type').hide();
	$('.template-type.type' + site_type_id).show();

	if(e.type == 'dblclick')
	{
		$('#wizard_type').trigger('click');
	}
});

$('.icon-select-template').on('click dblclick', function(e) {
	$('#site_template').val($(this).attr('data-template'));
    $('.icon-select-template').removeClass('active');
    $(this).addClass('active');
	if(e.type == 'dblclick')
	{
		$('#submit-form').trigger('click');
	}
});

var wiz = $('.ui-wizard').pixelWizard({
	onChange: function () {
		//console.log('Current step: ' + this.currentStep());
	},
	onFinish: function () {
		// Disable changing step. To enable changing step just call this.unfreeze()
		this.freeze();
		//console.log('Wizard is freezed');
		//console.log('Finished!');
	}
});

$('.wizard-next-step-btn').click(function () {
	setTimeout(verticalResizer, 300);

    var wizard_continue = true;
    var step = $('#wizard-form').pixelWizard('currentStep');

    if(step == 1)
    {
        var validate = $('form.custom-validate')
            .formValidation('validateField', 'name');

        var has_error = $(this).parents('.wizard-pane').find('.has-error').length;

        var campaign = ($('#campaign').val() != '') ? JSON.parse($('#campaign').val()).text : '';

		// Check campaign
		if(campaign.trim() == '')
		{
			has_error = true;
			$('form #campaign').closest('.form-group').addClass('has-error');
			$('form.custom-validate').formValidation('updateStatus', 'campaign', 'INVALID');
		}
		else
		{
			if(! has_error) has_error = false;
			$('form #campaign').closest('.form-group').removeClass('has-error');
			$('form.custom-validate').formValidation('updateStatus', 'campaign', 'VALID');
		}

        if(has_error)
        {
            wizard_continue = false;
        }
    }

	if(step == 2)
	{
		if($('#site_type_id').val() == '')
		{
			wizard_continue = false;
			swal("{{ trans('global.select_type_alert') }}", null, 'warning');
		}
	}

	if(wizard_continue) $('#wizard-form').pixelWizard('nextStep');
});

// Catch enters
$('input').keydown( function(e) {
	var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
	if(key == 13) {
		e.preventDefault();

		var step = $('#wizard-form').pixelWizard('currentStep');

		if(step == 1)
		{
			$('.wizard-next-step-btn').trigger('click');
		}
	}
});

$('#submit-form').on('click', function(){
	$('#pa-page-alerts-box').remove();
	if($('#site_template').val() == '')
	{
		wizard_continue = false;
		swal("{{ trans('global.select_template_alert') }}", null, 'warning');
	}
	else
    {
		blockUI();
        ajaxSubmitForm($('form.custom-validate').attr('action'), $('form.custom-validate').serialize(), true);
    }
});

function formSubmittedSuccess(r)
{
    if(r.result == 'error')
    {
        return;
    }

    // Increment count
    var count = parseInt($('#count_sites').text());
    $('#count_sites').text(count+1);

	unblockUI();

	// Open Site
	document.location = '#/site/edit/' + r.sl;
}

$('.wizard-prev-step-btn').click(function () {
	$(this).parents('.ui-wizard').pixelWizard('prevStep');
	setTimeout(verticalResizer, 300);
});

</script>
@stop