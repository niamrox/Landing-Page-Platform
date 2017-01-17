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
									<li data-target="#wizard-account" class="active">
										<span class="wizard-step-number">1</span>
										<span class="wizard-step-caption">
											Prerequisites
											<span class="wizard-step-description">Server Requirements</span>
										</span>
									</li>
                  <li data-target="#wizard-profile">
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
              <p class="lead panel-padding-h">Make sure your server meets all requirements below.</p>

<?php
// Check PHP version
if (! version_compare(phpversion(), '5.5.0', '>='))
{
	$error = true;
	$requirements[] = array(
		'success' => false,
		'msg' => 'PHP ' . phpversion() . ' - Version 5.5.0 or higher is required'
	);
}
else
{
	$requirements[] = array(
		'success' => true,
		'msg' => 'PHP ' . phpversion() . ''
	);
}

// Check mod rewrite
$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) == 'cgi')
{
	if (! strpos(shell_exec('/usr/local/apache/bin/apachectl -l'), 'mod_rewrite') !== false)
	{
		$error = true;
		$requirements[] = array(
			'success' => false,
			'msg' => 'Mod Rewrite isn\'t enabled'
		);
	}
	else
	{
		$requirements[] = array(
			'success' => true,
			'msg' => 'Mod Rewrite is enabled'
		);
	}
}
else
{
  if(! function_exists('apache_get_modules'))
  {
    if (getenv('HTTP_MOD_REWRITE') != 'On')
    {
      $error = true;
      $requirements[] = array(
        'success' => false,
        'msg' => 'Mod Rewrite isn\'t enabled'
      );
    }
    else
    {
      $requirements[] = array(
        'success' => true,
        'msg' => 'Mod Rewrite is enabled'
      );
    }
  }
  else
  {
    if (! in_array('mod_rewrite', apache_get_modules()))
    {
      $error = true;
      $requirements[] = array(
        'success' => false,
        'msg' => 'Mod Rewrite isn\'t enabled'
      );
    }
    else
    {
      $requirements[] = array(
        'success' => true,
        'msg' => 'Mod Rewrite is enabled'
      );
    }
  }
}

// Check if CURL is enabled and has the correct version
if (! function_exists('curl_version'))
{
	$error = true;
	$requirements[] = array(
		'success' => false,
		'msg' => 'cURL is not enabled'
	);
}
else
{
	// Check CURL version
	$curl_version = curl_version();
	if (! version_compare($curl_version['version'], '7.19.4', '>='))
	{
		$error = true;
		$requirements[] = array(
			'success' => false,
			'msg' => 'cURL ' . $curl_version['version'] . ' - Version 7.19.4 or higher is required'
		);
	}
	else
	{
		$requirements[] = array(
			'success' => true,
			'msg' => 'cURL ' . $curl_version['version'] . ''
		);
	}
}

// Check if SQLite is installed
if (! extension_loaded('sqlite3'))
{
	$error = true;
	$requirements[] = array(
		'success' => false,
		'msg' => 'SQLite3 is not installed'
	);
}
else
{
	$requirements[] = array(
		'success' => true,
		'msg' => 'SQLite3 is installed'
	);
}

// Check if PDO SQLite is installed
if (! extension_loaded('pdo_sqlite'))
{
	$error = true;
	$requirements[] = array(
		'success' => false,
		'msg' => 'SQLite PDO drivers not installed'
	);
}
else
{
	$requirements[] = array(
		'success' => true,
		'msg' => 'SQLite PDO drivers installed'
	);
}

// Check if mcrypt is installed
if (! extension_loaded('mcrypt'))
{
	$error = true;
	$requirements[] = array(
		'success' => false,
		'msg' => 'Mcrypt extension is missing'
	);
}
else
{
	$requirements[] = array(
		'success' => true,
		'msg' => 'Mcrypt extension is installed'
	);
}

// Check if fileinfo is installed
if (! extension_loaded('fileinfo') || ! function_exists('mime_content_type'))
{
	$error = true;
	$requirements[] = array(
		'success' => false,
		'msg' => 'Fileinfo extension is missing'
	);
}
else
{
	$requirements[] = array(
		'success' => true,
		'msg' => 'Fileinfo extension is installed'
	);
}

// Check if ZipArchive is installed
if (! class_exists('ZipArchive'))
{
	$error = true;
	$requirements[] = array(
		'success' => false,
		'msg' => 'ZipArchive class is missing'
	);
}
else
{
	$requirements[] = array(
		'success' => true,
		'msg' => 'ZipArchive class is installed'
	);
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
                <a href="{{ url('/?step=2') }}" class="btn btn-lg btn-primary">Next <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop