//Formbuilder namespace
MWP.Forms.Builder = {

  //Current objects
  current: {
    items: [],
    settings: {
      button: null,
      mailTo: null,
      successTitle: null,
      successMessage: null,
      successButton: null,
      successRedirect: null,
      mailChimpList: null,
      aweberList: null,
      getResponseList: null
    },
    form: null
  },

  //previous settings
  previous: {
    formHtml: null
  },


  /* Rollback to previous form */
  rollBack: function () {
    this.current.form.innerHTML = this.previous.formHtml;
  },

  /* Initialize builder and get info */
  init: function (formID) {

    // create the backdrop and wait for next modal to be triggered
    $('body').modalmanager('loading');

    //resety all values
    this.reset();

    //get Form
    var formObject = document.getElementById(formID);

    //Get obejcts in form
    if (!formObject) {
      MWP.Error.log('Can\'t find form', MWP.Helper.getFunctionName('MWP.Forms.Builder.init'));
      return;
    }

    //set forms
    this.previous.formHtml = formObject.innerHTML;
    this.current.form = formObject;

    //Load templates
    this.templates.load();

  },

  /* Templates */
  templates: {

    list: {},

    load: function () {

      $.ajax({
        cache: false,
        url: "/assets/js/custom/formbuilder.templates.json",
        dataType: "json",
        success: function (data) {

            //Load data into list
            MWP.Forms.Builder.templates.list = data;

            //Parse form
            MWP.Forms.Builder.parseForm();

        }
      }).fail(function () {

        MWP.Error.log('Unable to load json template file', MWP.Helper.getFunctionName('MWP.Forms.Builder.templates.load'));

      });

    },

    //Check if json object is array, if true flatten to one string
    makeString: function (jsonObj) {

      if (typeof jsonObj == "object") {
        return jsonObj.join("");
      }
      return jsonObj;

    },

    //Get public templates
    getClient: function (name) {

      return this.makeString(this.list.Client[name]);

    },

    //Get editor templates
    getEditor: function (name) {

      return this.makeString(this.list.Editor[name]);

    }

  },

  /* Parse current form */
  parseForm: function () {

    //get all input objects\
    var formObject = this.current.form,
      formInputs = formObject.querySelectorAll('.mwp-fb-input'),
      formSendBtn = formObject.querySelector('.mwp-fb-sendbutton'),
      formMailTo = formObject.querySelector('.mwp-fb-success-mail-to'),
      formSuccessTtl = formObject.querySelector('.mwp-fb-success-title'),
      formSuccessMsg = formObject.querySelector('.mwp-fb-success-msg'),
      formSuccessBtn = formObject.querySelector('.mwp-fb-success-btn'),
      formSuccessRedirect = formObject.querySelector('.mwp-fb-success-redirect');
      formMailChimpList = formObject.querySelector('.mwp-fb-success-mailchimp-list');
      formAweberList = formObject.querySelector('.mwp-fb-success-aweber-list');
      formGetResponseList = formObject.querySelector('.mwp-fb-success-getresponse-list');

    for (var i = 0; i < formInputs.length; i++) {

      //get Object
      var currentObject = formInputs[i];

      //extract info
      var item = this.items.get({ object: currentObject, number: i });
      if (item == null) {
        MWP.Error.log('Item is null and will not be included in editor', MWP.Helper.getFunctionName('MWP.Forms.Builder.parseForm'));
        continue;
      };
      this.current.items.push(item);
    }

    //If no input fields where found show default set and log
    if (formInputs.length == 0) {
      MWP.Error.log('No form input found will continue with default set', MWP.Helper.getFunctionName('MWP.Forms.Builder.parseForm'), 'info');
      this.insertDefaultFieldSet();
    }

    //Get button
    if (formSendBtn) {
      this.current.settings.button = formSendBtn.innerHTML;
    } else {
      this.current.settings.button = _lang['fb_default_submission_txt'];
      MWP.Error.log('No submit button found in selected form, will use default value.', MWP.Helper.getFunctionName('MWP.Forms.Builder.parseForm'), 'info');
    }

    //Mail to
    if (formMailTo) {
      this.current.settings.mailTo = formMailTo.innerHTML;
    } else {
      this.current.settings.mailTo = '';
    }

    //Get success modal
    if (!formSuccessTtl || !formSuccessMsg || !formSuccessBtn) MWP.Error.log('A success modal content item was not found in selected form, will use a default value.', MWP.Helper.getFunctionName('MWP.Forms.Builder.parseForm'), 'info');
    this.current.settings.successTitle = formSuccessTtl ? formSuccessTtl.innerHTML : _lang['fb_default_thanks_title'];
    this.current.settings.successMessage = formSuccessMsg ? formSuccessMsg.innerHTML : _lang['fb_default_thanks_msg'];
    this.current.settings.successButton = formSuccessBtn ? formSuccessBtn.innerHTML : _lang['fb_default_thanks_btn'];

    //Get redirect
    this.current.settings.successRedirect = formSuccessRedirect ? formSuccessRedirect.innerHTML : '';

    //Get third party lists
    this.current.settings.mailChimpList = formMailChimpList ? formMailChimpList.innerHTML : '';
    this.current.settings.aweberList = formAweberList ? formAweberList.innerHTML : '';
    this.current.settings.getResponseList = formGetResponseList ? formGetResponseList.innerHTML : '';

    //Load editor
    MWP.Forms.Builder.Editor.load();
  },

  //methods for extraction information from form items
  items: {

    //Get info from item by item type
    get: function (p) {

      //Get variables
      var currentObj = p.object ? p.object : null;
      if (currentObj) {

        //Get input type
        var inputType = currentObj.dataset.mwpInputtype;

        //Check inputType is filled
        if (typeof inputType == 'undefined') {
          MWP.Error.log('Inputtype is not defined', MWP.Helper.getFunctionName('MWP.Forms.Builder.items.get'));
          return;
        }

        //check if input type exists
        if (typeof MWP.Forms.Builder.Fields.Types[inputType] == 'undefined') {
          MWP.Error.log('Inputtype \'' + inputType + '\' is not a legal type', MWP.Helper.getFunctionName('MWP.Forms.Builder.items.get'));
          return;
        }

        //Return parsed item
        return this.parseInput(currentObj, inputType, p.number);


      } else {

        MWP.Error.log('currentObj is null', MWP.Helper.getFunctionName('MWP.Forms.Builder.items.get'));
        return;
      }


    },

    //Parse simple field types
    parseInput: function (inputObject, type, number) {

      //get elements
      var label = inputObject.querySelector('.mwp-fb-input-label'),
        input = inputObject.querySelectorAll('.mwp-fb-input-field'),
        properties;

      if (input && label) {

        //Check if value is not a list and if mandatory
        var value, isList, mandatory;
        if (type == 'radioList' || type == 'select' || type == 'selectMulti') {
          value = this.parseNodeList(input, type);
          isList = true;
          mandatory = inputObject.querySelector("select") ? inputObject.querySelector("select").required : false;
        } else {
          value = input[0].value;
          isList = false;
          mandatory = type != 'textArea' ? inputObject.querySelector("input").required : inputObject.querySelector("textarea").required;
        }

        properties = {
          Id: inputObject.id,
          Label: label.innerHTML,
          Placeholder: input.length == 1 ? input[0].getAttribute('placeholder') : null,
          Value: value,
          Rows: input.length == 1 ? input[0].getAttribute('rows') : null,
          IsList: isList,
          Mandatory: mandatory
        }

        //Return new input field object
        return new MWP.Forms.Builder.Fields.Types[type].create(properties);

      } else {
        var errorEl = !input ? 'input' : 'label';
        MWP.Error.log('Object with id:' + inputObject.id + ' and number:' + number + ' of type: ' + type + ' has missing ' + errorEl + ' element', MWP.Helper.getFunctionName('MWP.Forms.Builder.items.parseInput'));
      }

    },

    //Parse node list an return value array
    parseNodeList: function (nodeList, type) {

      //selected name
      var selectedName = type == 'select' ? 'selected' : 'checked';

      var values = [], listValue;
      for (var i = 0; i < nodeList.length; i++) {

        //set value
        listValue = nodeList[i].value;

        //Check if selected
        if (type == 'select') {
          if (nodeList[i].attributes.selected != undefined) listValue = '[x] ' + listValue;
        } else {
          if (nodeList[i].attributes.checked != undefined) listValue = '[x] ' + listValue;
        }

        values.push(listValue);
      }
      return values;
    }

  },

  //Insert default set fields
  insertDefaultFieldSet: function () {

    //insert name field
    var nameProps = {
      Id: '',
      Name: '',
      Label: 'Name',
      Placeholder: null,
      Value: '',
      Rows: null,
      IsList: false,
      Mandatory: true
    }
    this.current.items.push(new MWP.Forms.Builder.Fields.Types['text'].create(nameProps));

    //insert email field
    var emailProps = {
      Id: '',
      Name: '',
      Label: 'Email',
      Placeholder: null,
      Value: '',
      Rows: null,
      IsList: false,
      Mandatory: true
    }
    this.current.items.push(new MWP.Forms.Builder.Fields.Types['email'].create(emailProps));
  },

  //reset function
  reset: function () {
    this.current.items = [];
  },

  /* Compile and output complete form */
  compile: function () {

    //Get edit button to include it in the renewed html
    //var editFormBtn = this.current.form.querySelector(".app-form-editable").outerHTML; <- deprecated, edit button is outside form tag

    //private function for hidden fields
    function hide(name, value) {
      return '<textarea class="mwp-fb-hidden-value mwp-fb-success-' + name + '" name="mwp-fb-success-' + name + '">' + value + '</textarea>';
    }

    //Loop through fields and generate public side html 
    var fields = "",
      containsEmail = false,
      items = this.current.items;
    for (var i = 0; i < items.length; i++) {
      if (items[i].getProperties().type.sysType == 'email') containsEmail = true;
      fields += items[i].getClientHtml();
    }

    //Check if form contains email field
    if (!containsEmail) {
      MWP.Error.log('Could not find email field, cancelled compilation', MWP.Helper.getFunctionName('MWP.Forms.Builder.compile'));
      return;
    }

    //Button
    var submitBtn = this.templates.getClient('SUBMIT').replace('{{Button}}', this.current.settings.button);

    //Succes modal content
    var successModal = hide('title', this.current.settings.successTitle) +
            hide('msg', this.current.settings.successMessage) +
            hide('btn', this.current.settings.successButton) +
            hide('redirect', this.current.settings.successRedirect) +
            hide('mail-to', this.current.settings.mailTo) +
            hide('mailchimp-list', this.current.settings.mailChimpList) +
            hide('aweber-list', this.current.settings.aweberList) +
            hide('getresponse-list', this.current.settings.getResponseList);

    //Construct to complete form
    this.current.form.innerHTML =  fields + submitBtn + successModal;

    //Re-initialize parsley
    $(this.current.form).parsley();

    //Close modal
    $('#ajax-modal').modal('hide');

  }
};

