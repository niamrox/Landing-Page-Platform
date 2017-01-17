@extends('../app.layouts.partial')

@section('content')

    <ul class="breadcrumb breadcrumb-page">
        <div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
        <li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
        <li class="active">{{ trans('global.analytics') }}</li>
    </ul>
<?php
    //$title = ($site !== false) ? $site->name . ' <small> \ ' . $site->domain() . '</small>' : trans('global.statistics') . ' <small> \ ' . trans('global.web') . '</small>';
    $title = ($site !== false) ? $site->name : trans('global.analytics'); // . ' <small> \ ' . trans('global.web') . '</small>';
?>
		<div class="page-header">
			<div class="row">
				<h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-line-chart page-header-icon"></i> {{ $title }}</h1>

				<div class="col-xs-12 col-sm-8">
					<div class="row">
						<hr class="visible-xs no-grid-gutter-h">
<?php
if(count($sites) > 0)
{
    $select_site = trans('global.select_website');
    //$select_site = ($site !== false) ? $site->name : trans('global.select_site');
?>
						<div class="pull-right col-xs-12 col-sm-auto">
							<div class="btn-group" style="width:100%;">
								<button class="btn btn-primary btn-labeled dropdown-toggle" style="width:100%;" type="button" data-toggle="dropdown"><span class="btn-label icon fa fa-laptop"></span> {{ $select_site }} &nbsp; <span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu dropdown-menu-right" role="menu">
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

									<li class="{{ $class }}"><a href="#/web/analytics/{{ $sl_site }}" tabindex="-1">{{ $site_select->name }}</a></li>
<?php
}
?>
								</ul>
							</div>
						</div>
<?php
}
?>
<?php
if($site !== false)
{
?>
						<div class="pull-right col-xs-12 col-sm-auto">

							<div id="stats-range" class="pull-right daterange-selector btn btn-default"> 
								<i class="fa fa-calendar" style="margin:-1px 2px 0 0"></i> <span></span> <b class="caret" style="margin-left:5px"></b> 
							</div>

						</div>
<?php
}
?>
					</div>
				</div>
			</div>
		</div>
