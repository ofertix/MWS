<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebServiceProducts
 *  @copyright   Copyright 2008-2012 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2011-10-01
 */
/******************************************************************************* 
 * 
 *  Marketplace Web Service Products PHP5 Library
 * 
 */

class MarketplaceWebService_Model_ResponseHeaderMetadata {

  const REQUEST_ID = 'x-mws-request-id';
  const RESPONSE_CONTEXT = 'x-mws-response-context';
  const TIMESTAMP = 'x-mws-timestamp';
  const QUOTA_MAX = 'x-mws-quota-max';
  const QUOTA_REMAINING = 'x-mws-quota-remaining';
  const QUOTA_RESETS_AT = 'x-mws-quota-resetsOn';

  protected $metadata = array();

  public function __construct($requestId = null, $responseContext = null, $timestamp = null,
                              $quotaMax = null, $quotaRemaining = null, $quotaResetsAt = null) {
    $this->metadata[self::REQUEST_ID] = $requestId;
    $this->metadata[self::RESPONSE_CONTEXT] = $responseContext;
    $this->metadata[self::TIMESTAMP] = $timestamp;
  }


  public function getRequestId() {
    return $this->metadata[self::REQUEST_ID];
  }

  public function getResponseContext() {
    return $this->metadata[self::RESPONSE_CONTEXT];
  }

  public function getTimestamp() {
    return $this->metadata[self::TIMESTAMP];
  }

}

