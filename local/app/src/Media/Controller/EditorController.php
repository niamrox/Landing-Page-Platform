<?php
namespace Media\Controller;

/*
|--------------------------------------------------------------------------
| Editor controller
|--------------------------------------------------------------------------
|
| WYISWYG editor related logic
|
*/

class EditorController extends \BaseController {

    /**
	 * Construct
     */
    public function __construct()
    {
		if(\Auth::check())
		{
			$this->parent_user_id = (\Auth::user()->parent_id == NULL) ? \Auth::user()->id : \Auth::user()->parent_id;
		}
		else
		{
			$this->parent_user_id = NULL;
		}
    }

    /**
     * Show editor in lightbox
     */
    public function getEditor()
    {
/*        $locale = \Config::get('app.locale');
		$locale = str_replace('pt', 'pt_BR', $locale);

        if (!file_exists(public_path() . "/$dir/js/i18n/elfinder.$locale.js"))
        {
            $locale = false;
        }
*/
        return \View::make('app.media.editor', compact('locale'));
    }

    /**
     * TinyMCE templates
     */
    public function getTemplates()
    {
		$templates = 'templates = [ ';


		$templates = ']';


		return "templates = [
			{title: 'Content card', description: 'A content card for mobile layouts.', url: '" . url('/app/editor/template/content-card') . "'} 
		]";
    }

    /**
     * TinyMCE template
     */
    public function getTemplate($tpl)
    {
		if ($tpl == 'content-card')
		{
			$content = \View::make('app.media.templates.' . $tpl);
			return (string) $content;
		}
    }

}