<?php
if(count($sites) == 0)
{
    // No sites
?>
    <div class="callout pull-left">{{ Lang::get('global.no_sites') }}</div>
<?php
}
elseif($site === false)
{
    // Select site
?>
    <div class="callout pull-right arrow-right-up" style="margin-right:40px">{{ Lang::get('global.please_select_site') }}</div>
<?php
}
elseif($stats_found === false)
{
    // No stats found for period
?>
    <div class="callout pull-left">{{ Lang::get('global.no_stats_found') }}</div>
<?php
}
else
{
?>
		<div class="row">
			<div class="col-md-12">
<?php
$visitors_range = '';

foreach($summary_days as $date => $row) {
    $oDate = new DateTime($date);
    $date = $oDate->format('Y-m-d');

    // Get leads
    $leads = 0;
    foreach($leads_days as $lead)
    {
        if($lead['date'] == $date)
        {
            $leads = $lead['leads'];
            break;
        }
    }

    if(! empty($row))
    {
       // $visitors_range .= "{ day: '" . $date . "', visitors: " . $row['nb_visits'] . ", actions: " . $row['nb_actions'] . ", leads: " . $leads . " }";
        $visitors_range .= "{ day: '" . $date . "', visitors: " . $row['nb_visits'] . ", leads: " . $leads . " }";
    }
    elseif($site_created <= $oDate->format('Y-m-d'))
    {
        //$visitors_range .= "{ day: '" . $date . "', visitors: 0, actions: 0, leads: " . $leads . " }";
        $visitors_range .= "{ day: '" . $date . "', visitors: 0, leads: " . $leads . " }";
    }
    else
    {
       // $visitors_range .= "{ day: '" . $date . "', visitors: 0, actions: 0, leads: " . $leads . " }";
        $visitors_range .= "{ day: '" . $date . "', visitors: 0, leads: " . $leads . " }";	
    }
    if($oDate->format('Y-m-d') != $date_end) $visitors_range .= ',';
}

?>
<script>
var monthNames = ['<?php echo trans('global.january') ?>', '<?php echo trans('global.february') ?>', '<?php echo trans('global.march') ?>', '<?php echo trans('global.april') ?>', '<?php echo trans('global.may') ?>', '<?php echo trans('global.june') ?>', '<?php echo trans('global.july') ?>', '<?php echo trans('global.august') ?>', '<?php echo trans('global.september') ?>', '<?php echo trans('global.october') ?>', '<?php echo trans('global.november') ?>', '<?php echo trans('global.december') ?>'];
var monthNamesAbbr = ['<?php echo trans('global.january_abbr') ?>', '<?php echo trans('global.february_abbr') ?>', '<?php echo trans('global.march_abbr') ?>', '<?php echo trans('global.april_abbr') ?>', '<?php echo trans('global.may_abbr') ?>', '<?php echo trans('global.june_abbr') ?>', '<?php echo trans('global.july_abbr') ?>', '<?php echo trans('global.august_abbr') ?>', '<?php echo trans('global.september_abbr') ?>', '<?php echo trans('global.october_abbr') ?>', '<?php echo trans('global.november_abbr') ?>', '<?php echo trans('global.december_abbr') ?>'];

var stats_data = [
<?php echo $visitors_range ?>
];
Morris.Line({
    element: 'hero-graph',
    data: stats_data,
    xkey: 'day',
	yLabelFormat: function(y){return y != Math.round(y)?'':y;},
    ykeys: ['visitors'<?php /*, 'actions'*/ ?>, 'leads'],
    labels: ['{{ trans('global.visits') }}', '{{ trans('global.leads') }}'],
    lineColors: ['#fff', '#f4b04f'],
    lineWidth: 2,
    pointSize: 4,
    gridLineColor: 'rgba(255,255,255,.5)',
    resize: true,
    gridTextColor: '#fff',
    gridIntegers: true,
    xLabels: "day",
    xLabelFormat: function(d) {
        return monthNamesAbbr[d.getMonth()] + ' ' + d.getDate(); 
    },
});
</script>

				<div class="stat-panel">
					<div class="stat-row">
						<div class="stat-cell col-sm-3 padding-sm-hr bordered no-border-r valign-top">
							<h4 class="padding-sm no-padding-t padding-xs-hr"><i class="fa fa-bar-chart-o text-primary fa-border"></i> {{ trans('global.totals') }}</h4>
							<ul class="list-group no-margin">
								<li class="list-group-item no-border-hr padding-xs-hr no-bg no-border-radius">
									{{ trans('global.visits') }} <span class="label label-success pull-right">{{ $summary_range['nb_visits'] }}</span>
								</li>
<?php /*
								<li class="list-group-item no-border-hr padding-xs-hr no-bg">
									{{ trans('global.actions') }} <span class="label label-danger pull-right">{{ $summary_range['nb_actions'] }}</span>
								</li>
*/ ?>
								<li class="list-group-item no-border-hr padding-xs-hr no-bg">
									{{ trans('global.leads') }} <span class="label label-warning pull-right">{{ $lead_count }}</span>
								</li>
<?php /*
								<li class="list-group-item no-border-hr no-border-b padding-xs-hr no-bg">
									{{ trans('global.bounce_rate') }} <span class="label label-default pull-right">{{ $summary_range['bounce_rate'] }}</span>
								</li>
*/ ?>
<?php
$avg_time_on_site = ((int)$summary_range['avg_time_on_site'] > 60*60) ? date('H:i:s', $summary_range['avg_time_on_site']) . ' ' . Lang::get('global.hours_abbr') : date('i:s', $summary_range['avg_time_on_site']) . ' ' . Lang::get('global.minutes_abbr');
?>
								<li class="list-group-item no-border-hr no-border-b padding-xs-hr no-bg">
									{{ trans('global.avg_time_on_site') }} <span class="label label-pa-purple pull-right">{{ $avg_time_on_site }}</span>
								</li>
							</ul>
						</div>

						<div class="stat-cell col-sm-9 bg-primary padding-sm valign-middle">
							<div id="hero-graph" class="graph" style="height: 230px;"></div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<!-- Page wide horizontal line -->
		<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">

        <div class="panel panel-default">
            <div class="tab-content no-padding">


                <script>
                    $('#table-days').dataTable({
                        dom: "t"+
                              "<'table-footer clearfix'<'DT-label'i><'DT-pagination'p>>",
                        order: [
                            [0, "desc"]
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
						}
                    });
                </script>

                <div class="table-primary no-margin">
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table-days" style="margin:0">
                        <thead>
                            <tr>
                                <th>{{ trans('global.date') }}</th>
                                <th class="text-right">{{ trans('global.unique_visitors') }} &nbsp;</th>
                                <th class="text-right">{{ trans('global.visits') }} &nbsp;</th>
<?php /*                                <th class="text-right">{{ trans('global.actions') }} &nbsp;</th>
                                <th class="text-right">{{ trans('global.actions_per_visit') }} &nbsp;</th>
                                <th class="text-right">{{ trans('global.bounce_rate') }} &nbsp;</th>*/ ?>
                                <th class="text-right">{{ trans('global.avg_time_on_site') }} &nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
foreach($summary_days as $date => $row)
{
    $oDate = new DateTime($date);

	$formatter = new IntlDateFormatter(trans('i18n.intl_locale'), IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
	$formatter->setPattern(trans('i18n.intl_dateformat_date_long'));
    $date = $formatter->format($oDate);
    $show_row = true;

    if(! empty($row))
    {
        $nb_uniq_visitors = $row['nb_uniq_visitors'];
        $nb_visits = $row['nb_visits'];
        $nb_actions = $row['nb_actions'];
        $bounce_rate = $row['bounce_rate'];
        $nb_actions_per_visit = $row['nb_actions_per_visit'];
        $avg_time_on_site = $row['avg_time_on_site'];
        $avg_time_on_site = ((int)$avg_time_on_site > 60*60) ? date('H:i:s', $avg_time_on_site) . ' ' . Lang::get('global.hours_abbr') : date('i:s', $avg_time_on_site) . ' ' . Lang::get('global.minutes');
    }
    elseif($site_created <= $oDate->format('Y-m-d'))
    {
        $nb_uniq_visitors = 0;
        $nb_visits = 0;
        $nb_actions = 0;
        $bounce_rate = '0%';
        $nb_actions_per_visit = 0;
        $avg_time_on_site = '00:00 ' . Lang::get('global.minutes');
    }
    else
    {
        $show_row = false;
    }

	if($show_row)
	{
?>
                            <tr>
                                <td>{{ $date }}</td>
                                <td class="text-right">{{ $nb_uniq_visitors }}</td>
                                <td class="text-right">{{ $nb_visits }}</td>
<?php /*                                <td class="text-right">{{ $nb_actions }}</td>
                                <td class="text-right">{{ $nb_actions_per_visit }}</td>
                                <td class="text-right">{{ $bounce_rate }}</td>*/ ?>
                                <td class="text-right">{{ $avg_time_on_site }}</td>
                            </tr>
<?php
	}
}
?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

		<!-- Page wide horizontal line -->
		<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">

        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="panel-title"><i class="panel-title-icon fa fa-link"></i> {{ trans('global.referrers') }}</span>
            </div>
            <div class="tab-content no-padding">


                <script>
                    $('#table-referrers').dataTable({
                        dom: "t"+
                              "<'table-footer clearfix'<'DT-label'i><'DT-pagination'p>>",
                        order: [
                            [0, "desc"]
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
						}
                    });
                </script>

                <div class="table-primary no-margin">
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table-referrers" style="margin:0">
                        <thead>
                            <tr>
                                <th>{{ trans('global.website') }}</th>
                                <th>{{ trans('global.url') }}</th>
                                <th class="text-right">{{ trans('global.visits') }} &nbsp;</th>
<?php /*                                                                <th class="text-right">{{ trans('global.actions') }} &nbsp;</th>*/ ?>
                            </tr>
                        </thead>
                        <tbody>
<?php
foreach($referrers_range as $row)
{
    $oDate = new DateTime($date);
    $date = $oDate->format('Y-m-d');

/*
array (size=11)
  'label' => string 'piwik.dev' (length=9)
  'nb_visits' => int 1
  'nb_actions' => int 1
  'max_actions' => float 1
  'sum_visit_length' => int 0
  'bounce_count' => int 1
  'nb_visits_converted' => int 0
  'sum_daily_nb_uniq_visitors' => int 1
  'sum_daily_nb_users' => int 0
  'idsubdatatable' => int 7
  'subtable' => 
    array (size=1)
      0 => 
        array (size=9)
          'label' => string 'http://piwik.dev/referrer.html' (length=30)
          'nb_visits' => int 1
          'nb_actions' => int 1
          'max_actions' => float 1
          'sum_visit_length' => int 0
          'bounce_count' => int 1
          'nb_visits_converted' => int 0
          'sum_daily_nb_uniq_visitors' => int 1
          'sum_daily_nb_users' => int 0
*/

?>
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td><a href="{{ $row['subtable'][0]['label'] }}" target="_blank">{{ $row['subtable'][0]['label'] }}</a></td>
                                <td class="text-right">{{ $row['nb_visits'] }}</td>
<?php /*                                                                <td class="text-right">{{ $row['nb_actions'] }}</td>*/ ?>
                            </tr>
<?php
}
?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

		<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">

			<div class="row">
				<div class="col-md-4">

				<div class="panel panel-default">
					<div class="panel-heading">
						<span class="panel-title"><i class="panel-title-icon fa fa-compass"></i> {{ trans('global.browsers') }}</span>
					</div>
					<div class="tab-content no-padding">

						<div class="panel-body no-padding no-margin">

						<script>
                            Morris.Donut({
                                element: 'donut-browsers',
                                data: [
<?php
$donut = '';
$total = 0;
foreach($browsers_range as $row)
{
    $total += (int)$row['nb_visits'];
}

foreach($browsers_range as $row)
{
    $donut .= '{ label: "' . $row['label'] . '", value: ' . round(($row['nb_visits'] / $total) * 100, 0) . ' },';
}
$donut = trim($donut, ',');
echo $donut;
?>
                                ],
                                colors: CmsAdmin.settings.consts.COLORS,
                                resize: true,
                                labelColor: '#888',
                                formatter: function (y) { return y + "%" }
                            });

							$('#table-browsers').dataTable({
							    dom: "t"+
									  "<'table-footer clearfix'<'DT-label'i><'DT-pagination'p>>",
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
								}
							});
						</script>

                        <div id="donut-browsers" class="graph"></div>

						<div class="table-primary no-margin">
							<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table-browsers" style="margin:0">
								<thead>
									<tr>
										<th>{{ trans('global.browser') }}</th>
										<th class="text-right">{{ trans('global.visits') }} &nbsp;</th>
<?php /*                                										<th class="text-right">{{ trans('global.actions') }} &nbsp;</th>*/ ?>
									</tr>
								</thead>
								<tbody>
<?php
foreach($browsers_range as $row)
{
?>
									<tr>
										<td>{{ $row['label'] }}</td>
										<td class="text-right">{{ $row['nb_visits'] }}</td>
<?php /*                                										<td class="text-right">{{ $row['nb_actions'] }}</td>*/ ?>
									</tr>
<?php
}
?>

								</tbody>
							</table>
						</div>

						</div>

					</div>
				</div>


				</div>
				<div class="col-md-4">

				<div class="panel panel-default">
					<div class="panel-heading">
						<span class="panel-title"><i class="panel-title-icon fa fa-desktop"></i> {{ trans('global.operating_systems') }}</span>
					</div>
					<div class="tab-content no-padding">

						<div class="panel-body no-padding no-margin">

						<script>
                            Morris.Donut({
                                element: 'donut-os',
                                data: [
<?php
$donut = '';
$total = 0;
foreach($os_range as $row)
{
    $total += (int)$row['nb_visits'];
}

foreach($os_range as $row)
{
    $donut .= '{ label: "' . $row['label'] . '", value: ' . round(($row['nb_visits'] / $total) * 100, 0) . ' },';
}
$donut = trim($donut, ',');
echo $donut;
?>
                                ],
                                colors: CmsAdmin.settings.consts.COLORS,
                                resize: true,
                                labelColor: '#888',
                                formatter: function (y) { return y + "%" }
                            });

							$('#table-os').dataTable({
							    dom: "t"+
									  "<'table-footer clearfix'<'DT-label'i><'DT-pagination'p>>",
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
								}
							});
						</script>

						<div class="table-primary no-margin">
  
                            <div id="donut-os" class="graph"></div>

							<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table-os" style="margin:0">
								<thead>
									<tr>
										<th>{{ trans('global.os') }}</th>
										<th class="text-right">{{ trans('global.visits') }} &nbsp;</th>
