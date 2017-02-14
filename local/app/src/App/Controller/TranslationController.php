<?php
namespace App\Controller;

/*
|--------------------------------------------------------------------------
| Translation controller
|--------------------------------------------------------------------------
|
| Import and export translation files
|
*/

class TranslationController extends \BaseController {

    /**
     * Export all translation variables to Excel
     */
    public function getExport($locale = NULL)
    {
		if ($locale == NULL) $locale = \Config::get('app.locale');

		// General translations
		$exclude = array('countries.php', 'currencies.php'/*, 'i18n.php'*/, 'languages.php', 'validation.php', 'pagination.php', 'timezones.php');

		$languages_dir = public_path() . '/local/app/lang/' . $locale;

		if (\File::isDirectory($languages_dir))
		{
			foreach (glob($languages_dir . "/*.php") as $file)
			{
				$filename = basename($file);
				if (! in_array($filename, $exclude))
				{
					$vars[$filename] = include $file;
				}
			}
		}

		// Widget translations
		$exclude = array('_boilerplate');

		$widgets_dir = public_path() . '/widgets/';
		$widgets = \File::directories($widgets_dir);

		$widget_config = array();

		foreach($widgets as $widget)
		{
			$filename = basename($widget);
			if (! in_array($filename, $exclude))
			{
				$vars[$filename] = include $widget . '/lang/' . $locale . '/global.php';
			}
		}

		//$vars = array_flatten($vars);
		$excel_export = array();
		foreach ($vars as $filename => $translations)
		{
			foreach ($translations as $var => $translation)
			{
				if (is_array($translation))
				{
					foreach ($translation as $sub1 => $sub_translation1)
					{
						if (is_array($sub_translation1))
						{
							foreach ($sub_translation1 as $sub2 => $sub_translation2)
							{
								$excel_export[] = array(
									'file' => $filename,
									'sub1' => $var,
									'sub2' => $sub1,
									'sub3' => $sub2,
									'text' => $sub_translation2,
									'translation' => ''
								);
							}
						}
						else
						{
							$excel_export[] = array(
								'file' => $filename,
								'sub1' => $var,
								'sub2' => $sub1,
								'sub3' => '',
								'text' => $sub_translation1,
								'translation' => ''
							);
							
						}
					}
				}
				else
				{
					$excel_export[] = array(
						'file' => $filename,
						'sub1' => $var,
						'sub2' => '',
						'sub3' => '',
						'text' => $translation,
						'translation' => ''
					);
				}
			}
		}

		return \Excel::create('LandingPagePlatform-Translation-' . date('Y-m-d'), function($excel) use($excel_export){
			$excel->sheet('Translations', function($sheet) use($excel_export) {
				$sheet->fromArray($excel_export, null, 'A1', false, true);
			});
		})->download('xlsx');
    }

