/*
 * Globals and defaults
 */

var override_form = false;
var $modal = $('#ajax-modal');

/*
 * Arrays that contain select row DataTables
 */

var selected_log = [];
var selected_campaigns = [];
var public_users = [];
var selected_leads = [];

/*
Handlebars select list

<select>
    @{{#select status}}
    <option value="Completed">Betalad</option>
    <option value="OverDue">Förfallen</option>
    <option value="SentToPayer">Utskickad</option>
    <option value="None">Tillsatt</option>
    @{{/select}}
</select>
*/

window.Handlebars.registerHelper('select', function( value, options ){
    var $el = $('<select />').html( options.fn(this) );
    $el.find('[value=' + value + ']').attr({'selected':'selected'});
    return $el.html();
});

/*
Handlebars escape sinlge & double quotes

{{ escape name }}
*/

window.Handlebars.registerHelper('escape', function(variable) {
  return variable.replace(/(['"\\])/g, '\\$1');
});

/*
If inArray

http://axiacore.com/blog/check-if-item-array-handlebars/

"favourites": [2, 3]

{{#ifIn id ../favourites }}color: red{{/ifIn}}
*/

window.Handlebars.registerHelper('ifIn', function(elem, list, options) {
if (typeof elem !== 'undefined' && typeof list !== 'undefined')
{
  if(list.indexOf(elem) > -1) {
    return options.fn(this);
  }
  return options.inverse(this);
}
});

/*
Handlebars replace newline characters with <br>

<div>
    {{breaklines description}}
</div>

*/
Handlebars.registerHelper('breaklines', function(text) {
    text = Handlebars.Utils.escapeExpression(text);
    text = text.replace(/(\r\n|\n|\r)/gm, '<br>');
    return new Handlebars.SafeString(text);
});

/*
 * Executed when partial is opened
 */

function onPartialLoad()
{
    /*
     * Set overide_form to false (again)
     */

    override_form = false;

    /*
     * Form validation
     */

    formValidation();

    /*
     * Ajax malsup form
     */

    ajaxMalsupForm();

    /*
     * Bootstrap scrollable tabs
     */

    initScrollableTabs();

    /*
     * Multiline ellipsis
     */

    multiLineEllipsis();

    /*
     * Colorbox lightbox
     */

    $('.lightbox').colorbox(
    {
        fastIframe: true,
        iframe: true,
        width: '90%',
        height: '90%'
    });

    $('.colorbox').colorbox(
    {
        maxWidth: '90%',
        maxHeight: '90%'
    });

    /*
     * Form checkbox switch
     */

    $('[data-class]').switcher(
    {
        theme: 'square',
        on_state_content: '<span class="fa fa-check"></span>',
        off_state_content: '<span class="fa fa-times"></span>'
    });

    /*
     * Select2
     */

    select2();

    /*
     * Date picker
     */

	$('.date-picker-ymd').datepicker({
		format: 'yyyy-mm-dd'
	});

    /*
     * Styled file input
     */

    $('[type=file].styled').pixelFileInput(
    {
        choose_btn_tmpl: '<a href="#" class="btn btn-xs btn-primary">Choose</a>',
        clear_btn_tmpl: '<a href="#" class="btn btn-xs"><i class="fa fa-times"></i> Clear</a>',
        placeholder: 'No file selected...'
    });

    /*
     * moment.js
     */

    $('[data-moment=fromNowDateTime]').each(function(index, value)
    {
        var date = $(this).text();

        if (moment(date, 'YYYY-MM-DD HH:mm:ss').isValid())
        {
            $(this).html('<abbr data-toggle="tooltip" title="' + moment(date).format(_lang['date_time_notation']) + '">' + moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow() + '</abbr>');
        }
    });

    /*
     * Bootstrap Tooltips, Popovers and AJAX Popovers
     */

    bsTooltipsPopovers();

    /*
     * Bootstrap Modal
     */

    $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
        '<div class="loading-spinner" style="margin-top: -210px;">' +
        '<div class="spinner" id="spinner"> <div class="rect1" style="background-color:#fff"></div> <div class="rect2" style="background-color:#fff"></div> <div class="rect3" style="background-color:#fff"></div> <div class="rect4" style="background-color:#fff"></div> <div class="rect5" style="background-color:#fff"></div> </div>' +
        '</div>';

    $.fn.modalmanager.defaults.resize = true;

    $('[data-modal]').on('click', function()
    {
        // create the backdrop and wait for next modal to be triggered
        $('body').modalmanager('loading');

        $modal.load($(this).attr('data-modal'), '', function()
        {
            $modal.modal();
            onModalLoad();
        });
    });
};

/*
 * Switch App language
 */

function switchLanguage(code)
{
    var request = $.ajax(
    {
        url: app_root + '/api/v1/account/language',
        type: 'GET',
        data:
        {
            lang: code
        },
        dataType: 'json'
    });

    request.done(function(json)
    {
        if(json.result == 'success')
        {
            document.location.reload();
        }
    });

    request.fail(function(jqXHR, textStatus)
    {
        alert('Request failed, please try again (' + textStatus + ')');
    });
}

/*
 * DataTable loaded event
 */

function onDataTableLoad()
{
    /*
     * moment.js
     */

    $('[data-moment=fromNowDateTime]').each(function(index, value)
    {
        var date = $(this).text();

        if (moment(date, 'YYYY-MM-DD HH:mm:ss').isValid())
        {
            $(this).html('<abbr data-toggle="tooltip" title="' + moment(date).format(_lang['date_time_notation']) + '">' + moment(date, 'YYYY-MM-DD HH:mm:ss').fromNow() + '</abbr>');
        }
    });

    /*
     * Bootstrap Tooltips, Popovers and AJAX Popovers
     */

    bsTooltipsPopovers();
}

/*
 * Executed when modal is opened
 */

function onModalLoad()
{
    /*
     * Form validation
     */

    formValidation();

    /*
     * Ajax malsup form
     */

    ajaxMalsupForm();

    /*
     * Select2
     */

    select2();

    /*
     * Bootstrap Tooltips, Popovers and AJAX Popovers
     */

    bsTooltipsPopovers();

    /*
     * Form checkbox switch
     */

    $('[data-class]').switcher(
    {
        theme: 'square',
        on_state_content: '<span class="fa fa-check"></span>',
        off_state_content: '<span class="fa fa-times"></span>'
    });

    /*
     * Styled file input
     */

    $('[type=file].styled').pixelFileInput(
    {
        choose_btn_tmpl: '<a href="#" class="btn btn-xs btn-primary">' + _lang['choose'] + '</a>',
        clear_btn_tmpl: '<a href="#" class="btn btn-xs"><i class="fa fa-times"></i> ' + _lang['clear'] + '</a>',
        placeholder: _lang['no_file_selected']
    });
};

/*
 * Form validation
 */

function formValidation()
{
    if (override_form) return;

    $('form.validate').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'fa fa-check',
            invalid: 'fa fa-times',
            validating: 'fa fa-refresh'
        }
    }).on('success.form.fv', function(e)
    {
        // Prevent form submission
        e.preventDefault();

        blockUI();

        // Get the form instance
        var $form = $(e.target);

        // Get the formValidation instance
        var fv = $form.data('formValidation');

        // Use Ajax to submit form data
        $.post($form.attr('action'), $form.serialize(), function(r)
        {
            // Reset password fields if exists
            $('[type=password]').val('');

            if (r.result == 'success' || r.result == 'error')
            {
                if (typeof r.result_msg !== 'undefined')
                {
                    // Go to the top
                    $('html,body').animate(
                    {
                        scrollTop: 0
                    }, 200);

                    var alert_type = (r.result == 'error') ? 'danger' : 'success';

                    setTimeout(function()
                    {
                        var options = {
                            type: alert_type,
                            namespace: 'pa_page_alerts_default'
                        };
                        CmsAdmin.plugins.alerts.add(r.result_msg, options);
                    }, 300);
                }

                // Enable submit button(s) and reset form
                $(e.target).data('formValidation').disableSubmitButtons(false);
                $(e.target).data('formValidation').resetForm();

                unblockUI();

                // Form succesfully submitted
                if (typeof formSubmittedSuccess == 'function')
                {
                    formSubmittedSuccess(r.result, r);
                }
            }

        }, 'json');
    });
};

