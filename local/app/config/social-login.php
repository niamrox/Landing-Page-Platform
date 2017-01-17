<?php
/*
 |--------------------------------------------------------------------------
 | Social login config
 |--------------------------------------------------------------------------
 |
 | Here you can configure the login gateways.
 |
 */
return array(
  
  /*
  |--------------------------------------------------------------------------
  | Login with Twitter
  |--------------------------------------------------------------------------
  |
  | In order to let people login with their Twitter account, you need a
  | Twitter API key (https://dev.twitter.com/faq#46).
  | 
  | Go to https://apps.twitter.com/ and click "Create New App". Fill in the 
  | details and for "Callback URL" only enter your domain (this cannot be left
  | blank allthough the actual callback url is set in the script). When the App
  | has been created, click the "Keys and Access Tokens" tab and paste the 
  | Consumer Key (api_key) and Consumer Secret (api_secret) below.
  | 
  | This widget doesn't need write access, so you can click "modify app permissions"
  | at Access Level, and select "Read only". Click "Update Settings" and you're
  | ready to go.
  |
  | Alternatively, if you don't want to use Twitter login, leave this empty.
  |
  */

  'twitter' => array(
    'api_key' => '',
    'api_secret' => ''
  ),

  /*
  |--------------------------------------------------------------------------
  | Login with Facebook
  |--------------------------------------------------------------------------
  |
  | In order to let people login with their Facebook account, you need a Facebook 
  | Access token (https://developers.facebook.com/docs/facebook-login/access-tokens).
  | 
  | 1. Go to https://developers.facebook.com/ and log in. 
  | 2. At the top menu, select My Apps > Add a New App. Or, if you're not registered as a developer yet,
  |  choose My Apps > Register as a Developer and accept the Facebook Platform Policy. And then choose
  |  My Apps > Add a New App.
  | 3. You will be asked to select a platform, choose "Website".
  | 4. Enter a name for your new app and click "Create New Facebook App ID".
  | 5. Select a category, for example "Business", and click "Create App ID".
  | 6. Scroll down and enter the or of your website. Click "Next".
  | 7. Go to the top menu again and select My Apps > [Name of your newly created App]
  | 8. Click "Show" next to App Secret.
  | 9. Enter App ID and App Secret below.
  |
  | Alternatively, if you don't want to use Facebook login, leave this empty.
  |
  */

  'facebook' => array(
    'app_id' => '',
    'app_secret' => '',
    'api_version' => 'v2.8'
  )
);
