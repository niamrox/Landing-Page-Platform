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
                  <li data-target="#wizard-profile">
										<span class="wizard-step-number">2</span>
										<span class="wizard-step-caption">
											Permissions
											<span class="wizard-step-description">Directory Permissions</span>
										</span>
									</li>
                  <li data-target="#wizard-credit-card" class="active">
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
                <form class="wizard-pane form-bordered" action="{{ url('?step=4') }}" id="config_form" method="post">

@if($errors->any())
@foreach ($errors->all() as $error)

<div class="panel-padding-h">
<div class="alert alert-danger"><i class="fa fa-times text-danger"></i> {{ $error }}</div>
</div>

@endforeach
@endif

                <h3 class="panel-padding-h">MySQL</h3>

                <div class="form-group panel-padding-h">
                  <label for="mysql_host" class="col-md-3 control-label">Host</label>
                  <div class="col-md-9">
                    <input type="text" name="mysql_host" id="mysql_host" placeholder="localhost" value="{{ \Input::old('localhost', 'localhost') }}" class="form-control" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}">
                  </div>
                </div>

                <div class="form-group no-padding-t no-border-t panel-padding-h">
                  <label for="mysql_database" class="col-md-3 control-label">Database</label>
                  <div class="col-md-9">
                    <input type="text" name="mysql_database" id="mysql_database" placeholder="Database" class="form-control" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}" value="{{ \Input::old('mysql_database', '') }}">
                  </div>
                </div>

                <div class="form-group no-padding-t no-border-t panel-padding-h">
                  <label for="mysql_username" class="col-md-3 control-label">Username</label>
                  <div class="col-md-9">
                    <input type="text" name="mysql_username" id="mysql_username" placeholder="Username" class="form-control" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}" value="{{ \Input::old('mysql_username', '') }}">
                  </div>
                </div>

                <div class="form-group no-padding-t no-border-t panel-padding-h">
                  <label for="mysql_password" class="col-md-3 control-label">Password</label>
                  <div class="col-md-9">
                    <input type="password" name="mysql_password" id="mysql_password" placeholder="Password" class="form-control" value="{{ \Input::old('mysql_password', '') }}">
                  </div>
                </div>

                <br>

                <h3 class="panel-padding-h">Email</h3>

                <div class="form-group panel-padding-h">
                  <label for="email_driver" class="col-md-3 control-label">Driver</label>
                  <div class="col-md-9">
                  
                    <select class="form-control" name="email_driver" id="email_driver">
                      <option value="smtp"<?php if (\Input::old('email_driver', 'mail') == 'smtp') echo ' selected'; ?>>smtp</option>
                      <option value="mail"<?php if (\Input::old('email_driver', 'mail') == 'mail') echo ' selected'; ?>>mail</option>
                      <option value="sendmail"<?php if (\Input::old('email_driver', 'mail') == 'sendmail') echo ' selected'; ?>>sendmail</option>
                    </select>
                  </div>
                </div>

               <div id="smtp_settings" style="display:<?php echo (\Input::old('email_driver', 'mail') == 'smtp') ? 'block': 'none'; ?>">

                  <div class="form-group no-padding-t no-border-t panel-padding-h">
                    <label for="smtp_host" class="col-md-3 control-label">SMTP Host</label>
                    <div class="col-md-9">
                      <input type="text" name="smtp_host" id="smtp_host" placeholder="smtp.gmail.com" class="form-control" value="{{ \Input::old('smtp_host', '') }}">
                    </div>
                  </div>

                  <div class="form-group no-border-t panel-padding-h">
                    <label for="smtp_encryption" class="col-md-3 control-label">SMTP Encryption</label>
                    <div class="col-md-9">
                    
                      <select class="form-control" name="smtp_encryption" id="smtp_encryption">
                        <option value="ssl"<?php if (\Input::old('smtp_encryption', 'ssl') == 'ssl') echo ' selected'; ?>>ssl</option>
                        <option value="tls"<?php if (\Input::old('smtp_encryption', 'ssl') == 'tls') echo ' selected'; ?>>tls</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group no-padding-t no-border-t panel-padding-h">
                    <label for="smtp_port" class="col-md-3 control-label">SMTP Port</label>
                    <div class="col-md-9">
                      <input type="number" name="smtp_port" id="smtp_port" placeholder="465" class="form-control" value="{{ \Input::old('smtp_port', '465') }}">
                      <p class="help-block">Usually <strong>465</strong> if encryption is ssl and <strong>587</strong> if encryption is tls.</p>
                    </div>
                  </div>

                  <div class="form-group no-padding-t no-border-t panel-padding-h">
                    <label for="smtp_username" class="col-md-3 control-label">SMTP Username</label>
                    <div class="col-md-9">
                      <input type="text" name="smtp_username" id="smtp_username" placeholder="Username" class="form-control" value="{{ \Input::old('smtp_username', '') }}">
                    </div>
                  </div>

                  <div class="form-group no-padding-t no-border-t panel-padding-h">
                    <label for="smtp_password" class="col-md-3 control-label">SMTP Password</label>
                    <div class="col-md-9">
                      <input type="password" name="smtp_password" id="smtp_password" placeholder="Password" class="form-control" value="{{ \Input::old('smtp_password', '') }}">
                    </div>
                  </div>

                </div>

                <div class="form-group no-padding-t no-border-t panel-padding-h">
                  <label for="email_from_name" class="col-md-3 control-label">From name</label>
                  <div class="col-md-9">
                    <input type="text" name="email_from_name" id="email_from_name" placeholder="My Site" class="form-control" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}" value="{{ \Input::old('email_from_name', '') }}">
                  </div>
                </div>

                <div class="form-group no-padding-t no-border-t panel-padding-h">
                  <label for="email_from_address" class="col-md-3 control-label">From address</label>
                  <div class="col-md-9">
                    <input type="text" name="email_from_address" id="email_from_address" placeholder="info@example.com" class="form-control" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}" value="{{ \Input::old('email_from_address', '') }}">
                  </div>
                </div>

                <br>
                <hr>

                <p class="lead panel-padding-h">After installation you can login with <strong>info@example.com</strong> / <strong>welcome</strong>.</p>

                <p class="panel-padding-h">Make sure you install Piwik for analytics, <a href="http://nowsquare.com/landing-page-platform/documentation/v1/getting-started/install-piwik" target="_blank">http://nowsquare.com/landing-page-platform/documentation/v1/getting-started/install-piwik</a>.</p>

                <p class="panel-padding-h">For more information on configuration options, visit <a href="http://nowsquare.com/landing-page-platform/documentation/v1/getting-started" target="_blank">http://nowsquare.com/landing-page-platform/documentation/v1/getting-started</a>.</p>

                <hr>

                <div class="pull-right panel-padding-h">
                  <a href="{{ url('/?step=2') }}" class="btn btn-lg"><i class="fa fa-arrow-left" aria-hidden="true"></i> Prev</a>
                  <button type="submit" class="btn btn-lg btn-success"><i class="fa fa-check" aria-hidden="true"></i> Install</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="{{ url('/assets/js/frontend.js?v=' . Config::get('system.version')) }}"></script>

<script>
$(function() {
  $('#email_driver').on('change', function() {
    if ($(this).val() == 'smtp') {
     $('#smtp_settings').show();
    } else {
     $('#smtp_settings').hide();
    }
  });

  $('#config_form').formValidation({
    framework: 'bootstrap',
    icon: {
      valid: 'fa fa-check',
      invalid: 'fa fa-times',
      validating: 'fa fa-refresh'
    }
  });

});
</script>
<style type="text/css">
.form-control-feedback {
  top: 8px;
  right: 8px;
}
</style>
@stop