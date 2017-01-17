<?php

$type = \Request::get('type', 'url'); //'url'; // map | video

?>
<style type="text/css">
.oembedall-container { display: none; }
</style>
<div class="app-ie">
    <div class="modal-dialog" style="width: 720px">
        <div class="modal-content">
            <form id="ie-frame-editor" onsubmit="return false;">
                <input type="hidden" id="ie_id" value="">
                <input type="hidden" name="ie_type" id="ie_type" value="{{ $type }}">
                <input type="hidden" name="ie_src" id="ie_src" value="">

                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 class="modal-title">{{ trans('editor-iframe.iframe_editor') }}</h4>
                </div>
                <div class="modal-body">

                    <div class="panel-group" role="tablist" aria-multiselectable="false" id="ie-accordion">
                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingVideo">
                          <h4 class="panel-title">
                            <a<?php if($type != 'video') echo ' class="collapsed"'; ?> data-toggle="collapse" data-parent="#ie-accordion" href="#collapseVideo" aria-expanded="<?php echo($type == 'video') ? 'true': 'false'; ?>" aria-controls="collapseVideo">
                              {{ trans('editor-iframe.video') }}
                            </a>
                          </h4>
                        </div>
                        <div id="collapseVideo" class="panel-collapse collapse<?php if($type == 'video') echo ' in'; ?>" role="tabpanel" aria-labelledby="headingVideo">
                          <div class="panel-body">

                                <div class="form-group">
                                    <label for="ie_video">{{ trans('editor-iframe.video') }}</label>
                                    <input type="text" class="form-control" id="ie_video" name="ie_video" />
                                    <p class="help-block" id="ie_video_help">{{ trans('editor-iframe.video_help') }}</p>
                                </div>

                                <div style="display:none" id="ie_oembed"></div>

                          </div>
                        </div>
                      </div>
                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingMap">
                          <h4 class="panel-title">
                            <a<?php if($type != 'map') echo ' class="collapsed"'; ?> data-toggle="collapse" data-parent="#ie-accordion" href="#collapseMap" aria-expanded="<?php echo($type == 'map') ? 'true': 'false'; ?>" aria-controls="collapseMap">
                              {{ trans('editor-iframe.map') }}
                            </a>
                          </h4>
                        </div>
                        <div id="collapseMap" class="panel-collapse collapse<?php if($type == 'map') echo ' in'; ?>" role="tabpanel" aria-labelledby="headingMap">
                          <div class="panel-body">

                                <div class="form-group">
                                    <label for="ie_address">{{ trans('editor-iframe.address') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="ie_address" name="ie_address" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button" id="ie_map_find"><i class="fa fa-search"></i> {{ trans('editor-iframe.find') }}</button>
                                        </span>
                                    </div>
                                </div>

                                   <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ie_long">{{ trans('editor-iframe.longitude') }}</label>
                                            <input type="text" class="form-control" id="ie_long" name="ie_long" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ie_lat">{{ trans('editor-iframe.latitude') }}</label>
                                            <input type="text" class="form-control" id="ie_lat" name="ie_lat" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ie_zoom">{{ trans('editor-iframe.zoom') }}</label>
                                            <select class="form-control" id="ie_zoom" name="ie_zoom">
<?php
for($i=10;$i<1200;$i+=100)
{
?>
                                                <option value="{{ $i }}">{{ ($i + 90) / 100 }}</option>
<?php
}
?>
                                            </select>
                                        </div>
                                    </div>
                                   </div>

                                <div class="form-group" style="display:none">
                                    <label for="ie_marker">{{ trans('editor-iframe.marker_text') }}</label>
                                    <textarea class="form-control" rows="3" style="height: 78px; display:none" id="ie_marker" name="ie_marker"></textarea>
                                </div>

                          </div>
                        </div>
                      </div>
                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingUrl">
                          <h4 class="panel-title">
                            <a<?php if($type != 'url') echo ' class="collapsed"'; ?> data-toggle="collapse" data-parent="#ie-accordion" href="#collapseUrl" aria-expanded="<?php echo($type == 'url') ? 'true': 'false'; ?>" aria-controls="collapseUrl">
                              {{ trans('editor-iframe.custom_url') }}
                            </a>
                          </h4>
                        </div>
                        <div id="collapseUrl" class="panel-collapse collapse<?php if($type == 'url') echo ' in'; ?>" role="tabpanel" aria-labelledby="headingUrl">
                          <div class="panel-body">

                                <div class="form-group">
                                    <label for="ie_custom_url">{{ trans('editor-iframe.url') }}</label>
                                    <textarea class="form-control" id="ie_custom_url" name="ie_custom_url" style="height:80px" placeholder="http://"></textarea>
                                </div>

                          </div>
                        </div>
                      </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="ie-frame-editor-save" type="button">{{ trans('global.update') }}</button>
                    <button class="btn" data-dismiss="modal" type="button">{{ trans('global.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function ieLoaded(settings)
{
    $('#ie_type').val(settings.type);
    $('#ie_video').val(settings.video);
    $('#ie_address').val(settings.address);
    $('#ie_long').val(settings.long);
    $('#ie_lat').val(settings.lat);
    $('#ie_zoom').val(settings.zoom);
    $('#ie_marker').val(settings.marker);

    if(typeof settings.zoom === 'undefined' || settings.zoom == null)
    {
        $('#ie_zoom').val(710);
    }

    if(typeof settings.type === 'undefined')
    {
        $('#ie_custom_url').val($('#ie_src').val());
    }
    else
    {
        $('#ie_custom_url').val(settings.custom_url);
    }
}

$('#ie-frame-editor-save').on('click', function() {

    var id = $('#ie_id').val();

    if($('#collapseVideo').hasClass('in'))
    {
        var type = 'video';
        $('#ie_type').val(type);

        var video = $('#ie_video').val();

        if(video != '')
        {
            // oEmbed
            ie_oEmbed(video, function(url) {
                if(url !== false)
                {
                    var form = {
                        type: 'video',
                        video: $('#ie_video').val(),
                        address: $('#ie_address').val(),
                        long: $('#ie_long').val(),
                        lat: $('#ie_lat').val(),
                        zoom: $('#ie_zoom').val(),
                        marker: $('#ie_marker').val(),
                        custom_url: $('#ie_custom_url').val(),
                        src: url
                    };
                    editorSaveFrame(id, form);
                }
            });
            return false;
        }
        else
        {
            $('#ie_video').parent('.form-group').addClass('has-error');
            return false;
        }
    }
    else if($('#collapseMap').hasClass('in'))
    {
        var type = 'map';
        $('#ie_type').val(type);

        var src = ($('#ie_marker').val() == '') ? 'about:blank': $('#ie_marker').val();

        var form = {
            type: 'map',
            video: $('#ie_video').val(),
            address: $('#ie_address').val(),
            long: $('#ie_long').val(),
            lat: $('#ie_lat').val(),
            zoom: $('#ie_zoom').val(),
            marker: $('#ie_marker').val(),
            custom_url: $('#ie_custom_url').val(),
            src: src
        };
        editorSaveFrame(id, form);
    }
    else if($('#collapseUrl').hasClass('in'))
    {
        var type = 'url';
        $('#ie_type').val(type);

        var form = {
            type: 'url',
            video: $('#ie_video').val(),
            address: $('#ie_address').val(),
            long: $('#ie_long').val(),
            lat: $('#ie_lat').val(),
            zoom: $('#ie_zoom').val(),
            marker: $('#ie_marker').val(),
            custom_url: $('#ie_custom_url').val(),
            src: $('#ie_custom_url').val()
        };
        editorSaveFrame(id, form);
    }
});

$('#ie_video').on('keydown keyup change', function() {

    $('#ie_video').parent('.form-group').removeClass('has-success has-error');

    if($(this).val() == '')
    {
        $('#ie_video').parent('.form-group').addClass('has-error');
    }
    else
    {
        $('#ie_video').parent('.form-group').addClass('has-success');
    }
});

var ie_map_size = 20;
var ie_default_zoom = 510;

$('#ie_map_find').on('click', geocodeAddress);
$('#ie_address').on('change', geocodeAddress);

$('#ie_zoom,#ie_lat,#ie_long').on('keydown keyup change', function() {
    var lat = $('#ie_lat').val();
    var lon = $('#ie_long').val();
    var zoom = parseInt($('#ie_zoom').val());
    if(isNaN(zoom)) zoom = ie_default_zoom;

    if(lat != '' && lon != '')
    {
        var lat_top = (lat * zoom + ie_map_size / 2) / zoom;
        var lat_bottom = (lat * zoom - ie_map_size / 2) / zoom;
        var lon_left =  (lon * zoom + ie_map_size / 2) / zoom;
        var lon_right = (lon * zoom - ie_map_size / 2) / zoom;
        var src = 'http://www.openstreetmap.org/export/embed.html?bbox=' + lon_left + '%2C' + lat_top + '%2C' + lon_right + '%2C' + lat_bottom + '&layer=mapnik&marker=' + lat + '%2C' + lon + '';
        $('#ie_marker').val(src);
    }
});

function geocodeAddress()
{
    var address = $('#ie_address').val();
    var zoom = parseInt($('#ie_zoom').val());
    if(isNaN(zoom)) zoom = ie_default_zoom;

    if(address != '')
    {
        blockUI();
        var geocode = 'http://nominatim.openstreetmap.org/search.php';

        $.ajax({
          url: geocode,
          method: 'GET',
          dataType: 'json',
          data: {q : address, format: 'json'},
          timeout: 12000,
          success: function(data) {
                var found = false;

                if(data.length > 0)
                {
                    found = true;
                    var lat = data[0].lat;
                    var lon = data[0].lon;
        
                    var lat_top = (lat * zoom + ie_map_size / 2) / zoom;
                    var lat_bottom = (lat * zoom - ie_map_size / 2) / zoom;
                    var lon_left =  (lon * zoom + ie_map_size / 2) / zoom;
                    var lon_right = (lon * zoom - ie_map_size / 2) / zoom;
                    var src = 'http://www.openstreetmap.org/export/embed.html?bbox=' + lon_left + '%2C' + lat_top + '%2C' + lon_right + '%2C' + lat_bottom + '&layer=mapnik&marker=' + lat + '%2C' + lon + '';
        
                    $('#ie_lat').val(lat);
                    $('#ie_long').val(lon);
        
                    $('#ie_marker').val(src);
                }
        
                var $address_group = $('#ie_address').parent('.input-group').parent('.form-group');
                $address_group.removeClass('has-success has-error');
            
                if(! found)
                {
                    $address_group.addClass('has-error');
                }
                else
                {
                    $address_group.addClass('has-success');
                }
            }
        }).always(function() {
             unblockUI();
        }).error(function(jqXHR, status, errorThrown) {
            alert(status);
        });
    }
}

function ie_oEmbed(url, callback)
{
    var i = 0;
    var src = '';

    $('#ie_oembed').html('<a href="' + url + '">' + url + '</a>');
    $('#ie_oembed').oembed(url, {
        embedMethod: 'fill',
        afterEmbed: function(e) {

            var iframe = $('#ie_oembed').find('iframe');

            if(iframe.length)
            {
                src = iframe.attr('src');
            }
            else
            {
                src = false;
            }
        },
        onProviderNotFound: function(e)
        {
            console.log('onProviderNotFound');
            src = false;
        },
        onError: function(e)
        {
            src = false;
        }
    });

    (function test_function() {
        if (src == '') {
            console.log(i);
            console.log(src);
            i++;
            if(i > 3)
            {
                $('#ie_video').parent('.form-group').removeClass('has-success has-error');
                $('#ie_video').parent('.form-group').addClass('has-error');
                $('#ie_video_help').text("{{ trans('editor-iframe.no_embeddable_url_found') }}");
                callback(false);
            }
            else
            {
                setTimeout(test_function, 200);
            }
        }
        else
        {
            if(src === false)
            {
                $('#ie_video').parent('.form-group').removeClass('has-success has-error');
                $('#ie_video').parent('.form-group').addClass('has-error');
                $('#ie_video_help').text("{{ trans('editor-iframe.no_embeddable_url_found') }}");
            }
            else
            {
                callback(src);
            }
        }
    })();
}
</script>