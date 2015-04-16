<?php
/*
$longLivedSession = $session->getLongLivedSession();
$accessToken = $longLivedSession->getToken();
*/
session_start();
// added in v4.0.0
require_once 'autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;


// start session

// init app with app id and secret
FacebookSession::setDefaultApplication( '909458832430139','f148ab9b8732a2a7f4a415ea6c3f13b1' );

// login helper with redirect_uri

    $helper = new FacebookRedirectLoginHelper('http://ec2-52-74-71-176.ap-southeast-1.compute.amazonaws.com/facebookWithPhp/test.php' );

try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
} catch( Exception $ex ) {
  // When validation fails or other local issues
}

// see if we have a session
if ( isset( $session ) ) {
  // graph api request for user data

  $request = new FacebookRequest( $session, 'GET', '/me' );  
  $response = $request->execute();
  // get response
  $graphObject = $response->getGraphObject();
  
		$fbid = $graphObject->getProperty('id');              // To Get Facebook ID
 	    $fbuname = $graphObject->getProperty('username');  // To Get Facebook Username
 	    $fbfullname = $graphObject->getProperty('name'); // To Get Facebook full name
	    $femail = $graphObject->getProperty('email');
		$accessToken = $session->getAccessToken();		// To Get Facebook email ID
			    
	/* ---- Session Variables -----*/
	    $_SESSION['FBID'] = $fbid;           
	    $_SESSION['USERNAME'] = $fbuname;
        $_SESSION['FULLNAME'] = $fbfullname;
	    $_SESSION['EMAIL'] =  $femail;
		
    echo '<pre>' . print_r( $graphObject, 1 ) . '</pre>';
	
	
	/******GET friends list*******/
$friends = (new FacebookRequest( $session, 'GET', '/me/friends' ))->execute()->getGraphObject()->asArray();
echo '<pre>' . print_r( $friends, 1 ) . '</pre>';



/******POST data******/
/*   try {
    $response = (new FacebookRequest(
      $session, 'POST', '/me/feed', array(
        'link' => 'http://m.indiatimes.com/culture/who-we-are/12-reasons-why-love-is-a-waste-of-time-230832.html',
        'message' => 'FACTS OF LOVE STORIES'
      )
    ))->execute()->getGraphObject();

    echo "Posted with id: " . $response->getProperty('id');

  } catch(FacebookRequestException $e) {
    echo "Exception occured, code: " . $e->getCode();
    echo " with message: " . $e->getMessage();
  }  */  

  /****/
  
  
  
  

  
  
 /* function curl_get_file_contents($URL) {
 
	$ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $URL);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
	 curl_setopt($ch, CURLOPT_TIMEOUT, 400);
	 $contents = curl_exec($ch);
	 $err  = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	 curl_close($ch);
	$contents=json_decode($contents,true);
	print_r($contents);
	if ($contents) return $contents;
	else return FALSE;
	}

	$longLivedSession = $session->getLongLivedSession();
	$accessToken = $longLivedSession->getToken();
	try {
	  // Exchange the short-lived token for a long-lived token.
	  $longLivedAccessToken = $accessToken->extend();
	} catch(FacebookSDKException $e) {
	  echo 'Error extending short-lived access token: ' . $e->getMessage();
	  exit;
	}
	$url = " https://graph.facebook.com/$fbid/feed?access_token=$accessToken";
	
	$posts = curl_get_file_contents($url); 
	print_r($posts);  */
	
	
	
	/***********/
	$longLivedSession = $session->getLongLivedSession();
	$accessToken = $longLivedSession->getToken();
	
	 $graph_url = "https://graph.facebook.com/1377800019109207/feed?access_token=".$accessToken;
		 
	 $user = json_decode(file_get_contents($graph_url));
   // print_r($user);
   echo "<h3>Notification Data:</h3>";
    $content = '<style>
		    .container{
			border: solid 1px black;
			
		    }
		    .profile{
			width: 90px;
			padding: 5px;
			vertical-align: top;
		    }
		    .text{
			width: 390px;
			padding: 5px;
			vertical-align: top;
		    }
		    .clean{
			margin: 10px;
		    }
		    .main{
			width: 500px;
			margin-left: auto;
			margin-right: auto;
		    }
		    .link{
			background-color: #f6f7f8;
		    }
		</style>';
    $content .='<div class="main">';
    foreach($user->data as $data)
    {
        if($data->type == 'status' or $data->type == 'photo' or $data->type == 'video' or $data->type == 'link'){
	        if($data->status_type == 'mobile_status_update'){
                $content .= '
                <table class="container">
                    <tr>
                        <td class="profile"><img src="http://graph.facebook.com/'.$data->from->id.'/picture?type=large" alt="'.$data->from->name.'" width="90" height="90"></td>
                        <td class="text">
                            <strong>'.$data->from->name.' update status</strong><br />
                            <p>'.$data->message.'</p>
                            <a href="'.$data->actions[0]->link.'">View on Facebook</a>
                        </td>
                    </tr>
                </table>
                <div class="clean"></div>
                ';
            }
            elseif($data->status_type == 'added_photos'){
                $content .= '
                <table class="container">
                    <tr>
                        <td class="profile"><img src="http://graph.facebook.com/'.$data->from->id.'/picture?type=large" alt="'.$data->from->name.'" width="90" height="90"></td>
                        <td class="text">
                            <strong>'.$data->from->name.' added a picture</strong><br />
                            <p>'.$data->message.'</p>
                            <p><img src="'.$data->picture.'"></p>
                            <a href="'.$data->actions[0]->link.'">View on Facebook</a>
                        </td>
                    </tr>
                </table>
                <div class="clean"></div>
                ';
            }
            elseif($data->status_type == 'shared_story'){
                if($data->type == "link")
                {
                    $content .= '
                    <table class="container">
                        <tr>
                            <td class="profile"><img src="http://graph.facebook.com/'.$data->from->id.'/picture?type=large" alt="'.$data->from->name.'" width="90" height="90"></td>
                            <td class="text">
                                <strong>'.$data->from->name.' shared a link</strong><br />
                                <p>'.$data->message.'</p>
                                <table class="link">
                                    <tr>
                                        <td valign="top"><a href="'.$data->link.'"><img src="'.$data->picture.'"></a></td>
                                        <td>
                                            <p>'.$data->name.'</p>
                                            <p>'.$data->description.'</p>
                                        </td>
                                    </tr>
                                </table>
                                <a href="'.$data->actions[0]->link.'">View on Facebook</a>
                            </td>
                        </tr>
                    </table>
                    <div class="clean"></div>
                    ';   
                }
                if($data->type == "video")
                {
                    $content .= '
                    <table class="container">
                        <tr>
                            <td class="profile"><img src="http://graph.facebook.com/'.$data->from->id.'/picture?type=large" alt="'.$data->from->name.'" width="90" height="90"></td>
                            <td class="text">
                                <strong>'.$data->from->name.' shared a video</strong><br />
                                <p>'.$data->message.'</p>
                                <table class="link">
                                    <tr>
                                        <td valign="top"><a href="'.$data->link.'"><img src="'.$data->picture.'"></a></td>
                                        <td>
                                            <p>'.$data->name.'</p>
                                            <p>'.$data->description.'</p>
                                        </td>
                                    </tr>
                                </table>
                                <a href="'.$data->actions[0]->link.'">View on Facebook</a>
                            </td>
                        </tr>
                    </table>
                    <div class="clean"></div>
                    ';   
                }
            }
        }
    }
    $content .= '</div>';



echo $content;
	
	
	
	
	
} else {
  // show login url
  echo '<a href="' . $helper->getLoginUrl(array('email','publish_actions','manage_pages')) . '">Login</a>';
}






?>