    /**
     * Import Excel file with translation 
	 * in the format of the exported translations (getExport)
     */
    public function getImport($locale = NULL)
    {
		if ($locale == NULL) die();

		$import_file = public_path() . '/translation.xlsx';
		$translation_dir_root = public_path() . '/translations';

		// Clean dirs
		\File::cleanDirectory($translation_dir_root . '/local');
		\File::cleanDirectory($translation_dir_root . '/widgets');

		$translation = \Excel::load($import_file, function($reader) use($locale) {

			$sheet = $reader->toArray();

			// Create lang dir
			$translation_dir = public_path() . '/translations/local/app/lang/' . $locale . '/';
			if (! \File::isDirectory($translation_dir)) 
			{
				\File::makeDirectory($translation_dir, 0777, true);
			}
			else
			{
				\File::cleanDirectory($translation_dir);
			}
	
			$old_file = '';
			$old_sub1 = '';
			$old_sub2 = '';
			$old_sub3 = '';

			foreach ($sheet as $row)
			{
			//$reader->each(function($row) use($translation_dir, $locale, $old_file) {

				// It's a file
				if (ends_with($row['file'], '.php'))
				{
					$file = $translation_dir . $row['file'];

					// Start of new file
					if (! \File::isFile($file))
					{
						\File::put($file, '<?php' . PHP_EOL . PHP_EOL . 'return array(');
					}

					$file_append = '';

					// End of array
					if ($old_file == $row['file'] && $row['sub2'] != $old_sub2 && $old_sub3 != '')
					{
						$file_append .= PHP_EOL . "\t\t" . '),';
					}

					if ($old_file == $row['file'] && $row['sub1'] != $old_sub1 && $old_sub2 != '' && $old_sub3 == '')
					{
						//$file_append .= PHP_EOL . "\t\t" . '),';
					}

					if ($old_file == $row['file'] && $row['sub1'] != $old_sub1 && $old_sub2 != '')
					{
						$file_append .= PHP_EOL . "\t" . '),';
					}

					// Check recursiveness
					if ($row['sub1'] != '' && $row['sub2'] != '' && $row['sub1'] != $old_sub1)
					{
						// Start of new dimension
						$file_append .= PHP_EOL . "\t" . '"' . $row['sub1'] . '" => array(';
					}
					
					if ($row['sub2'] != '' && $row['sub3'] == '' && $row['sub1'] != $old_sub1 && $row['sub2'] != $old_sub2)
					{
						// Start of new dimension
						//$file_append .= PHP_EOL . "\t\t" . '"' . $row['sub2'] . '" => array(';
					}
					
					if ($row['sub2'] != '' && $row['sub3'] != '' && $row['sub2'] != $old_sub2)
					{
						$file_append .= PHP_EOL . "\t\t" . '"' . $row['sub2'] . '" => array(';
					}


					if ($row['sub1'] != '' && $row['sub2'] == '')
					{
						$file_append .= PHP_EOL . "\t" . '"' . $row['sub1'] . '" => "' . str_replace('"', '\"', $row['translation']) . '",';
					}

					if ($row['sub2'] != '' && $row['sub3'] == '')
					{
						$file_append .= PHP_EOL . "\t\t" . '"' . $row['sub2'] . '" => "' . str_replace('"', '\"', $row['translation']) . '",';
					}

					if ($row['sub2'] != '' && $row['sub3'] != '')
					{
						$file_append .= PHP_EOL . "\t\t\t" . '"' . $row['sub3'] . '" => "' . str_replace('"', '\"', $row['translation']) . '",';
					}

					\File::append($file, $file_append);

					// End of file
					if ($old_file != '' && $old_file != $row['file'])
					{
						$file_append = '';

						if ($old_sub3 != '')
						{
							$file_append .= PHP_EOL . "\t\t" . ')';
						}

						if ($old_sub2 != '')
						{
							$file_append .= PHP_EOL . "\t" . ')';
						}

						$file_append .= PHP_EOL . ');';
						\File::append($translation_dir . $old_file, $file_append);
					}
				}
				else
				{
					// It's a widget
					$first = false;
					$widget_dir = public_path() . '/translations/widgets/' . $row['file'] . '/lang/' . $locale . '/';
					$file = $widget_dir . 'global.php';

					if (! \File::isDirectory($widget_dir)) 
					{
						\File::makeDirectory($widget_dir, 0777, true);
					}
					else
					{
						$first = true;
						//\File::cleanDirectory($widget_dir);
					}

					// Start of new file
					if (! \File::isFile($file))
					{
						\File::put($file, '<?php' . PHP_EOL . PHP_EOL . 'return array(');
					}

					\File::append($file, PHP_EOL . "\t" . '"' . $row['sub1'] . '" => "' . str_replace('"', '\"', $row['translation']) . '",');

					$old_widget_file = public_path() . '/translations/widgets/' . $old_file . '/lang/' . $locale . '/global.php';

					// End of file
					if ($old_file != '' && $old_file != $row['file'])
					{
						if (ends_with($old_file, '.php')) 
						{
							$file_append = '';

							if ($old_sub3 != '')
							{
								$file_append .= PHP_EOL . "\t\t" . ')';
							}

							if ($old_sub2 != '')
							{
								$file_append .= PHP_EOL . "\t" . ')';
							}

							$file_append .= PHP_EOL . ');';
							\File::append($translation_dir . $old_file, $file_append);
						}
						else
						{
							if (! $first)
							{
								\File::append($old_widget_file, PHP_EOL . ');');
							}
						}
					}
				}

				$old_file = $row['file'];
				$old_sub1 = $row['sub1'];
				$old_sub2 = $row['sub2'];
				$old_sub3 = $row['sub3'];
			}

			// Close last file
			\File::append($old_widget_file, PHP_EOL . ');');
		});
		echo 'ready';
	}
}