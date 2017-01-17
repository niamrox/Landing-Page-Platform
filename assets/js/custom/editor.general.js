
/*
Global MWP JS object
*/
'use strict';
var MWP = MWP || {};

//Available namespaces 
MWP.Error = {};
MWP.Helper = {};
MWP.Editor = {};
MWP.Forms = {};
MWP.Windows = {};

//Error handler
//To set debug to display information use MWP.Error.debug to true
MWP.Error = {

  /*
   * Set debug mode
   */
  debug: true,

  /*Log error
  msg: Text to log
  src: string defining the source of the error
  fn: callback function
  */
  log: function (msg, src, type) {
    var logTypeName = !type ? 'Error' : type.toLowerCase() == 'error' ? 'Error' : type.toLowerCase() == 'warning' ? 'Warning' : type.toLowerCase() == 'info' ? 'Information' : 'Warning';
    if(this.debug) typeof console === "object" ? console.log('MWP '+logTypeName+': \"' + src + '\" returns -> ' + msg) : alert(src + ':\n' + msg)
    if (typeof fn == 'function') fn();
  }
}

//Helper methods
MWP.Helper = {

  /*
  Get current function name
  NOT FINISHED, USES BACKUP STRING INSTEAD
  */
  getFunctionName: function (name) {
    return name;
  }
}

MWP.Windows = {
  modal: {
    elements: {
      bg: function () { return document.getElementById('tmp_modal') }
    },

    show: function (content_url) {
      this.elements.bg().style.display = "block";
    }
  }
};

var isDirty, appLaunchEditorTour, editorSaveFrame, editorSaveLink, editorLinkBrowser, editorTogglePreview, editorSavePage, editorPublishPage, editorUnPublishPage, editorGetTemplateJson;

var tether = new Object();

