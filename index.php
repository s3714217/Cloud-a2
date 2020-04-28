<?php
session_start();
/*
https://github.com/GoogleCloudPlatform/php-docs-samples
index.php file is created to work php76
*/
include 'backend/tradeService.php'; 
//include_once 'backend/schedulerService.php';


if(isset($_SESSION["username"]))
{
    switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
        case '/':
        require 'redirect.html';
            break;
        case '/login.html':
        require 'login.html';
            break;            
        case '/register.html':
            require 'register.html';
            break;
        case '/main.html':
            require 'main.html';
            break;
        case '/post.html':
            require 'post.html';
            break;
        case '/profile.html':
            require 'profile.html';
            break;
        case '/search.html':
            require 'search.html';
            break;
        case '/product.html':
            require 'product.html';
            break;
        case '/trade.html':
            require 'trade.html';
            break;
        case '/seller.html':
            require 'seller.html';
            break;
        case '/backend/searchService.php':
            require 'backend/searchService.php';
            break;
        case '/notificationsending/adminaccess01/action=cronjobnotifyingallusers/time=every12h':
            require 'backend/schedulerService.php';
            break;    
        default:
            http_response_code(404);
            exit('Not Found');
    }
}
else
{
    switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
        case '/':
            require 'redirect.html';
            break;
        case '/login.html':
            require 'login.html';
            break;
        case '/register.html':
            require 'register.html';
            break;
        case '/notificationsending/adminaccess01/action=cronjobnotifyingallusers/time=every12h':
            require 'backend/schedulerService.php';
            break;
        default:
            http_response_code(404);
            exit('Not Found');
        }
        
}
