<?php
namespace App\Controller;

/*
|--------------------------------------------------------------------------
| Installation controller
|--------------------------------------------------------------------------
|
| Installation related logic
|
*/

class InstallationController extends \BaseController {

  /**
   * Construct
   */
  public function __construct()
  {
  }

  /**
   * Check installation
   */
  public static function check()
  {
    /**
     * Database checks
     */

    \App::error(function(\PDOException $exception)
    {
      if($exception->getCode() == '3D000' || $exception->getCode() == '42S02' || $exception->getCode() == 1045 || $exception->getCode() == 1044)
      {
        $step = \Input::get('step', 1);

        if ($step == 1) {
          return \Response::view('installation.check');
        } elseif ($step == 2) {
          return \Response::view('installation.permissions');
        } elseif ($step == 3) {
          return \Response::view('installation.configuration');
        } elseif ($step == 4) {

          $msgs = array();

          // Test database
          $mysql_host = \Request::get('mysql_host', '');
          $mysql_database = \Request::get('mysql_database', '');
          $mysql_username = \Request::get('mysql_username', '');
          $mysql_password = \Request::get('mysql_password', '');

          $mysql = array(
              'host'      => $mysql_host,
              'driver'    => 'mysql',
              'database'  => $mysql_database,
              'username'  => $mysql_username,
              'password'  => $mysql_password,
              'charset'   => 'utf8',
              'collation' => 'utf8_unicode_ci',
              'prefix'    => '',
          );

          \Config::set('database.connections.test', $mysql);

          try {
            if (\DB::connection('test')->getDatabaseName()) {
              //$msgs[] = "Yes! successfully connected to the DB: " . \DB::connection()->getDatabaseName();
            }
          } catch (\Exception $e) {
            $msgs[] = 'MySQL error: ' .  $e->getMessage();
          }

          // Test mail
          $email_driver = \Request::get('email_driver', '');
          $smtp_host = \Request::get('smtp_host', '');
          $smtp_encryption = \Request::get('smtp_encryption', '');
          $smtp_port = \Request::get('smtp_port', '');
          $smtp_username = \Request::get('smtp_username', '');
          $smtp_password = \Request::get('smtp_password', '');
          $email_from_name = \Request::get('email_from_name', '');
          $email_from_address = \Request::get('email_from_address', '');

          \Config::set('mail.driver', $email_driver);
          \Config::set('mail.host', $smtp_host);
          \Config::set('mail.port', $smtp_port);
          \Config::set('mail.from.address', $email_from_address);
          \Config::set('mail.from.name', $email_from_name);
          \Config::set('mail.encryption', $smtp_encryption);
          \Config::set('mail.username', $smtp_username);
          \Config::set('mail.password', $smtp_password);

          try {
            //\Mail::pretend();
            \Mail::send('emails.web.lead', array('body' => 'This is a test mail sent from your ' . trans('global.app_title') . ' installation. If you receive this mail, the configuration is working!'), function($message) use($email_from_name, $email_from_address)
            {
                $message->to($email_from_address, $email_from_name)->subject('Test Mail ' . trans('global.app_title') . ' Installation');
            });

          } catch (\Exception $e) {
            $msgs[] = 'Email error: ' .  $e->getMessage();
          }

          if (count($msgs) > 0) {
            return \Redirect::back()->withErrors($msgs)->withInput(\Input::all());
          } else {

            // Copy files, rewrite config
            $sourceDir = base_path() . '/app/config/production-config';
            $destinationDir = base_path() . '/app/config/production';

            $success = \File::copyDirectory($sourceDir, $destinationDir);

            if ($success) {
              // Rewrite config files
              $cfg_file = base_path() . '/app/config/production/database.php';

              //$contents = \File::get($cfg_file);
              $contents = "<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => 'mysql',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => __DIR__.'/../database/production.sqlite',
			'prefix'   => '',
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => '" . $mysql_host . "',
			'database'  => '" . $mysql_database . "',
			'username'  => '" . $mysql_username . "',
			'password'  => '" . $mysql_password . "',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),

		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'     => 'localhost',
			'database' => 'forge',
			'username' => 'forge',
			'password' => '',
			'charset'  => 'utf8',
			'prefix'   => '',
			'schema'   => 'public',
		),

		'sqlsrv' => array(
			'driver'   => 'sqlsrv',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => '',
			'prefix'   => '',
		),

	),

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
";

              \File::put($cfg_file, $contents);

              $cfg_file = base_path() . '/app/config/production/mail.php';

              $contents = "<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Mail Driver
	|--------------------------------------------------------------------------
	|
	| Laravel supports both SMTP and PHP's \"mail\" function as drivers for the
	| sending of e-mail. You may specify which one you're using throughout
	| your application here. By default, Laravel is setup for SMTP mail.
	|
	| Supported: \"smtp\", \"mail\", \"sendmail\"
	|
	*/

	'driver' => '" . $email_driver . "',

	/*
	|--------------------------------------------------------------------------
	| SMTP Host Address
	|--------------------------------------------------------------------------
	|
	| Here you may provide the host address of the SMTP server used by your
	| applications. A default option is provided that is compatible with
	| the Postmark mail service, which will provide reliable delivery.
	|
	*/

	'host' => '" . $smtp_host . "',

	/*
	|--------------------------------------------------------------------------
	| SMTP Host Port
	|--------------------------------------------------------------------------
	|
	| This is the SMTP port used by your application to delivery e-mails to
	| users of your application. Like the host we have set this value to
	| stay compatible with the Postmark e-mail application by default.
	|
	*/

	'port' => " . $smtp_port . ", // or 587 in case of tls 

	/*
	|--------------------------------------------------------------------------
	| Global \"From\" Address
	|--------------------------------------------------------------------------
	|
	| You may wish for all e-mails sent by your application to be sent from
	| the same address. Here, you may specify a name and address that is
	| used globally for all e-mails that are sent by your application.
	|
	*/

	'from' => array('address' => '" . $email_from_address . "', 'name' => '" . $email_from_name . "'),

	/*
	|--------------------------------------------------------------------------
	| E-Mail Encryption Protocol
	|--------------------------------------------------------------------------
	|
	| Here you may specify the encryption protocol that should be used when
	| the application send e-mail messages. A sensible default using the
	| transport layer security protocol should provide great security.
	|
	*/

	'encryption' => '" . $smtp_encryption . "', // ssl | tls

	/*
	|--------------------------------------------------------------------------
	| SMTP Server Username
	|--------------------------------------------------------------------------
	|
	| If your SMTP server requires a username for authentication, you should
	| set it here. This will get used to authenticate with your server on
	| connection. You may also set the \"password\" value below this one.
	|
	*/

	'username' => '" . $smtp_username . "',

	/*
	|--------------------------------------------------------------------------
	| SMTP Server Password
	|--------------------------------------------------------------------------
	|
	| Here you may set the password required by your SMTP server to send out
	| messages from your application. This will be given to the server on
	| connection so that the application will be able to send messages.
	|
	*/

	'password' => '" . $smtp_password . "',

	/*
	|--------------------------------------------------------------------------
	| Sendmail System Path
	|--------------------------------------------------------------------------
	|
	| When using the \"sendmail\" driver to send e-mails, we will need to know
	| the path to where Sendmail lives on this server. A default path has
	| been provided here, which will work well on most of your systems.
	|
	*/

	'sendmail' => '/usr/sbin/sendmail -bs',

	/*
	|--------------------------------------------------------------------------
	| Mail \"Pretend\"
	|--------------------------------------------------------------------------
	|
	| When this option is enabled, e-mail will not actually be sent over the
	| web and will instead be written to your application's logs files so
	| you may inspect the message. This is great for local development.
	|
	*/

	'pretend' => false,

);";

              \File::put($cfg_file, $contents);

              $cfg_file = base_path() . '/app/config/production/app.php';

              $contents = "<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => true,

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the Illuminate encrypter service and should be set
	| to a random, 32 character string, otherwise these encrypted strings
	| will not be safe. Please do this before deploying an application!
	|
	*/

	'key' => '" . str_random(32) . "',

);
";
              \File::put($cfg_file, $contents);

              // Create db tabes
              //InstallationController::migrate();

            } else {
              $msgs[] = 'Error copying ' . $sourceDir . ' to ' . $destinationDir . '<br><br>Check directory permissions and run installer again.';

            }

            if (count($msgs) > 0) {
              return \Redirect::back()->withErrors($msgs)->withInput(\Input::all());
            } else {
              return \Redirect::to(url('/'));
            }
          }
        }

        die();
      }
    });

    if(! \Schema::hasTable('users'))
    {
      // Database exists, no tables found
      InstallationController::migrate();
    }/*
    elseif (! \Schema::hasTable('public_users'))
    {
      // Public users upgrade
      \Artisan::call('migrate', ['--path' => "app/database/migrations", '--force' => true]);
    }*/
  }

  /**
   * Install database and seed
   */
  public static function migrate()
  {
    //set_time_limit(0);

    \Artisan::call('migrate', ['--path' => "app/database/migrations", '--force' => true]);
    \Artisan::call('db:seed', ['--force' => true]);
  }

  /**
   * Remove all tables
   */
  public static function clean()
  {
    /**
     * Empty all user directories
     */
    $gitignore = '*
!.gitignore';

    $dirs = array(
      '/app/storage/logs/',
      '/app/storage/userdata/',
      '/app/storage/views/',
      '/../uploads/attachments/',
      '/../uploads/user/'
    );

    foreach($dirs as $dir)
    {
      $full_dir = base_path() . $dir;
      $success = \File::deleteDirectory($full_dir, true);
      if($success)
      {
        // Deploy .gitignore
        \File::put($full_dir . '.gitignore', $gitignore);
      }
    }

    /**
     * Delete all Piwik sites
     */

    if(\Config::get('piwik.url', '') != '')
    {
      //$sites = \Piwik::custom('SitesManager.getAllSitesId', array(), false, false, 'php');
      $sites = \Web\Model\Site::whereNotIn('id', [2,3,4,5,6,7,8,9,10])->get();

      if($sites->count() > 0)
      {
        foreach($sites as $site)
        {
          $response = \Piwik::custom('SitesManager.deleteSite', array(
            'idSite' => urlencode($site->piwik_site_id)
          ), false, false, 'php');
        }
      }
    }

    /**
     * Clear cache
     */
    \Artisan::call('cache:clear');

    /**
     * Drop all tables in database
     */
    $tables = [];
 
    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
 
    foreach(\DB::select('SHOW TABLES') as $k => $v)
    {
      $tables[] = array_values((array)$v)[0];
    }
 
    foreach($tables as $table)
    {
      \Schema::drop($table);
    }
  }

  public function reset($key)
  {
    if($key == \Config::get('app.key'))
    {
      $demo_path = base_path() . '/../../demo';
      if(\File::isDirectory($demo_path))
      {
        // Clean cache, database and files
        \App\Controller\InstallationController::clean();

        // Database tables
        \App\Controller\InstallationController::migrate();

        // Uploads
        $user_files_src = $demo_path . '/user/R4/';
        $user_files_tgt = base_path() . '/../uploads/user/' . \App\Core\Secure::staticHash(1) . '/';

        \File::copyDirectory($user_files_src, $user_files_tgt);

        // Seed demo data
        $seed_sql = $demo_path . '/demo-seeds.sql';
        $seed_sql = \File::get($seed_sql);
				$seed_sql = str_replace('2016-00-00 00:00:00', date('Y-m-d H:i:s'), $seed_sql);
				$seed_sql = str_replace('user_hash_id', \App\Core\Secure::staticHash(1), $seed_sql);

        \DB::unprepared($seed_sql);
      }
    }
  }

  public function update()
  {
    if (\Auth::check() && \Auth::user()->can('system_management'))
    {
      $client = new \GuzzleHttp\Client(['verify' => base_path() . '/cacert.pem']);
      $response = $client->get(\Config::get('version.update_server') . '/updates.txt');
      $updates_txt = $response->getBody()->getContents();

      if ($updates_txt === false)
      {
        die("Update server not found");
      }

      $updates_txt = explode(PHP_EOL, $updates_txt);
      $current_version = \Config::get('version.version');
      $current_version_found = false;
      $update_found = false;

      foreach ($updates_txt as $version)
      {
        if ($version == $current_version)
        {
          $update_found = false;
          $current_version_found = true;
        }
        else
        {
          $update_found = true;
        }
      }

      $update_found = ($current_version_found && $update_found) ? true : false;

      return \View::make('app.update.update', compact('update_found'));
    }
  }

  public function doUpdate()
  {
    if (\Auth::check() && \Auth::user()->can('system_management'))
    {
      $client = new \GuzzleHttp\Client(['verify' => base_path() . '/cacert.pem']);
      $response = $client->get(\Config::get('version.update_server') . '/updates.txt');
      $updates_txt = $response->getBody()->getContents();

      if ($updates_txt === false)
      {
        die("Update server not found");
      }

      $updates_txt = explode(PHP_EOL, $updates_txt);
      $current_version = \Config::get('version.version');
      $current_version_found = false;
      $update_found = false;
      $updates = array();

      foreach ($updates_txt as $version)
      {
        if ($version == $current_version)
        {
          $update_found = false;
          $current_version_found = true;
        }
        else
        {
          $update_found = true;
          if ($current_version_found) $updates[] = $version;
        }
      }

      $update_found = ($current_version_found && $update_found) ? true : false;

      if ($update_found)
      {
        \Artisan::call('down');

        $root_path = substr(base_path(), 0, strlen(base_path()) - 6);
        $update_dir = storage_path() . '/userdata/updates';
        $client = new \GuzzleHttp\Client(['verify' => base_path() . '/cacert.pem']);

        if(! \File::isDirectory($update_dir))
        {
          \File::makeDirectory($update_dir, 0777, true);
        }
        else
        {
          \File::cleanDirectory($update_dir);
        }

        foreach ($updates as $version)
        {
          $php_file = 'update-' . $version . '.php';
          $zip_file = 'update-' . $version . '.zip';
          $deleted_file = 'update-' . $version . '-deleted.txt';

          $php = \Config::get('version.update_server') . '/' . $php_file;

          $response = $client->get($php, [
            'save_to' => $update_dir . '/' . $php_file,
          ]);

          $zip = \Config::get('version.update_server') . '/' . $zip_file;

          $response = $client->get($zip, [
            'save_to' => $update_dir . '/' . $zip_file,
          ]);

          $deleted = \Config::get('version.update_server') . '/' . $deleted_file;

          $response = $client->get($deleted, [
            'save_to' => $update_dir . '/' . $deleted_file,
          ]);

          // Extract zip
          $zip_archive = new \ZipArchive();

          if ($zip_archive->open($update_dir . '/' . $zip_file) === TRUE) {
            $zip_archive->extractTo($root_path);
            $zip_archive->close();
          } else {
            echo 'Zip extract failed';
          }

          // Delete files
          $delete_files = \File::get($update_dir . '/' . $deleted_file);
          $delete_files = explode(PHP_EOL, $delete_files);
          foreach ($delete_files as $delete_file)
          {
            if (starts_with($delete_file, '| D  public/'))
            {
              $delete_file_path = $root_path . '/' . str_replace('| D  public/', '', $delete_file);
              if (\File::isFile($delete_file_path))
              {
                \File::delete($delete_file_path);
              }
            }
          }

          // Execute PHP
          $execute_php = $update_dir . '/' . $php_file;
          include($execute_php);
        }

        \File::cleanDirectory($update_dir);

        \Artisan::call('up');
      }
      else
      {
        die('No updates found');
      }

      return \View::make('app.update.updated');
    }
  }
}