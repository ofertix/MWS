<?php

namespace Ofertix\Mws\Model;

class AmazonRequest
{

    const REQUEST_TYPE_LIST_ORDERS = "LIST_ORDERS";
    const REQUEST_TYPE_LIST_ORDER_ITEMS = "LIST_ORDER_ITEMS";
    const REQUEST_TYPE_FEED = "FEED";

    /** @var  int */
    protected $id;
    /** @var int|null */
    protected $feedSubmissionId;
    /** @var null|string  */
    protected $feedType;
    /** @var \DateTime  */
    protected $submittedDate;
    /** @var null|string  */
    protected $status;
    /** @var null|string  */
    protected $requestId;
    /** @var  \Datetime */
    protected $startedProcessingDate;
    /** @var  \Datetime */
    protected $completedProcessingDate;
    /** @var null|string */
    protected $xmlRequest;
    /** @var  null|string */
    protected $xmlResponse;
    /** @var  \DateTime */
    protected $createdAt;
    /** @var string */
    protected $requestType;
    /** @var integer */
    protected $remainingQuota;

    public function __construct(
        $requestId,
        $feedSubmissionId,
        $feedType,
        \Datetime $submittedDate,
        $xmlRequest,
        $requestType = null,
        $status = null,
        $remainingQuota = null
    )
    {
        $this->requestType = $requestType;
        $this->requestId = $requestId;
        $this->feedSubmissionId = $feedSubmissionId;
        $this->feedType = $feedType;
        $this->submittedDate = $submittedDate;
        $this->xmlRequest = $xmlRequest;
        $this->status = $status;
        $this->remainingQuota = $remainingQuota;
    }

    /**
     * Get Id
     *
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return AmazonRequest
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get FeedSubmissionId
     *
     * @return mixed
     */
    public function feedSubmissionId()
    {
        return $this->feedSubmissionId;
    }

    /**
     * @param mixed $feedSubmissionId
     *
     * @return AmazonRequest
     */
    public function setFeedSubmissionId($feedSubmissionId)
    {
        $this->feedSubmissionId = $feedSubmissionId;

        return $this;
    }

    /**
     * Get FeedType
     *
     * @return mixed
     */
    public function feedType()
    {
        return $this->feedType;
    }

    /**
     * @param mixed $feedType
     *
     * @return AmazonRequest
     */
    public function setFeedType($feedType)
    {
        $this->feedType = $feedType;

        return $this;
    }

    /**
     * Get SubmittedDate
     *
     * @return mixed
     */
    public function submittedDate()
    {
        return $this->submittedDate;
    }

    /**
     * @param mixed $submittedDate
     *
     * @return AmazonRequest
     */
    public function setSubmittedDate($submittedDate)
    {
        $this->submittedDate = $submittedDate;

        return $this;
    }

    /**
     * Get Status
     *
     * @return mixed
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     *
     * @return AmazonRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get RequestId
     *
     * @return mixed
     */
    public function requestId()
    {
        return $this->requestId;
    }

    /**
     * @param mixed $requestId
     *
     * @return AmazonRequest
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Get StartedProcessingDate
     *
     * @return mixed
     */
    public function startedProcessingDate()
    {
        return $this->startedProcessingDate;
    }

    /**
     * @param mixed $startedProcessingDate
     *
     * @return AmazonRequest
     */
    public function setStartedProcessingDate($startedProcessingDate)
    {
        $this->startedProcessingDate = $startedProcessingDate;

        return $this;
    }

    /**
     * Get CompletedProcessingDate
     *
     * @return mixed
     */
    public function completedProcessingDate()
    {
        return $this->completedProcessingDate;
    }

    /**
     * @param mixed $completedProcessingDate
     *
     * @return AmazonRequest
     */
    public function setCompletedProcessingDate($completedProcessingDate)
    {
        $this->completedProcessingDate = $completedProcessingDate;

        return $this;
    }

    /**
     * Get Xml
     *
     * @return mixed
     */
    public function xml()
    {
        return $this->xml;
    }

    /**
     * @param mixed $xml
     *
     * @return AmazonRequest
     */
    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }

    /**
     * Get XmlRequest
     *
     * @return null|string
     */
    public function xmlRequest()
    {
        return $this->xmlRequest;
    }

    /**
     * @param null|string $xmlRequest
     *
     * @return AmazonRequest
     */
    public function setXmlRequest($xmlRequest)
    {
        $this->xmlRequest = $xmlRequest;

        return $this;
    }

    /**
     * Get XmlResponse
     *
     * @return null|string
     */
    public function xmlResponse()
    {
        return $this->xmlResponse;
    }

    /**
     * @param null|string $xmlResponse
     *
     * @return AmazonRequest
     */
    public function setXmlResponse($xmlResponse)
    {
        $this->xmlResponse = $xmlResponse;

        return $this;
    }

    /**
     * Get CreatedAt
     *
     * @return \DateTime
     */
    public function createdAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return AmazonRequest
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get RequestType
     *
     * @return string
     */
    public function requestType()
    {
        return $this->requestType;
    }

    /**
     * @param string $requestType
     *
     * @return AmazonRequest
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;

        return $this;
    }

    /**
     * Hook que se llama antes de persistir
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get RemainingQuota
     *
     * @return int
     */
    public function remainingQuota()
    {
        return $this->remainingQuota;
    }

    /**
     * @param int $remainingQuota
     * @return AmazonRequest
     */
    public function setRemainingQuota($remainingQuota)
    {
        $this->remainingQuota = $remainingQuota;
        return $this;
    }

}
