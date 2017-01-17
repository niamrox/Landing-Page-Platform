<?php

$type = \Request::get('type', 'link');

?>
<div class="app-ie">
    <div class="modal-dialog" style="width: 720px">
        <div class="modal-content">
            <form id="le-button-editor" onsubmit="return false;">
                <input type="hidden" id="le_id" value="">
                <input type="hidden" name="le_type" id="le_type" value="{{ $type }}">
                <input type="hidden" name="le_href_only" id="le_href_only" value="">

                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 class="modal-title">{{ trans('editor-link.button_editor') }}</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group" id="le_html_field">
                        <label for="le_html">{{ trans('editor-link.button_text') }}</label>
                        <input type="text" class="form-control" id="le_html" name="le_html" />
                    </div>
                    <br style="clear:both">
                    <br>
                    <div class="panel-group" role="tablist" aria-multiselectable="false" id="le-accordion">
                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingLink">
                          <h4 class="panel-title">
                            <a<?php if($type != 'link') echo ' class="collapsed"'; ?> data-toggle="collapse" data-parent="#le-accordion" href="#collapseLink" aria-expanded="<?php echo($type == 'link') ? 'true': 'false'; ?>" aria-controls="collapseLink">
                              {{ trans('editor-link.link_to_url') }}
                            </a>
                          </h4>
                        </div>
                        <div id="collapseLink" class="panel-collapse collapse<?php if($type == 'link') echo ' in'; ?>" role="tabpanel" aria-labelledby="headingLink">
                          <div class="panel-body">

                                <div class="form-group">
                                    <label for="le_href">{{ trans('editor-link.url') }}</label>
                                    <div class="input-group">
                                    	<input type="text" class="form-control" id="le_href" name="le_href" placeholder="http://" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button" id="le_browse_file" data-id="le_href"><i class="fa fa-folder-open-o"></i> {{ trans('editor-link.browse_file') }}</button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="le_target">{{ trans('editor-link.open_in') }}</label>
                                    <select name="le_target" id="le_target" class="form-control">
                                        <option value="">{{ trans('editor-link.same_window') }}</option>
                                        <option value="_blank">{{ trans('editor-link.new_window') }}</option>
                                    </select>
                                </div>

                          </div>
                        </div>
                      </div>
                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingPayPal">
                          <h4 class="panel-title">
                            <a<?php if($type != 'paypal') echo ' class="collapsed"'; ?> data-toggle="collapse" data-parent="#le-accordion" href="#collapsePayPal" aria-expanded="<?php echo($type == 'paypal') ? 'true': 'false'; ?>" aria-controls="collapsePayPal">
                              {{ trans('editor-link.paypal') }}
                            </a>
                          </h4>
                        </div>
                        <div id="collapsePayPal" class="panel-collapse collapse<?php if($type == 'paypal') echo ' in'; ?>" role="tabpanel" aria-labelledby="headingPayPal">
                          <div class="panel-body">

                                <div class="form-group">
                                    <label for="le_paypal_email">{{ trans('editor-link.email_address') }}</label>
                                    <input type="text" class="form-control" id="le_paypal_email" name="le_paypal_email" />
                                    <p class="help-block">{{ trans('editor-link.email_address_help') }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="le_paypal_item_name">{{ trans('editor-link.item_name') }}</label>
                                    <input type="text" class="form-control" id="le_paypal_item_name" name="le_paypal_item_name" />
                                </div>

                               <div class="row">
<?php /*
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="le_paypal_item_id">{{ trans('editor-link.item_id') }}</label>
                                        <input type="text" class="form-control" id="le_paypal_item_id" name="le_paypal_item_id" />
                                    </div>
                                </div>
*/ ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="le_paypal_item_price">{{ trans('editor-link.price') }}</label>
                                        <input type="number" step="any" class="form-control" id="le_paypal_item_price" name="le_paypal_item_price" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="le_paypal_currency">{{ trans('editor-link.currency') }}</label>
                                        <select class="form-control" id="le_paypal_currency" name="le_paypal_currency">
<?php
foreach(trans('editor-link.currencies') as $abbr => $name)
{
    echo '<option value="' . $abbr . '">' . $name . '</option>';
}
?>
                                        </select>
                                    </div>
                                </div>
                               </div>

                               <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="le_paypal_tax_rate">{{ trans('editor-link.tax_rate') }}</label>
                                        <input type="number" step="any" class="form-control" id="le_paypal_tax_rate" name="le_paypal_tax_rate" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="le_paypal_shipping_cost">{{ trans('editor-link.shipping_cost') }}</label>
                                        <input type="number" step="any" class="form-control" id="le_paypal_shipping_cost" name="le_paypal_shipping_cost" />
                                    </div>
                                </div>
                                <div class="col-md-4">

                                    <div class="form-group">
                                        <label for="le_paypal_sandbox">{{ trans('editor-link.sandbox_mode') }}</label>
                                        <select class="form-control" id="le_paypal_sandbox" name="le_paypal_sandbox">
                                            <option value="1">{{ trans('editor-link.sandbox_mode_on') }}</option>
                                            <option value="0">{{ trans('editor-link.sandbox_mode_off') }}</option>
                                        </select>
                                    </div>


                                </div>
                               </div>

                          </div>
                        </div>
                      </div>


                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="heading2Checkout">
                          <h4 class="panel-title">
                            <a<?php if($type != '2checkout') echo ' class="collapsed"'; ?> data-toggle="collapse" data-parent="#le-accordion" href="#collapse2Checkout" aria-expanded="<?php echo($type == '2checkout') ? 'true': 'false'; ?>" aria-controls="collapse2Checkout">
                              {{ trans('editor-link.2checkout') }}
                            </a>
                          </h4>
                        </div>
                        <div id="collapse2Checkout" class="panel-collapse collapse<?php if($type == '2checkout') echo ' in'; ?>" role="tabpanel" aria-labelledby="heading2Checkout">
                          <div class="panel-body">

                                <div class="row">
                                <div class="col-md-8">

                                <div class="form-group">
                                    <label for="le_checkout_account">{{ trans('editor-link.checkout_account') }}</label>
                                    <input type="text" class="form-control" id="le_checkout_account" name="le_checkout_account" />
                                </div>

                                </div>
                                <div class="col-md-4">

                                    <div class="form-group">
                                        <label for="le_checkout_sandbox">{{ trans('editor-link.sandbox_mode') }}</label>
                                        <select class="form-control" id="le_checkout_sandbox" name="le_checkout_sandbox">
                                            <option value="1">{{ trans('editor-link.sandbox_mode_on') }}</option>
                                            <option value="0">{{ trans('editor-link.sandbox_mode_off') }}</option>
                                        </select>
                                    </div>

                                </div>
                                </div>

                                <div class="form-group">
                                    <label for="le_checkout_item_name">{{ trans('editor-link.item_name') }}</label>
                                    <input type="text" class="form-control" id="le_checkout_item_name" name="le_checkout_item_name" />
                                </div>

                                <div class="form-group">
                                    <label for="le_checkout_item_description">{{ trans('editor-link.item_description') }} {{ trans('editor-link._optional_') }}</label>
                                    <input type="text" class="form-control" id="le_checkout_item_description" name="le_checkout_item_description" />
                                </div>

                               <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="le_checkout_item_id">{{ trans('editor-link.item_id') }} {{ trans('editor-link._optional_') }}</label>
                                        <input type="text" class="form-control" id="le_checkout_item_id" name="le_checkout_item_id" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="le_checkout_item_price">{{ trans('editor-link.price') }}</label>
                                        <input type="number" step="any" class="form-control" id="le_checkout_item_price" name="le_checkout_item_price" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="le_checkout_currency">{{ trans('editor-link.currency') }}</label>
                                        <select class="form-control" id="le_checkout_currency" name="le_checkout_currency">
<?php
foreach(trans('editor-link.2checkout_currencies') as $abbr => $name)
{
    echo '<option value="' . $abbr . '">' . $name . '</option>';
}
?>
                                        </select>
                                    </div>
                                </div>
                               </div>


                          </div>
                        </div>
                      </div>


                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="le-link-editor-save" type="button">{{ trans('global.update') }}</button>
                    <button class="btn" data-dismiss="modal" type="button">{{ trans('global.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function ieLoaded(settings)
{
    /* Defaults */
    if(typeof settings.paypal_sandbox === 'undefined')
    {
        settings.paypal_sandbox = 0;
        settings.paypal_shipping_cost = 0;
        settings.paypal_tax_rate = 0;
    }

    $.each(settings, function(index, value) {
        if($('#le_' + index).length) {
            $('#le_' + index).val(value);
        }
    });

	if(settings.href_only === true)
	{
		$('#le_html_field').hide();
        $('#le_href_only').val('1');
	}
    else
    {
        $('#le_href_only').val('0');
    }

    if(typeof settings.type === 'undefined')
    {
        $('#le_custom_url').val($('#le_src').val());
    }
    else
    {
        $('#le_custom_url').val(settings.custom_url);
    }
}

$('#le-link-editor-save').on('click', function() {

    var id = $('#le_id').val();
    var href_only = ($('#le_href_only').val() == '1') ? true : false;

    if($('#collapseLink').hasClass('in'))
    {
        var type = 'link';
    }
    else if($('#collapsePayPal').hasClass('in'))
    {
        var type = 'paypal';
    }
    else if($('#collapse2Checkout').hasClass('in'))
    {
        var type = '2checkout';
    }

    var form = {
        type: type,
        href_only: href_only,
        html: $('#le_html').val(),
        href: $('#le_href').val(),
        target: $('#le_target').val(),
        paypal_email: $('#le_paypal_email').val(),
        paypal_item_name: $('#le_paypal_item_name').val(),
/*        paypal_item_id: $('#le_paypal_item_id').val(),*/
        paypal_item_price: $('#le_paypal_item_price').val(),
        paypal_currency: $('#le_paypal_currency').val(),
        paypal_tax_rate: $('#le_paypal_tax_rate').val(),
        paypal_shipping_cost: $('#le_paypal_shipping_cost').val(),
        paypal_sandbox: $('#le_paypal_sandbox').val(),
        checkout_account: $('#le_checkout_account').val(),
        checkout_item_name: $('#le_checkout_item_name').val(),
        checkout_item_description: $('#le_checkout_item_description').val(),
        checkout_item_id: $('#le_checkout_item_id').val(),
        checkout_item_price: $('#le_checkout_item_price').val(),
        checkout_currency: $('#le_checkout_currency').val(),
        checkout_sandbox: $('#le_checkout_sandbox').val(),
    };
    editorSaveLink(id, form);
});

// Browse for file link
$('#le_browse_file').on('click', function()
{
    editorLinkBrowser($(this).attr('data-id'));
});
</script>