<?php

namespace Ofertix\Mws;

/**
 * Class AmazonMock
 * @package Ofertix\Mws
 */
class AmazonMock
{

    protected $basedir;
    protected $postHooks;

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
        $responseHeaderMetadata = new \ResponseHeaderMetadata(
            'requestId',
            'responseContext',
            'timestamp',
            '30',
            '30'
        );

        if (array_key_exists($method, $this->postHooks) && $xml !== false) {
            $postHook = $this->postHooks[$method];
            $xml = (is_callable($postHook)) ? $postHook($xml) : $xml;
            $objResponse = $className::fromXML($xml);
            $objResponse->setResponseHeaderMetadata($responseHeaderMetadata);
            return $objResponse;
        } else {
            return;
        }

    }

    /**
     * Get Basedir
     *
     * @return null|string
     */
    public function basedir()
    {
        return $this->basedir;
    }

    /**
     * @param null|string $basedir
     * @return AmazonMock
     */
    public function setBasedir($basedir)
    {
        $this->basedir = $basedir;
        return $this;
    }

    /**
     * Get PostHooks
     *
     * @return mixed
     */
    public function postHooks()
    {
        return $this->postHooks;
    }

    /**
     * @param mixed $postHooks
     * @return AmazonMock
     */
    public function setPostHooks($postHooks)
    {
        $this->postHooks = $postHooks;
        return $this;
    }

}

