<?php

    //https://cloud.google.com/datastore/docs/datastore-api-tutorial
    require __DIR__ . '/vendor/autoload.php';

    use Google\Cloud\Datastore\DatastoreClient;
    use Google\Cloud\Storage\StorageClient;

    $projectId = 'mycloudapp-2';
    $datastore = new DatastoreClient([
        'projectId' => $projectId]);


    function createTradeSession($buyer, $item)
    {
        $datastore = new DatastoreClient([
            'projectId' => $projectId]);
        $kind = 'trade';

       

        $id = htmlspecialchars($buyer).htmlspecialchars($item);
        $key = $datastore->key($kind, $id);
        $trade = $datastore->lookup($key);
        
       

        if(!isset($trade))
        {   
           
            $temp = $datastore->entity($key,
            [
                'buyer' => htmlspecialchars($buyer),
                'item' => htmlspecialchars($item),
                'notify' => 'false'
            ]);
            $datastore->insert($temp);

        }
        
        //$mess = htmlspecialchars($buyer). ' wants to accept one of your offer! Please visit the website for details';
        //pushbullet($mess, $user['PBid']);
        
    }

    function pushbullet($msg, $id)
    {
        $datastore = new DatastoreClient([
            'projectId' => $projectId]);
        $data = json_encode(array(
            'type' => 'note',
            'title' => 'Game Trading Center',
            'body' => $msg
        ));
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.pushbullet.com/v2/pushes');
        curl_setopt($curl, CURLOPT_USERPWD, $id);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($data)]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_exec($curl);
        curl_close($curl);
    
    }

    function getAllTrade($username)
    {
        $bucketstorage = 'mycloudapp-image-storage';
    
        $datastore = new DatastoreClient([
            'projectId' => $projectId]);
        $kind = 'trade';
       

        $query = $datastore->query()
        ->kind($kind);
        $result = $datastore->runQuery($query);

        foreach($result as $trade)
        {
            if($trade['buyer'] == htmlspecialchars($_SESSION['username']))
            {
                $kind = 'game';
                $id = $trade['item'];
                $key = $datastore->key($kind, $id);
                $game = $datastore->lookup($key);

                echo " 
        <div class='panel panel-default'>
        <div class='panel-body' style='text-align:left'>
          
          <div class='col-sm-2'>
            <img src='https://storage.cloud.google.com/".$bucketstorage."/".$game['user']."/".$game['accessID'].".jpg' width='80' height='80'>
          </div>
         
          <div class='col-sm-8' style='text-align:left'>
          <div>Title: <a href=''>".$game['title']."</a></div>
            <div>Transaction method: ".$game['transaction']."</div>
            <div>Platform: ".$game['platform']."</div>
            <div>Date posted: ".$game['date']->format('Y-m-d h:i')."</div>
            <div>Posted by: ".$game['user']."</div>
            ";
                if($game['transaction'] != "Trade only")
                {
                    echo  "<div>Price: $".$game['price']."</div>";
                }
           echo "
          </div>
          <div class='col-sm-2' style='text-align:center'>
              <form method='post'>
                <button type='submit' value='".$game['accessID']."' name='Remove'>
                    Remove
                </button>
                <button type='submit' value='".$game['user']."' name='Contact'>
                     Contact
                    </button>";
              

              if(isset($_POST['Contact']))
              {
                  $kind = 'user';
                  $key = $datastore->key($kind, htmlspecialchars($_POST['Contact']));
                  $user = $datastore->lookup($key);
                  if($trade['notify'] == 'false')
                    { 
                    $mess = htmlspecialchars($_SESSION['username']). 'is interested in your item ';    
                    $trade['notify'] = 'true';
                     $datastore->update($trade);
                     pushbullet($mess, $user['PBid']);
                    }
                    $_SESSION['sellerprofile'] = $game['user'];
                  echo "<script type='text/javascript'>
                      window.location.href='/seller.html';
                      </script>";
                      exit();
              }


      echo        "</form>
          </div>
         </div>
      </div>";
            }
        }
    }

    function removeTrade($tradeID)
    {
        $datastore = new DatastoreClient([
            'projectId' => $projectId]);
        $kind = 'trade';
        $id = htmlspecialchars($_SESSION['username']).$tradeID;
        $key = $datastore->key($kind, $id);
        $datastore->delete($key);
    }

    if(isset($_POST['trade']))
    {
        if($_SESSION['seller'] != $_SESSION['username'])
        {
            echo "<script type='text/javascript'>
            window.location.href='/trade.html';
            </script>";
            $_SESSION['tradeItem'] = htmlspecialchars($_SESSION['viewItem']);
            createTradeSession($_SESSION['username'],$_SESSION['tradeItem']);
            
        }
        else
        {
            echo "You can't add this offer";
        }
    }
    
    if(isset($_POST['Remove']))
    {
        removeTrade($_POST['Remove']);
    }

   

   
   

?>