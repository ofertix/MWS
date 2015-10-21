<?php

require_once __DIR__.'/src/MarketplaceWebService/Client.php';

class MwsClient extends MarketplaceWebService_Client
{

    public function __construct($config){
        //possible adaptation

        parent::__construct($config['aws_access_id'],
            $config['aws_access_secret'],
            $config['config'],
            $config['app_name'],
            $config['app_version']);
    }
}
