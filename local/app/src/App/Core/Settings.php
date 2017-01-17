<?php
namespace App\Core;

class Settings {

    /**
     * Get setting
     */
    public static function get($name, $default = NULL, $user_id = 0)
	{
		$reseller = \App\Controller\ResellerController::get();

		$return = \Cache::rememberForever('settings_' . $name . '_' . $reseller->id . '_' . $user_id, function() use($name, $default, $user_id, $reseller)
		{
			$oSetting = \App\Model\Setting::where('name', $name)->where('reseller_id', $reseller->id)->where('user_id', $user_id)->first();

			if(! empty($oSetting))
			{
				return $oSetting->value;
			}
			elseif($default != NULL)
			{
				return $default;
			}
			else
			{
				return NULL;
			}
		});

		return $return;
    }

    /**
     * Set setting
     */
    public static function set($name, $value, $user_id = 0)
	{
		$reseller = \App\Controller\ResellerController::get();

		\Cache::forget('settings_' . $name . '_' . $reseller->id . '_' . $user_id);

		$oSetting = \App\Model\Setting::where('name', $name)->where('reseller_id', $reseller->id)->where('user_id', $user_id);

		if($oSetting->exists()) 
		{
			if($value == NULL)
			{
				$oSetting->delete();
			}
			else
			{
				$oSetting->update(array(
					'value' =>$value
				));
			}
		}
		elseif($value != NULL)
		{
			$oSetting = new \App\Model\Setting;

			$oSetting->reseller_id = $reseller->id;
			$oSetting->user_id = $user_id;
			$oSetting->name = $name;
			$oSetting->value = $value;
			$oSetting->save();
		}
		return true;
    }

    /**
     * Save json encoded string to database and check for existing data
	 * \App\Core\Settings::json($array, $column->settings);
     */
    public static function json($data, $column = '')
	{
		if ($column != '')
		{
			$existing_data = json_decode($column, true);
			$new_data = $existing_data;

			foreach ($data as $key => $val)
			{
				$new_data[$key] = $val;
			}
		}
		else
		{
			$new_data = $data;
		}

		return json_encode($new_data);
    }
}