<?php /*                                										<th class="text-right">{{ trans('global.actions') }} &nbsp;</th>*/ ?>
									</tr>
								</thead>
								<tbody>
<?php
foreach($os_range as $row)
{
?>
									<tr>
										<td>{{ $row['label'] }}</td>
										<td class="text-right">{{ $row['nb_visits'] }}</td>
<?php /*                                										<td class="text-right">{{ $row['nb_actions'] }}</td>*/ ?>
									</tr>
<?php
}
?>

								</tbody>
							</table>
						</div>

						</div>

					</div>
				</div>


				</div>
				<div class="col-md-4">

				<div class="panel panel-default">
					<div class="panel-heading">
						<span class="panel-title"><i class="panel-title-icon fa fa-globe"></i> {{ trans('global.countries') }}</span>
					</div>
					<div class="tab-content no-padding">

						<div class="panel-body no-padding no-margin">

						<script>
                            Morris.Donut({
                                element: 'donut-countries',
                                data: [
<?php
$donut = '';
$total = 0;
foreach($countries_range as $row)
{
    $total += (int)$row['nb_visits'];
}

foreach($countries_range as $row)
{
    $donut .= '{ label: "' . $row['label'] . '", value: ' . round(($row['nb_visits'] / $total) * 100, 0) . ' },';
}
$donut = trim($donut, ',');
echo $donut;
?>
                                ],
                                colors: CmsAdmin.settings.consts.COLORS,
                                resize: true,
                                labelColor: '#888',
                                formatter: function (y) { return y + "%" }
                            });

							$('#table-countries').dataTable({
							    dom: "t"+
									  "<'table-footer clearfix'<'DT-label'i><'DT-pagination'p>>",
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
								}
							});
						</script>

						<div class="table-primary no-margin">

                            <div id="donut-countries" class="graph"></div>

							<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="table-countries" style="margin:0">
								<thead>
									<tr>
										<th>{{ trans('global.country') }}</th>
										<th class="text-right">{{ trans('global.visits') }} &nbsp;</th>
