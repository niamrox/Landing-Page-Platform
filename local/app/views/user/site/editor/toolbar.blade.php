<div class="app-ie" id="app-props">
    <div id="app-collapse">
        <i class="fa fa-caret-left"></i>
    </div>
<div class="panel-group" role="tablist" aria-multiselectable="true">
<?php
$tabs = array(
		'headers' => trans('global.headers'),
		'content' => trans('global.content'),
		'forms' => trans('global.forms'),
		'images' => trans('global.images'),
		'maps' => trans('global.maps'),
		'navigation' => trans('global.navigation'),
		'footers' => trans('global.footers')
);

foreach ($tabs as $tab => $tab_title)
{
	$blocks = File::files(public_path() . '/blocks/' . $tab . '/');
?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="h{{ $tab }}">
      <h4 class="panel-title">
        <a class="btn-block collapsed" data-toggle="collapse" data-parent="#accordion" href="#c{{ $tab }}" aria-expanded="true" aria-controls="c{{ $tab }}">
          {{ $tab_title }}
        </a>
      </h4>
    </div>
    <div id="c{{ $tab }}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="h{{ $tab }}">
      <div class="panel-body">
<?php
        foreach($blocks as $block)
        {
            $block_filename = basename($block);
            $filename = pathinfo($block_filename, PATHINFO_FILENAME);
            $path = pathinfo($block, PATHINFO_DIRNAME);
            $ext = pathinfo($block_filename, PATHINFO_EXTENSION);

            if($ext == 'php')
            {
                $thumb = url('/blocks/assets/screenshots/' . $filename . '-250.png');
?>
                <div class="block-element" data-dir="{{ $tab }}" data-block="{{ $filename }}">
                    <img src="{{ $thumb }}">
                </div>
<?php
            }
        }
?>
      </div>
    </div>
  </div>
<?php
}
?>
</div>
</div>
<style type="text/css">
.block-placeholder:after {
    content: "{{ trans('global.drop_here') }}";
}
.block-loading:after {
    content: "{{ trans('global.loading') }}";
}
</style>