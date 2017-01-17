@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li class="active">{{ trans('global.form_entries') }}</li>
	</ul>

	<div class="page-header">
		<div class="row">
			<h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-pencil-square-o page-header-icon"></i> {{ trans('global.form_entries') }}<?php
//echo ' <small> \ ' . trans('global.web') . '</small>';
?>
</h1>

			<div class="col-xs-12 col-sm-8">
				<div class="row">

					<hr class="visible-xs no-grid-gutter-h">

                    <div class="pull-right col-xs-12 col-sm-auto">
                        <div class="btn-group" style="width: 100%;">
                            <button type="button" class="btn btn-primary dropdown-toggle" id="with_selected" data-toggle="dropdown" aria-expanded="false" style="width: 100%;">
                            {{ trans('global.actions') }} <span class="caret"></span>
                            </button>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#/lead"><i class="fa fa-plus"></i> {{ Lang::get('global.add_form_entry') }}</a></li>
                                <li class="divider"></li>
                                <li<?php if($leads->count() == 0) echo ' class="disabled"'; ?>><a href="javascript:void(0);" id="select-all">{{ Lang::get('global.select_all') }}</a></li>
                                <li<?php if($leads->count() == 0) echo ' class="disabled"'; ?>><a href="javascript:void(0);" id="deselect-all">{{ Lang::get('global.deselect_all') }}</a></li>
                                <li class="divider"></li>
                                <li<?php if($leads->count() == 0) echo ' class="disabled"'; ?>><a href="javascript:void(0);" id="selected-export">{{ Lang::get('global.export') }}</a></li>
                            </ul>
                        </div>
                    </div>

<?php
if(count($sites) > 0)
{
    $select_site = trans('global.filter_website');
?>
						<div class="pull-right col-xs-12 col-sm-auto">
							<div class="btn-group" style="width:100%;">
								<button class="btn btn-info btn-labeled dropdown-toggle<?php if($leads->count() == 0) echo ' disabled'; ?>" style="width:100%;" type="button" data-toggle="dropdown"><span class="btn-label icon fa fa-filter"></span> {{ $select_site }} &nbsp; <span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu dropdown-menu-right" role="menu" id="site_filter">
<?php
$campaign_name_old = '';
foreach($sites as $site_select)
{
	if($campaign_name_old != $site_select->campaign_name)
	{
		echo '<li class="nav-header disabled"><a>' . $site_select->campaign_name . '</a></li>';
	}

	$campaign_name_old = $site_select->campaign_name;

    $sl_site = \App\Core\Secure::array2string(array('site_id' => $site_select->id));
    $class = (isset($site->id) && $site->id == $site_select->id) ? 'active': '';
?>

									<li class="{{ $class }}" data-sl="{{ $sl_site }}"><a href="javascript:void(0);" tabindex="-1">{{ $site_select->name }}</a></li>
<?php
}
?>
                                    <li class="divider"></li>
                                    <li><a href="javascript:void(0);"><i class="fa fa-trash-o"></i> {{ Lang::get('global.remove_filter') }}</a></li>
								</ul>
							</div>
						</div>
<?php
}
?>
					<hr class="visible-xs no-grid-gutter-h">


					<div class="pull-right col-xs-12 col-sm-auto">
                        <div class="btn-group" id="with_selected" style="width: 100%;">
                            <button type="button" class="btn dropdown-toggle btn-info" disabled data-toggle="dropdown" aria-expanded="false" style="width: 100%;">
                            {{ trans('global.with_selected') }} <span class="caret"></span>
                            </button>
            
                            <ul class="dropdown-menu" role="menu">
<?php /*                                <li><a href="javascript:void(0);" id="selected-view"><i class="fa fa-search"></i> {{ Lang::get('global.view_details') }}</a></li>
								<li class="divider"></li>*/ ?>
                                <li><a href="javascript:void(0);" id="selected-delete"><i class="fa fa-trash-o"></i> {{ Lang::get('global.delete') }}</a></li>
                            </ul>
                        </div>
                    </div>

				</div>
			</div>

		</div>
	</div>

