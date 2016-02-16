<?php
use Ofertix\Mws\Model\AmazonOrderAcknowledgement;

class OrderClient extends MarketplaceWebServiceOrders_Client
{
    public $feedClient;
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
        $configFeed = $config;
        $configFeed['ServiceURL'] = "https://mws.amazonservices.es";
        $this->feedClient = \Ofertix\Mws\MwsClientFactory::getClient($configFeed);
        parent::__construct(
            $config['aws_access_id'],
            $config['aws_access_secret'],
            $config['app_name'],
            $config['app_version'],
            array('ServiceURL' => "https://mws-eu.amazonservices.com/Orders/2013-09-01")
        );
    }

    /**
     * @param $amazonOrders
     * @param string $marketPlaceId
     * @return AmazonRequest
     * @throws \Exception
     */
    public function updateOrderFulfillment($amazonOrders, $marketPlaceId = 'default')
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;
        foreach ($amazonOrders as $amazonOrder) {
            if ($amazonOrder instanceof $this->orderFulfillmentClass) {
                continue;
            }
            throw new \Exception('OrderFulfillment must be or extend \Ofertix\Mws\Model\AmazonOrderFulfillment');
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->feedClient->createXmlFeed($amazonOrders);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->feedClient->submitFeed($xmlFeed, $marketPlaceId, $amazonOrder);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->feedClient->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }

    /**
     * @param $amazonOrders
     * @param string $marketPlaceId
     * @return AmazonRequest
     * @throws \Exception
     */
    public function cancelOrder($amazonOrderId, $marketPlaceId = 'default')
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        $amazonOrderAck = new AmazonOrderAcknowledgement($amazonOrderId, false);
        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->feedClient->createXmlFeed($amazonOrderAck->xmlNode());

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->feedClient->submitFeed($xmlFeed, $marketPlaceId, $amazonOrder);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->feedClient->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }


}
