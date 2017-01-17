<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button class="close" type="button" data-dismiss="modal">×</button>
			<?php echo trans('global.view_details') ?>
        </div>
		<div class="modal-body">
			<div class="container-fluid" id="record-view">

				<div class="spinner" id="spinner" style="margin: 10px auto">
				  <div class="rect1"></div>
				  <div class="rect2"></div>
				  <div class="rect3"></div>
				  <div class="rect4"></div>
				  <div class="rect5"></div>
				</div>

			</div>
		</div>
		<div class="modal-footer">
<?php /*
			<button class="btn btn-primary pull-left" id="record-first" disabled type="button" data-toggle="tooltip" title="<?php echo Lang::get('global.first') ?>"><i class="fa fa-fast-backward"></i></button>
			<button class="btn btn-primary pull-left" id="record-prev" disabled type="button" data-toggle="tooltip" title="<?php echo Lang::get('global.previous') ?>"><i class="fa fa-step-backward"></i></button>
			<span id="record-current" class="btn btn-default pull-left" disabled>&nbsp;</span>
			<button class="btn btn-primary pull-left" id="record-next" disabled type="button" data-toggle="tooltip" title="<?php echo Lang::get('global.next') ?>"><i class="fa fa-step-forward"></i></button>
			<button class="btn btn-primary pull-left" id="record-last" disabled type="button" data-toggle="tooltip" title="<?php echo Lang::get('global.last') ?>"><i class="fa fa-fast-forward"></i></button>
*/ ?>
			<button class="btn" data-dismiss="modal" type="button"><?php echo Lang::get('global.close') ?></button>
			<button class="btn btn-primary" type="button" onclick="printData()"><i class="fa fa-print"></i></button>
		</div>
	</div>
</div>
<script>
Handlebars.registerHelper('breaklines', function(text) {
    text = Handlebars.Utils.escapeExpression(text);
    text = text.replace(/(\r\n|\n|\r)/gm, '<br>');
    return new Handlebars.SafeString(text);
});

function printData()
{
   var divToPrint=document.getElementById("leadData");
   newWin= window.open("");
   newWin.document.write(divToPrint.outerHTML);
   newWin.print();
   newWin.close();
}
var current_record = 0;

<?php
if($sl != '')
{
?>
var view_leads = new Array;

viewRecord('{{ $sl }}');

<?php
}
else
{
?>
var view_leads = selected_leads;
<?php
}
?>

if(view_leads.length > 0)
{
	viewRecord();
}

$('#record-first').on('click', function() {
	current_record = 0;
	viewRecord();
});

$('#record-prev').on('click', function() {
	current_record -= 1;
	viewRecord();
});

$('#record-next').on('click', function() {
	current_record += 1;
	viewRecord();
});

$('#record-last').on('click', function() {
	current_record = view_leads.length - 1;
	viewRecord();
});

function viewRecord(record)
{
	$('#record-view').html('<div class="spinner" id="spinner" style="margin: 10px auto"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div>');

	var url = (typeof record === 'undefined') ? "{{ url('/api/v1/lead/record?id=') }}" + view_leads[current_record] : "{{ url('/api/v1/lead/record?sl=') }}" + record;

	var jqxhr = $.getJSON(url, function(r) {
		var source = $("#view-template").html();
		var template = Handlebars.compile(source);
		var html = template(r.response);

		$('#record-view').html(html);
/*
		$('#record-current').text((current_record + 1) + ' / ' + view_leads.length);

		$('#record-first').prop('disabled', true);
		$('#record-prev').prop('disabled', true);
		$('#record-next').prop('disabled', true);
		$('#record-last').prop('disabled', true);

		if(current_record > 0)
		{
			$('#record-first').prop('disabled', false);
			$('#record-prev').prop('disabled', false);
		}

		if((current_record + 1) < view_leads.length)
		{
			$('#record-next').prop('disabled', false);
			$('#record-last').prop('disabled', false);
		}
*/
	})
	.fail(function() {
		console.log("error");
	})
}

</script>
<script id="view-template" type="text/x-handlebars-template">
<table width="100%" class="table table-bordered table-hover table-striped" id="leadData">
	<tbody>
		<tr>
			<td width="120"><strong>{{ trans('global.source') }}</strong></td>
			<td>@{{app_name}}@{{site_name}}</td>
		</tr>
<?php /*
		<tr>
			<td width="120"><strong>{{ trans('global.email') }}</strong></td>
			<td>@{{email}}</td>
		</tr>
*/ ?>
		@{{#settings}}
		<tr>
			<td><strong>@{{name}}</strong></td>
			<td>@{{breaklines val}}</td>
		</tr>
		@{{/settings}}
		<tr>
			<td><strong>{{ trans('global.created') }}</strong></td>
			<td>@{{created_at}}</td>
		</tr>
		<tr>
			<td><strong>{{ trans('global.language') }}</strong></td>
			<td>@{{language}}</td>
		</tr>
<?php /*
		<tr>
			<td><strong>{{ trans('global.os') }}</strong></td>
			<td>@{{os}}</td>
		</tr>
		<tr>
			<td><strong>{{ trans('global.browser') }}</strong></td>
			<td>@{{client}}</td>
		</tr>
		<tr>
			<td><strong>{{ trans('global.device') }}</strong></td>
			<td>@{{device}}</td>
		</tr>
*/ ?>
	</tbody>
</table>
</script>