<?php

/*
https://github.com/GoogleCloudPlatform/php-docs-samples
index.php file is created to work php76
*/
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':
        require 'frontend/main.html';
        break;
    default:
        http_response_code(404);
        exit('Not Found');
}
