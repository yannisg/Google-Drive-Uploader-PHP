<?php

class gdrive{
	
	//credentials (get those from google developer console https://console.developers.google.com/)
	var $clientId = '...';
	var $clientSecret = '...';
	var $redirectUri = '...';
	
	//variables
	var $fileRequest;
	var $mimeType;
	var $filename;
	var $path;
	var $client;
	
	
	function __construct(){
		require_once 'src/Google/autoload.php'; // get from here https://github.com/google/google-api-php-client.git 
		$this->client = new Google_Client();
	}
	
	
	function initialize(){
		echo "initializing class\n";
		$client = $this->client;
		
		$client->setClientId($this->clientId);
		$client->setClientSecret($this->clientSecret);
		$client->setRedirectUri($this->redirectUri);
				
		$refreshToken = file_get_contents(__DIR__ . "/token.txt"); 
		$client->refreshToken($refreshToken);
		$tokens = $client->getAccessToken();
		$client->setAccessToken($tokens);
		
		$client->setDefer(true);
		$this->processFile();
		
	}
	
	function processFile(){
		
		$fileRequest = $this->fileRequest;
		echo "Process File $fileRequest\n";
		$path_parts = pathinfo($fileRequest);
		$this->path = $path_parts['dirname'];
		$this->fileName = $path_parts['basename'];

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$this->mimeType = finfo_file($finfo, $fileRequest);
		finfo_close($finfo);
		
		echo "Mime type is " . $this->mimeType . "\n";
		
		$this->upload();
			
	}
	
	function upload(){
		$client = $this->client;
		
		$file = new Google_Service_Drive_DriveFile();
		$file->title = $this->fileName;
		$chunkSizeBytes = 1 * 1024 * 1024;
		
		$fileRequest = $this->fileRequest;
		$mimeType = $this->mimeType;
		
		$service = new Google_Service_Drive($client);
		$request = $service->files->insert($file);

		// Create a media file upload to represent our upload process.
		$media = new Google_Http_MediaFileUpload(
		  $client,
		  $request,
		  $mimeType,
		  null,
		  true,
		  $chunkSizeBytes
		);
		$media->setFileSize(filesize($fileRequest));

		// Upload the various chunks. $status will be false until the process is
		// complete.
		$status = false;
		$handle = fopen($fileRequest, "rb");
		
		// start uploading		
		echo "Uploading: " . $this->fileName . "\n";  
		
		$filesize = filesize($fileRequest);
		
		// while not reached the end of file marker keep looping and uploading chunks
		while (!$status && !feof($handle)) {
			$chunk = fread($handle, $chunkSizeBytes);
			$status = $media->nextChunk($chunk);  
		}
		
		// The final value of $status will be the data from the API for the object
		// that has been uploaded.
		$result = false;
		if($status != false) {
		  $result = $status;
		}

		fclose($handle);
		// Reset to the client to execute requests immediately in the future.
		$client->setDefer(false);	
	}
	
}

?>