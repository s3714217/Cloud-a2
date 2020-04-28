<?php

    //https://cloud.google.com/datastore/docs/datastore-api-tutorial
    require __DIR__ . '/vendor/autoload.php';
    require_once 'config.php';

    use Google\Cloud\Datastore\DatastoreClient;
    use Google\Cloud\Storage\StorageClient;
    use Google\Cloud\PubSub\PubSubClient;

   
      
        notifyAll();
    

    

    function notifyAll()
    {
            $projectId = getenv("MY_PROJECT_ID");
            $kind = 'user';
    
            $datastore = new DatastoreClient([
            'projectId' => $projectId]);
            $query = $datastore->query()
            ->kind($kind);
            $result = $datastore->runQuery($query);
            
            foreach($result as $user)
          {
            if( isset($user['PBid']) )
            {
                $mess ="Trading time! No posting fee for all items (1 hour left)";
                pushbullet($mess, $user['PBid']);
                $_SESSION['Ttime'] = 'running';
                time_sleep_until(time()+3600);
                $_SESSION['Ttime'] = 'end';
            } 
          }
    }
?>