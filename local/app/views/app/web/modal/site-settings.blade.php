<div class="modal-dialog" style="width:800px">
<?php
echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/site/site-settings'))
	->method('POST');

echo Former::hidden()
    ->value($sl)
    ->name('sl');
?>
	<div class="modal-content">
		<div class="modal-header">
			<button class="close" type="button" data-dismiss="modal">×</button>
			<?php echo trans('global.website_settings') ?>
        </div>
		<div class="modal-body">

			<div class="container-fluid">

				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#tab-title" data-toggle="tab" onclick="return false;">{{ trans('global.title_and_seo') }}</a>
					</li>
					<li>
						<a href="#tab-general" data-toggle="tab" onclick="return false;">{{ trans('global.general') }}</a>
					</li>
					<li>
						<a href="#tab-tracking" data-toggle="tab" onclick="return false;">{{ trans('global.tracking_codes') }}</a>
					</li>
					<li>
						<a href="#tab-js" data-toggle="tab" onclick="return false;">{{ trans('global.js') }}</a>
					</li>
					<li>
						<a href="#tab-css" data-toggle="tab" onclick="return false;">{{ trans('global.css') }}</a>
					</li>
				</ul>

				<div class="tab-content tab-content-bordered">

					<div class="tab-pane fade active in" id="tab-title">
<?php
echo Former::text()
	->class('form-control')
    ->name('app_page_name')
    ->forceValue($page->meta_title)
	->label(trans('global.page_title'))
    ->required();

echo "<br/>";

echo Former::textarea()
	->class('form-control')
    ->name('app_meta_description')
    ->forceValue($page->meta_desc)
    ->style('height:52px; width: 100%;')
	->label(trans('global.meta_description'));

echo "<br/>";

echo Former::select('app_meta_robots')
	->class('form-control')
    ->name('app_meta_robots')
    ->forceValue($page->meta_robots)
    ->style('width: 200px; width: 100%;')
	->options(array(
		'index, follow' => 'index, follow',
		'index, nofollow' => 'index, nofollow',
		'noindex, nofollow' => 'noindex, nofollow'
	))
	->label(trans('global.search_engines'));
?>

					</div>
					<div class="tab-pane fade" id="tab-general">

<?php
echo Former::text()
    ->name('name')
    ->forceValue($site->name)
	->label(trans('global.name'))
	->dataFvNotempty()
    ->required();


$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';

echo Former::text()
	->name('local_domain')
	->forceValue($site->local_domain)
	->label(trans('global.domain'))
	->help(' ')
	->prepend($protocol . \Request::server('HTTP_HOST') . '/web/');


if ($domain)
{
	echo Former::text()
		->name('domain')
		->forceValue($site->domain)
		->label(trans('global.custom_domain'))
		->help(trans('global.custom_domain_info', array('host' => \Request::server('HTTP_HOST'))))
		->prepend('http://');
}
/*
echo Former::text()
    ->name('domain')
    ->forceValue($site->domain)
	->label(trans('global.domain'))
	->help(trans('global.custom_domain_info', array('host' => \Request::server('HTTP_HOST'))))
    ->prepend('http://');
*/
echo '<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">';

echo Former::select('language')
	->class('select2-required form-control')
    ->name('language')
    ->id('language')
    ->forceValue($site->language)
	->options(trans('languages.languages'))
	->label(trans('global.language'));

echo Former::select('timezone')
	->class('select2-required form-control')
    ->name('timezone')
    ->forceValue($site->timezone)
	->options(trans('timezones.timezones'))
	->label(trans('global.timezone'));
?>

					</div>
					<div class="tab-pane fade" id="tab-tracking">
<?php
echo '<p><strong>' . trans('global.publish_required_for_site_changes') . '</strong></p>';
echo '<p>' . trans('global.head_tag_tracking') . '</p>';

$head_tag = (isset($site->settings->head_tag)) ? $site->settings->head_tag : '';

echo '<textarea name="head_tag" id="head_tag" rows="6" class="form-control">' . $head_tag . '</textarea>';

echo '<br><p>' . trans('global.end_of_body_tag_tracking') . '</p>';

$end_of_body_tag = (isset($site->settings->end_of_body_tag)) ? $site->settings->end_of_body_tag : '';

echo '<textarea name="end_of_body_tag" id="end_of_body_tag" rows="6" class="form-control">' . $end_of_body_tag . '</textarea>';
?>

					</div>
					<div class="tab-pane fade" id="tab-js">
<?php
echo '<p><strong>' . trans('global.publish_required_for_site_changes') . '</strong></p>';

$js = (isset($site->settings->js)) ? $site->settings->js : '';

echo '<textarea name="js" id="js" rows="12" class="form-control">' . $js . '</textarea>';
?>


					</div>
					<div class="tab-pane fade" id="tab-css">
<?php
echo '<p><strong>' . trans('global.publish_required_for_site_changes') . '</strong></p>';

$css = (isset($site->settings->css)) ? $site->settings->css : '';

echo '<textarea name="css" id="css" rows="12" class="form-control">' . $css . '</textarea>';
?>
					</div>
				</div>


			</div>

		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" type="submit"><?php echo Lang::get('global.save') ?></button>
			<button class="btn" data-dismiss="modal" type="button"><?php echo Lang::get('global.close') ?></button>
		</div>
<?php
echo Former::close();
?>
	</div>
</div>
<script>
var url_changed = false;

$('#local_domain,#domain').keydown(function() { url_changed = true; });

function formSubmittedSuccess(result, r)
{
	if (result == 'error')
	{
		$('#local_domain').closest('.form-group').addClass('has-error');
		$('#local_domain').parent().next().html(r.msg);
	}
	else
	{
		parent.$('#site_name').editable('setValue', $('#name').val());
		local_domain = $('#local_domain').val();
		if (url_changed) document.getElementById('site-preview').contentDocument.location = r.url;
		angular.element(document.getElementById('qrModal')).scope().url = r.url;
		parent.showSaved();
		$modal.modal('hide');
	}
}
</script>