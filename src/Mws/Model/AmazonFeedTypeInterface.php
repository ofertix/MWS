<?php

namespace Ofertix\Mws\Model;

interface AmazonFeedTypeInterface
{


    /**
     * @return string
     */
    public function feedName();

    /**
     * @return string
     */
    public function feedType();

    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode();
}
