<?php

include('vendor/api/autoload.php'); //Подключаем библиотеку
use Telegram\Bot\Api; 

/*
 * Input: file_name
 * Output: file_content
 */

Class TelegramStorage{
	
	private $telegram = null;
	private $chat_id = '@my_activity';
	private $token = null;
	
	function __construct($token = null)
    {
        $this->token = $token;
		$this->telegram = new Api($this->token); //Устанавливаем токен, полученный у BotFather
	}
	
	public function getFile($localpath){

	    $file_name = $this->prepareFileName($localpath);

		$file_id = $this->getFileId($file_name);

		if($file_id) {

            $answer = $this->telegram->getFile(['chat_id' => $this->chat_id, 'file_id' => $file_id]);

            $url = 'https://api.telegram.org/file/bot' . $this->token . '/' . $answer['file_path'];

            return $url;
        }
        else {

		    return false;

        }

	}

	public function saveFile($localpath = ''){

        $file_name = $this->prepareFileName($localpath);

        $key = md5($file_name);

        $tData = $this->telegram->sendDocument([ 'chat_id' => $this->chat_id, 'document' => $localpath ]);

        $file_id = $tData['document']['file_id'];

        $this->memcachedSet($key,$file_id);

        return $file_id;

	}

	private function getFileId($file_name){

        $key = md5($file_name);

        echo 'get key: ' . $key . "\r\n";

        $a = $this->memcachedGet($key);

        return $a;
    }

    private function memcachedSet($key,$val){

        $mem_var = $this->createMemcached_server();

        echo 'set key: ' . $key . "\r\n";

        $response = $mem_var->set($key,$val);

        if ($response) {
            return $response;
        } else {
            return false;
        }

	}

    private function memcachedGet($key){

		$mem_var = $this->createMemcached_server();

        $response = $mem_var->get($key);

        if ($response) {
            return $response;
        } else {
            return false;
        }

    }

    private function createMemcached_server(){
        $mem_var = new Memcached();
        $mem_var->addServer("127.0.0.1", 11211);
        return $mem_var;
	}

	private function prepareFileName($localpath){

        $match = explode('/',$localpath);

        $file_name = $match[ sizeof( $match ) - 1 ];

        return $file_name;
    }
}

?>