MWP.Forms.Builder.Editor = {

  //Load editor
  load: function () {

    ////Load form editor
    //$jq(function ($) {

    //  //Load form builder
    //  $.ajax("/app/modal/web/form-editor").done(
    //      function (data) {
    //        MWP.Forms.Builder.Editor.loadHtmlInModal(data);
    //      }
    //    );

    //})

    var $modal = $('#ajax-modal');
    $modal.load("/app/modal/web/form-editor", '', function () {
      $modal.modal();
      MWP.Forms.Builder.Editor.init();
    });


  },

  //Init editor after load
  init: function () {

    //Get target tbody to load rows in
    var formBody = document.querySelector('#fb-form-editor'),
      tbody = formBody.querySelector('#fb-field-tbody'),
      editRowHtml = MWP.Forms.Builder.templates.getEditor('Row');

    //Clear tbody
    tbody.innerHTML = "";

    //loop through available fields and load the list items
    var items = MWP.Forms.Builder.current.items;
    for (var item in items) {
      var newRow = MWP.Forms.Builder.Fields.Tools.compile.editor(items[item], editRowHtml);
      tbody.insertAdjacentHTML('beforeend', newRow);
    }

    //Set button text + mail to
    formBody.querySelector('#fb_btn_text').value = MWP.Forms.Builder.current.settings.button;
    formBody.querySelector('#fb_mail_to').value = MWP.Forms.Builder.current.settings.mailTo;

    //Set success modal content
    formBody.querySelector('#fb_success_title').value = MWP.Forms.Builder.current.settings.successTitle;
    formBody.querySelector('#fb_success_msg').value = MWP.Forms.Builder.current.settings.successMessage;
    formBody.querySelector('#fb_success_btn').value = MWP.Forms.Builder.current.settings.successButton;

    //Set redirect
    formBody.querySelector('#fb_success_redirect').value = MWP.Forms.Builder.current.settings.successRedirect;

    //Set third party lists
    if (formBody.querySelector('#fb_mailchimp_list')) formBody.querySelector('#fb_mailchimp_list').value = MWP.Forms.Builder.current.settings.mailChimpList;
    if (formBody.querySelector('#fb_aweber_list')) formBody.querySelector('#fb_aweber_list').value = MWP.Forms.Builder.current.settings.aweberList;
    if (formBody.querySelector('#fb_getresponse_list')) formBody.querySelector('#fb_getresponse_list').value = MWP.Forms.Builder.current.settings.getResponseList;

    //Set collapse behaviour
    $('#fb-settings-form-btn').click(function () { MWP.Forms.Builder.Editor.methods.toggle('#fb-settings-form', this) });
    $('#fb-settings-success-btn').click(function () { MWP.Forms.Builder.Editor.methods.toggle('#fb-settings-success', this) });
    $('#fb-settings-redir-btn').click(function () { MWP.Forms.Builder.Editor.methods.toggle('#fb-settings-redir', this) });
    $('#fb-settings-mailchimp-btn').click(function () { MWP.Forms.Builder.Editor.methods.toggle('#fb-settings-mailchimp', this) });
    $('#fb-settings-aweber-btn').click(function () { MWP.Forms.Builder.Editor.methods.toggle('#fb-settings-aweber', this) });
    $('#fb-settings-getresponse-btn').click(function () { MWP.Forms.Builder.Editor.methods.toggle('#fb-settings-getresponse', this) });

    //Add all functionality
    this.addFunctions();

  },

  //Add editor functionality
  addFunctions: function () {

    var formEditor = document.querySelector('#fb-form-editor'),
      fieldList = document.querySelector('#fb-field-list'),
      tbody = fieldList.querySelector('#fb-field-tbody');

    //Add methods to buttons
    var rows = tbody.querySelectorAll('tr');
    for (var i = 0; i < rows.length; i++) {

      //select onchange function
      rows[i].querySelector('.fb-type-select').onchange = this.methods.typeChange;

      //list options
      $jq(function ($) {

        var optBtn = $('.fb-field-options-btn', rows[i]);

        //Init popover function
        optBtn.popover({
          placement: 'bottom',
          html: true,
          template: MWP.Forms.Builder.templates.getEditor('OptionPopover'),
          content: MWP.Forms.Builder.Editor.methods.listOptions(),
          container:'body'
        });

        //Event triggered before popover is shown
        optBtn.on('shown.bs.popover', function () {
          var trigger = $(this),
            popover = $('#' + trigger.attr('aria-describedby')),
            listData = this.dataset.mwpList;

          $('.fb-popover-options', popover).html(listData);
          $('.fb-popover-options-btn', popover).bind('click', { btn: trigger }, function (event) {
            event.data.btn.popover('hide');
          });
        })

        //Event triggered before popover is hiding
        optBtn.on('hide.bs.popover', function () {
          var trigger = $(this),
            popover = $('#' + trigger.attr('aria-describedby')),
            listValues = $('.fb-popover-options', popover).val();

          //Save data
          this.dataset.mwpList = listValues;
        })


      })

      //delete row function
      rows[i].querySelector('.fb-delete-row').onclick = this.methods.deleteRow;

    }

    //Add row function
    fieldList.querySelector('.fb-add-row-btn').onclick = this.methods.addRow;

    //Add save function
    formEditor.querySelector('.fb-form-editor-save').onclick = this.methods.save;

    //Make sortable
    $jq(function ($) {
      $('#fb-field-tbody').sortable({
        handle: ".fb-field-move-v",
        placeholder: "fb-field-placeholder",
        axis: "y"
      });
    });

    //Add validation
    //$(formEditor).validate();

  },


  //Editor view functions

  methods: {

    toggle: function(target, trigger){

      var icon = $('.fa', trigger);
      var el = $(target);
      if (el.is(":visible")) {
        el.slideUp(400);
        icon.removeClass('fa-caret-down').addClass('fa-caret-right');
      } else {
        el.slideDown(400);
        icon.removeClass('fa-caret-right').addClass('fa-caret-down');
      }

    }, 

    deleteRow: function () {
      var row = this.parentNode.parentNode;
      row.parentNode.removeChild(row);
    },

    addRow: function () {

      //Get objects
      var tbody = document.querySelector('#fb-field-tbody'),
        editRow = MWP.Forms.Builder.templates.getEditor('Row'),
        typeList = MWP.Forms.Builder.Fields.Tools.compile.typeList('');

      //Add Clean row
      editRow = editRow.replace('{{type}}', typeList).replace(/{{(.*?)}}/g, '');
      tbody.insertAdjacentHTML('beforeend', editRow);

      //Add all functionality
      MWP.Forms.Builder.Editor.addFunctions();

    },

    //on change of type select
    typeChange: function () {

      var val = this.value,
        optBtn = this.parentNode.parentNode.querySelector('.fb-field-options-btn');
      if (val == 'radioList' || val == 'select' || val == 'selectMulti') {
        optBtn.style.display = 'inline-block';
      } else {
        optBtn.style.display = 'none';
      }

    },

    //Show popover with list
    listOptions: function () {

      var content = MWP.Forms.Builder.templates.getEditor('OptionPopoverContent')
        .replace('{{info1}}', _lang['fb_popover_intro1'])
        .replace('{{info2}}', _lang['fb_popover_intro2'])
        .replace('{{savebutton}}', _lang['ok']);

      return content;

    },

    //Save form
    save: function () {

      //Save if valid
      var formEditor = $('#fb-form-editor');
      if (formEditor.parsley().validate()) MWP.Forms.Builder.Editor.parseForm();

    }

  },

  /* Parse current form */
  parseForm: function () {

    //resety all values
    MWP.Forms.Builder.reset();

    //get all input objects

    var editor = document.querySelector('#fb-form-editor'),
      rows = editor.querySelectorAll('.fb-field-row');
    for (var i = 0; i < rows.length; i++) {

      //get Object
      var currentRow = rows[i];

      //extract info
      var item = this.items.get({ object: currentRow, number: i });
      if (item == null) {
        MWP.Error.log('item is null, init is cancelled', MWP.Helper.getFunctionName('MWP.Forms.Builder.init'));
        return
      };
      MWP.Forms.Builder.current.items.push(item);
    }

    //Get current
    var current = MWP.Forms.Builder.current;

    //Get button text
    current.settings.button = editor.querySelector('#fb_btn_text').value;

    //Get mail to
    current.settings.mailTo = editor.querySelector('#fb_mail_to').value;

    //Get success message
    current.settings.successTitle = editor.querySelector('#fb_success_title').value;
    current.settings.successMessage = editor.querySelector('#fb_success_msg').value;
    current.settings.successButton = editor.querySelector('#fb_success_btn').value;

    //Get redirect
    current.settings.successRedirect = editor.querySelector('#fb_success_redirect').value;

    //Get third party lists
    if (editor.querySelector('#fb_mailchimp_list')) current.settings.mailChimpList = editor.querySelector('#fb_mailchimp_list').value;
    if (editor.querySelector('#fb_aweber_list')) current.settings.aweberList = editor.querySelector('#fb_aweber_list').value;
    if (editor.querySelector('#fb_getresponse_list')) current.settings.getResponseList = editor.querySelector('#fb_getresponse_list').value;

    //Compile
    MWP.Forms.Builder.compile();
  },

  //methods for extraction information from form items
  items: {

    //Get info from item by item type
    get: function (p) {

      //Get variables
      var currentObj = p.object ? p.object : null;
      if (currentObj) {

        //Get input type
        var inputType = currentObj.querySelector('.fb-type-select').value;

        //check if input type exists
        if (typeof MWP.Forms.Builder.Fields.Types[inputType] == 'undefined') {
          MWP.Error.log('inputtype \'' + inputType + '\' is not a legal type', MWP.Helper.getFunctionName('MWP.Forms.Builder.items.get'));
          return;
        }

        //Return parsed item
        return this.parseInput(currentObj, inputType, p.number);


      } else {

        MWP.Error.log('currentObj is null', MWP.Helper.getFunctionName('MWP.Forms.Builder.items.get'));
        return;
      }


    },

    //Parse simple field types
    parseInput: function (inputObject, type, number) {

      //get elements fb-field-name
      var formID = MWP.Forms.Builder.current.form.id,
        nameFld = inputObject.querySelector('.fb-field-name'),
        optionsBtn = inputObject.querySelector('.fb-field-options-btn'),
        mandatoryCb = inputObject.querySelector('.fb-field-mandatory'),
        valueLst, idValue, nameValue,
        isList = false;

      //Check if value should contain list
      if (type == 'radioList' || type == 'select' || type == 'selectMulti') {
        valueLst = optionsBtn.dataset.mwpList.split('\n');
        isList = true;
      }

      //set name and id
      idValue = formID + '-' + number;
      if (type != 'email') {
        nameValue = idValue + '[]';
        if (type == 'selectMulti') nameValue = idValue + '[opts][]';
      } else {
        nameValue = 'email[]';
      }

      properties = {
        Id: idValue,
        Name: nameValue,
        Label: nameFld.value,
        Placeholder: null,
        Value: valueLst,
        Rows: null,
        IsList: isList,
        Mandatory: mandatoryCb.checked
      }

      //Return new input field object
      //var newField = new MWP.Forms.Builder.Fields.Types[type].create(properties);
      //newField.prototype = MWP.Forms.Builder.Fields.prototype;
      return new MWP.Forms.Builder.Fields.Types[type].create(properties);


      return item;

    },

    //Parse node list an return value array
    parseNodeList: function (nodeList) {

      var values = [];
      for (var i = 0; i < nodeList.length; i++) {
        values.push(nodeList[i].value);
      }
      return values;
    }

  }


}

