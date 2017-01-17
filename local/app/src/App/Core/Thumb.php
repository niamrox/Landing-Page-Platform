<?php
namespace App\Core;

/**
 * Thumbnail class
 *
 */

class Thumb extends \BaseController {

  /**
   * Create thumbnail
   */
  public function getNail() {
    $return = \Input::get('return', 'img');
    $img = \Input::get('img', '');
    $target = \Input::get('target', ''); // If empty elFinder .tmb path is used
    $w = \Input::get('w', 0);
    $h = \Input::get('h', 0);
    $t = \Input::get('t', 'crop');

    $img_part = pathinfo($img);

    $s3 = (\Config::get('s3.active', false)) ? true : false;
    $s3_root = (\Config::get('s3.active', false)) ? \Config::get('s3.url') . '/' . \Config::get('s3.media_root_bucket') : '';

    $root = ($s3) ? $s3_root : substr(url('/'), strpos(url('/'), \Request::server('HTTP_HOST')));
    $abs_path_prefix = ($s3) ? $s3_root : str_replace(\Request::server('HTTP_HOST'), '', $root);

    $img_part['dirname'] = ($s3) ? '/uploads/user' . str_replace($s3_root, '', $img_part['dirname']) : str_replace($abs_path_prefix, '', $img_part['dirname']);
    $img = ($s3) ? $img : public_path() . str_replace($abs_path_prefix, '', $img);

    if($target == '')
    {
      $target = $img_part['dirname'] . '/.tmb/' . $img_part['filename'] . '-' . $w . 'x' . $h . '-' . $t . '.' . $img_part['extension'];
    }

    if($w == 0) $w = NULL;
    if($h == 0) $h = NULL;

    if(! \File::exists(public_path() . $target))
    {
      // Create dir
      if(! \File::isDirectory(public_path() . $img_part['dirname'] . '/.tmb/'))
      {
        \File::makeDirectory(public_path() . $img_part['dirname'] . '/.tmb/');
      }

      if ($t == 'crop')
      {
        $img = \Image::make($img)->fit($w, $h, function ($constraint) use($t) {
          //$constraint->aspectRatio();
        })->save(public_path() . $target);
      }
      elseif($t == 'fit')
      {
        $img = \Image::make($img)->crop($w, $h, function ($constraint) use($t) {
          //$constraint->aspectRatio();
        })->save(public_path() . $target);
      }
      elseif($t == 'resize')
      {
        $img = \Image::make($img)->resize($w, $h, function ($constraint) use($t) {
          $constraint->aspectRatio();
        })->save(public_path() . $target);
      }
      elseif($t == 'resize-ratio')
      {
        $img = \Image::make($img)->resize($w, $h, function ($constraint) use($t) {
          $constraint->aspectRatio();
        })->save(public_path() . $target);
      }
    }

    if($return == 'img') {
      $type = 'image/' . $img_part['extension'];

      \Response::make('', 200, 
        array(
          'Content-Type' => $type,
          'Content-Transfer-Encoding' => 'binary',
          'Content-Disposition' => 'inline',
          'Expires' => 0,
          'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
          'Pragma' => 'public'
        )
      );

      readfile(public_path() . $target);
    } elseif($return == 'path') {
      return $target;
    }
  }

  /**
   * Create thumbnail
   * \App\Core\Thumb::nail('/path/to/img', '/path/to/thumb/dir', 64, 64, 'crop');
   */
  public static function nail($img, $dir, $w = 0, $h = 0, $t = 'crop') {
    $img_part = pathinfo($img);

    $s3 = (\Config::get('s3.active', false)) ? true : false;
    $s3_root = (\Config::get('s3.active', false)) ? \Config::get('s3.url') . '/' . \Config::get('s3.media_root_bucket') : '';

    $root = ($s3) ? $s3_root : substr(url('/'), strpos(url('/'), \URL::current()));
    $abs_path_prefix = ($s3) ? $s3_root : $root;

    $img_part['dirname'] = ($s3) ? '/uploads/user' . str_replace($s3_root, '', $img_part['dirname']) : str_replace($abs_path_prefix, '', $img_part['dirname']);
    $img = ($s3) ? $img : public_path() . str_replace($abs_path_prefix, '', $img);

    $target = $dir . '/' . $img_part['filename'] . '-' . $w . 'x' . $h . '-' . $t . '.' . $img_part['extension'];

    if($w == 0) $w = NULL;
    if($h == 0) $h = NULL;

    if(! \File::exists(public_path() . $target))
    {
      // Create dir
      if(! \File::isDirectory(public_path() . $dir))
      {
        \File::makeDirectory(public_path() . $dir);
      }

      if ($t == 'crop')
      {
        $img = \Image::make($img)->fit($w, $h, function ($constraint) use($t) {
          //$constraint->aspectRatio();
        })->save(public_path() . $target);
      }
      elseif($t == 'fit')
      {
        $img = \Image::make($img)->crop($w, $h, function ($constraint) use($t) {
          //$constraint->aspectRatio();
        })->save(public_path() . $target);
      }
      elseif($t == 'resize')
      {
        $img = \Image::make($img)->resize($w, $h, function ($constraint) use($t) {
          $constraint->aspectRatio();
        })->save(public_path() . $target);
      }
      elseif($t == 'resize-ratio')
      {
        $img = \Image::make($img)->resize($w, $h, function ($constraint) use($t) {
          $constraint->aspectRatio();
        })->save(public_path() . $target);
      }
    }

    return $target;
  }

  /**
   * Make template thumbnail, \App\Core\Thumb::template($template, 0);
   */
  public static function template($template, $empty_cache = 0)
  {
    $url = url('/web/view/' . $template);
    $src = public_path() . '/templates/' . $template . '/assets/img/preview.png';
    $thumb_url = url('/templates/' . $template . '/assets/img/preview.png');

    if(! \File::exists($src) || $empty_cache == 1)
    {
      \App\Core\Screenshot::responsive($url, $src, $empty_cache, 2000);
    }
    return $thumb_url;
  }
}