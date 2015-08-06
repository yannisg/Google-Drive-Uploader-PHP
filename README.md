1. Enter credentials in both scripts
2. Run gdrive_token.php to authenticate and save token. This you only need to do once.
3. Include gdrive_upload.php in your project and upload files like so: 

include_once("gdrive_upload.php");
$fullPath =  "/media/myCloud/small.mp4";  // path to file you want to upload 
$gdrive = new gdrive;
$gdrive->fileRequest = $fullPath;
$gdrive->initialize();

Have fun!