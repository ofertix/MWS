<?php

namespace Ofertix\Mws\Model;

interface AmazonFeedTypeInterface
{

    /**
     * @return string
     */
    public function feedType();

    public function xmlNode();
}