var appEditor = $jq(function ($)
{
  var elfinderUrl = '/elfinder/standalonepopup/',
    dirty = false,
    color_picker = {};

  isDirty = function () {
    return dirty;
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   * Initialize editable regions, button positions and page height
   */

  appPageInit();

  /*
   * Bind resize: Set body height to make .full-height compatible with editor
   */
  $(window).resize(appSetDimensions);

  /*
   * Activate bootstrap tooltips
   */
  $('[data-toggle="tooltip"]').tooltip();

  /*
   * --------------------------------------------------------------------------------------------------------------------
   * Warning on page leave
   */

  window.onbeforeunload = function (e) {
    if (dirty) {
    var e = e || window.event;
    var message = 'You have unsaved changes. If you leave they will be lost. ' +
      'To save them click \'Stay on this Page\', and then click ' +
      'the \'Save\' button.';
    // For IE and Firefox
    if (e) {
      e.returnValue = message
    }
  
    // For Safari
    return message;
    };
  };

  /* Collapse side nav */
  $('#app-collapse').on('click', function() {
    if($('body').hasClass('app-collapsed')) {
      $('body').removeClass('app-collapsed');
      $(this).find('.fa').removeClass('fa-caret-right').addClass('fa-caret-left');
      $('#ascrail2000').show();
    } else {
      $('body').addClass('app-collapsed');
      $(this).find('.fa').removeClass('fa-caret-left').addClass('fa-caret-right');
      $('#ascrail2000').hide();
    }
  });

  /*
   * --------------------------------------------------------------------------------------------------------------------
   * Blocks dropout menu
   */

  var timeout;

  $('#blocks-collapse .list-group-item').on('mouseenter', function(){
    clearTimeout(timeout);
    $('#blocks-collapse .block-select').hide();
    $('#blocks-collapse .list-group-item').removeClass('active');
    $(this).addClass('active');
    var id = $(this).attr('data-select');
    //$('iframe').hide();
    $('#' + id).delay(100).show();
  });

  $('#blocks-collapse').on('mouseleave', function(){
    timeout = setTimeout(function(){
      $('#blocks-collapse .block-select').delay(100).fadeOut(50);
      $('#blocks-collapse .list-group-item').removeClass('active');
      //$('iframe').show();
    }, 300);
  });

  $('#blocks-collapse .list-group-item').on('click', function(){
    return false;
  });

  /*
   * --------------------------------------------------------------------------------------------------------------------
   * Blocks events
   */

  /*
   * Make initial blocks draggable
   */
  $('section').each(function(i, el)
  {
    if($(el).attr('id'))
    {
      $(el).prepend('<div class="app-section-btn app-drag"></div>');
      $(el).prepend('<div class="app-section-btn app-delete"></div>');
    }
  });

  /*
   * Delete block
   */
   $('body').on('click', '.app-section-btn.app-delete', function() {
    if(confirm(_lang['confirm_delete_block']))
    {
      $(this).parent().remove();
      repositionElements();
      dirty = true;

      if ($.trim($('#blocks').html()) == '')
      {
        $('body').addClass('blocks-empty');
      }
      else
      {
        $('body').removeClass('blocks-empty');
      }
    }
   });

  /*
   * Sortable
   */
  var draggedItem;

  appBindSortables();

  function appBindSortables()
  {
    $('#blocks').sortable(
    {
      placeholder: 'block-placeholder',
      handle: '.app-section-btn.app-drag',
      //opacity: 0.7,
      axis: 'y',
      revert: false,
      scroll: true,   
      helper: 'original',
      opacity: 0.85,
      zIndex: 1000,
      tolerance: 'intersect',
      distance: 80,
      cursor: 'ns-resize',
      stop: function(event, ui) {

        if(typeof draggedItem !== 'undefined')
        {
          dirty = true;
          var block_dir = $(draggedItem).attr('data-dir');
          var block_name = $(draggedItem).attr('data-block');

          // Show loader
          $(draggedItem).html('<div class="block-loading"></div>');

          $.ajax({
            type: 'GET',
            dataType: 'json',
            url: app_root + '/api/v1/site-edit/block-html',
            cache: true,
            data: { block_dir: block_dir, block_name: block_name }
          })
          .done(function(response) {
            // Unique id
            var id = 'block-' + new Date().getTime();

            // Set html
            var html = $(response.html);
            html.prepend('<div class="app-section-btn app-drag"></div>');
            html.prepend('<div class="app-section-btn app-delete"></div>');

            // Add named anchor for internal links
            var shortid = id.replace(/block-/g, '');

            html.prepend('<a name="' + block_name + '-' + shortid + '" class="internal-link"></a>');

            var $target = $(draggedItem).replaceWith(html);

            html.attr('id', id);
            html.attr('data-dir', block_dir);
            html.attr('data-block', block_name);
            html.attr('data-name', block_name + '-' + shortid);

            draggedItem = 'undefined';

            // Re-init page
            appPageInit();
          })
          .fail(function() {
            alert('AJAX call went wrong');
          });
        }
        // Reposition elements always
        repositionElements();
      }
    })/*.droppable(
    {
      // Notice the integration of Droppable here so that we can get to the exposed hoverClass 
      // feature which is not available to a sortable list.
      hoverClass: 'block-hover',
      accept: '.block-element',
      tolderance: 'intersect'
    })*/;
  }

  appBindDraggables();

  function appBindDraggables()
  {
    /*
     * Draggable
     */
    $('.block-element').draggable({
      connectToSortable: '#blocks',
      scroll: true,
      helper: 'clone', 
      opacity: 0.80, 
      revert: 'invalid',
      appendTo: 'body',
      cursor: 'move',
      start: function() {
        $('body').removeClass('blocks-empty');
      }
    });

    /*
     * Dropable
     */
    $('#blocks').droppable({
      hoverClass: 'block-hover',
      accept: 'div.block-element',
      greedy: true,    
      tolerance: 'pointer',
      drop: function( event, ui ) {
        dirty = true;
        draggedItem = ui.draggable;
        repositionElements();
      }
    });
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   * Save page
   */

  editorSavePage = function()
  {
    var app_page_name = $('#app_page_name').val();
    var app_meta_description = $('#app_meta_description').val();
    var app_meta_robots = $('#app_meta_robots').val();
    
    var page_nodes = appGetPageNodes();
    
    var jqxhr = $.ajax(
      {
        url: app_root + '/api/v1/site-edit/page',
        data:
        {
          sl: sl,
          blocks: page_nodes.blocks,
          text: page_nodes.text,
          icons: page_nodes.icons,
          images: page_nodes.images,
          bg_images: page_nodes.bg_images,
          color: page_nodes.color,
          links: page_nodes.links,
          iframes: page_nodes.iframes,
          forms: page_nodes.forms,
          app_page_name: app_page_name,
          app_meta_description: app_meta_description,
          app_meta_robots: app_meta_robots
        },
        method: 'POST'
      })
      .done(function()
      {
        dirty = false;
        $.notify(_lang['save_succes'],
        {
          position: 'top right',
          className: 'success'
        });
      })
      .fail(function()
      {
        console.log('error');
      })
      .always(function()
      {
        unblockUI();
      });
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   * Publish page
   */

  editorPublishPage = function()
  {
    var app_page_name = $('#app_page_name').val();
    var app_meta_description = $('#app_meta_description').val();
    var app_meta_robots = $('#app_meta_robots').val();

    var page_nodes = appGetPageNodes();

    var jqxhr = $.ajax(
      {
        url: app_root + '/api/v1/site-edit/page',
        data:
        {
          sl: sl,
          publish: 1,
          blocks: page_nodes.blocks,
          text: page_nodes.text,
          icons: page_nodes.icons,
          images: page_nodes.images,
          bg_images: page_nodes.bg_images,
          color: page_nodes.color,
          links: page_nodes.links,
          iframes: page_nodes.iframes,
          forms: page_nodes.forms,
          app_page_name: app_page_name,
          app_meta_description: app_meta_description,
          app_meta_robots: app_meta_robots
        },
        method: 'POST'
      })
      .done(function()
      {
        dirty = false;
        $.notify(_lang['publish_succes'],
        {
          position: 'top right',
          className: 'success'
        });
      })
      .fail(function()
      {
        console.log('error');
      })
      .always(function()
      {
        unblockUI();
      });
  };

  editorGetTemplateJson = function()
  {
    var page_nodes = appGetPageNodes();
    page_nodes = JSON.stringify(page_nodes, 'false', true);
    //window.prompt("Copy to clipboard: Ctrl+C, Enter", page_nodes);
    document.write('<textarea style="width:600px;height:600px;">' + page_nodes.replace(/</g, '&lt;').replace(/>/g, '>') + '</textarea>');
    return;
    unblockUI();
  };

  editorUnPublishPage = function()
  {
    if(confirm(_lang['confirm_unpublish']))
    {
      blockUI();
      var jqxhr = $.ajax(
        {
          url: app_root + '/api/v1/site-edit/unpublish-page',
          data:
          {
            sl: sl
          },
          method: 'POST'
      })
      .done(function()
      {
        $.notify(_lang['unpublish_succes'],
        {
          position: 'top right',
          className: 'success'
        });
      })
      .fail(function()
      {
        console.log('error');
      })
      .always(function()
      {
        unblockUI();
      });
    }
  };

  function appGetPageNodes()
  {
    // Show loader
    blockUI();

    // Unbind TinyMCE instances before cloning
    if(typeof(tinyMCE) !== 'undefined') {
      var length = tinyMCE.editors.length;
      for (var i=length; i>0; i--) {
        tinyMCE.editors[i-1].remove();
      };
    }

    // Destroy draggable
    $('#blocks').sortable('destroy');
    $('.block-element').draggable('destroy');
    $('#blocks').droppable('destroy');

    // Hide color pickers
    $.each(color_picker, function(i, item)
    {
      $(this).spectrum('destroy');
    });

    // Save current dom
    var $old_dom = $('#blocks').clone(true, true);

    /*
     * --------------------------------------------------------------------------------------------------------------------
     * Parse blocks
     */

    var blocks = new Array();
    $('section').each(function(i, el)
    {
      if($(el).attr('id'))
      {
        blocks.push(
        {
          id: $(el).attr('id'),
          dir: $(el).attr('data-dir'),
          block: $(el).attr('data-block')
        });
      }

      // Remove ID's to get better CSS paths
      $(el).find('div, ul, li, span, a, img, i, p, button, h1, h2, h3, h4, h5, h6, iframe, form, small').removeAttr('id');

      // Unwrap for better paths
      var unwrap = ['img-wrap', 'icon-wrap', 'form-wrap', 'link-wrap', 'iframe-wrap', 'bg-color-wrap', 'color-wrap', 'bg-img-wrap'];
      $.each(unwrap, function(i, item)
      {
        $('.' + item).contents().unwrap();
      });

      // Remove elements
      var remove_elements = ['app-section-btn', 'app-wrap-btn', 'app-wrap', 'dropdown', 'parsley-errors-list', 'internal-link', 'iframe-settings'];
      $.each(remove_elements, function(i, item)
      {
        $('.' + item).remove();
      });
    });

    // Parsley reset
    resetForms();

    /*
     * --------------------------------------------------------------------------------------------------------------------
     * Parse content
     */

    // Text
    var text = new Array();
    $('.text-editable').each(function()
    {
      var html = $(this).html();
      var path = cssPath($(this)[0]);

      text.push(
      {
        path: path,
        html: html.replace('type="mce-', 'type="').replace("type='mce-", "type='")
      });
    });

    // Icons
    var icons = new Array();
    $('.icon-editable').each(function()
    {
      if($(this).attr('data-icon'))
      {
        var path = cssPath($(this)[0]);
        var icon = $(this).attr('data-icon');
        var color = $(this).css('color');

        icons.push(
        {
          path: path,
          color: color,
          icon: icon
        });
      }
    });

    // Images
    var images = new Array();
    $('.img-editable').each(function()
    {
      var path = cssPath($(this)[0]);
      var src = $(this).attr('src');

      images.push(
      {
        path: path,
        src: src
      });
    });

    // Background images
    var bg_images = new Array();
    $('.bg-img-editable').each(function()
    {
      var path = cssPath($(this)[0]);
      var url = $(this).css('background-image');
      if (url != 'none')
      {
        url = url.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');

        bg_images.push(
        {
          path: path,
          url: url
        });
      }
    });

    // Background color
    var color = new Array();
    $('.bg-color-editable').each(function()
    {
      if(typeof $(this).attr('style') !== typeof undefined && $(this).attr('style') !== false)
      {
        var path = cssPath($(this)[0]);
        var rgba = $(this).css('background-color');

        color.push(
        {
          path: path,
          rgba: rgba
        });
      }
    });

    // Links
    var links = new Array();
    $('.link-editable').each(function()
    {
      var path = cssPath($(this)[0]);
      path = path.replace(" > form:nth-child(", ' > a:nth-child('); // replace with original tag from template block
      var settings = ($(this).next('.link-settings').length) ? $(this).next('.link-settings').val() : '';

      links.push(
      {
        path: path,
        settings: settings
      });
    });

    // Iframes
    var iframes = new Array();
    $('.iframe-editable').each(function()
    {
      var path = cssPath($(this)[0]);
      var src = ($(this).attr('src')) ? $(this).attr('src') : '#';
      var settings = ($(this).next('.iframe-settings').length) ? $(this).next('.iframe-settings').val() : '';

      iframes.push(
      {
        path: path,
        src: src,
        settings: settings
      });
    });

    // Forms
    var forms = new Array();
    $('.form-editable').each(function()
    {
      var path = cssPath($(this).parent()[0]);
      var html = $(this).html();

      forms.push(
      {
        path: path,
        html: html
      });
    });

    // Restore old dom
    $('#blocks').replaceWith($old_dom);

    // Rebind tehtered elements
    parseLinkNodes(true);
    parseImageNodes(true);
    parseIconNodes(true);
    parseFormNodes(true);
    parseIframeNodes(true);

    appBindSortables();
    appBindDraggables();
    siteBindForms();

    return {
      blocks: blocks,
      text: text,
      icons: icons,
      images: images,
      bg_images: bg_images,
      color: color,
      links: links,
      iframes: iframes,
      forms: forms
    };
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   * General (G)UI
   */

  /*
   * Add niceScroll to sidemenu
   */
  $('#app-props').niceScroll(
  {
    cursorborder: 14,
    horizrailenabled: false,
    railoffset:
    {
      top: 2,
      left: -2
    }
  });
  $('#ascrail2000').show();
  $('#ascrail2000').getNiceScroll().resize();
  /*
   * Toggle preview mode
   */
  editorTogglePreview = function() {
    parent.$('#app-toggle-buttons i').removeClass('fa-eye');
    parent.$('#app-toggle-buttons i').removeClass('fa-eye-slash');

    if ($('body').hasClass('app-preview'))
    {
      parent.$('#app-toggle-buttons i').addClass('fa-eye');
      parent.$('#app-toggle-buttons').removeClass('active');
      $('body').removeClass('app-preview')
    }
    else
    {
      parent.$('#app-toggle-buttons i').addClass('fa-eye-slash');
      parent.$('#app-toggle-buttons').addClass('active');
      $('body').addClass('app-preview')

    }

    $('.app-inline-btn').toggle();
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Parse nodes and recalculate positions and body height
   */
  function appPageInit()
  {
    // Make nodes editable
    parseImageNodes();
    parseLinkNodes();
    parseIconNodes();
    parseIframeNodes();
    parseBgColorNodes();
    parseBgImageNodes();
    parseFormNodes();

    // (re)set dimensions
    appSetDimensions();

    // Bind AJAX forms
    if(typeof siteBindForms !== 'undefined')
    {
      siteBindForms();
    }

    // Reposition edit buttons
    setTimeout(repositionElements, 100);

    if ($.trim($('#blocks').html()) == '')
    {
      $('body').addClass('blocks-empty');
    }
    else
    {
      $('body').removeClass('blocks-empty');
    }
  }

  function appSetDimensions()
  {
    $('body').css('height', parseInt($(document).height()) + 'px');
    $('.full-height').css('height', parseInt($(window).height()) + 'px');
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Add inline buttons for links
   */
  function parseLinkNodes(rewrap)
  {
    rewrap = (typeof rewrap === 'undefined') ? false : rewrap;
    var i = 0;

    $('.link-editable').each(function(i, el)
    {
      if(! $(el).attr('id') || rewrap)
      {
        if (rewrap)
        {
          // Get unique id
          var id = $(el).attr('id');
        } else {
          // Set unique id
          var id = 'link-' + new Date().getTime() + '-' + i;
          $(el).attr('id', id);
        }

        i++;

        var $wrap = $('<span class="link-wrap"></span>');
      $(el).wrap($wrap);

        var $element = $(el).parent().prepend('<div class="app-wrap-btn app-link-select" data-id="' + id + '"></div>');

        tether[id] = new Tether({
          element: $element.find('.app-wrap-btn'),
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px 10px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });

      }
      else
      {
        var id = $(el).attr('id');
        var $element = $('[data-id="' + id + '"]');

        tether[id].setOptions({
          element: $element,
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px 10px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });
      }
    });
  }

  // Link modal
  $('body').on('click', '.app-link-select', function()
  {
    var id = $(this).attr('data-id');
    var settings = ($(this).parent('.link-wrap').next('.link-settings').length) ? $(this).parent('.link-wrap').next('.link-settings').val() : '';
    settings = (settings == '') ? {} : JSON.parse(settings);

    if(typeof settings.href === 'undefined')
    {
      var href = $('#' + id).attr('href');
      if(typeof href === 'undefined') href = '#';
      settings.href = href;
    }
    if(typeof settings.href_only === 'undefined')
    {
      var href_only = ($('#' + id).hasClass('href-only')) ? true : false;
      settings.href_only = href_only;
    }
    if(typeof settings.html === 'undefined')
    {
      var html = (! href_only) ? $('#' + id).html() : null;
      settings.html = html;
    }

    var type = settings.type || 'link';

    siteOpenModal('/app/modal/web/link-editor?type=' + type, function() {
      $('#le_id').val(id);
      ieLoaded(settings);
    });
  });

  // Open browser
  editorLinkBrowser = function(id)
  {
    $.colorbox(
    {
      href: elfinderUrl + id,
      fastIframe: true,
      iframe: true,
      width: '70%',
      height: '80%'
    });
  }

  // Save link
  editorSaveLink = function(id, form)
  {
    dirty = true;

    // Get classes & html
    var classes = ($('#' + id).is('a')) ? $('#' + id).attr('class') : $('#' + id).find('button').attr('class');
    classes = classes.replace(/link-editable/g, '');
    form.classes = classes;

    var html = ($('#' + id).is('a')) ? $('#' + id).html() : $('#' + id).find('button').html();
    form.html_only = html;

    var $settings = $('#' + id).parent('.link-wrap').next('.link-settings');

    if($settings.length)
    {
      $settings.replaceWith('<textarea style="display:none" class="link-settings">' + JSON.stringify(form) + '</textarea>');
    }
    else
    {
      $('#' + id).parent('.link-wrap').after('<textarea style="display:none" class="link-settings">' + JSON.stringify(form) + '</textarea>');
    }

    var $wrap = $('#' + id).parent('.link-wrap');

    if(form.type == 'link')
    {
       // var link_html = $('<div>').append($('#' + id).clone()).html(); 
      if(form.href_only)
      {
        var link_html = '<a href="' + form.href + '" id="' + id + '" target="' + form.target + '" class="link-editable ' + classes + '">' + html + '</a>'; 
      }
      else
      {
        var link_html = '<a href="' + form.href + '" id="' + id + '" target="' + form.target + '" class="link-editable ' + classes + '">' + form.html + '</a>'; 
      }

      var link_el = $.parseHTML(link_html);

      $.when($wrap.find('.link-editable').remove()).then(function() {
        $wrap.append($(link_el));
        // Rebind Tether
        parseLinkNodes();
      });
    }

    if(form.type == 'paypal')
    {
      if(form.paypal_sandbox == '1')
      {
        var link_html = '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top" class="link-editable" id="' + id + '">';
      }
      else
      {
        var link_html = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" class="link-editable" id="' + id + '">';
      }
      link_html += '<input type="hidden" name="cmd" value="_xclick">';
      link_html += '<input type="hidden" name="business" value="' + form.paypal_email + '">';
      link_html += '<input type="hidden" name="lc" value="US">';
      link_html += '<input type="hidden" name="item_name" value="' + form.paypal_item_name + '">';
      //link_html += '<input type="hidden" name="item_number" value="id">';
      link_html += '<input type="hidden" name="amount" value="' + form.paypal_item_price + '">';
      link_html += '<input type="hidden" name="currency_code" value="' + form.paypal_currency + '">';
      link_html += '<input type="hidden" name="button_subtype" value="services">';
      link_html += '<input type="hidden" name="no_note" value="0">';
      link_html += '<input type="hidden" name="tax_rate" value="' + form.paypal_tax_rate + '">';
      link_html += '<input type="hidden" name="shipping" value="' + form.paypal_shipping_cost + '">';
      link_html += '<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">';
      if(form.href_only)
      {
        link_html += '<button type="submit" class="' + classes + '">' + html + '</button>'; 
      }
      else
      {
        link_html += '<button type="submit" class="' + classes + '">' + form.html + '</button>'; 
      }
      link_html += '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';
      link_html += '</form>';

      var link_el = $.parseHTML(link_html);

      $.when($wrap.find('.link-editable').remove()).then(function() {
        $wrap.append($(link_el));
        // Rebind Tether
        parseLinkNodes();
      });
    }

    if(form.type == '2checkout')
    {
      if(form.checkout_sandbox == '1')
      {
        var link_html = '<form action="https://sandbox.2checkout.com/checkout/purchase" method="post" target="_top" class="link-editable" id="' + id + '">';
      }
      else
      {
        var link_html = '<form action="https://www.2checkout.com/checkout/purchase" method="post" target="_top" class="link-editable" id="' + id + '">';
      }
 
      link_html += '<input type="hidden" name="sid" value="' + form.checkout_account + '">';
      link_html += '<input type="hidden" name="mode" value="2CO" />';
      link_html += '<input type="hidden" name="li_0_type" value="product" />';
      link_html += '<input type="hidden" name="li_0_product_id" value="' + form.checkout_item_id + '">';
      link_html += '<input type="hidden" name="li_0_name" value="' + form.checkout_item_name + '">';
      link_html += '<input type="hidden" name="li_0_description" value="' + form.checkout_item_description + '">';
      link_html += '<input type="hidden" name="li_0_quantity" value="1">';
      link_html += '<input type="hidden" name="li_0_tangible" value="Y" />';
      link_html += '<input type="hidden" name="li_0_price" value="' + form.checkout_item_price + '">';
      link_html += '<input type="hidden" name="currency_code" value="' + form.checkout_currency + '">';

      if(form.href_only)
      {
        link_html += '<button type="submit" class="' + classes + '">' + html + '</button>'; 
      }
      else
      {
        link_html += '<button type="submit" class="' + classes + '">' + form.html + '</button>'; 
      }
      link_html += '</form>';

      var link_el = $.parseHTML(link_html);

      $.when($wrap.find('.link-editable').remove()).then(function() {
        $wrap.append($(link_el));
        // Rebind Tether
        parseLinkNodes();
      });
    }

    siteCloseModal();
    repositionElements();
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Parse Iframe nodes
   */
  function parseIframeNodes(rewrap)
  {
    rewrap = (typeof rewrap === 'undefined') ? false : rewrap;
    var i = 0;

    $('.iframe-editable').each(function(i, el)
    {
      if(! $(el).attr('id') || rewrap)
      {
        if (rewrap)
        {
          // Get unique id
          var id = $(el).attr('id');
        } else {
          // Set unique id
          var id = 'iframe-' + new Date().getTime() + '-' + i;
          $(el).attr('id', id);
        }

        i++;

        var src = $(el).attr('src');
        var type = $(el).attr('data-type') || 'url';
        var html = $(el).html();

        var $wrap = $('<span class="iframe-wrap"></span>');
        $(el).wrap($wrap);

        var $element = $(el).parent().prepend('<div class="app-wrap-btn app-iframe-select" data-id="' + id + '" data-type="' + type + '"></div>');

        tether[id] = new Tether({
          element: $element.find('.app-wrap-btn'),
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px 10px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });
      }
      else
      {
        var id = $(el).attr('id');
        var $element = $('[data-id="' + id + '"]');

        tether[id].setOptions({
          element: $element,
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px 10px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });
      }
    });
  }

  // Iframe modal
  $('body').on('click', '.app-iframe-select', function()
  {
    var id = $(this).attr('data-id');
    var settings = ($(this).parent('.iframe-wrap').next('.iframe-settings').length) ? $(this).parent('.iframe-wrap').next('.iframe-settings').val() : '';
    settings = (settings == '') ? {} : JSON.parse(settings);

    var type = settings.type || 'url';
    var src = $('#' + id).attr('src');
    if(typeof src === 'undefined') src = '#';

    siteOpenModal('/app/modal/web/iframe-editor?type=' + type, function() {
      $('#ie_id').val(id);
      $('#ie_src').val(src);
      ieLoaded(settings);
    });
  });

  // Save iframe
  editorSaveFrame = function(id, form)
  {
    dirty = true;
    $('#' + id).attr('src', form.src);

    var $settings = $('#' + id).parent('.iframe-wrap').next('.iframe-settings');

    if($settings.length)
    {
      $settings.replaceWith('<textarea style="display:none" class="iframe-settings">' + JSON.stringify(form) + '</textarea>');
    }
    else
    {
      $('#' + id).parent('.iframe-wrap').after('<textarea style="display:none" class="iframe-settings">' + JSON.stringify(form) + '</textarea>');
    }

    siteCloseModal();
    repositionElements();
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Add inline buttons for images
   */
  function parseImageNodes(rewrap)
  {
    rewrap = (typeof rewrap === 'undefined') ? false : rewrap;
    var i = 0;

    $('.img-editable').each(function(count, el)
    {
      if(! $(el).attr('id') || rewrap)
      {
        if (rewrap)
        {
          // Get unique id
          var id = $(el).attr('id');
        } else {
          // Set unique id
          var id = 'img-' + new Date().getTime() + '-' + i;
          $(el).attr('id', id);
        }

        i++;

        var $wrap = $('<div class="img-wrap"></div>');
        $(el).wrap($wrap);

        var $element = $(el).parent().prepend('<div class="dropdown"><div class="app-wrap-btn app-img-select" data-id="' + id + '" id="dropdown-' + id + '" data-toggle="dropdown"></div><ul class="dropdown-menu" role="menu" aria-labelledby="dropdown-' + id + '" style="left:10px;top:30px"><li><a href="javascript:void(0);" class="app-img-select-browser" data-id="' + id + '">' + _lang['select_image'] + '</a></li><li><a href="javascript:void(0);" class="app-img-select-none" data-id="' + id + '">' + _lang['no_image'] + '</a></li></ul></div>');

        tether[id] = new Tether({
          element: $element.find('.dropdown'),
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '35px 11px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });
      }
      else
      {
        var id = $(el).attr('id');
        var $element = $('[data-id="' + id + '"]');

        tether[id].setOptions({
          element: $element,
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '35px 11px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });
      }
    });
  }

  // No image
  $('body').on('click', '.app-img-select-none', function()
  {
    dirty = true;
    $('#' + $(this).attr('data-id')).attr('src', app_root + '/assets/images/interface/1.gif');
    setTimeout(repositionElements, 100);
  });

  // Image selector popup
  $('body').on('click', '.app-img-select-browser', function()
  {
    // trigger the reveal modal with elfinder inside
    $.colorbox(
    {
      href: elfinderUrl + $(this).attr('data-id'),
      fastIframe: true,
      iframe: true,
      width: '70%',
      height: '80%'
    });
  });

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Add inline buttons for background colors
   */
  function parseBgColorNodes()
  {
    var i = 0;
    $('.bg-color-editable').each(function(i, el)
    {
      if(! $(el).attr('id'))
      {
        // Set unique id
        var id = 'bg-color-' + new Date().getTime() + '-' + i;
        $(el).attr('id', id);
        i++;

        $(el).prepend('<div class="app-wrap-btn app-bg-color-select" data-id="' + id + '" id="picker-' + id + '"></div>');
      }
    });
  }

  // Bind color picker
  $('body').on('click', '.app-bg-color-select', function()
  {
    var el = $(this).parent('.bg-color-editable');
    var id = $(el).attr('id');
    var color = $(el).css('background-color');

    color_picker['#picker-' + id] = $('#picker-' + id).spectrum({
      color: color,
      containerClassName: 'sp-dark',
      /*appendTo: $('#picker-' + id),*/
      appendTo: $('body'),
      showInitial: true,
      showButtons: true,
      cancelText: _lang['cancel'],
      chooseText: _lang['ok'],
      flat: false,
      showInput: true,
      allowEmpty: false,
      showAlpha: true,
      clickoutFiresChange: false,
      preferredFormat: 'hex',
      change: function(color) {
        dirty = true;
        var new_color = color.toRgbString();
        $(el).css('background-color', new_color);
      },
      hide: function(color) {
        dirty = true;
        var new_color = color.toRgbString();
        $(el).css('background-color', new_color);
      }
    });

    $('#picker-' + id).on('dragstop.spectrum', function(e, color) {
      var new_color = color.toRgbString();
      $(el).css('background-color', new_color);
    });
    setTimeout(function() {
      $('#picker-' + id).spectrum('show');
    }, 200);
  });

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Add inline buttons for background images
   */
  function parseBgImageNodes()
  {
    var i = 0;
    $('.bg-img-editable').each(function(i, el)
    {
      if(! $(el).attr('id'))
      {
        // Set unique id
        var id = 'bg-img-' + new Date().getTime() + '-' + i;
        $(el).attr('id', id);
        i++;

        $(el).prepend('<div class="dropdown"><div class="app-wrap-btn app-bg-img-select" data-id="' + id + '" data-toggle="dropdown"></div><ul class="dropdown-menu" role="menu"><li><a href="javascript:void(0);" class="app-bg-img-select-browser" data-id="' + id + '">' + _lang['select_image'] + '</a></li><li><a href="javascript:void(0);" class="app-bg-img-select-none" data-id="' + id + '">' + _lang['no_image'] + '</a></li></ul></div>');
      }
    });
  }

  // Image picker
  $('body').on('click', '.app-bg-img-select-browser', function()
  {
    // trigger the reveal modal with elfinder inside
    $.colorbox(
    {
      href: elfinderUrl + $(this).attr('data-id'),
      fastIframe: true,
      iframe: true,
      width: '70%',
      height: '80%'
    });
  });

  // No image
  $('body').on('click', '.app-bg-img-select-none', function()
  {
    dirty = true;
    $('#' + $(this).attr('data-id')).css('background-image', 'none');
  });

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */
  /*
  * Add inline buttons for form editor
  */

  function parseFormNodes(rewrap) {
    var i = 0;
    $('.form-editable').each(function (i, el) {
      var id;
      if (! $(el).attr('id') || rewrap)
      {
        if (rewrap)
        {
          // Get unique id
          var id = $(el).attr('id');
        } else {
          // Set unique id
          var id = 'form-' + new Date().getTime() + '-' + i;
          $(el).attr('id', id);
        }

        i++;

        var $wrap = $('<div class="form-wrap"></div>');
        $(el).wrap($wrap);

        var $element = $(el).parent().prepend('<div class="app-wrap-btn app-form-edit" data-id="' + id + '" onclick="MWP.Forms.Builder.init(\'' + id + '\')"></div>');

        tether[id] = new Tether({
          element: $element.find('.app-wrap-btn'),
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px 11px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });

      }
      else
      {
        var id = $(el).attr('id');
        var $element = $('[data-id="' + id + '"]');

        tether[id].setOptions({
          element: $element,
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px 11px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });

      }
    });
  }
  /*
   * --------------------------------------------------------------------------------------------------------------------
   */


  // Callback after elfinder selection
  window.processSelectedFile = function(filePath, requestingField)
  {
    dirty = true;
    var tagName = $('#' + requestingField).prop('tagName').toLowerCase();

    // Change image
    if(tagName == 'img')
    {
      $('#' + requestingField).attr('src', filePath);
    }
    else if(tagName == 'input')
    {
      $('#' + requestingField).val(filePath);
    }
    else if(tagName == 'div')
    {
      $('#' + requestingField).css('background-image', 'url('+filePath+')');

      // Check if color-overlay exists
      var color_overlay = $('#' + requestingField).find('.color-overlay');
      if(color_overlay.length)
      {
        // Check if opacity is 0
      }
    }
    setTimeout(repositionElements, 500);
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Add icon class to font icons
   */
  function parseIconNodes(rewrap)
  {
    rewrap = (typeof rewrap === 'undefined') ? false : rewrap;
    var i = 0;

    $('.icon-editable').each(function(i, el)
    {
      if(! $(el).attr('id') || rewrap)
      {
        if (rewrap)
        {
          // Get unique id
          var id = $(el).attr('id');
        } else {
          // Set unique id
          var id = 'icon-' + new Date().getTime() + '-' + i;
          $(el).attr('id', id);
        }

        i++;

        var $wrap = $('<div class="icon-wrap"></div>');
        $(el).wrap($wrap);

        var $iconSelect = $(el).parent().prepend('<div class="app-wrap-btn app-icon-select" data-id="' + id + '"></div>');
        var $colorSelect = $(el).parent().prepend('<div class="app-wrap-btn app-icon-color-select" data-id="' + id + '" id="picker-' + id + '"></div>');

        tether['colorSelect' + id] = new Tether({
          element: $colorSelect.find('.app-wrap-btn'),
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px -15px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });

        tether['iconSelect' + id] = new Tether({
          element: $iconSelect.find('.app-wrap-btn'),
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px 11px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });
      }
      else
      {
        var id = $(el).attr('id');
        var $iconSelect = $('.app-icon-select[data-id="' + id + '"]');
        var $colorSelect = $('.app-icon-color-select[data-id="' + id + '"]');

        tether['colorSelect' + id].setOptions({
          element: $colorSelect,
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px -15px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });

        tether['iconSelect' + id].setOptions({
          element: $iconSelect,
          target: $(el),
          attachment: 'bottom left',
          targetAttachment: 'top left',
          offset: '12px 11px',
          constraints: [{
              to: 'window',
              attachment: 'together'
          }]
        });
      }
    });
  }

  // Bind color picker
  $('body').on('click', '.app-icon-color-select', function()
  {
    var el = $('#' + $(this).attr('data-id'));
    var id = $(el).attr('id');
    var color = $(el).css('color');

    color_picker['#picker-' + id] = $('#picker-' + id).spectrum({
      color: color,
      containerClassName: 'sp-dark',
      /*appendTo: $('#picker-' + id),*/
      appendTo: $('body'),
      showInitial: true,
      showButtons: true,
      cancelText: _lang['cancel'],
      chooseText: _lang['ok'],
      flat: false,
      showInput: true,
      allowEmpty: false,
      showAlpha: true,
      clickoutFiresChange: false,
      preferredFormat: 'hex',
      change: function(color) {
        dirty = true;
        var new_color = color.toRgbString();
        $(el).css('color', new_color);
      },
      hide: function(color) {
        dirty = true;
        var new_color = color.toRgbString();
        $(el).css('color', new_color);
      }
    });

    $('#picker-' + id).on('dragstop.spectrum', function(e, color) {
      var new_color = color.toRgbString();
      $(el).css('color', new_color);
    });
    setTimeout(function() {
      $('#picker-' + id).spectrum('show');
    }, 200);
  });

  // Icon selector popup
  $('body').on('click', '.app-icon-select', function()
  {
    var el_id = $(this).attr('data-id');
    var current_icon = $('#' + el_id).attr('data-icon');

    $.colorbox(
    {
      href: app_root + '/api/v1/site-edit/icon-picker?current_icon=' + current_icon,
      iframe: false,
      scrolling: false,
      innerWidth:'100',
      innerHeight:'100',
      width: '624',
      height: '80%',
      onComplete: function() {

        $('#app-icon-search').keydown($.throttle(400, function(){
          var filter = $(this).val(), count = 0;

          $('#app-icon-picker .app-pick-icon').each(function(){
            if ($(this).attr('data-icon').search(new RegExp(filter, 'i')) < 0) {
              $(this).hide();
            } else {
              $(this).show();
              count++;
            }
          });

          $('#app-icon-picker').getNiceScroll().resize();

          var numberItems = count;
          //$('#filter-count').text('Number of Comments = '+count);
        }));

        $('#app-icon-picker').niceScroll(
        {
          cursorborder: 14,
          horizrailenabled: false,
          railoffset:
          {
            top: 2,
            left: -2
          }
        });
        $('#ascrail2001 *').show();

        $('.app-pick-icon').on('click', function() {
          dirty = true;
          $('.app-pick-icon').removeClass('btn-primary');
          $(this).addClass('btn-primary');
          var icon = $(this).attr('data-icon');
          $('#' + el_id).removeClass(current_icon);
          $('#' + el_id).addClass(icon);
          $('#' + el_id).attr('data-icon', icon);

          $.colorbox.close();
        });
      }
    });
  });

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  function repositionElements()
  {
    Tether.position();
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Update page title real time when typing input
   */

  $('#app_page_name').on('keydown', function()
  {
    dirty = true;
    document.title = $(this).val();
  });

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * Show inline TinyMCE editor when creating a new editor
   */

  tinyMCE.on('AddEditor', function(e) {
    e.editor.on('NodeChange', function(e) {  // now that we know the editor set a callback at "NodeChange."
      e.target.fire("focusin");     // NodeChange is at the end of editor create. Fire focusin to render and show it
    });
  });

  /*
   * TinyMCE browser
   */

  function elFinderBrowser (field_name, url, type, win) {
    tinyMCE.activeEditor.windowManager.open({
    file: '/elfinder/tinymce',
    title: 'Files',
    width: 940,
    height: 450,
    resizable: 'yes',
    inline: 'yes',  // This parameter only has an effect if you use the inlinepopups plugin!
    popup_css: false, // Disable TinyMCE's default popup CSS
    close_previous: 'no'
    }, {
    setUrl: function (url) {
      win.document.getElementById(field_name).value = url;
    }
    });
    return false;
  }

  /*
   * Bind TinyMCE on click text-editable
   */
  $('body').on('click', '.text-editable', function()
  {
    var el = this;

    // Check if TinyMCE already is attached
    if ($(this).hasClass('mce-content-body')) return false;

     // $(this).attr('contenteditable', true);

    // Set unique id
    var id = 'text-' + new Date().getTime();
    $(this).attr('id', id);

    tinymce.init({
      selector: '#' + id,
      inline: true,
      schema: "html5",
      relative_urls: false,
      apply_source_formatting: false, 
      extended_valid_elements: 'span[style,class],script[charset|defer|language|src|type]',
      verify_html: false, 
      file_browser_callback: elFinderBrowser,
      plugins: [
        "advlist autolink lists link image charmap anchor",
        "searchreplace visualblocks code textcolor colorpicker",
        "insertdatetime media table contextmenu paste fontawesome noneditable"
      ],
      content_css: '//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css',
      menubar: "edit insert format table tools",
      toolbar: "undo redo | forecolor backcolor | bold italic | bullist numlist outdent indent | link image fontawesome",
      link_list: getLinkList(),
      init_instance_callback : function(editor) {
        editor.serializer.addNodeFilter('script,style', function(nodes, name) {
            var i = nodes.length, node, value, type;

            function trim(value) {
                return value.replace(/(<!--\[CDATA\[|\]\]-->)/g, '\n')
                        .replace(/^[\r\n]*|[\r\n]*$/g, '')
                        .replace(/^\s*((<!--)?(\s*\/\/)?\s*<!\[CDATA\[|(<!--\s*)?\/\*\s*<!\[CDATA\[\s*\*\/|(\/\/)?\s*<!--|\/\*\s*<!--\s*\*\/)\s*[\r\n]*/gi, '')
                        .replace(/\s*(\/\*\s*\]\]>\s*\*\/(-->)?|\s*\/\/\s*\]\]>(-->)?|\/\/\s*(-->)?|\]\]>|\/\*\s*-->\s*\*\/|\s*-->\s*)\s*$/g, '');
            }
            while (i--) {
                node = nodes[i];
                value = node.firstChild ? node.firstChild.value : '';

                if (value.length > 0) {
                    node.firstChild.value = trim(value);
                }
            }
        });
      }
    });
  });

  function getLinkList()
  {
    var internal_links = [];

    $('section[data-block]').each(function(i) {
      var name = $(this).attr('data-block');
      var id = $(this).attr('id');
      id = id.replace(/block-/g, '');

      internal_links.push({title: name, value: '#' + name + '-' + id});
    });

    return internal_links;
  }

  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  // Get css path to element
  function cssPath(el, echo)
  {
    if (typeof echo !== 'undefined')
    {
      console.log(el);
    }

    if (!(el instanceof Element))
    {
      return;
    }
    var path = [];
    while (el.nodeType === Node.ELEMENT_NODE)
    {
      var selector = el.nodeName.toLowerCase();
      if (el.id)
      {
        selector += '#' + el.id;
        path.unshift(selector);
        break;
      }
      else
      {
        var sib = el,
          nth = 1;
        while (sib = sib.previousElementSibling)
        {
          nth++;
        }
        selector += ":nth-child(" + nth + ")";
      }
      path.unshift(selector);
      el = el.parentNode;
    }
    return path.join(" > ");
  }
  /*
   * --------------------------------------------------------------------------------------------------------------------
   */

  /*
   * BlockUI
   */
  function blockUI(el)
  {
    if (typeof el === 'undefined')
    {
      $.blockUI(
      {
        message: '<div class="app-spinner" id="app-spinner"> <div class="rect1" style="background-color:#fff"></div> <div class="rect2" style="background-color:#fff"></div> <div class="rect3" style="background-color:#fff"></div> <div class="rect4" style="background-color:#fff"></div> <div class="rect5" style="background-color:#fff"></div> </div>',
        fadeIn: 0,
        fadeOut: 50,
        baseZ: 10000000,
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
        message: '<div class="app-spinner" id="app-spinner"> <div class="rect1" style="background-color:#fff"></div> <div class="rect2" style="background-color:#fff"></div> <div class="rect3" style="background-color:#fff"></div> <div class="rect4" style="background-color:#fff"></div> <div class="rect5" style="background-color:#fff"></div> </div>',
        fadeIn: 0,
        fadeOut: 50,
        baseZ: 10000000,
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
      "padding": "10px 10px 10px 16px",
      "text-shadow": "0 1px 0 rgba(255, 255, 255, 0.5)",
      "background-color": "#fcf8e3",
      "border": "1px solid #fbeed5",
      "border-radius": "0px",
      "white-space": "nowrap",
      "padding-left": "20px",
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
      "padding-left": "26px",
      "background-position": "8px",
      "background-image": "url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAKCAYAAACALL/6AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAIhJREFUeNpi+P//PwMh7NbhLgjEoSA2I4jAB9w7PQSB1G4gNgbidLwa0BSDARNUohyIjQkpBtsAdNtMICMNiN8DsevO8h1ncSkGys0CaXgH5AhCBUGawoC4A5timJNcoQoZoBp341IM1gByApomBlyK4Z7GoQlDMQigBCs0pECBMAubYhAACDAAQ+5dHlI7OxUAAAAASUVORK5CYII=)"
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