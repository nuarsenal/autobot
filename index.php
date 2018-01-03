<?PHP
require_once('./vendor/autoload.php'); 
// Namespace 
use \LINE\LINEBot\HTTPClient\CurlHTTPClient; 
use \LINE\LINEBot; 
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder; 
$channel_token = 'ohm7HGbyR8rqmNuryddTXETn18dFngQo2Xy1VnJL27E0KJB6BbKjj0F8hgNCjdMkAIb2wBoQpuayS33w1XW61HIKt1F76IsjFQU5T5d1fZUVKNpgJ36UUw/YFy87VXdkqLLoffTe53BJU3BHQXJ68QdB04t89/1O/w1cDnyilFU='; 
$channel_secret = 'c70f830212cdded6367ad678cce5d73f'; 
// Get message from Line API 
$content = file_get_contents('php://input'); 
$events = json_decode($content, true); 
if (!is_null($events['events'])) { 
// Loop through each event 
		foreach ($events['events'] as $event) { 
		// Line API send a lot of event type, we interested in message only. 
		if ($event['type'] == 'message')
				 { 
				 	switch($event['message']['type']) 
							{ 
								case 'text': 
										// Get replyToken 
										$replyToken = $event['replyToken']; 
										// Reply message 
										$respMessage = 'Hello, your message is '. $event['message']['text']; $httpClient = new CurlHTTPClient($channel_token); $bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret)); $textMessageBuilder = new TextMessageBuilder($respMessage); $response = $bot->replyMessage($replyToken, $textMessageBuilder); break; 
						}
				}
		}
}

//echo "ok";
//echo "<br> ทดสอบ Bot";

