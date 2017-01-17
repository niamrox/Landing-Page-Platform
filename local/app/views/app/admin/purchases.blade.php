@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
		<li class="active">{{ trans('admin.purchases') }}</li>
	</ul>


	<div class="page-header">
		<div class="row">
			<h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-money page-header-icon"></i> {{ trans('admin.purchases') }}</h1>
		</div>
	</div>


<?php
if($users->count() > 0)
{
?>
<script>
var admin_purchases_table = $('#dt-table-admin_purchases').DataTable({
    ajax: "{{ url('/api/v1/admin/purchase-data') }}",
    order: [
        [0, "desc"]
    ],
    dom: "<'row'<'col-sm-12 dt-header'<'pull-left'lr><'pull-right'f><'pull-right hidden-sm hidden-xs'T><'clearfix'>>>t<'row'<'col-sm-12 dt-footer'<'pull-left'i><'pull-right'p><'clearfix'>>>",
    processing: true,
    serverSide: true,
    stateSave: true,
    stripeClasses: [],
    lengthMenu: [
        [10, 25, 50, 75, 100, 1000000],
        [10, 25, 50, 75, 100, "{{ trans('global.all') }}"]
    ],
    columns: [
    {
        data: "invoice"
    },
    {
        data: "user_mail"
    },
    {
        data: "user_name"
    },
    {
        data: "plan_name"
    },
    {
        data: "expires"
    },
    {
        data: "payment_method"
    },
    {
        data: "cost_str"
    },
    {
        data: "invoice_date"
    },
    {
        data: "status"
    },
    {
        data: "sl",
        sortable: false
    }],
	fnDrawCallback: function() {
		onDataTableLoad();
	},
	columnDefs: [
		{
			render: function (data, type, row) {
				return '<span data-moment="fromNowDateTime">' + data + '</span>';
			},
			targets: [7] /* Column to re-render */
		},
		{
			render: function (data, type, row) {
				return '<strong>' + data + '</strong>';
			},
			targets: [8] /* Column to re-render */
		},
		{
			render: function (data, type, row) {
				return '<div class="row-actions-wrap"><div class="text-right row-actions" data-sl="' + data + '">' + 
					'<a href="javascript:viewSingleRecord(\'' + data + '\');" class="btn btn-xs btn-primary row-btn-details" data-toggle="tooltip" title="{{ trans('admin.update_status') }}"><i class="fa fa-pencil"></i></a> ' + 
					'<a href="javascript:void(0);" class="btn btn-xs btn-danger row-btn-delete" data-toggle="tooltip" title="{{ trans('global.delete') }}"><i class="fa fa-trash"></i></a>' + 
					'</div></div>';
			},
			targets: 9 /* Column to re-render */
		}
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
        aButtons: [{
            "sExtends": "copy",
            "sButtonText": '<i class="fa fa-files-o"></i>'
        }, {
            "sExtends": "xls",
            "sFileName": "*.xls",
            "sButtonText": '<i class="fa fa-file-excel-o"></i>'
        }, {
            "sExtends": "pdf",
            "sButtonText": '<i class="fa fa-file-pdf-o"></i>'
        }]
    }
});

$('#dt-table-admin_purchases_wrapper .dataTables_filter input').attr('placeholder', "{{ trans('global.search_') }}");

</script>
	<div class="table-primary">
		<table class="table table-striped table-bordered table-hover" id="dt-table-admin_purchases">
			<thead>
				<tr>
					<th>{{ Lang::get('admin.id') }}</th>
					<th>{{ Lang::get('global.email') }}</th>
					<th>{{ Lang::get('global.name') }}</th>
					<th>{{ Lang::get('admin.plan') }}</th>
					<th>{{ Lang::get('admin.expires') }}</th>
					<th>{{ Lang::get('admin.payment_method') }}</th>
					<th>{{ Lang::get('admin.amount') }}</th>
					<th>{{ Lang::get('admin.purchase_date') }}</th>
					<th>{{ Lang::get('admin.status') }}</th>
					<th class="text-right">{{ Lang::get('global.actions') }}</th>
				</tr>
			</thead>
		</table>
	</div>

<script>
function viewSingleRecord(sl)
{
	$('body').modalmanager('loading');
	
	$modal.load("{{ url('/api/v1/admin/invoice-modal?sl=') }}" + sl, '', function(){
		$modal.modal();
		onModalLoad();
	});
}

$('#dt-table-admin_purchases').on('click', '.row-btn-delete', function() {
    var sl = $(this).parent('.row-actions').attr('data-sl');

	swal({
	  title: _lang['confirm'],
	  type: "warning",
	  showCancelButton: true,
	  cancelButtonText: _lang['cancel'],
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: _lang['yes_delete']
	}, 
	function(){
		blockUI();
	
		var jqxhr = $.ajax({
			url: "{{ url('/api/v1/admin/delete-invoice') }}",
			data: { sl: sl},
			method: 'POST'
		})
		.done(function(data) {
            if(data.result == 'success')
            {
    			admin_purchases_table.ajax.reload();
            }
            else
            {
                swal(data.msg);
            }
		})
		.fail(function() {
			console.log('error');
		})
		.always(function() {
			unblockUI();
		});
	});
});

</script>
<?php
}
?>

@stop