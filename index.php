<?PHP
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
            try {
                // Check to see user already answer
                $host = 'ec2-50-17-250-30.compute-1.amazonaws.com';
                $dbname = 'dbvl9ckm0gjlci';
                $user = 'jtmypzcqbwwcuf';
                $pass = 'cce251fff9748a8c0205def179b6979aec503f8e67e656a056fd2a354d57181d';
                $connection = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass); 
                
                //$sql = sprintf("SELECT * FROM poll WHERE user_id='%s' ", $event['source']['userId']);
                $sql = sprintf("SELECT * FROM poll WHERE user_id='%ใครสวยที่สุด' ");
               
                //$sql = sprintf("SELECT * FROM ansbot WHERE ans_text='%%'");
                $result = $connection->query($sql);
                error_log($sql);
                if($result == true || $result->rowCount() >=0) {
    
                    switch($event['message']['text']) {
                        
                        case $result->qu_text:
                           
                            $respMessage = $result->ans_text;
                            break;
                        
                        
                        
                        
                        
                        default:
                            $respMessage = "
                                คิดไม่ออกอะ
                            ";
                            break;
                    }
    
                } else {
                    $respMessage = 'ไม่คำตอบคิดเอาแล้วกัน';
                }
    
                $httpClient = new CurlHTTPClient($channel_token);
                $bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));
    
                $textMessageBuilder = new TextMessageBuilder($respMessage);
                $response = $bot->replyMessage($replyToken, $textMessageBuilder);
            } catch(Exception $e) {
                error_log($e->getMessage());
            }
		}
	}
}

echo "OK";