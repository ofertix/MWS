<?php

class FeedClient extends MarketplaceWebService_Client
{
    protected $responseBodyContents;



    /**
     * Método extendido del original para añadirle los datos de cuota en el header metadata.
     * Setup and execute the request via cURL and return the server response.
     *
     * @param $action - the MWS action to perform.
     * @param $converted - the MWS parameters for the Action.
     * @param $dataHandle - A stream handle to either a feed to upload, or a report/feed submission result to download.
     * @param $contentMd5 - The Content-MD5 HTTP header value used for feed submissions.
     * @return array
     */
    protected function performRequest($action, array $converted, $dataHandle = null, $contentMd5 = null) {

        $curlOptions = $this->configureCurlOptions($action, $converted, $dataHandle, $contentMd5);

        if (is_null($curlOptions[CURLOPT_RETURNTRANSFER]) || !$curlOptions[CURLOPT_RETURNTRANSFER]) {
            $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        }

        $this->curlClient = curl_init();
        curl_setopt_array($this->curlClient, $curlOptions);

        $this->headerContents = @fopen('php://memory', 'rw+');
        $this->errorResponseBody = @fopen('php://memory', 'rw+');

        $httpResponse = curl_exec($this->curlClient);

        rewind($this->headerContents);
        $header = stream_get_contents($this->headerContents);

        $parsedHeader = $this->parseHttpHeader($header);

        require_once('Model/ResponseHeaderMetadata.php');
        $responseHeaderMetadata = new ResponseHeaderMetadata(
            $parsedHeader['x-mws-request-id'],
            $parsedHeader['x-mws-response-context'],
            $parsedHeader['x-mws-timestamp'],
            (isset($parsedHeader['x-mws-quota-max'])) ? $parsedHeader['x-mws-quota-max']: null,
            (isset($parsedHeader['x-mws-quota-remaining'])) ? $parsedHeader['x-mws-quota-remaining']: null,
            (isset($parsedHeader['x-mws-quota-resetson'])) ? $parsedHeader['x-mws-quota-resetson']: null
        );

        $code = (int) curl_getinfo($this->curlClient, CURLINFO_HTTP_CODE);

        // Only attempt to verify the Content-MD5 value if the request was successful.
        if (RequestType::getRequestType($action) === RequestType::POST_DOWNLOAD) {
            if ($code != 200) {
                rewind($this->errorResponseBody);
                $httpResponse =  stream_get_contents($this->errorResponseBody);
            } else {
                $this->verifyContentMd5($this->getParsedHeader($parsedHeader,'Content-MD5'), $dataHandle);
                $httpResponse = $this->getDownloadResponseDocument($action, $parsedHeader);
            }
        }

        // Cleanup open streams and cURL instance.
        @fclose($this->headerContents);
        @fclose($this->errorResponseBody);
        curl_close($this->curlClient);


        return array (
            'Status' => $code,
            'ResponseBody' => $httpResponse,
            'ResponseHeaderMetadata' => $responseHeaderMetadata);
    }


    /**
     * cURL callback to write the response HTTP body into a stream. This is only intended to be used
     * with RequestType::POST_DOWNLOAD request types, since the responses can potentially become
     * large.
     *
     * @param $ch - The curl handle.
     * @param $string - body portion to write.
     * @return int - number of byes written.
     */
    protected function responseCallback($ch, $string) {
        $httpStatusCode = (int) curl_getinfo($this->curlClient, CURLINFO_HTTP_CODE);

        // For unsuccessful responses, i.e. non-200 HTTP responses, we write the response body
        // into a separate stream.
        if ($httpStatusCode == 200) {
            $responseHandle = $this->responseBodyContents;
        } else {
            $responseHandle = $this->errorResponseBody;
        }

        return fwrite($responseHandle, $string);

    }

    protected function configureCurlOptions($action, array $converted, $streamHandle = null, $contentMd5 = null)
    {
        $curlOptions = parent::configureCurlOptions($action, $converted, $streamHandle, $contentMd5);
        if (RequestType::getRequestType($action) == RequestType::POST_DOWNLOAD) {
            $this->responseBodyContents = $streamHandle;
            $curlOptions[CURLOPT_WRITEFUNCTION] = array ($this, 'responseCallback');
        }
        return $curlOptions;
    }


}
