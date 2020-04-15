<?php

require __DIR__ . '/vendor/autoload.php';
use Google\Cloud\Datastore\DatastoreClient;

$projectId = 'mycloudapp-2';


function register($username, $password, $email, $phone)
{
    $datastore = new DatastoreClient([
        'projectId' => $projectId]);
    $kind = 'user';

    $key = $datastore->key($kind, htmlspecialchars($username));
    $user = $datastore->lookup($key);

    if(isset($user['password']))
    {
        $message = "Username already existed";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
    else
    {   
        $temp = $datastore->entity($key,
        [   
            'password' => htmlspecialchars($password),
            'email' => htmlspecialchars($email),
            'phone' => (int)htmlspecialchars($phone),
            'PBid' => null,
            'PBdevice' => null
        ]);
        $datastore->insert($temp);
        $message = "Account Registered";
        echo "<script type='text/javascript'>alert('$message');</script>";
        echo "<script type='text/javascript'>
                window.location.href='/';
              </script>";
        exit();
        
    }
}

function login($username, $password)
{   
    $datastore = new DatastoreClient([
        'projectId' => $projectId]);
    $kind = 'user';
    $key = $datastore->key($kind, htmlspecialchars($username));
    
    $user = $datastore->lookup($key);

    if($user['password'] == htmlspecialchars($password))
    {   
        $_SESSION["username"] = $username;
       echo "<script type='text/javascript'>
                 window.location.href='/main.html';
            </script>";
        exit();
       
    }
    else
    {
        $message = "Username or password is invalid";
        echo "<script type='text/javascript'>alert('$message');</script>";
    
    }

}

function displayUserInfo($username)
{   
    $datastore = new DatastoreClient([
        'projectId' => $projectId]);
    $kind = 'user';

    $key = $datastore->key($kind, htmlspecialchars($username));
    $user = $datastore->lookup($key);

    echo " <img src='https://storage.cloud.google.com/mycloudapp-image-storage/profile.jpg' width='200' height='200'>
             <h2 style='text-align:center; line-height:300%'></h2>
                <div>Username: ".htmlspecialchars($username)."</div>
                <div>Email: ".$user['email']." </div>
                <div>Phone: ".$user['phone']." </div>
                <div>Pushbullet id: ".$user['PBid']." </div>
              
    ";

}

function editInfo($username, $type, $newInfo)
{   
    $datastore = new DatastoreClient([
        'projectId' => $projectId]);
    $kind = 'user';
    $key = $datastore->key($kind, htmlspecialchars($username));
    $user = $datastore->lookup($key);

    $user[htmlspecialchars($type)] = htmlspecialchars($newInfo);
    $datastore->update($user);
  //  $datastore->commit();

}

function displayedit()
{
    if(isset($_POST['edit']))
 {
     echo " <form method='post'> 
                <select name='editing'>
                          <option value='email'>Email</option>
                          <option value='phone'>Phone</option>
                          <option value='password'>Password</option>
                          <option value='PBid'>Pushbullet id</option>
                </select>
                <h2 style='text-align:center; line-height:300%'></h2>
                <div>New info:</div>
                
                <input  style='text-align:left' type='text' name='info' pattern='{1,30}' required>
               
                <h2 style='text-align:center; line-height:300%'></h2>
                <div style='text-align:left'><input type='submit' value='Confirm' name='confirm'></div>
            </form>";
 }
}

 if(isset($_POST['register']))
 {
    register($_POST['reusername'], $_POST['repassword'], $_POST['email'], $_POST['phone']);
 }

 if(isset($_POST['login']))
 {
    login($_POST['username'], $_POST['password']);
 }

if(isset($_POST['confirm']))
{
       
        if(htmlspecialchars($_POST['editing']) == 'email')
        {
            $email = htmlspecialchars($_POST['info']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "New email is invalid";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
            else
            {
                editInfo($_SESSION['username'], $_POST['editing'], $_POST['info']);
                $message = "New email updated";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
        }
        
        else if(htmlspecialchars($_POST['editing']) == 'phone')
        {   
            if(strlen(strval(htmlspecialchars($_POST['info']))) == 10)
            {
                editInfo($_SESSION['username'], $_POST['editing'], $_POST['info']);
                $message = "New phone number updated";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
            else
            {
                $message = "New phone number is invalid";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
        }
        else
        {
            $message = "New info updated";
            echo "<script type='text/javascript'>alert('$message');</script>";
            editInfo($_SESSION['username'], $_POST['editing'], $_POST['info']);
        }
}
    

?>
