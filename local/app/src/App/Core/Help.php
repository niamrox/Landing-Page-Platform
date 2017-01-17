<?php
namespace App\Core;

/**
 * Help class
 *
 *
 * @package		Core
 * @category	Base
 * @version		0.01
 * @since		2014-09-18
 * @author		Sem Kokhuis
 */

class Help extends \BaseController {

    /**
     * Show help text in popover, AJAX loaded
	 * echo \App\Core\Help::popover('incorrect_time', 'top');
     */
    public static function popover($item, $placement = 'top')
    {
        // ng-click="setPopoverContent(\'' . url('/app/help/' . $item) . '\')" 
        // popover="{{helpPopover}}"
        return '<span class="help-popover" popover="' . Help::getHelp($item) . '" popover-trigger="mouseenter" popover-placement="' . $placement . '" popover-append-to-body="true"><i class="fa fa-question-circle"></i></span>';
    }

    /**
     * Show help text in popover
     */
    public static function getHelp($item)
    {
        switch($item)
        {
            case 'role': 
                return '<p>' . trans('global.help_role1') . '</p><p>' . trans('global.help_role2') . '</p>'; 
                break;
            case 'role_owner': 
                return '<p>' . trans('global.help_role_owner1') . '</p><p>' . trans('global.help_role_owner2') . '</p>'; 
                break;
			case 'incorrect_time': return trans('global.help_incorrect_time'); break;
        }
    }
}