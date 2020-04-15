<?php

    //https://cloud.google.com/datastore/docs/datastore-api-tutorial
    require __DIR__ . '/vendor/autoload.php';

    use Google\Cloud\Datastore\DatastoreClient;
    use Google\Cloud\Storage\StorageClient;

    $projectId = 'mycloudapp-2';
    $kind = 'game';

    $datastore = new DatastoreClient([
    'projectId' => $projectId]);
    $tocount =0;
    $socount =0;
    $tscount =0;
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
            <img src='https://storage.cloud.google.com/".$bucketstorage."/".$display['user']."/".$display['accessID'].".jpg' width='60' height='80'>
          </div>
         
          <div class='col-sm-8' style='text-align:left'>
          <div>Title:".$display['title']."</div>
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

          ";
           
          if($user == $_SESSION['username'])
          {
           echo "<form method='post'>
                <button type='submit' value='".$display['accessID']."' name='Delete'>
                    Delete
                </button>
              </form>";
          }

          echo"
              <form method='post'>
                <button type='submit' value='".$display['accessID']."' name='View'>
                    View
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
            <img src='https://storage.cloud.google.com/".$bucketstorage."/".$display['user']."/".$display['accessID'].".jpg' width='60' height='80'>
          </div>
         
          <div class='col-sm-8' style='text-align:left'>
          <div>Title:".$display['title']."</div>
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
            <button type='submit' value='".$display['accessID']."' name='View'>
                View
            </button>
          </form>
        </div>
         </div>
      </div>";
      }
        }
    }
    
    function displayFullItem($itemid)
    {
        $projectId = 'mycloudapp-2';
        $kind = 'game';

        $datastore = new DatastoreClient([
        'projectId' => $projectId]);
        $key = $datastore->key($kind, htmlspecialchars($itemid));
        $item = $datastore->lookup($key);

        echo "
            <h2 style='text-align:center; line-height:400%'></h2>
            <div>Title: ".$item['title']."</div>
            <h2 style='text-align:center; line-height:45%'></h2>
            <div>Item ID: ".$item['accessID']."</div>
            <h2 style='text-align:center; line-height:45%'></h2>
            <div>Condition: ".$item['condition']."</div>
            <h2 style='text-align:center; line-height:45%'></h2>
            <div>Platform: ".$item['platform']."</div>
            <h2 style='text-align:center; line-height:45%'></h2>
            <div>Delivery type: ".$item['posting']."</div>
            <h2 style='text-align:center; line-height:45%'></h2>
            <div>Date posted: ".$item['date']->format('Y-m-d h:i')."</div>
            <h2 style='text-align:center; line-height:45%'></h2>
            <div>Transaction type: ".$item['transaction']."</div>
            <h2 style='text-align:center; line-height:45%'></h2>
            ";
        if($item['transaction'] != 'Trade only')
        {
            echo "<div>Price: $".$item['price']."</div>
            <h2 style='text-align:center; line-height:45%'></h2>
            ";

        }
        echo  " <div>Offered by: ".$item['user']."</div>
                <h2 style='text-align:center; line-height:45%'></h2>
                <div>Description: ".$item['description']."</div>
                <h2 style='text-align:center; line-height:50%'></h2>
        ";
        if($item['posting'] == 'Pick-up')
        {
          echo "<div>Pick up location:</div>
          <img src = 'https://maps.googleapis.com/maps/api/staticmap?center=".$item['location']."&zoom=15&size=400x400&region=AU&key=AIzaSyDJ8xJEuceNVuFVNuOpZImdcvyakOYbJYk' width='200' height='200'>";
        }

    }


    if(isset($_POST['Delete']))
    {
        deleteItems($_POST['Delete']);
    }

    if(isset($_POST['View']))
    {
        $_SESSION['viewItem'] = htmlspecialchars($_POST['View']);
        $projectId = 'mycloudapp-2';
        $kind = 'game';

        $datastore = new DatastoreClient([
        'projectId' => $projectId]);
        $key = $datastore->key($kind, htmlspecialchars($_SESSION['viewItem']));
        $item = $datastore->lookup($key);
        
        $_SESSION['seller'] = $item['user'];
        

        echo "<script type='text/javascript'>
                 window.location.href='/product.html';
            </script>";
        exit();
    }

    


?>