/*
 * Select2
 */

function select2()
{

    $('.select2').select2(
    {
        allowClear: true
    });

    $('.select2-required').select2(
    {
        allowClear: false
    });

    $('.select2-nosearch-required').select2(
    {
        allowClear: false,
        minimumResultsForSearch: -1
    });

    $('.select2-tags').select2(
    {
		tags: [],
		tokenSeparators: [',', ';', ' ']
    }); 

	var select2_choices;

    $('.select2-datalist').select2(
        {
            dropdownCssClass: 'select2-datalist',
            initSelection: function(element, callback)
            {
                select2_choices = $('#datalist_' + $(element).attr('id') + ' option').map(function(item)
                {
                    return {
                        id: this.text,
                        text: this.value
                    };
                }).get();

                var value = $(element).val(); // Value needs to be JSON string like: {"id": "1", "text": "Store" } (with quotes)

                if (value)
                    callback($.parseJSON(value));
            },
            query: function(query)
            {
                // match
                var items = $.grep(select2_choices, function(item)
                {
                    if (!query.term)
                        return true;

                    return query.matcher(query.term, item.text);
                });

                if (query.term)
                    items.push(
                    {
                        id: 0,
                        text: query.term
                    });

                var data = {};
                data.results = items;
                query.callback(data);

            }
        })
        .on("change", function(e)
        {
            $(this).val(JSON.stringify(e.added));

            if (typeof $('form').data('formValidation') !== 'undefined')
            {
                $('form').data('formValidation')
                    .updateStatus($(this), 'NOT_VALIDATED')
                    .validateField($(this));
            }
        });

    // Set placeholder
    $('.select2-datalist input').attr('placeholder', _lang['select2_datalist_placeholder']);

    $('.select2-multiple').select2();
}