<?php
if($leads->count() > 0)
{
?>

<script>
$('#site_filter li').on('click', function() {
    $('#site_filter li').removeClass('active');
    var sl_site = $(this).attr('data-sl');

    if(typeof sl_site !== 'undefined')
    {
        $(this).addClass('active');
        leads_table.ajax.url("{{ url('/api/v1/lead/data?filter=') }}" + sl_site).load();
    }
    else
    {
        leads_table.ajax.url("{{ url('/api/v1/lead/data') }}").load();
    }
});

var leads_table = $('#dt-table-leads').DataTable({
    ajax: "{{ url('/api/v1/lead/data') }}",
    order: [
        [5, "desc"]
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
    rowCallback: function(row, data) {
		if($.inArray(data.DT_RowId.replace('row_', ''), selected_leads) !== -1)
		{
			$(row).addClass('success');
		}
	},
	fnDrawCallback: function() {
		onDataTableLoad();
	},
	columns: [{
		data: "source"
	},{
		data: "email"
	},{
		data: "language"
	},{
		data: "client"
	}, {
		data: "os"
	}, {
		data: "created_at"
	},
    {
        data: "sl",
        sortable: false
    }],
	columnDefs: [
		{
			render: function (data, type, row) {
				return '<span data-moment="fromNowDateTime">' + data + '</span>';
			},
			targets: 5 /* Column to re-render */
		},
		{
			render: function (data, type, row) {
				return '<div class="row-actions-wrap"><div class="text-right row-actions" data-sl="' + data + '">' + 
					'<a href="javascript:viewSingleRecord(\'' + data + '\');" class="btn btn-xs btn-primary row-btn-view" data-toggle="tooltip" title="{{ trans('global.view') }}"><i class="fa fa-search"></i></a> ' + 
					'<a href="#/lead/' + data + '" class="btn btn-xs btn-success row-btn-edit" data-toggle="tooltip" title="{{ trans('global.edit') }}"><i class="fa fa-pencil"></i></a> ' + 
					'<a href="javascript:void(0);" class="btn btn-xs btn-danger row-btn-delete" data-toggle="tooltip" title="{{ trans('global.delete') }}"><i class="fa fa-trash"></i></a>' + 
					'</div></div>';
			},
			targets: 6 /* Column to re-render */
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
})
.on('init.dt', function() {
	var count = $(this).dataTable().fnGetData().length;
	if(count == 0)
	{
		$('#with_selected').prop('disabled', true);
	}
});

// Click
$('#dt-table-leads').on('click', 'tr', function() {
	checkButtonVisibility();
});

$('#dt-table-leads_wrapper .dataTables_filter input').attr('placeholder', "{{ trans('global.search_') }}");

$('#dt-table-leads tbody').on('click', 'tr', function(e) {
    if(e.target.nodeName == 'TD')
    {
        var td_index = $(e.target).index();
    }
    else
    {
        var td_index = $(e.target).parents('td').index();
    }
    if(td_index == 6) return;

    var id = this.id.replace('row_', '');
    var index = $.inArray(id, selected_leads);

    if (index === -1) {
        selected_leads.push(id);
    } else {
        selected_leads.splice(index, 1);
    }

    $(this).toggleClass('success');
});

checkButtonVisibility();

function checkButtonVisibility()
{
    var disabled = (parseInt(selected_leads.length) > 0) ? false : true;
    $('#with_selected button:not(#select-all)').prop('disabled', disabled);
}
</script>

	<div class="table-primary">
		<table class="table table-striped table-bordered table-hover" id="dt-table-leads">
			<thead>
				<tr>
					<th>{{ Lang::get('global.source') }}</th>
					<th>{{ Lang::get('global.email') }}</th>
					<th>{{ Lang::get('global.language') }}</th>
					<th>{{ Lang::get('global.browser') }}</th>
					<th>{{ Lang::get('global.os') }}</th>
					<th>{{ Lang::get('global.created') }}</th>
					<th class="text-right" style="width:71px">{{ Lang::get('global.actions') }}</th>
				</tr>
			</thead>
		</table>
	</div>

<script>
$('#select-all').on('click', function() {
	selected_leads = [];

	$('#dt-table-leads tbody tr').each(function() {
		var id = this.id.replace('row_', '');
		selected_leads.push(id);
	});

	checkButtonVisibility();
	leads_table.ajax.reload();
});

$('#deselect-all').on('click', function() {
	selected_leads = [];
	checkButtonVisibility();
	leads_table.ajax.reload();
});

// View record(s)
$('#selected-view').on('click', viewRecords);

function viewRecords(e)
{
    if(e.target.nodeName == 'TD')
    {
        var td_index = $(e.target).index();
    }
    else
    {
        var td_index = $(e.target).parents('td').index();
    }
    if(td_index == 3) return;

	$('body').modalmanager('loading');
	
	$modal.load("{{ url('/app/leads/leads-view') }}", '', function(){
		$modal.modal();
		onModalLoad();
	});
}

function viewSingleRecord(sl)
{
	$('body').modalmanager('loading');
	
	$modal.load("{{ url('/app/leads/leads-view?sl=') }}" + sl, '', function(){
		$modal.modal();
		onModalLoad();
	});
}

$('#selected-export').on('click', function() {
	$('body').modalmanager('loading');

	$modal.load("{{ url('/app/leads/leads-export') }}", '', function(){
		$modal.modal();
		onModalLoad();
	});
});

$('#selected-delete').on('click', function() {
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
			url: "{{ url('/api/v1/lead/delete') }}",
			data: { ids: selected_leads },
			method: 'POST'
		})
		.done(function() {
			selected_leads = [];
			leads_table.ajax.reload();
			checkButtonVisibility();
		})
		.fail(function() {
			console.log('error');
		})
		.always(function() {
			unblockUI();
		});
	});
});

$('#dt-table-leads').on('click', '.row-btn-delete', function() {
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
			url: "{{ url('/api/v1/lead/delete') }}",
			data: { sl: sl},
			method: 'POST'
		})
		.done(function(data) {
            if(data.result == 'success')
            {
    			leads_table.ajax.reload();
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
else
{
	// No records yet
?>
<div class="callout pull-left">{{ Lang::get('global.no_leads') }}</div>
<?php
}
?>

@stop