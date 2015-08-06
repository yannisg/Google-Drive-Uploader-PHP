Upload files to your google drive via gdrive api. 

Features:
- Supports offline token so that you don't need human intervention to authenticate each time script is run
- Chunked upload to support large files 

To get started: 

1. Enter credentials in both scripts (get here https://console.developers.google.com/)
2. Run gdrive_token.php to authenticate and save token locally to file. This you only need to do once.
3. Include gdrive_upload.php in your project and upload files like so: 

```php
include_once("gdrive_upload.php");
$fullPath =  "/media/myCloud/small.mp4";  // path to file you want to upload 
$gdrive = new gdrive;
$gdrive->fileRequest = $fullPath;
$gdrive->initialize();
```

Have fun!