/*
 * Ajax click
 */

function _confirm(url, data, verb, callback, msg)
{
    if (typeof msg === 'undefined') msg = _lang['confirm'];

    swal(
        {
            title: msg,
            type: "warning",
            showCancelButton: true,
            cancelButtonText: _lang['cancel'],
            confirmButtonColor: "#DD6B55",
            confirmButtonText: _lang['yes']
        },
        function()
        {
            _click(url, data, verb, callback);
        });
};

function _click(url, data, verb, callback)
{
    var callback_arg1 = arguments[4];
    var callback_arg2 = arguments[5];

    var request = $.ajax(
    {
        url: url,
        type: verb,
        data:
        {
            data: data
        },
        dataType: 'json'
    });

    request.done(function(json)
    {
        callback(callback_arg1, callback_arg2, json);
    });

    request.fail(function(jqXHR, textStatus)
    {
        alert('Request failed, please try again (' + textStatus + ')');
    });
};

/*
 * Ajax submit form
 */

function ajaxSubmitForm(action, serialized, remove_alerts)
{
    if (typeof remove_alerts === 'undefined' || remove_alerts == true)
    {
        $('#pa-page-alerts-box').remove();
    }

    // Use Ajax to submit form data
    $.post(action, serialized, function(r)
    {
        if (r.result == 'success' || r.result == 'error')
        {
            if (typeof r.result_msg !== 'undefined')
            {
                // Go to the top
                $('html,body').animate(
                {
                    scrollTop: 0
                }, 200);

                var alert_type = (r.result == 'error') ? 'danger' : 'success';

                setTimeout(function()
                {
                    var options = {
                        type: alert_type,
                        namespace: 'pa_page_alerts_default'
                    };
                    CmsAdmin.plugins.alerts.add(r.result_msg, options);
                }, 300);

                if (r.result == 'success')
                {
                    // Reset password fields if submission was successful
                    $('[type=password]').val('');
                }
            }

            // Form succesfully submitted
            if (typeof formSubmittedSuccess == 'function')
            {
                formSubmittedSuccess(r);
            }
        }

    }, 'json');
}

