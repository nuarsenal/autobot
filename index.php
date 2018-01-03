<?PHP
require_once('./vendor/autoload.php');
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
// Token
$channel_token = 'ohm7HGbyR8rqmNuryddTXETn18dFngQo2Xy1VnJL27E0KJB6BbKjj0F8hgNCjdMkAIb2wBoQpuayS33w1XW61HIKt1F76IsjFQU5T5d1fZUVKNpgJ36UUw/YFy87VXdkqLLoffTe53BJU3BHQXJ68QdB04t89/1O/w1cDnyilFU='; 
$channel_secret = 'c70f830212cdded6367ad678cce5d73f'; 
// Create bot
$httpClient = new CurlHTTPClient($channel_token);
$bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));
// Database connection 
 $host = 'ec2-50-17-250-30.compute-1.amazonaws.com';
                $dbname = 'dbvl9ckm0gjlci';
                $user = 'jtmypzcqbwwcuf';
                $pass = 'cce251fff9748a8c0205def179b6979aec503f8e67e656a056fd2a354d57181d';
                $connection = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass); 
// Get message from Line API
$content = file_get_contents('php://input');
$events = json_decode($content, true);
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
    
        // Line API send a lot of event type, we interested in message only.
		if ($event['type'] == 'message') {
            switch($event['message']['type']) {
                case 'text':
                    
                    $sql = sprintf(
                        "SELECT * FROM slips WHERE slip_date='%s' AND user_id='%s' ", 
                        date('Y-m-d'),
                        $event['source']['userId']);
                    $result = $connection->query($sql);
                    if($result !== false && $result->rowCount() >0) {
                        // Save database
                        $params = array(
                            'name' => $event['message']['text'],
                            'slip_date' => date('Y-m-d'),
                            'user_id' => $event['source']['userId'],
                        );
                        $statement = $connection->prepare('UPDATE slips SET name=:name WHERE slip_date=:slip_date AND user_id=:user_id'); 
                        $statement->execute($params);
                    } else {
                        $params = array(
                            'user_id' => $event['source']['userId'] ,
                            'slip_date' => date('Y-m-d'),
                            'name' => $event['message']['text'],
                        );
                        $statement = $connection->prepare('INSERT INTO slips (user_id, slip_date, name) VALUES (:user_id, :slip_date, :name)');
                         
                        $effect = $statement->execute($params);
                    }
                    // Bot response 
                    $respMessage = 'Your data has saved.';
                    $replyToken = $event['replyToken'];
                    $textMessageBuilder = new TextMessageBuilder($respMessage);
                    $response = $bot->replyMessage($replyToken, $textMessageBuilder);
                    break;
                case 'image':
                    // Get file content.
                    $fileID = $event['message']['id'];
                    
                    $response = $bot->getMessageContent($fileID);
                    $fileName = md5(date('Y-m-d')).'.jpg';
                    
                    if ($response->isSucceeded()) {
                        // Create file.
                        $file = fopen($fileName, 'w');
                        fwrite($file, $response->getRawBody());
                        $sql = sprintf(
                                    "SELECT * FROM slips WHERE slip_date='%s' AND user_id='%s' ", 
                                    date('Y-m-d'),
                                    $event['source']['userId']);
                        $result = $connection->query($sql);
                        if($result !== false && $result->rowCount() >0) {
                            // Save database
                            $params = array(
                                'image' => $fileName,
                                'slip_date' => date('Y-m-d'),
                                'user_id' => $event['source']['userId'],
                            );
                            $statement = $connection->prepare('UPDATE slips SET image=:image WHERE slip_date=:slip_date AND user_id=:user_id');
                            $statement->execute($params);
                            
                        } else {
                            $params = array(
                                'user_id' => $event['source']['userId'] ,
                                'image' => $fileName,
                                'slip_date' => date('Y-m-d'),
                            );
                            $statement = $connection->prepare('INSERT INTO slips (user_id, image, slip_date) VALUES (:user_id, :image, :slip_date)');
                            $statement->execute($params);
                        }
                    }
                    // Bot response 
                    $respMessage = 'Your data has saved.';
                    $replyToken = $event['replyToken'];
                    $textMessageBuilder = new TextMessageBuilder($respMessage);
                    $response = $bot->replyMessage($replyToken, $textMessageBuilder);
                    
                    break; 
            }
		}
	}
}