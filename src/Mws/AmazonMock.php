<?php

namespace Ofertix\Mws;

/**
 * Class AmazonMock
 * @package Ofertix\Mws
 */
class AmazonMock
{

    public $basedir;

    public function __construct($feedType, $basedir = null)
    {
        $this->baseClassName = '\MarketplaceWebService' . ucfirst($feedType) . '_Model_';
        $this->basedir = (empty($basedir)) ? __DIR__ : $basedir;
    }

    public function __call($method, $args)
    {
        $baseName = ucfirst($method) . 'Response';
        $className = $this->baseClassName . $baseName;
        $path = $this->basedir . '/Mock/' . $baseName . '.xml';
        $xml = file_get_contents($path);
        return $className::fromXML($xml);
    }

}