/*
 * Ajax malsup form
 */

function ajaxMalsupForm()
{
    var ajax_form_opts = {
        dataType: 'json',
        beforeSerialize: beforeSerialize,
        success: formResponse,
        error: formResponse
    };

    if (override_form) return;

    $('form.ajax-validate').formValidation(
    {
        framework: 'bootstrap',
        icons:
        {
            valid: 'fa fa-check',
            invalid: 'fa fa-times',
            validating: 'fa fa-refresh'
        }
    }).on('success.form.fv', function(e)
    {
        // Prevent form submission
        //$('form.ajax').ajaxForm(ajax_form_opts);
        $('form.ajax').ajaxSubmit(ajax_form_opts);
        e.preventDefault();
    }).on('err.form.fv', function(e)
    {
        // Prevent form submission
        e.preventDefault();
    });
}

function beforeSerialize($jqForm, options)
{
    var form = $jqForm[0];

    // Loading state
    blockUI();
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').addClass('loading');
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').attr('disabled', 'disabled');
}

function formResponse(responseText, statusText, xhr, $jqForm)
{
    var form = $jqForm[0];

    // Remove possible old markup
    $(form).find('.ajax-help-inline').remove();
    $(form).find('.form-group').removeClass('has-error has-warning has-info has-success');

    // Process JSON response
    unblockUI();

    // Redirect
    if (typeof responseText.redir !== 'undefined')
    {
        if (responseText.redir == 'reload')
        {
            /*angular.element($('#content-wrapper')).injector().get("$route").reload();*/
            document.location.reload();
        }
        else
        {
            document.location.href = responseText.redir;
        }
    }

    if (typeof responseText.result_msg !== 'undefined' && typeof responseText.result !== 'undefined' && (responseText.result == 'error' || responseText.result == 'success'))
    {
        // Go to the top
        $('html,body').animate(
        {
            scrollTop: 0
        }, 200);

        var alert_type = (responseText.result == 'error') ? 'danger' : 'success';

        setTimeout(function()
        {
            var options = {
                type: alert_type,
                namespace: 'pa_page_alerts_default'
            };
            CmsAdmin.plugins.alerts.add(responseText.result_msg, options);
        }, 300);
    }

    // Form succesfully submitted
    if (typeof formSubmittedSuccess == 'function')
    {
        formSubmittedSuccess(responseText);
    }

    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').removeAttr('disabled');
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').removeClass('loading');
}

/*
 * Multiline ellipsis
 * Requirements:
 *  - The element must have a fixed height so that content overflows.
 *  - The element must have child elements (eg. <p>s).
 */

function multiLineEllipsis()
{
    $('.ellipsis').dotdotdot(
    {
        watch: 'window',
    });

    $('.ellipsis-oneline').dotdotdot(

    {
        watch: 'window',
        height: 32,
    });

    $('.ellipsis-oneline-small').dotdotdot(
    {
        watch: 'window',
        height: 24,
    });
}

/*
 * Stat panels equal heights
 */

var setEqHeight = function()
{
    $('#content-wrapper .row').each(function()
    {
        var $p = $(this).find('.stat-panel');
        if (!$p.length) return;
        $p.attr('style', '');
        var h = $p.first().height(),
            max_h = h;
        $p.each(function()
        {
            h = $(this).height();
            if (max_h < h) max_h = h;
        });
        $p.css('height', max_h);
    });
};

