<?php
namespace App\Core;

use Cache;

class Geo {

  /**
   * Geocode address
   */
  public static function geocode($address) {
    try {
      $geocode = Cache::rememberForever('geo_' . md5($address), function() use($address)
      {
        $ssl = (\Request::server('HTTP_X_FORWARDED_PROTO') == NULL || ! \Request::secure()) ? false: true;
        $geocoder = new \Geocoder\Geocoder;
        $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter;
        $dumper   = new \Geocoder\Dumper\GeoJsonDumper;
        $chain  = new \Geocoder\Provider\ChainProvider(array(
          new \Geocoder\Provider\GeoPluginProvider($adapter),
          new \Geocoder\Provider\FreeGeoIpProvider($adapter),
          new \Geocoder\Provider\IpGeoBaseProvider($adapter),
          new \Geocoder\Provider\HostIpProvider($adapter),
          new \Geocoder\Provider\OIORestProvider($adapter),
          new \Geocoder\Provider\GoogleMapsProvider($adapter, NULL, NULL, $ssl),
        ));
        $geocoder->registerProvider($chain);
        $geocoded = $geocoder->geocode($address);

        return $geocoded;
      });

      return $geocode;

    } catch (\Exception $e) {
      return array('error' => true, 'msg' => $e->getMessage());
    }
  }

  /**
   * Reverse address from coordinates
   */
  public static function reverse($lat, $lng) {
    try {
      $geocode = Cache::rememberForever('geo_' . md5($lat . '-' . $lng), function() use($lat, $lng)
      {
        $ssl = (\Request::server('HTTP_X_FORWARDED_PROTO') == NULL || ! \Request::secure()) ? false: true;
        $geocoder = new \Geocoder\Geocoder;
        $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter;
        $dumper   = new \Geocoder\Dumper\GeoJsonDumper;
        $chain  = new \Geocoder\Provider\ChainProvider(array(
          new \Geocoder\Provider\GoogleMapsProvider($adapter, NULL, NULL, $ssl),
          new \Geocoder\Provider\GeoPluginProvider($adapter),
          new \Geocoder\Provider\FreeGeoIpProvider($adapter),
          new \Geocoder\Provider\IpGeoBaseProvider($adapter),
          new \Geocoder\Provider\HostIpProvider($adapter),
          new \Geocoder\Provider\OIORestProvider($adapter),
        ));
        $geocoder->registerProvider($chain);
        $geocoded = $geocoder->reverse($lat, $lng);

        return $geocoded;
      });
      return $geocode;
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   * IP to address + coordinates
   * $geo = \App\Core\Geo::ip2address();
   */
  public static function ip2address($ip = NULL) {
    if ($ip == NULL) $ip = \App\Core\IP::address();

    try {
      $geocode = Geo::geocode($ip);
    } catch (\Exception $e) {
      //return $e->getMessage();
      $geocode['latitude'] = 0;
      $geocode['longitude'] = 0;
      $geocode['city'] = 'Unkown';
      $geocode['region'] = 'Unkown';
      $geocode['country'] = 'Unkown';
      $geocode['countryCode'] = 'Unkown';
    }
    return $geocode;
  }

  /**
   * Calculate distance between 2 lat / lng coordinates
   */
  public static function distance($lat1, $lon1, $lat2, $lon2, $unit) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);
  
    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
      return $miles;
    }
  }
}