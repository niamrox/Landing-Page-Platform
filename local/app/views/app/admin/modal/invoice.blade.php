<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button class="close" type="button" data-dismiss="modal">×</button>
			<?php echo trans('admin.invoice') ?>
        </div>
		<div class="modal-body">
			<div class="container-fluid" id="invoice">

				<table class="table table-striped table-bordered">
					<tbody>
						<tr>
							<td colspan="2"><h4>{{ $user_name }}</h4></td>
						</tr>
						<tr>
							<td style="min-width:140px"><strong>{{ trans('admin.status') }}:</strong></td>
							<td>
								<div class="input-group">
									<input type="text" class="form-control" id="status" value="{{ $status }}">
									<span class="input-group-btn">
										<button class="btn btn-primary" type="button" id="updateStatus">{{ trans('global.update') }}</button>
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<td><strong>{{ trans('admin.date') }}:</strong></td>
							<td>{{ $invoice_date }}</td>
						</tr>
						<tr>
							<td><strong>{{ trans('admin.order') }}:</strong></td>
							<td>{{ trans('admin.order_line', array('plan' => $plan_name, 'date' => $expires)) }}</td>
						</tr>
						<tr>
							<td><strong>{{ trans('admin.payment_method') }}:</strong></td>
							<td>{{ trans('admin.' . $payment_method) }}</td>
						</tr>
						<tr>
							<td><strong>{{ trans('admin.total') }}:</strong></td>
							<td>{{ $cost_str }}</td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" type="button"><?php echo Lang::get('global.close') ?></button>
			<button class="btn btn-info" type="button" onclick="printData()"><i class="fa fa-print"></i> {{ trans('global.print') }}</button>
		</div>
	</div>
</div>
<script>
$('#updateStatus').on('click', function() {
	blockUI();
    var request = $.ajax(
    {
        url: "{{ url('api/v1/admin/update-invoice-status') }}",
        type: 'POST',
        data:
        {
            sl: "{{ $sl }}",
			status: $('#status').val()
        },
        dataType: 'json'
    });

    request.done(function(json)
    {
		admin_purchases_table.ajax.reload();
		parent.showSaved();
		$modal.modal('hide');
		unblockUI();
    });

    request.fail(function(jqXHR, textStatus)
    {
        alert('Request failed, please try again (' + textStatus + ')');
    });
});

function printData()
{
   var divToPrint=document.getElementById("invoice");
   newWin= window.open("");
   newWin.document.write(divToPrint.outerHTML);
   newWin.print();
   newWin.close();
}
</script>