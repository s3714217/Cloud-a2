<?php

    //https://cloud.google.com/datastore/docs/datastore-api-tutorial
    require __DIR__ . '/vendor/autoload.php';
    require_once 'config.php';
    
    use Google\Cloud\Datastore\DatastoreClient;
    use Google\Cloud\Storage\StorageClient;

    $projectId = getenv("MY_PROJECT_ID");
    

function uploadImg($filename, $name, $user)
{
    $bucketstorage = getenv("MY_BUCKET_ID");
    $datastore = new DatastoreClient([
        'projectId' => $projectId
    ]);
    $myfile = fopen($filename,'r');
    $storage = new StorageClient();
    $bucket = $storage->bucket($bucketstorage); // Put your bucket name here.
    $object = $bucket->upload($myfile,
    [ 'name' => $user.'/'.$name.'.jpg']);
}

function getImg($itemid, $username)
{
    
    $bucketstorage = getenv("MY_BUCKET_ID");
    echo "  <img src='https://storage.cloud.google.com/".$bucketstorage."/".$username."/".$itemid.".jpg' width='400' height='600'>"; 

}

function postsmth()
{   
    $bucketstorage = getenv("MY_BUCKET_ID");
    $datastore = new DatastoreClient([
        'projectId' => $projectId
    ]);
    $kind = 'game';
    if(isset($_FILES['img']['name']))
    {   
        $id = mt_rand();
        $key = $datastore->key($kind, $id);
        $game = $datastore->lookup($key);
        while(isset($game))
        {
            $id = mt_rand();
        }
        $temp = $datastore->entity($key,
            [   
                'title' => htmlspecialchars($_POST['title']),
                'condition' => htmlspecialchars($_POST['condition']),
                'description' => htmlspecialchars($_POST['description']),
                'transaction' => htmlspecialchars($_POST['transaction']),
                'posting' => htmlspecialchars($_POST['posting']),
                'location' => htmlspecialchars($_POST['location']),
                'date' => new DateTime(),
                'user' => htmlspecialchars($_SESSION["username"]),
                'accessID' => $id,
                'platform' => htmlspecialchars($_POST['platform']),
                'price' => htmlspecialchars($_POST['price'])
            ]);
            $datastore->insert($temp);
         uploadImg($_FILES['img']['tmp_name'], $id, htmlspecialchars($_SESSION["username"]));
         $message = "Posted";
         echo "<script type='text/javascript'>alert('$message');</script>";
        
    }
    else
    {
        $message = "No Image to upload";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
    
}

if(isset($_POST['post'])) 
{
   postsmth();
   
}
   
   
?>