MWP.Forms.Builder.Fields = function (params) {

  var properties = params;
  var htmlTemplateClient;
  this.validator;

  this.getProperties = function () {
    return properties;
  }

  this.getClientHtml = function () {
    return MWP.Forms.Builder.Fields.Tools.compile.client(properties, htmlTemplateClient);
  }

  this.getEditorHtml = function () {
    return MWP.Forms.Builder.Fields.Tools.compile(properties, htmlTemplate);
  }

  //Set html for template for clienbt / end-user view
  this.setHtmlTemplate = function (html) {
    htmlTemplateClient = html;
  }

  //Add property
  this.addProperty = function (key, value) {
    properties[key] = value;
  }
};

//Returns rendered HTML from array objects
//Make sure the key / value sets use exactly the same key name (case sensitive) as used in HTML
//{ KeyName: value } would render <input type="{KeyName}" /> to <input type="value" />
MWP.Forms.Builder.Fields.Tools = {

  compile: {

    editor: function (item, htmlSrc) {

      var blocks = item.getProperties(),
        html = htmlSrc;

      //private get tag from list element
      var getTag = function (tag) {
        var newTag = tag.split(':')[1].replace('}}', '');
        return newTag;
      }

      //private create rendered html list from object array
      //a = replacement tag
      //b = list
      var listOut = function (a, b) {
        //Get list element
        var elRegx = new RegExp('{{' + a + '(.*?)}}+', 'g');
        var el = getTag(html.match(elRegx)[0]);

        //loop through list
        var newEls = "";
        for (var i = 0; i < b.length; i++) {
          newEls += el.replace('[0]', b[i]);
        }
        return newEls;
      }


      //loop through render blocks
      for (var block in blocks) {

        //Get replacement values
        var a = block,
          b = blocks[block];

        //Check for list
        //Object.prototype.toString.call(b) == '[object Array]' && (b = listOut(a, b), a = new RegExp("{" + a + "(.*?)}", "g"));
        if (Object.prototype.toString.call(b) == '[object Array]') {
          b = listOut(a, b);
          a = new RegExp("{{" + a + "(.*?)}}", "g");
        } else if (a == 'type') {
          a = new RegExp("{{" + a + "}}", "g");
          b = this.typeList(b.description);
        }
        else {
          a = new RegExp("{{" + a + "}}", "g");
        }

        html = html.replace(a, b);
      }

      //Check wether to show options button
      if (blocks.IsList) {
        html = html.replace("{{options}}", "display:inline-block");
      } else {
        //remove options from code and remove data attribute
        html = html.replace("{{options}}", "").replace("{{Value:[0]&#xa;}}", "");
        
      }

      //Check if mandatory
      if (blocks.Mandatory) {
        html = html.replace("{{mandatory}}", "checked");
      } else {
        html = html.replace("{{mandatory}}", "");
      }

      //Check for mandatory email field
      if (item.getProperties().type.sysType == 'email') {
        html = html.replace('{{delBtnDisplay}}', 'style="display:none;"')
          .replace('{{mandatoryNoEdit}}', 'disabled');
      } else {
        html = html.replace('{{delBtnDisplay}}', '')
          .replace('{{mandatoryNoEdit}}', '');
      }

      return html;

    },


    //Create a typelist
    typeList: function (selected) {

      var selectList = MWP.Forms.Builder.templates.getEditor("TypeList"),
        outputHtml;

      //Check if email
      if (selected != 'Email') {

        var types = MWP.Forms.Builder.Fields.Types,
          options = '',
          listDisabled = selected == 'Email' ? 'disabled' : '';

        for (var type in types) {
          var description = types[type].description,
            systype = types[type].sysType,
            selectedoption = selected == description ? ' selected' : '';
          if (systype!='email') options += '<option value="' + systype + '"' + selectedoption + '>' + description + '</option>';
        }
        outputHtml = selectList.replace('{{disabled}}', '').replace('{{options}}', options);

      } else {

        outputHtml = selectList.replace('{{disabled}}', 'disabled').replace('{{options}}', '<option value="email" selected>' + _lang['fb_fieldtype_email'] + '</option>');

      }
      return outputHtml;

    },


    //Compile Client HMTL
    client: function (blocks, htmlSrc) {

      //Add hidden textarea for friendly naming of submitted fields
      var name = blocks.Name.replace('[opts][]', '[]');
      var html = '<textarea class=\"mwp-fb-hidden-value\" name=\"' + name + '\">' + blocks.Label + '</textarea>';
      html += htmlSrc;
      html += '<textarea class=\"mwp-fb-hidden-value\" name=\"' + name + '-type\">' + blocks.type.sysType + '</textarea>';

      //private get tag from list element
      var getTag = function (tag) {
        var newTag = tag.split(':')[1].replace('}}', '');
        return newTag;
      }

      //private create rendered html list from object array
      //a = replacement tag
      //b = list
      var listOut = function (a, b, type) {

        //Get list element
        var elRegx = new RegExp('{{' + a + '(.*?)}}+', 'gi'),
          el = getTag(html.match(elRegx)[0]),
          selectedName = type == 'select' ? 'selected' : 'checked';

        //loop through list
        var compiledList = "", listItem = "";
        for (var i = 0; i < b.length; i++) {
          if (b[i] != "") {
            listItem = el.replace(/\[0\]/gi, b[i].replace('[x] ', '').replace('[x]', ''));
            if (b[i].indexOf('[x]') == -1) {
              listItem = listItem.replace('[1]', '');
            } else {
              listItem = listItem.replace('[1]', selectedName);
            }
            compiledList += listItem;
          }
        }
        return compiledList;
      }


      //loop through render blocks
      for (var block in blocks) {

        //Get replacement values
        var a = block,
          b = blocks[block];

        //Check for list
        //Object.prototype.toString.call(b) == '[object Array]' && (b = listOut(a, b), a = new RegExp("{" + a + "(.*?)}", "g"));
        if (Object.prototype.toString.call(b) == '[object Array]') {
          b = listOut(a, b, blocks.type.sysType);
          a = new RegExp("{{" + a + "(.*?)}}", "g");
        } else {
          a = new RegExp("{{" + a + "}}", "g");
        }

        html = html.replace(a, b);
      }

      //Check if mandatory
      if (blocks.Mandatory) {
        html = html.replace('{{mandatoryinput}}', 'required');
      } else {
        html = html.replace('{{mandatoryinput}}', '');
      }

      return html;

    }
  }
};

