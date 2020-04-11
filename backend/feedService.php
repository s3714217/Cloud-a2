<?php

    //https://cloud.google.com/datastore/docs/datastore-api-tutorial
    require __DIR__ . '/vendor/autoload.php';

    use Google\Cloud\Datastore\DatastoreClient;
    use Google\Cloud\Storage\StorageClient;

    $tocount =0;
    $socount =0;
    $tscount =0;
    $projectId = 'mycloudapp-2';
    $kind = 'game';
    $datastore = new DatastoreClient([
            'projectId' => $projectId
        ]);
    $query = $datastore->query()->kind($kind);
    $result = $datastore->runQuery($query);

    foreach($result as $post )
    {
      if($post['transaction'] == "Trade only")
      {      
        $tocount +=1;
      }
      else if($post['transaction'] == "Sell only")
      {
        $socount +=1;    
      }
        $tscount +=1;
      
    }
    
    function deleteItems($gameID)
    {
        $projectId = 'mycloudapp-2';
        $kind = 'game';
        $datastore = new DatastoreClient([
            'projectId' => $projectId
        ]);
        $key = $datastore->key($kind, htmlspecialchars($gameID));
        $datastore->delete($key);

        $bucketstorage = 'mycloudapp-image-storage';
        $storage = new StorageClient();
        $bucket = $storage->bucket($bucketstorage); // Put your bucket name here.
        
        $objectName = htmlspecialchars($_SESSION['username']).'/'.htmlspecialchars($gameID).'.jpg';
        $object = $bucket->object($objectName);   
        $object->delete();
        $message = "Offer deleted";
        echo "<script type='text/javascript'>alert('$message');</script>";
        
    }

    function display($user)
    {
        $projectId = 'mycloudapp-2';
        $kind = 'game';
        $bucketstorage = 'mycloudapp-image-storage';

        $datastore = new DatastoreClient([
            'projectId' => $projectId
        ]);

        $query = $datastore->query()
        ->kind($kind)->order('date');
    
        $result = $datastore->runQuery($query);
        $arr_result = iterator_to_array($result);

     if(isset($user))
     {
        foreach(array_reverse($arr_result) as $display)
      {
        if($display['user'] == $user)
        {
        echo " 
        <div class='panel panel-default'>
        <div class='panel-body' style='text-align:left'>
          
          <div class='col-sm-2'>
            <img src='https://storage.cloud.google.com/".$bucketstorage."/".$display['user']."/".$display['accessID'].".jpg' width='80' height='80'>
          </div>
         
          <div class='col-sm-8' style='text-align:left'>
          <div>Title: <a href=''>".$display['title']."</a></div>
            <div>Transaction method: ".$display['transaction']."</div>
            <div>Platform: ".$display['platform']."</div>
            <div>Date posted: ".$display['date']->format('Y-m-d h:i')."</div>
            <div>Posted by: ".$display['user']."</div>
            ";
                if($display['transaction'] != "Trade only")
                {
                    echo  "<div>Price: $".$display['price']."</div>";
                }
           echo "
          </div>
          <div class='col-sm-2' style='text-align:center'>
              <form method='post'>
                <button type='submit' value='".$display['accessID']."' name='Delete'>
                    Delete
                </button>
              </form>
          </div>
         </div>
      </div>";
        
        }
      }
     }
     else
     {
        
      foreach(array_reverse($arr_result) as $display)
      {
        echo " 
        <div class='panel panel-default'>
        <div class='panel-body' style='text-align:left'>
          
          <div class='col-sm-2'>
            <img src='https://storage.cloud.google.com/".$bucketstorage."/".$display['user']."/".$display['accessID'].".jpg' width='80' height='80'>
          </div>
         
          <div class='col-sm-10' style='text-align:left'>
          <div>Title: <a href=''>".$display['title']."</a></div>
            <div>Transaction method: ".$display['transaction']."</div>
            <div>Platform: ".$display['platform']."</div>
            <div>Date posted: ".$display['date']->format('Y-m-d h:i')."</div>
            <div>Posted by: ".$display['user']."</div>
            ";
                if($display['transaction'] != "Trade only")
                {
                    echo  "<div>Price: $".$display['price']."</div>";
                }
           echo "
          </div>
         </div>
      </div>";
      }
        }
    }
    
    if(isset($_POST['Delete']))
    {
        deleteItems($_POST['Delete']);
    }
?>