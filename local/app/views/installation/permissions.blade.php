@extends('app.layouts.clean')

@section('content')

  <div class="container">
    <div class="col-md-12">
        <br><br>
				<div class="panel">
					<div class="panel-heading">
						<span class="panel-title"><strong>{{ Lang::get('global.app_title') }}</strong> Installation</span>
					</div>
					<div class="panel-body no-padding">

						<div class="wizard no-margin" id="wizard-forms">

							<div class="wizard-wrapper no-border">
								<ul class="wizard-steps">
									<li data-target="#wizard-account">
										<span class="wizard-step-number">1</span>
										<span class="wizard-step-caption">
											Prerequisites
											<span class="wizard-step-description">Server Requirements</span>
										</span>
									</li>
                  <li data-target="#wizard-profile" class="active">
										<span class="wizard-step-number">2</span>
										<span class="wizard-step-caption">
											Permissions
											<span class="wizard-step-description">Directory Permissions</span>
										</span>
									</li>
                  <li data-target="#wizard-credit-card">
										<span class="wizard-step-number">3</span>
										<span class="wizard-step-caption">
											Configuration
											<span class="wizard-step-description">Database &amp; Email</span>
										</span>
									</li>
                  <li data-target="#wizard-finish">
										<span class="wizard-step-number">4</span>
										<span class="wizard-step-caption">
											Ready
										</span>
									</li>
								</ul>
							</div>

							<div class="wizard-content panel no-margin no-border-hr no-border-b no-padding-hr">
              <p class="lead panel-padding-h">Make sure all files and directories below are writable.</p>
<?php

/**
 * Directory permissions
 */

$dirs = array(
  '/app/config/production/',
  '/app/storage/cache/',
  '/app/storage/logs/',
  '/app/storage/meta/',
  '/app/storage/meta/services.json',
  '/app/storage/sessions/',
  '/app/storage/userdata/',
  '/app/storage/views/',
  '/../uploads/',
  '/../uploads/attachments/',
  '/../uploads/user/',
  '/../stock/.tmb/',
  '/../stock/.quarantine/'
);

$error = '';

foreach($dirs as $dir)
{
  $full_dir = base_path() . $dir;
  if(! \File::isWritable($full_dir))
  {
    if(strpos($full_dir, '../') !== false) $full_dir = str_replace('local/../', '', $full_dir);

    $requirements[] = array(
      'success' => false,
      'msg' => $full_dir . ' is not writeable'
    );
 
  } else {
    if(strpos($full_dir, '../') !== false) $full_dir = str_replace('local/../', '', $full_dir);

    $requirements[] = array(
      'success' => true,
      'msg' => $full_dir . ' is writeable'
    );
  }
}

foreach($requirements as $requirement)
{
	if ($requirement['success'])
	{
    echo '<div class="panel-padding-h">';
		echo '<div class="alert alert-success"><i class="fa fa-check text-success"></i> ' . $requirement['msg'] . '</div>';
    echo '</div>';
	}
	else
	{
    echo '<div class="panel-padding-h">';
		echo '<div class="alert alert-danger"><i class="fa fa-times text-danger"></i> ' . $requirement['msg'] . '</div>';
    echo '</div>';
	}
}

?>
              <hr>

              <div class="pull-right panel-padding-h">
                <a href="{{ url('/') }}" class="btn btn-lg"><i class="fa fa-arrow-left" aria-hidden="true"></i> Prev</a>
                <a href="{{ url('/?step=3') }}" class="btn btn-lg btn-primary">Next <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop