<?php
require_once('aweber_api/aweber_api.php');
// Replace with the keys of your application
// NEVER SHARE OR DISTRIBUTE YOUR APPLICATIONS'S KEYS!
$consumerKey    = "************************";
$consumerSecret = "*********************************";

$aweber = new AWeberAPI($consumerKey, $consumerSecret);

if (empty($_COOKIE['accessToken'])) {
    if (empty($_GET['oauth_token'])) {
        $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        list($requestToken, $requestTokenSecret) = $aweber->getRequestToken($callbackUrl);
        setcookie('requestTokenSecret', $requestTokenSecret);
        setcookie('callbackUrl', $callbackUrl);
        header("Location: {$aweber->getAuthorizeUrl()}");
        exit();
    }

    $aweber->user->tokenSecret = $_COOKIE['requestTokenSecret'];
    $aweber->user->requestToken = $_GET['oauth_token'];
    $aweber->user->verifier = $_GET['oauth_verifier'];
    list($accessToken, $accessTokenSecret) = $aweber->getAccessToken();
    setcookie('accessToken', $accessToken);
    setcookie('accessTokenSecret', $accessTokenSecret);
    header('Location: '.$_COOKIE['callbackUrl']);
    exit();
}

# set this to true to view the actual api request and response
$aweber->adapter->debug = false;

$account = $aweber->getAccount($_COOKIE['accessToken'], $_COOKIE['accessTokenSecret']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>AWeber Test Application</title>
  <link type="text/css" rel="stylesheet" href="styles.css" />
<body>
<?php
foreach($account->lists as $offset => $list) {
?>
<h1>List: <?php echo $list->name; ?></h1>
<h3><?php echo $list->id; ?></h3>
<table>
  <tr>
    <th class="stat">Subject</th>
    <th class="value">Sent</th>
    <th class="value">Stats</th>
  </tr>
<?php
foreach($list->campaigns as $campaign) {
    if ($campaign->type == 'broadcast_campaign') {
?>
    <tr>
        <td class="stat"><em><?php echo $campaign->subject; ?></em></td>
        <td class="value"><?php echo date('F j, Y h:iA', strtotime($campaign->sent_at)); ?></td>
        <td class="value"><ul>
              <li><b>Opened:</b> <?php echo $campaign->total_opens; ?></li>
              <li><b>Sent:</b>  <?php echo $campaign->total_sent; ?></li>
              <li><b>Clicked:</b>  <?php echo $campaign->total_clicks; ?></li>
            </ul>
        </td>
    <?php
    }
} ?>
</table>
<?php }
?>
<body>
</html>
