<?php
$docs = 'README* CHANGELOG* FAQ* CONTRIBUTING* HISTORY* UPGRADING* UPGRADE* package* demo example examples doc docs readme*';
$tests = '.travis.yml .scrutinizer.yml phpunit.xml* phpunit.php test tests Tests';
$other = '.editorconfig .gitignore composer.json LICENSE*';
$standard = "{$docs} {$tests} {$other}";

return array(

    /*
    |--------------------------------------------------------------------------
    | Vendor dir
    |--------------------------------------------------------------------------
    |
    | Default vendor dir
    |
    */
    'dir'   => base_path() . '/vendor',

    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    |
    | Additional rules, to do your own cleanups
    |
    */
    'rules' => array(
        // Symfony components
        'symfony/browser-kit/Symfony/Component/BrowserKit'           => "{$standard}",
        'symfony/class-loader/Symfony/Component/ClassLoader'         => "{$standard}",
        'symfony/console/Symfony/Component/Console'                  => "{$standard}",
        'symfony/css-selector/Symfony/Component/CssSelector'         => "{$standard}",
        'symfony/debug/Symfony/Component/Debug'                      => "{$standard}",
        'symfony/dom-crawler/Symfony/Component/DomCrawler'           => "{$standard}",
        'symfony/event-dispatcher/Symfony/Component/EventDispatcher' => "{$standard}",
        'symfony/filesystem/Symfony/Component/Filesystem'            => "{$standard}",
        'symfony/finder/Symfony/Component/Finder'                    => "{$standard}",
        'symfony/http-foundation/Symfony/Component/HttpFoundation'   => "{$standard}",
        'symfony/http-kernel/Symfony/Component/HttpKernel'           => "{$standard}",
        'symfony/process/Symfony/Component/Process'                  => "{$standard}",
        'symfony/routing/Symfony/Component/Routing'                  => "{$standard}",
        'symfony/security/Symfony/Component/Security'                => "{$standard}",
        'symfony/security-core/Symfony/Component/Security/Core'      => "{$standard}",
        'symfony/translation/Symfony/Component/Translation'          => "{$standard}",

        // Default Laravel 4 install
        'd11wtq/boris'                          => "{$standard}",
        'filp/whoops'                           => "{$standard}",
        'ircmaxell/password-compat'             => "{$standard}",
        'jeremeamia/SuperClosure'               => "{$standard}",
        'laravel/framework'                     => "{$standard} build",
        'monolog/monolog'                       => "{$standard}",
        'nesbot/carbon'                         => "{$standard}",
        'nikic/php-parser'                      => "{$standard} test_old",
        'patchwork/utf8'                        => "{$standard}",
        'phpseclib/phpseclib'                   => "{$standard}",
        'predis/predis'                         => "{$standard} bin",
        'stack/builder'                         => "{$standard}",
        'swiftmailer/swiftmailer'               => "{$standard} build* notes test-suite create_pear_package.php",

        // Common packages
        'anahkiasen/former'                     => "{$standard}",
        'anahkiasen/html-object'                => "{$docs} phpunit.xml* tests/*",
        'anahkiasen/underscore-php'             => "{$standard}",
        'anahkiasen/rocketeer'                  => "{$standard}",
        'barryvdh/laravel-debugbar'             => "{$standard}",
        'bllim/datatables'                      => "{$standard}",
        'cartalyst/sentry'                      => "{$standard}",
        'dflydev/markdown'                      => "{$standard}",
        'doctrine/annotations'                  => "{$standard} bin",
        'doctrine/cache'                        => "{$standard} bin",
        'doctrine/collections'                  => "{$standard}",
        'doctrine/common'                       => "{$standard} bin lib/vendor",
        'doctrine/dbal'                         => "{$standard} bin build* docs2 lib/vendor",
        'doctrine/inflector'                    => "{$standard}",
        'dompdf/dompdf'                         => "{$standard} www",
        'guzzle/guzzle'                         => "{$standard}",
        'guzzlehttp/guzzle'                     => "{$standard}",
        'guzzlehttp/oauth-subscriber'           => "{$standard}",
        'guzzlehttp/streams'                    => "{$standard}",
        'imagine/imagine'                       => "{$standard} lib/Imagine/Test",
        'intervention/image'                    => "{$standard} public",
        'jasonlewis/basset'                     => "{$standard}",
        'jeremeamia/SuperClosure'               => "{$standard} demo",
        'kriswallsmith/assetic'                 => "{$standard}",
        'leafo/lessphp'                         => "{$standard} Makefile package.sh",
        'league/stack-robots'                   => "{$standard}",
        'maximebf/debugbar'                     => "{$standard} demo",
        'mockery/mockery'                       => "{$standard}",
        'mrclay/minify'                         => "{$standard} MIN.txt min_extras min_unit_tests min/builder min/config* min/quick-test* min/utils.php min/groupsConfig.php min/index.php",
        'mustache/mustache'                     => "{$standard} bin",
        'oyejorge/less.php'                     => "{$standard}",
        'phenx/php-font-lib'                    => "{$standard} www",
        'phpdocumentor/reflection-docblock'     => "{$standard}",
        'phpoffice/phpexcel'                    => "{$standard} Examples unitTests changelog.txt",
        'rcrowe/twigbridge'                     => "{$standard}",
        'tijsverkoyen/css-to-inline-styles'     => "{$standard}",
        'twig/twig'                             => "{$standard}",
        'venturecraft/revisionable'             => "{$standard}",
        'willdurand/geocoder'                   => "{$standard}",

        // Project specific packages
        'watson/validating'                     => "{$standard}",
        'leafo/scssphp'                         => "{$standard}",
        'league/color-extractor'                => "{$standard}",
        'essence/essence'                       => "{$standard}",
        'hashids/hashids'                       => "{$standard}",
        'cocur/slugify'                         => "{$standard}",
        'true/punycode'                         => "{$standard}",
        'baum/baum'                             => "{$standard}",
        'yzalis/identicon'                      => "{$standard}",
        'codesleeve/laravel-stapler'            => "{$standard}",
        'piwik/device-detector'                 => "{$standard}",
        'barryvdh/laravel-elfinder'             => "{$standard}",
        'zizaco/confide'                        => "{$standard}",
        'zizaco/entrust'                        => "{$standard}",
        'laravelbook/ardent'                    => "{$standard}",
        'fitztrev/laravel-html-minify'          => "{$standard}",
        'artdarek/oauth-4-laravel'              => "{$standard}",
        'simplepie/simplepie'                   => "{$standard}",
        'league/oauth1-client'                  => "{$standard}",
        'league/oauth2-client'                  => "{$standard}",
        'embed/embed'                           => "{$standard}",
        'agentejo/mongo-lite'                   => "{$standard}",
        'gabordemooij/redbean'                  => "{$standard}",
        'league/geotools'                       => "{$standard}",
        'toin0u/geocoder-laravel'               => "{$standard}",
        'gajus/dindent'                         => "{$standard}",
        'querypath/QueryPath'                   => "{$standard}",
        'masterminds/html5'                     => "{$standard}",
        'league/oauth2-instagram'               => "{$standard}",
        'tecnick.com/tcpdf'                     => "{$standard}",
        'mpdf/mpdf'                             => "{$standard}",
        'maatwebsite/excel'                     => "{$standard}",
        'league/oauth2-facebook'                => "{$standard}",
        'barryvdh/elfinder-flysystem-driver'    => "{$standard}",
        'league/flysystem'                      => "{$standard}",
        'electrolinux/phpquery'                 => "{$standard}",
        'robbrazier/piwik'                      => "{$standard}",
        'aws/aws-sdk-php'                       => "{$standard}",
        'graham-campbell/flysystem'             => "{$standard}",
        'barryvdh/elfinder-flysystem-driver'    => "{$standard}",
        'league/flysystem-aws-s3-v3'            => "{$standard}",
        'league/flysystem-aws-s3-v2'            => "{$standard}",
        'league/glide'                          => "{$standard}",
        'league/glide-laravel'                  => "{$standard}"
    )
);