<?php /*                                										<th class="text-right">{{ trans('global.actions') }} &nbsp;</th>*/ ?>
									</tr>
								</thead>
								<tbody>
<?php
foreach($countries_range as $row)
{
?>
									<tr>
										<td>{{ $row['label'] }}</td>
										<td class="text-right">{{ $row['nb_visits'] }}</td>
<?php /*                                										<td class="text-right">{{ $row['nb_actions'] }}</td>*/ ?>
									</tr>
<?php
}
?>

								</tbody>
							</table>
						</div>

						</div>

					</div>
				</div>



				</div>
			</div>
<?php
} // No site selected or stats found
?>

<script>

/*
setEqHeight();
$(window).on('pa.resize', setEqHeight);
$(window).resize();
*/
<?php
if($site !== false)
{
?>

$('#stats-range').daterangepicker({
	ranges: {
		 '<?php echo trans('global.today') ?>': [ Date.today(), Date.today() ],
		 '<?php echo trans('global.yesterday') ?>': [ Date.today().add({ days: -1 }), Date.today().add({ days: -1 }) ],
		 '<?php echo trans('global.last_7_days') ?>': [ Date.today().add({ days: -6 }), Date.today() ],
		 '<?php echo trans('global.last_30_days') ?>': [ Date.today().add({ days: -29 }), Date.today() ],
		 '<?php echo trans('global.this_month') ?>': [ Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth() ],
		 '<?php echo trans('global.last_month') ?>': [ Date.today().moveToFirstDayOfMonth().add({ months: -1 }), Date.today().moveToFirstDayOfMonth().add({ days: -1 }) ]
	},
	opens: 'left',
	format: 'MM-DD-YYYY',
	separator: ' <?php echo trans('global.date_to') ?> ',
	startDate: Date.parse('<?php echo $date_start ?>').toString('MM-d-yyyy'),
	endDate: Date.parse('<?php echo $date_end ?>').toString('MM-d-yyyy'),
	minDate: Date.parse('<?php echo $site_created ?>').toString('MM-d-yyyy'),
	maxDate: '<?php echo date('m/d/Y') ?>',
	locale: {
		applyLabel: '<?php echo trans('global.submit') ?>',
		cancelLabel: '<?php echo trans('global.reset') ?>',
		fromLabel: '<?php echo trans('global.date_from') ?>',
		toLabel: '<?php echo trans('global.date_to') ?>',
		customRangeLabel: '<?php echo trans('global.custom_range') ?>',
		daysOfWeek: ['<?php echo trans('global.su') ?>', '<?php echo trans('global.mo') ?>', '<?php echo trans('global.tu') ?>', '<?php echo trans('global.we') ?>', '<?php echo trans('global.th') ?>', '<?php echo trans('global.fr') ?>','<?php echo trans('global.sa') ?>'],
		monthNames: monthNames,
		firstDay: 1
	},
	showWeekNumbers: true,
	buttonClasses: ['btn']
});

$('#stats-range').on('apply.daterangepicker', function(ev, picker) {
    var start = picker.startDate.format('YYYY-MM-DD');
    var end = picker.endDate.format('YYYY-MM-DD');
    document.location = '#/web/analytics/' + start + '/' + end + '/{{ $sl }}';
});

/* Set the initial state of the picker label */
var d_start = Date.parse('<?php echo $date_start ?>');
var d_end = Date.parse('<?php echo $date_end ?>');

d_start = monthNames[d_start.getMonth()] + ' ' + d_start.toString('d, yyyy');
d_end = monthNames[d_end.getMonth()] + ' ' + d_end.toString('d, yyyy');

var d_string = (d_start == d_end) ? d_start : d_start + ' - ' + d_end;

$('#stats-range span').html(d_string);
<?php
}
?>
</script>
@stop