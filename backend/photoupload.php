<?php

    //https://cloud.google.com/datastore/docs/datastore-api-tutorial
    require __DIR__ . '/vendor/autoload.php';

    use Google\Cloud\Storage\StorageClient;
   
    
function uploadImg($filename, $name)
{
    $myfile = fopen($filename,'r');
    $storage = new StorageClient();
    $bucket = $storage->bucket('mycloudapp-image-storage'); // Put your bucket name here.
    $object = $bucket->upload($myfile,
    [ 'name' => 'user/'.$name.'.jpg']);
}
function getImg($name)
{
    header('Content-type: image/jpeg');
    $url = 'https://storage.cloud.google.com/mycloudapp-image-storage/user/' .$name.'.jpg';
    echo file_get_contents($url);
}

if(isset($_POST["submit"])) {
    
    uploadImg($_FILES["fileToUpload"]["name"], "test01");
    getImg("test01");
}
   
   
?>
