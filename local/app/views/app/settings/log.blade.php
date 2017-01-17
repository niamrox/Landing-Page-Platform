@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li><a href="javascript:void(0);">{{ trans('global.settings') }}</a></li>
		<li class="active">{{ trans('global.log') }}</li>
	</ul>

	<div class="page-header">
		<h1 style="height:32px"><i class="fa fa-history page-header-icon"></i> {{ trans('global.log') }}</h1>
	</div>
<script>
$('#dt-table-log').DataTable({
	ajax: "{{ url('/api/v1/log/data') }}",
    order: [[ 5, "desc" ]],
    dom: "<'row'<'col-sm-12 dt-header'<'pull-left'lr><'pull-right'f><'pull-right hidden-sm hidden-xs'T><'clearfix'>>>t<'row'<'col-sm-12 dt-footer'<'pull-left'i><'pull-right'p><'clearfix'>>>",
	processing: true,
	serverSide: true,
	stateSave: true,
	stripeClasses: [],
	lengthMenu: [ [10, 25, 50, 75, 100, 1000000], [10, 25, 50, 75, 100, "{{ trans('global.all') }}"] ],
	rowCallback: function(row, data) {
		if($.inArray(data.DT_RowId, selected_log) !== -1) {
			$(row).addClass('success');
		}
	},
	columns: [
		{ data: "desc" },
		{ data: "os" },
		{ data: "client" },
		{ data: "device" },
		{ data: "ip" },
/*		{ data: "brand" },
		{ data: "model" },*/
		{ data: "created_at" }
	],

	language: {
		emptyTable: "{{ trans('global.empty_table') }}",
		info: "{{ trans('global.dt_info') }}",
		infoEmpty: "",
		infoFiltered: "(filtered from _MAX_ total entries)",
		thousands: "{{ trans('i18n.thousands_sep') }}",
		lengthMenu: "{{ trans('global.show_records') }}",
		processing: '<i class="fa fa-circle-o-notch fa-spin"></i>',
		paginate: {
			first: '<i class="fa fa-fast-backward"></i>',
			last: '<i class="fa fa-fast-forward"></i>',
			next: '<i class="fa fa-caret-right"></i>',
			previous: '<i class="fa fa-caret-left"></i>'
		}
	},
	oTableTools: {
		sSwfPath: "{{ url('/assets/swf/tabletools/copy_csv_xls_pdf.swf') }}",
		sRowSelect: "os",
		aButtons:  [
				{
					"sExtends": "copy", 
					"sButtonText": '<div data-toggle="tooltip" title="dsf sdffsdfsd"><i class="fa fa-files-o"></i></div>'
				},
				{
					"sExtends": "xls",
					"sFileName": "*.xls",
					"sButtonText": '<i class="fa fa-file-excel-o"></i>'
				},
				{
					"sExtends": "pdf", 
					"sButtonText": '<i class="fa fa-file-pdf-o"></i>'
				}
			]
	}
}).on('init.dt', onDataTableLoad);

$('#dt-table-log_wrapper .dataTables_filter input').attr('placeholder', "{{ trans('global.search_') }}");

$('#dt-table-log tbody').on('click', 'tr', function () {
	var id = this.id;
	var index = $.inArray(id, selected_log);

	if ( index === -1 ) {
		selected_log.push( id );
	} else {
		selected_log.splice( index, 1 );
	}

	$(this).toggleClass('success');
});
</script>
	<div class="table-primary">
		<table class="table table-striped table-bordered table-hover" id="dt-table-log">
			<thead>
				<tr>
					<th>{{ Lang::get('global.description') }}</th>
					<th>{{ Lang::get('global.os') }}</th>
					<th>{{ Lang::get('global.browser') }}</th>
					<th>{{ Lang::get('global.device') }}</th>
					<th>{{ Lang::get('global.ip') }}</th>
					<th>{{ Lang::get('global.created') }}</th>
				</tr>
			</thead>
		</table>
	</div>
@stop