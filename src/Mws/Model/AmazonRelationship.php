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

    /** @var  AmazonProduct */
    private $parent;
    /** @var  AmazonProduct[] */
    private $children;

    /**
     * AmazonRelationship constructor.
     * @param AmazonProduct $parent
     * @param AmazonProduct[] $children
     */
    public function __construct(AmazonProduct $parent, array $children)
    {
        $this->parent = $parent;
        $this->children = $children;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');

        $rootNode->addChild('ParentSKU', $this->parent->sku());

        foreach ($this->children as $child) {
            $this->addRelationNode($rootNode, $child);
        }

        return $rootNode;
    }

    /**
     * @param \SimpleXMLElement $rootNode
     * @param AmazonProduct $product
     * @return \SimpleXMLElement
     */
    private function addRelationNode(\SimpleXMLElement $rootNode, AmazonProduct $product)
    {
        $relationNode = $rootNode->addChild('Relation');

        $relationNode->addChild('SKU', $product->sku());
        $relationNode->addChild('Type', 'Variation');

        return $relationNode;
    }
}
