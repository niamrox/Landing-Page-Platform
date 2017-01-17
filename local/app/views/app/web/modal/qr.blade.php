<form name="qrForm">
<div class="modal-dialog">
    <div class="modal-dialog" style="width:511px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans('global.qr_code') }}</h4>
            </div>
            <div class="modal-body" style="padding-bottom:0">

                <div id="qr" style="width:100%"></div>

                <div class="form-group">
                    <label for="url_area">{{ trans('global.url') }}</label>
                    <textarea id="url_area" name="url" class="form-control" maxlength="2953">http://</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
            </div>
        </div>
    </div>
</div>
</form>

<script>
var id = "#{{ Request::get('id', '') }}";
var text = parent.$(id).attr('data-text');
$('#url_area').val(decodeURIComponent(text));

var draw_qrcode = function(text, typeNumber, errorCorrectLevel) {
	document.write(create_qrcode(text, typeNumber, errorCorrectLevel) );
};

var create_qrcode = function(text, typeNumber, errorCorrectLevel, table) {

	var qr = qrcode(typeNumber || 6, errorCorrectLevel || 'M');
	qr.addData(text);
	qr.make();

	return qr.createImgTag(11, 10);
};

var update_qrcode = function() {
  var form = document.forms['qrForm'];
  var text = form.elements['url_area'].value.
    replace(/^[\s\u3000]+|[\s\u3000]+$/g, '');
  var t = 6;
  var e = 'M';
	document.getElementById('qr').innerHTML = create_qrcode(text, t, e);
};

$('#size,#version,#level,#url_area').on('change keyup keydown', update_qrcode);

/*
$(window).resize( $.debounce( 200, update_qrcode) );
*/
update_qrcode();
</script>