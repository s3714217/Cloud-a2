<?php
    function pushbullet($msg){

        $data = json_encode(array(
            'type' => 'note',
            'title' => 'NOTIFICATION ALERT',
            'body' => $msg,
            'device_iden' => 'ujCf2RlGpNcsjwmjkYnFWS'
        ));
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.pushbullet.com/v2/pushes');
        curl_setopt($curl, CURLOPT_USERPWD, 'o.C3FgdE4rHtw1gwoJMJw1vAfnJMH87Wps');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($data)]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_exec($curl);
        curl_close($curl);
    
    }
    if(isset($_POST['submit']))
    {
        pushbullet(htmlspecialchars($_POST['msg']));
        
    }
?>

