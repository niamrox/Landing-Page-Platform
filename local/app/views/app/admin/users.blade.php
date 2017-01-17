@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
		<li class="active">{{ trans('admin.user_administration') }}</li>
	</ul>


	<div class="page-header">
		<div class="row">
			<h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-database page-header-icon"></i> {{ trans('admin.user_administration') }}</h1>

            <div class="pull-right col-xs-12 col-sm-auto">
                <a href="#/admin/user" class="btn btn-primary btn-labeled" style="width: 100%;"><span class="btn-label icon fa fa-plus"></span> {{ trans('global.new_user') }}</a>
            </div>

		</div>
	</div>


<?php
if($users->count() > 0)
{
?>

<script>
var admin_users_table = $('#dt-table-admin_users').DataTable({
    ajax: "{{ url('/api/v1/admin/user-data') }}",
    order: [
        [0, "asc"]
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
        data: "email"
    },
    {
        data: "username"
    },
    {
        data: "roles",
        sortable: false
    },
    {
        data: "plan"
    },
    {
        data: "expires"
    },
    {
        data: "logins"
    },
    {
        data: "last_login"
    },
    {
        data: "confirmed"
    },
    {
        data: "created_at"
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
			targets: [6, 8] /* Column to re-render */
		},
		{
			render: function (data, type, row) {
				var disabled = (row.undeletable == '1') ? ' disabled' : '';
				return '<div class="row-actions-wrap"><div class="text-right row-actions" data-sl="' + data + '">' + 
					'<a href="{{ url('/api/v1/admin/login-as') }}/' + data + '" class="btn btn-xs btn-primary row-btn-login" data-toggle="tooltip" title="{{ trans('global.login') }}"><i class="fa fa-sign-in"></i></a> ' + 
					'<a href="#/admin/user/' + data + '" class="btn btn-xs btn-success row-btn-edit" data-toggle="tooltip" title="{{ trans('global.edit') }}"><i class="fa fa-pencil"></i></a> ' + 
					'<a href="javascript:void(0);" class="btn btn-xs btn-danger row-btn-delete" data-toggle="tooltip" title="{{ trans('global.delete') }}"' + disabled + '><i class="fa fa-trash"></i></a>' + 
					'</div></div>';
			},
			targets: 9 /* Column to re-render */
		},
		{
			render: function (data, type, row) {
				if(data == 1)
				{
					return '<div class="text-center"><i class="fa fa-check icon-active"></i></div>';
				}
				else
				{
					return '<div class="text-center"><i class="fa fa-times icon-nonactive"></i></div>';
				}
			},
			targets: 7
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

$('#dt-table-admin_users_wrapper .dataTables_filter input').attr('placeholder', "{{ trans('global.search_') }}");

</script>
	<div class="table-primary">
		<table class="table table-striped table-bordered table-hover" id="dt-table-admin_users">
			<thead>
				<tr>
					<th>{{ Lang::get('global.email') }}</th>
					<th>{{ Lang::get('global.username') }}</th>
					<th>{{ Lang::get('global.role') }}</th>
					<th>{{ Lang::get('global.plan') }}</th>
					<th>{{ Lang::get('admin.expires') }}</th>
					<th>{{ Lang::get('global.logins') }}</th>
					<th>{{ Lang::get('global.last_login') }}</th>
					<th class="text-center">{{ Lang::get('global.active') }}</th>
					<th>{{ Lang::get('global.created') }}</th>
					<th class="text-right">{{ Lang::get('global.actions') }}</th>
				</tr>
			</thead>
		</table>
	</div>

<script>

$('#dt-table-admin_users').on('click', '.row-btn-delete', function() {
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
			url: "{{ url('/api/v1/admin/user-delete') }}",
			data: { sl: sl},
			method: 'POST'
		})
		.done(function(data) {
            if(data.result == 'success')
            {
    			admin_users_table.ajax.reload();
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