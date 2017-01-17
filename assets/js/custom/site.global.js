var siteGlobal = $(function($)
{
  /*
   * Fix Chrome ampersand (&) issue
   */

  $('.iframe-editable').each(function() { 
    var src = $(this).attr('data-src');
    if(typeof src === 'undefined') src = $(this).attr('src');
    src = src.replace(/&amp;/g,'&');
    $(this).attr('src', src);
  });

  /*
   * Form validation
   */

  $('.form-editable').parsley({
    focus: 'first',
    validateIfUnchanged: false
  });

  /*
   * Add named anchors
   */

  $('section[data-block]').each(function() {
    var name = $(this).attr('data-block');
    var id = $(this).attr('id');
    id = id.replace(/block-/g, '');
    $(this).prepend('<a name="' + name + '-' + id + '" class="internal-link"></a>');
  });

  /*
   * Lightbox
   */

  $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
    var self = $(this);
    $(this).attr('href', $(this).attr('src'));

    $('*[data-gallery]').each(function() {
      $(this).attr('href', $(this).attr('src'));
    });

    event.preventDefault();
    $(self).ekkoLightbox();
  }); 

  resetForms = (function()
  {
    var pageForms = document.querySelectorAll('.form-editable');
    for (var i = 0; i < pageForms.length;i++){
      $(pageForms[i]).parsley().reset();
    }
  });

  /*
   * Bootstrap Modal
   */

  var $modal = $('#ajax-modal');

  $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
    '<div class="loading-spinner" style="margin-top: -210px;">' +
    '<div class="spinner" id="spinner"> <div class="rect1" style="background-color:#fff"></div> <div class="rect2" style="background-color:#fff"></div> <div class="rect3" style="background-color:#fff"></div> <div class="rect4" style="background-color:#fff"></div> <div class="rect5" style="background-color:#fff"></div> </div>' +
    '</div>';

  $.fn.modalmanager.defaults.resize = true;

  siteOpenModal = (function(url, callback)
  {
    // create the backdrop and wait for next modal to be triggered
    $('body').modalmanager('loading');

    $modal.load(url, '', function () {
      $modal.modal();
      callback(url);
    });
  });

  siteCloseModal = (function()
  {
    $modal.modal('hide');
  });

  /*
   * Forms
   */

  siteBindForms = (function()
  {
    var ajax_form_opts = {dataType : 'json', beforeSerialize: beforeSerialize, success: formResponse, error: formResponse};
    $('form.form-editable,form.app-form').ajaxForm(ajax_form_opts);
  });

  siteBindForms();

  function beforeSerialize($jqForm, options) {
    var form = $jqForm[0];

    if(app_edit)
    {
      var event_submitted_btn = ($(form).find('.mwp-fb-success-btn').length) ? $(form).find('.mwp-fb-success-btn').text() : _lang['fb_default_thanks_btn'];
      var event_submitted_title = ($(form).find('.mwp-fb-success-title').length) ? $(form).find('.mwp-fb-success-title').text() : _lang['fb_default_thanks_title'];
      var event_submitted_msg = ($(form).find('.mwp-fb-success-msg').length) ? $(form).find('.mwp-fb-success-msg').text() : _lang['fb_default_thanks_msg'];
      var event_submitted_redirect = ($(form).find('.mwp-fb-success-redirect').length) ? $(form).find('.mwp-fb-success-redirect').text() : '';

      swal({
        allowHtml: true,
        title: event_submitted_title,
        text: _lang['fb_demo_mode'] + '\n' + event_submitted_msg,
        type: "success",
        showCancelButton: false,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: event_submitted_btn
      }, 
      function(){
      });
      
      $(':input', $(form))
       .not(':button, :submit, :reset, :hidden')
       .val('')
       .removeAttr('checked')
       .removeAttr('selected')
       .removeClass('parsley-success');

      return;
    }

    // Loading state
    blockUI();
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').addClass('loading');
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').attr('disabled', 'disabled');
  }

  function formResponse(responseText, statusText, xhr, $jqForm) {
    var form = $jqForm[0];
    var help_class = ($(form).hasClass('form-horizontal')) ? 'help-block' : 'help-inline';

    var event_submitted_btn = ($(form).find('.mwp-fb-success-btn').length) ? $(form).find('.mwp-fb-success-btn').text() : _lang['fb_default_thanks_btn'];
    var event_submitted_title = ($(form).find('.mwp-fb-success-title').length) ? $(form).find('.mwp-fb-success-title').text() : _lang['fb_default_thanks_title'];
    var event_submitted_msg = ($(form).find('.mwp-fb-success-msg').length) ? $(form).find('.mwp-fb-success-msg').text() : _lang['fb_default_thanks_msg'];
    var event_submitted_redirect = ($(form).find('.mwp-fb-success-redirect').length) ? $(form).find('.mwp-fb-success-redirect').text() : '';

    if(responseText.result == 'success')
    {
      $(':input', $(form))
       .not(':button, :submit, :reset, :hidden, :checkbox, :radio')
       .val('')
       .removeAttr('checked')
       .removeAttr('selected')
       .removeClass('parsley-success');

      //Reset checkboxes and radio to original state
      $.each($(':checkbox, :radio', $(form)), function (i, val) {
        var cb = $(val);
        cb.prop('checked', (cb.attr('checked') != undefined));
      });

      /*$(form)[0].reset();*/
      if (responseText.redirect == '')
      {
        swal({
          allowHtml: true,
          title: event_submitted_title,
          text: event_submitted_msg,
          type: "success",
          showCancelButton: false,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: event_submitted_btn
        }, 
        function(){
        });
      }
      else
      {
        document.location = responseText.redirect;
      }
    }

    // Remove possible old markup
    $(form).find('.ajax-help-inline').remove();
    $(form).find('.control-group').removeClass('error warning info success');

    unblockUI();

    // Redirect
    if(defined(responseText.redir)) {
      document.location.href = responseText.redir;
    }

    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').removeAttr('disabled');
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').removeClass('loading');
  }

  function defined(x) {
    var undefined;
    return x !== undefined;
  }
  /*
   * BlockUI
   */
  blockUI = function(el)
  {
    if(typeof el === 'undefined')
    {
      $.blockUI({ 
        message : '<div class="app-spinner" id="app-spinner"> <div class="rect1" style="background-color:#fff"></div> <div class="rect2" style="background-color:#fff"></div> <div class="rect3" style="background-color:#fff"></div> <div class="rect4" style="background-color:#fff"></div> <div class="rect5" style="background-color:#fff"></div> </div>',
        fadeIn: 0,
        fadeOut: 50,
        baseZ: 1000000000,
        overlayCSS: { 
          backgroundColor: '#000'
          },
        css: { 
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
      $(el).block({ 
        message : '<div class="app-spinner" id="app-spinner"> <div class="rect1" style="background-color:#fff"></div> <div class="rect2" style="background-color:#fff"></div> <div class="rect3" style="background-color:#fff"></div> <div class="rect4" style="background-color:#fff"></div> <div class="rect5" style="background-color:#fff"></div> </div>',
        fadeIn: 0,
        fadeOut: 50,
        baseZ: 1000000000,
        overlayCSS: { 
          backgroundColor: '#000'
          },
        css: { 
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

  unblockUI = function(el)
  {
    if(typeof el === 'undefined')
    {
      $.unblockUI();
    }
    else
    {
      $(el).unblock();
    }
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Notify style
   */

  $.notify.addStyle("bootstrap", {
    html: "<div>\n<span data-notify-text></span>\n</div>",
    classes: {
    base: {
      "font-weight": "bold",
      "padding": "8px 15px 8px 14px",
      "text-shadow": "0 1px 0 rgba(255, 255, 255, 0.5)",
      "background-color": "#fcf8e3",
      "border": "1px solid #fbeed5",
      "border-radius": "4px",
      "white-space": "nowrap",
      "padding-left": "25px",
      "background-repeat": "no-repeat",
      "background-position": "3px 7px"
    },
    error: {
      "color": "#B94A48",
      "background-color": "#F2DEDE",
      "border-color": "#EED3D7",
      "background-image": "url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAtRJREFUeNqkVc1u00AQHq+dOD+0poIQfkIjalW0SEGqRMuRnHos3DjwAH0ArlyQeANOOSMeAA5VjyBxKBQhgSpVUKKQNGloFdw4cWw2jtfMOna6JOUArDTazXi/b3dm55socPqQhFka++aHBsI8GsopRJERNFlY88FCEk9Yiwf8RhgRyaHFQpPHCDmZG5oX2ui2yilkcTT1AcDsbYC1NMAyOi7zTX2Agx7A9luAl88BauiiQ/cJaZQfIpAlngDcvZZMrl8vFPK5+XktrWlx3/ehZ5r9+t6e+WVnp1pxnNIjgBe4/6dAysQc8dsmHwPcW9C0h3fW1hans1ltwJhy0GxK7XZbUlMp5Ww2eyan6+ft/f2FAqXGK4CvQk5HueFz7D6GOZtIrK+srupdx1GRBBqNBtzc2AiMr7nPplRdKhb1q6q6zjFhrklEFOUutoQ50xcX86ZlqaZpQrfbBdu2R6/G19zX6XSgh6RX5ubyHCM8nqSID6ICrGiZjGYYxojEsiw4PDwMSL5VKsC8Yf4VRYFzMzMaxwjlJSlCyAQ9l0CW44PBADzXhe7xMdi9HtTrdYjFYkDQL0cn4Xdq2/EAE+InCnvADTf2eah4Sx9vExQjkqXT6aAERICMewd/UAp/IeYANM2joxt+q5VI+ieq2i0Wg3l6DNzHwTERPgo1ko7XBXj3vdlsT2F+UuhIhYkp7u7CarkcrFOCtR3H5JiwbAIeImjT/YQKKBtGjRFCU5IUgFRe7fF4cCNVIPMYo3VKqxwjyNAXNepuopyqnld602qVsfRpEkkz+GFL1wPj6ySXBpJtWVa5xlhpcyhBNwpZHmtX8AGgfIExo0ZpzkWVTBGiXCSEaHh62/PoR0p/vHaczxXGnj4bSo+G78lELU80h1uogBwWLf5YlsPmgDEd4M236xjm+8nm4IuE/9u+/PH2JXZfbwz4zw1WbO+SQPpXfwG/BBgAhCNZiSb/pOQAAAAASUVORK5CYII=)"
    },
    success: {
      "color": "#468847",
      "background-color": "#DFF0D8",
      "border-color": "#D6E9C6",
      "padding-left": "30px",
      "background-position": "5px",
      "background-image": "url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAutJREFUeNq0lctPE0Ecx38zu/RFS1EryqtgJFA08YCiMZIAQQ4eRG8eDGdPJiYeTIwHTfwPiAcvXIwXLwoXPaDxkWgQ6islKlJLSQWLUraPLTv7Gme32zoF9KSTfLO7v53vZ3d/M7/fIth+IO6INt2jjoA7bjHCJoAlzCRw59YwHYjBnfMPqAKWQYKjGkfCJqAF0xwZjipQtA3MxeSG87VhOOYegVrUCy7UZM9S6TLIdAamySTclZdYhFhRHloGYg7mgZv1Zzztvgud7V1tbQ2twYA34LJmF4p5dXF1KTufnE+SxeJtuCZNsLDCQU0+RyKTF27Unw101l8e6hns3u0PBalORVVVkcaEKBJDgV3+cGM4tKKmI+ohlIGnygKX00rSBfszz/n2uXv81wd6+rt1orsZCHRdr1Imk2F2Kob3hutSxW8thsd8AXNaln9D7CTfA6O+0UgkMuwVvEFFUbbAcrkcTA8+AtOk8E6KiQiDmMFSDqZItAzEVQviRkdDdaFgPp8HSZKAEAL5Qh7Sq2lIJBJwv2scUqkUnKoZgNhcDKhKg5aH+1IkcouCAdFGAQsuWZYhOjwFHQ96oagWgRoUov1T9kRBEODAwxM2QtEUl+Wp+Ln9VRo6BcMw4ErHRYjH4/B26AlQoQQTRdHWwcd9AH57+UAXddvDD37DmrBBV34WfqiXPl61g+vr6xA9zsGeM9gOdsNXkgpEtTwVvwOklXLKm6+/p5ezwk4B+j6droBs2CsGa/gNs6RIxazl4Tc25mpTgw/apPR1LYlNRFAzgsOxkyXYLIM1V8NMwyAkJSctD1eGVKiq5wWjSPdjmeTkiKvVW4f2YPHWl3GAVq6ymcyCTgovM3FzyRiDe2TaKcEKsLpJvNHjZgPNqEtyi6mZIm4SRFyLMUsONSSdkPeFtY1n0mczoY3BHTLhwPRy9/lzcziCw9ACI+yql0VLzcGAZbYSM5CCSZg1/9oc/nn7+i8N9p/8An4JMADxhH+xHfuiKwAAAABJRU5ErkJggg==)"
    },
    info: {
      "color": "#3A87AD",
      "background-color": "#D9EDF7",
      "border-color": "#BCE8F1",
      "background-image": "url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QYFAhkSsdes/QAAA8dJREFUOMvVlGtMW2UYx//POaWHXg6lLaW0ypAtw1UCgbniNOLcVOLmAjHZolOYlxmTGXVZdAnRfXQm+7SoU4mXaOaiZsEpC9FkiQs6Z6bdCnNYruM6KNBw6YWewzl9z+sHImEWv+vz7XmT95f/+3/+7wP814v+efDOV3/SoX3lHAA+6ODeUFfMfjOWMADgdk+eEKz0pF7aQdMAcOKLLjrcVMVX3xdWN29/GhYP7SvnP0cWfS8caSkfHZsPE9Fgnt02JNutQ0QYHB2dDz9/pKX8QjjuO9xUxd/66HdxTeCHZ3rojQObGQBcuNjfplkD3b19Y/6MrimSaKgSMmpGU5WevmE/swa6Oy73tQHA0Rdr2Mmv/6A1n9w9suQ7097Z9lM4FlTgTDrzZTu4StXVfpiI48rVcUDM5cmEksrFnHxfpTtU/3BFQzCQF/2bYVoNbH7zmItbSoMj40JSzmMyX5qDvriA7QdrIIpA+3cdsMpu0nXI8cV0MtKXCPZev+gCEM1S2NHPvWfP/hL+7FSr3+0p5RBEyhEN5JCKYr8XnASMT0xBNyzQGQeI8fjsGD39RMPk7se2bd5ZtTyoFYXftF6y37gx7NeUtJJOTFlAHDZLDuILU3j3+H5oOrD3yWbIztugaAzgnBKJuBLpGfQrS8wO4FZgV+c1IxaLgWVU0tMLEETCos4xMzEIv9cJXQcyagIwigDGwJgOAtHAwAhisQUjy0ORGERiELgG4iakkzo4MYAxcM5hAMi1WWG1yYCJIcMUaBkVRLdGeSU2995TLWzcUAzONJ7J6FBVBYIggMzmFbvdBV44Corg8vjhzC+EJEl8U1kJtgYrhCzgc/vvTwXKSib1paRFVRVORDAJAsw5FuTaJEhWM2SHB3mOAlhkNxwuLzeJsGwqWzf5TFNdKgtY5qHp6ZFf67Y/sAVadCaVY5YACDDb3Oi4NIjLnWMw2QthCBIsVhsUTU9tvXsjeq9+X1d75/KEs4LNOfcdf/+HthMnvwxOD0wmHaXr7ZItn2wuH2SnBzbZAbPJwpPx+VQuzcm7dgRCB57a1uBzUDRL4bfnI0RE0eaXd9W89mpjqHZnUI5Hh2l2dkZZUhOqpi2qSmpOmZ64Tuu9qlz/SEXo6MEHa3wOip46F1n7633eekV8ds8Wxjn37Wl63VVa+ej5oeEZ/82ZBETJjpJ1Rbij2D3Z/1trXUvLsblCK0XfOx0SX2kMsn9dX+d+7Kf6h8o4AIykuffjT8L20LU+w4AZd5VvEPY+XpWqLV327HR7DzXuDnD8r+ovkBehJ8i+y8YAAAAASUVORK5CYII=)"
    },
    warn: {
      "color": "#C09853",
      "background-color": "#FCF8E3",
      "border-color": "#FBEED5",
      "background-image": "url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAMAAAC6V+0/AAABJlBMVEXr6eb/2oD/wi7/xjr/0mP/ykf/tQD/vBj/3o7/uQ//vyL/twebhgD/4pzX1K3z8e349vK6tHCilCWbiQymn0jGworr6dXQza3HxcKkn1vWvV/5uRfk4dXZ1bD18+/52YebiAmyr5S9mhCzrWq5t6ufjRH54aLs0oS+qD751XqPhAybhwXsujG3sm+Zk0PTwG6Shg+PhhObhwOPgQL4zV2nlyrf27uLfgCPhRHu7OmLgAafkyiWkD3l49ibiAfTs0C+lgCniwD4sgDJxqOilzDWowWFfAH08uebig6qpFHBvH/aw26FfQTQzsvy8OyEfz20r3jAvaKbhgG9q0nc2LbZxXanoUu/u5WSggCtp1anpJKdmFz/zlX/1nGJiYmuq5Dx7+sAAADoPUZSAAAAAXRSTlMAQObYZgAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxMAAAsTAQCanBgAAAAHdElNRQfdBgUBGhh4aah5AAAAlklEQVQY02NgoBIIE8EUcwn1FkIXM1Tj5dDUQhPU502Mi7XXQxGz5uVIjGOJUUUW81HnYEyMi2HVcUOICQZzMMYmxrEyMylJwgUt5BljWRLjmJm4pI1hYp5SQLGYxDgmLnZOVxuooClIDKgXKMbN5ggV1ACLJcaBxNgcoiGCBiZwdWxOETBDrTyEFey0jYJ4eHjMGWgEAIpRFRCUt08qAAAAAElFTkSuQmCC)"
    }
    }
  });

});