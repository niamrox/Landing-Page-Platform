<?php
namespace Web\Model;

use Eloquent, DB;

Class Site extends Eloquent
{
  protected $table='sites';

  public function __construct(array $attributes = array()) {

    parent::__construct($attributes);

    static::creating(function($item)
    {
      $item->created_by = \Auth::user()->id;
      $item->updated_by = \Auth::user()->id;
      \App\Controller\LogController::Log(\Auth::user(), trans('global.' . $item->siteType->name), 'created site (' . $item->name . ')', 'site');
    });

    static::updating(function($item)
    {
      $item->updated_by = \Auth::user()->id;
      //\App\Controller\LogController::Log(\Auth::user(), trans('global.' . $item->siteType->name), 'updated site (' . $item->name . ')', 'site');
    });
  }

  public function getAttribute($key)
  {
    $value = parent::getAttribute($key);
    if(($key == 'settings' || $key == 'settings_published') && $value)
    {
      $value = json_decode($value);
    }
    return $value;
  }

  public function setAttribute($key, $value)
  {
    if(($key == 'settings' || $key == 'settings_published') && $value)
    {
      $value = json_encode($value);
    }
    parent::setAttribute($key, $value);
  }

  public function toArray()
  {
    $attributes = parent::toArray();
    if(isset($attributes['settings']))
    {
      $attributes['settings'] = json_decode($attributes['settings']);
    }
    if(isset($attributes['settings_published']))
    {
      $attributes['settings_published'] = json_decode($attributes['settings_published']);
    }
    return $attributes;
  }

  public function scopeDomain($query)
  {
  return (trim($this->domain) != '') ? 'http://' . $this->domain : url('/web/' . $this->local_domain);
  }

  public function user()
  {
    return $this->belongsTo('User');
  }

  public function campaign()
  {
    return $this->belongsTo('Campaign\Model\Campaign');
  }

  public function siteType()
  {
    return $this->belongsTo('Web\Model\SiteType')->orderBy('sort');
  }

  public function sitePages()
  {
    return $this->hasMany('Web\Model\SitePage', 'site_id', 'id');
  }

  public function leads()
  {
    return $this->hasMany('Lead\Model\Lead');
  }

  public function scenarioBoards()
  {
    return $this->belongsToMany('Beacon\Model\ScenarioBoard', 'site_scenario_board');
  }
}