/*
 * Generate random password
 */

function randomString(string_length)
{
    if (typeof string_length === 'undefined') string_length = 8;
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var randomstring = '';
    for (var i = 0; i < string_length; i++)
    {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum, rnum + 1);
    }
    return randomstring;
}

/*
 * Generate random number code
 */

function randomCode(string_length)
{
    if (typeof string_length === 'undefined') string_length = 8;
    var chars = "0123456789";
    var randomstring = '';
    for (var i = 0; i < string_length; i++)
    {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum, rnum + 1);
    }
    return randomstring;
}

/*
 * Toggle password text visibility
 */

function togglePassword(field_name, show)
{
    if (show)
    {
        var pwd = $('#' + field_name).val();
        $('#' + field_name).attr('id', field_name + '2');
        $('#' + field_name + '2').after($('<input id="' + field_name + '" name="' + field_name + '" class="form-control" autocapitalize="off" autocorrect="off" autocomplete="off" type="text">'));
        $('#' + field_name + '2').remove();
        $('#' + field_name).val(pwd);
    }
    else
    {
        var pwd = $('#' + field_name).val();
        $('#' + field_name).attr('id', field_name + '2');
        $('#' + field_name + '2').after($('<input id="' + field_name + '" name="' + field_name + '" class="form-control" type="password">'));
        $('#' + field_name + '2').remove();
        $('#' + field_name).val(pwd);
    }
}

/*
 * Callback when user is deleted
 */

function userDeleted()
{
    /* Empty AngularJS cache */
    angular.element($('#content-wrapper')).injector().get("$templateCache").remove('/app/users');
    document.location = '#/users';
}

/*
 * Bootstrap scrollable tabs
 */

function initScrollableTabs()
{
    if ($('.scrolltabs').length)
    {
        var hidWidth;
        var scrollBarWidths = 40;

        var widthOfList = function()
        {
            var itemsWidth = 0;
            $('.scrolltabs li').each(function()
            {
                var itemWidth = $(this).outerWidth();
                itemsWidth += itemWidth;
            });
            return itemsWidth;
        };

        var widthOfHidden = function()
        {
            return (($('.scrolltabs-wrapper').outerWidth()) - widthOfList() - getLeftPosi()) - scrollBarWidths;
        };

        var getLeftPosi = function()
        {
            if ($('.scrolltabs').length)
            {
                return $('.scrolltabs').position().left;
            }
        };

        var reAdjust = function()
        {
            if (($('.scrolltabs-wrapper').outerWidth()) < widthOfList())
            {
                $('.scroller-right').show();
            }
            else
            {
                $('.scroller-right').hide();
            }

            if (getLeftPosi() < 0)
            {
                $('.scroller-left').show();
            }
            else
            {
                $('.item').animate(
                {
                    left: "-=" + getLeftPosi() + "px"
                }, 'slow');
                $('.scroller-left').hide();
            }
        }

        reAdjust();

        $(window).on('resize', function(e)
        {
            reAdjust();
        });

        $('.scroller-right').on('click', function()
        {
            $('.scroller-left').fadeIn('slow');
            $('.scroller-right').fadeOut('slow');

            $('.scrolltabs').animate(
            {
                left: "+=" + widthOfHidden() + "px"
            }, 'slow', function() {

            });
        });

        $('.scroller-left').on('click', function()
        {

            $('.scroller-right').fadeIn('slow');
            $('.scroller-left').fadeOut('slow');

            $('.scrolltabs').animate(
            {
                left: "-=" + getLeftPosi() + "px"
            }, 'slow', function() {

            });
        });
    }
}

/*
 * Generate UUID
 * var uuid = guid();
 */

var guid = (function()
{
    function s4()
    {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }
    return function()
    {
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
            s4() + '-' + s4() + s4() + s4();
    };
})();

/*
 * Bootstrap Tooltips, Popovers and AJAX Popovers
 */

