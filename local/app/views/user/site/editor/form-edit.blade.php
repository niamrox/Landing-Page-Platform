<div class="app-ie">
    <div class="modal-dialog" style="width: 720px">
        <div class="modal-content">
            <form id="fb-form-editor" onsubmit="return false;">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 class="modal-title">{{ trans('global.form_builder') }}</h4>
                </div>
                <div class="modal-body">

                    <div id="fb-field-list">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('global.name') }}</th>
                                    <th>{{ trans('global.type') }}</th>
                                    <th class="text-center">{{ trans('global.options') }}</th>
                                    <th class="text-center">{{ trans('global.mandatory') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="fb-field-tbody">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <button type="button" class="btn btn-success btn-lg fb-add-row-btn btn-block"><i class="fa fa-plus"></i> {{ trans('global.add_new_field') }}</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>

                    <div class="fb-form-options">
                        <div class="panel-group">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab">
                                    <h4 class="panel-title"><a id="fb-settings-form-btn"><i class="fa fa-caret-right"></i> {{ trans('global.form') }}</a></h4>
                                </div>
                                <div id="fb-settings-form" class="panel-collapse" role="tabpanel" style="display:none">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="fb_btn_text">{{ trans('global.button') }}</label>
                                            <input type="text" class="form-control" id="fb_btn_text" required />
                                        </div>
                                        <div class="form-group">
                                            <label for="fb_mail_to">{{ trans('editor-form.send_copy_to') }}</label>
                                            <input type="text" class="form-control" id="fb_mail_to" placeholder="{{ \Auth::user()->email }}" />
											<p class="help-block">{{ trans('editor-form.send_copy_to_info') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab">
                                    <h4 class="panel-title"><a id="fb-settings-success-btn"><i class="fa fa-caret-right"></i> {{ trans('global.form_success_info') }}</a></h4>
                                </div>
                                <div id="fb-settings-success" class="panel-collapse" role="tabpanel">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="fb_success_msg">{{ trans('global.title') }}</label>
                                            <input type="text" class="form-control" id="fb_success_title" required />
                                        </div>
                                        <div class="form-group">
                                            <label for="fb_success_msg">{{ trans('global.message') }}</label>
                                            <textarea class="form-control" rows="3" style="height: 70px" id="fb_success_msg" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="fb_success_msg">{{ trans('global.button') }}</label>
                                            <input type="text" class="form-control" id="fb_success_btn" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab">
                                    <h4 class="panel-title"><a id="fb-settings-redir-btn"><i class="fa fa-caret-right"></i> {{ trans('editor-form.redirect_after_submission') }}</a></h4>
                                </div>
                                <div id="fb-settings-redir" class="panel-collapse" role="tabpanel" style="display:none">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="fb_success_redirect">{{ trans('global.website') }}</label>
											<select class="form-control" id="fb_success_redirect">
												<option value="">{{ trans('editor-form.no_redirect') }}</option>
<?php
if (count($sites) > 0)
{
	foreach ($sites as $site)
	{
   		//$sl_site = \App\Core\Secure::array2string(array('site_id' => $site->id));
		echo '<option value="' . $site->id . '">' . $site->name . '</option>';
	}
}
?>
											</select>
                                        </div>
                                    </div>
                                </div>
                            </div>

<?php 
if ($aweber_available) {
?>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab">
                                    <h4 class="panel-title"><a id="fb-settings-aweber-btn"><i class="fa fa-caret-right"></i> Aweber</a></h4>
                                </div>
                                <div id="fb-settings-aweber" class="panel-collapse" role="tabpanel" style="display:none">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="fb_aweber_list">{{ trans('global.list') }}</label>
											<select class="form-control" id="fb_aweber_list">
												<option value="">{{ trans('global.no_list_selected') }}</option>
<?php
if (count($aweber_lists) > 0)
{
	foreach ($aweber_lists as $list)
	{
		echo '<option value="' . $list['id'] . '">' . $list['name'] . '</option>';
	}
}
?>
											</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
<?php } ?>
<?php 
if ($getresponse_available) {
?>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab">
                                    <h4 class="panel-title"><a id="fb-settings-getresponse-btn"><i class="fa fa-caret-right"></i> GetResponse</a></h4>
                                </div>
                                <div id="fb-settings-getresponse" class="panel-collapse" role="tabpanel" style="display:none">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="fb_getresponse_list">{{ trans('global.list') }}</label>
											<select class="form-control" id="fb_getresponse_list">
												<option value="">{{ trans('global.no_list_selected') }}</option>
<?php
if (count($getresponse_lists) > 0)
{
	foreach ($getresponse_lists as $list)
	{
		echo '<option value="' . $list['id'] . '">' . $list['name'] . '</option>';
	}
}
?>
											</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
<?php } ?>
<?php 
if ($mailchimp_available) {
?>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab">
                                    <h4 class="panel-title"><a id="fb-settings-mailchimp-btn"><i class="fa fa-caret-right"></i> MailChimp</a></h4>
                                </div>
                                <div id="fb-settings-mailchimp" class="panel-collapse" role="tabpanel" style="display:none">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="fb_mailchimp_list">{{ trans('global.list') }}</label>
											<select class="form-control" id="fb_mailchimp_list">
												<option value="">{{ trans('global.no_list_selected') }}</option>
<?php
if (count($mailchimp_lists) > 0)
{
	foreach ($mailchimp_lists as $list)
	{
		echo '<option value="' . $list['id'] . '">' . $list['name'] . '</option>';
	}
}
?>
											</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
<?php } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary fb-form-editor-save" type="submit">{{ trans('global.update') }}</button>
                    <button class="btn" data-dismiss="modal" type="button">{{ trans('global.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
