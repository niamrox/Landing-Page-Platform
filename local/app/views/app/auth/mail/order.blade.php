<h3>{{ trans('admin.purchase') }}</h3>

<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tbody>
		<tr>
			<td><strong>{{ trans('admin.date') }}:</strong></td>
			<td>{{ $invoice_date }}</td>
		</tr>
		<tr>
			<td><strong>{{ trans('admin.payment_method') }}:</strong></td>
			<td>{{ $payment_method }}</td>
		</tr>
		<tr>
			<td><strong>{{ trans('admin.order') }}:</strong></td>
			<td>{{ trans('admin.order_line', array('plan' => $plan_name, 'date' => $expires)) }}</td>
		</tr>
		<tr>
			<td><strong>{{ trans('admin.total') }}:</strong></td>
			<td>{{ $cost_str }}</td>
		</tr>
	</tbody>
</table>