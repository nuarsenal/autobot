<?php
/**
 * Use for return easy answer.
 */
require_once('./vendor/autoload.php');
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
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            // Get replyToken
            $replyToken = $event['replyToken'];


           		$host = 'ec2-50-17-250-30.compute-1.amazonaws.com';
                $dbname = 'dbvl9ckm0gjlci';
                $user = 'jtmypzcqbwwcuf';
                $pass = 'cce251fff9748a8c0205def179b6979aec503f8e67e656a056fd2a354d57181d';
                $connection = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass); 
                
                //$sql = sprintf("SELECT * FROM poll WHERE user_id='%s' ", $event['source']['userId']);
                //$sql = sprintf("SELECT * FROM ansbot WHERE qu_text like '%s' ",  $event['message']['text']);
                $revmessage = $event['message']['text'];
                $sql = "SELECT * FROM ansbot where qu_text like '$revmessage'";
                //$result = $connection->query("SELECT * FROM ansbot where qu_text=1");
                $result = $connection->query($sql);
               // error_log($sql);

                if($result->rowCount()) {
                	//while($row=pg_fetch_assoc($result)){
                	// 	$respMessage = $row['ans_text'];
                			
                	//}
                	$packageId = 1; 
					$stickerId = 410; 
                	foreach($result as $row) {
               				 $respMessage = $row['ans_text']."\n 9*******9".$result->rowCount();
        			}

                	//$respMessage = 
                	
                }else{
                	$respMessage = "อย่างไงวะเนีย ฮ่าฮ่าฮ่า";
                }
                //$respMessage = "9*******9".$result->rowCount();

           /* switch($event['message']['text']) {
                 
                //case '123456':
               //            
                   //  $respMessage = $result->ans_text;
                //   break;
                 case 'ใครสวยที่สุด':
                    $respMessage = 'ก้อยไงจ๊ะ'.$result->rowCount();
                    break;
                case 'tel':
                    $respMessage = '089-5124512';
                    break;
                case 'address':
                    $respMessage = '99/451 Muang Nonthaburi';
                    break;
                case 'boss':
                    $respMessage = '089-2541545';
                    break;
                case 'idcard':
                    $respMessage = '5845122451245';
                    break;
                default:
                	$respMessage = "อย่างไงวะเนีย";
                    break;
            }*/
            $httpClient = new CurlHTTPClient($channel_token);
            $bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));
            $textMessageBuilder = new TextMessageBuilder($respMessage);
            $textMessageBuilderSt = new StickerMessageBuilder($packageId, $stickerId);
            $response = $bot->replyMessage($replyToken, $textMessageBuilder, $textMessageBuilderSt);
		}
	}
}
echo "OK";