function bsTooltipsPopovers()
{
    $('[data-toggle~=tooltip]').tooltip(
    {
        container: 'body'
    });

    $('[data-toggle~=popover]').popover(
    {
        container: 'body',
        html: true
    });

    $('*[data-poload]').bind('click', function()
    {
        var e = $(this);
        e.unbind('click');
        e.popover(
        {
            html: true,
            container: 'body',
            content: '<div class="spinner" style="margin:0"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div>'
        }).popover('show');
        $.get(e.data('poload'), function(d)
        {
            e.data('bs.popover').options.content = d;
            e.popover('show');
        });
    });

    // Hide popovers and tooltips when clicking outside
    $('body').on('click', function(e)
    {
        $('[data-poload]').each(function()
        {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0)
            {
                $(this).popover('hide');
            }
        });

        // Hide tooltips when clicking link with tooltip
        $('[data-toggle~=tooltip]').each(function()
        {
            $(this).tooltip('hide');
        });
		$('.tooltip').remove();
    });
}

function verticalResizer()
{
    $('.vertical-center').each(function()
    {
        var $this = $(this); // store the object
        var parentHeight = $this.parent().height();

        $this.css(
            'margin-top', parseInt((parentHeight - $this.height()) / 2)
        );
    });
}

/*
 * BlockUI
 */

function blockUI(el)
{
    if (typeof el === 'undefined')
    {
        $.blockUI(
        {
            message: '<div class="spinner" id="spinner"> <div class="rect1" style="background-color:#fff"></div> <div class="rect2" style="background-color:#fff"></div> <div class="rect3" style="background-color:#fff"></div> <div class="rect4" style="background-color:#fff"></div> <div class="rect5" style="background-color:#fff"></div> </div>',
            fadeIn: 0,
            fadeOut: 100,
            baseZ: 21000,
            overlayCSS:
            {
                backgroundColor: '#000'
            },
            css:
            {
                border: 'none',
                padding: '0',
                top: '30%',
                backgroundColor: 'transparant',
                opacity: 1,
                color: '#fff'
            }
        });
    }
    else
    {
        $(el).block(
        {
            message: '<div class="spinner" id="spinner"> <div class="rect1" style="background-color:#fff"></div> <div class="rect2" style="background-color:#fff"></div> <div class="rect3" style="background-color:#fff"></div> <div class="rect4" style="background-color:#fff"></div> <div class="rect5" style="background-color:#fff"></div> </div>',
            fadeIn: 0,
            fadeOut: 100,
            baseZ: 21000,
            overlayCSS:
            {
                backgroundColor: '#000'
            },
            css:
            {
                border: 'none',
                padding: '0',
                top: '30%',
                backgroundColor: 'transparant',
                opacity: 1,
                color: '#fff'
            }
        });
    }
}

/*
 * unblockUI
 */

function unblockUI(el)
{
    if (typeof el === 'undefined')
    {
        $.unblockUI();
    }
    else
    {
        $(el).unblock();
    }
}

/*
 * Show "Saved" notification in top bar
 */

function showSaved()
{
    $('#msg-saved').show();
    setTimeout(function()
    {
        $('#msg-saved').fadeOut(500);
    }, 1200);
}


/* override bootstrap popover to include callback */

var showPopover = $.fn.popover.Constructor.prototype.show;
$.fn.popover.Constructor.prototype.show = function() {
	showPopover.call(this);
	if (this.options.showCallback) {
		this.options.showCallback.call(this);
	}
}

var hidePopover = $.fn.popover.Constructor.prototype.hide;
$.fn.popover.Constructor.prototype.hide = function() {
	if (this.options.hideCallback) {
		this.options.hideCallback.call(this);
	}
	hidePopover.call(this);
}

/* usage */

/* 
$('#example').popover({
	showCallback : function() {
		console.log('popover is shown');
	},
	hideCallback : function() {
		console.log('popover is hidden');	
	}
});

*/