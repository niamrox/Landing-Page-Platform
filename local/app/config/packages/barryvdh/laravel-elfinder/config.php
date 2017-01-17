<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Upload dir
    |--------------------------------------------------------------------------
    |
    | The dir where to store the images (relative from public)
    |
    */

    'dir' => 'uploads/user',

	/*
	|--------------------------------------------------------------------------
	| Filesystem disks (Flysytem)
	|--------------------------------------------------------------------------
	|
	| Define an array of Filesystem disks, which use Flysystem.
	| You can set extra options, example:
	|
	| 'my-disk' => [
	|        'URL' => url('to/disk'),
	|        'alias' => 'Local storage',
	|    ]
	*/
	
	'disks' => [
		'uploads' => [
			'glideURL' => '/glide',
		]
	],

    /*
    |--------------------------------------------------------------------------
    | Access filter
    |--------------------------------------------------------------------------
    |
    | Filter callback to check the files
    |
    */

    'access' => 'Barryvdh\Elfinder\Elfinder::checkAccess',

    /*
    |--------------------------------------------------------------------------
    | Roots
    |--------------------------------------------------------------------------
    |
    | By default, the roots file is LocalFileSystem, with the above public dir.
    | If you want custom options, you can set your own roots below.
    |
    */

    'roots' => null,

/*
 array(
			array(
				'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
				'path'          => 'C:\wamp\www\Dropbox\cms\1\public/uploads/user/kM',  // path to files (REQUIRED)
				'URL'           => '//cms.dev/uploads/user/kM',   // URL to files (REQUIRED)
				'alias'         => 'admin', // The name to replace your actual path name. (OPTIONAL)
				'accessControl' => 'access'      // disable and hide dot starting files (OPTIONAL)
			),
			array(
				'driver'        => 'LocalFileSystem',
				'path'          => 'C:\wamp\www\Dropbox\cms\1\public/uploads/stock',
				'URL'           => '//cms.dev/uploads/stock',
				'defaults'   	=> array('read' => true, 'write' => false),
				'alias'         => 'Stock Images'
			)
		)
*/

/*
S3 Support

    'roots' => array(
        array(
            'driver' => 'S3',
            'path' => '/',
            'URL' => asset('bucketname.s3.amazonaws.com', true),
            'accessControl' => 'Barryvdh\Elfinder\Elfinder::checkAccess', // filter callback (OPTIONAL)
            'accesskey' => 'access key',
            'secretkey' => 'secret key',
            'bucket' => 'bucketname',
            'tmpPath' => 'tmp',
        )
    )

*/

    /*
    |--------------------------------------------------------------------------
    | CSRF
    |--------------------------------------------------------------------------
    |
    | CSRF in a state by default false.
    | If you want to use CSRF it can be replaced with true (boolean).
    |
    */

    'csrf'=>null,

);
