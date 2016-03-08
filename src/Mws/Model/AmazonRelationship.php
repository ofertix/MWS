<?php
/**
 * Created by PhpStorm.
 * User: dani
 * Date: 8/03/16
 * Time: 12:50
 */

namespace Ofertix\Mws\Model;

/**
 * Class AmazonRelationship
 * @package Ofertix\Mws\Model
 */
class AmazonRelationship implements AmazonFeedTypeInterface
{
    use AmazonFeedTypeTrait;

    const FEED_NAME = 'Relationship';

    public function __construct()
    {

    }
}