MWP.Forms.Builder.Fields.Types = {
  text: {
    description: _lang['fb_fieldtype_text'],
    sysType: "text",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('TEXT'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.text);
    }
  },
  name: {
    description: _lang['fb_fieldtype_name'],
    sysType: "name",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('NAME'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.name);
    }
  },
  textArea: {
    description: _lang['fb_fieldtype_textarea'],
    sysType: "textArea",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('TEXTAREA'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.textArea);
    }
  },
  email: {
    description: _lang['fb_fieldtype_email'],
    sysType: "email",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('EMAIL'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.email);
    }
  },
  number: {
    description: _lang['fb_fieldtype_number'],
    sysType: "number",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('NUMBER'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.number);
    }
  },
  tel: {
    description: _lang['fb_fieldtype_phone'],
    sysType: "tel",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('TEL'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.tel);
    }
  },
  url: {
    description: _lang['fb_fieldtype_url'],
    sysType: "url",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('URL'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.url);
    }
  },
  date: {
    description: _lang['fb_fieldtype_date'],
    sysType: "date",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('DATE'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.date);
    }
  },
  time: {
    description: _lang['fb_fieldtype_time'],
    sysType: "time",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('TIME'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.time);
    }
  },
  dateTime: {
    description: _lang['fb_fieldtype_datetime'],
    sysType: "dateTime",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('DATETIME'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.dateTime);
    }
  },
  checkBox: {
    description: _lang['fb_fieldtype_checkbox'],
    sysType: "checkBox",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('CHECKBOX'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.checkBox);
    }
  },
  radioList: {
    description: _lang['fb_fieldtype_options'],
    sysType: "radioList",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('RADIOLIST'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.radioList);
    }
  },
  select: {
    description: _lang['fb_fieldtype_select'],
    sysType: "select",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('SELECT'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.select);
    }
  },
  selectMulti: {
    description: _lang['fb_fieldtype_multiselect'],
    sysType: "selectMulti",
    create: function (properties) {
      MWP.Forms.Builder.Fields.call(this, properties);
      this.setHtmlTemplate(MWP.Forms.Builder.templates.getClient('MULTI'));
      this.addProperty('type', MWP.Forms.Builder.Fields.Types.selectMulti);
    }
  }

}