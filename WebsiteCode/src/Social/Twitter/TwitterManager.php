<?php

namespace itechTest\Components\Social\Twitter;

use itechTest\Components\Social\Twitter\Exceptions\TwitterManagerRequestException;
use itechTest\Components\Social\Twitter\Exceptions\TwitterManagerMissingCredentialException;
use itechTest\Components\Social\Twitter\Exceptions\TwitterManagerMissingHandleException;
use itechTest\Components\Social\Twitter\Request\Contracts\CanInteractWithTwitterApiContract;
use itechTest\Components\Social\Twitter\Request\TwitterHttpRequest;


/**
 * Class TwitterManager
 *
 * @package itechTest\Components\Social\Twitter
 */
class TwitterManager
{
    public const        OAUTH_SIGNATURE_METHOD = 'HMAC-SHA1';
    public const        DEFAULT_COUNT          = 20;
    public const        MIN_ALLOWABLE_COUNT    = 1;
    public const        MAX_ALLOWABLE_COUNT    = 120;
    public const        HTTP_METHOD            = 'GET';
    /**
     * Only the user timeline is implemented
     *
     * @var string
     */
    public $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    /**
     * @var string
     */
    private $oauth_access_token;
    /**
     * @var string
     */
    private $oauth_access_token_secret;
    /**
     * @var string
     */
    private $consumer_key;
    /**
     * @var string
     */
    private $consumer_secret;
    /**
     * @var string
     */
    private $queryString;
    /**
     * @var array
     */
    private $rawQueryEntries = [];
    /**
     * @var string
     */
    private $parsedQueryString;
    /**
     * @var string
     */
    private $twitterHandle;
    /**
     * @var int
     */
    private $count = self::DEFAULT_COUNT;
    /**
     * @var string
     */
    private $callableForUrlSignature = 'rawurlencode';
    /**
     * @var int
     */
    private $responseStatusCode;
    /**
     * @var null|TwitterManagerRequestException
     */
    private $responseException;

    /**
     * @var CanInteractWithTwitterApiContract
     */
    private $requestHandler;

    /**
     * TwitterManager constructor.
     *
     * @param string|null $consumer_key
     * @param string|null $consumer_secret
     * @param string|null $oauth_access_token
     * @param string|null $oauth_access_token_secret
     */
    public function __construct(
        ?string $consumer_key,
        ?string $consumer_secret,
        ?string $oauth_access_token,
        ?string $oauth_access_token_secret
    ) {

        /**
         * check that all credentials are supplied
         */
        $credentials = [$consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret];
        if (\count(array_filter($credentials)) < 4) {
            throw new TwitterManagerMissingCredentialException();
        }

        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->oauth_access_token = $oauth_access_token;
        $this->oauth_access_token_secret = $oauth_access_token_secret;

        // set the default request handler
        $this->setRequestHandler(new TwitterHttpRequest());
    }


    /**
     * This method will enforce that a handle exists before sending a request
     */
    private function validateTwitterHandle():void
    {
        $twitterHandle = $this->getTwitterHandle();
        if(empty($twitterHandle)){
            throw new TwitterManagerMissingHandleException();
        }
    }

    /**
     * Send a request to the Twitter API
     *
     * @return string
     */
    public function initiateRequest(): string
    {
        $this->validateTwitterHandle();

        // construct the query string
        $this->createQueryStringFromAttributes();

        /*
         * clear any existing response exception
         */
        $this->setResponseException(null);

        $fullUrl = $this->url . $this->parsedQueryString;

        $requestHandler = $this->getRequestHandler();
        $requestHandler->setOption(CURLOPT_HTTPHEADER, [$this->buildRequestHeader(), 'Expect:',]);
        $requestHandler->setOption(CURLOPT_TIMEOUT, 10);
        $requestHandler->setOption(CURLOPT_URL, $fullUrl);
        $requestHandler->setOption(CURLOPT_HEADER, false);
        $requestHandler->setOption(CURLOPT_RETURNTRANSFER, true);
        $requestHandler->setOption(CURLOPT_FAILONERROR, true);

        $response = $requestHandler->initiateRequest();


        $curlChannelErrorMessage = $requestHandler->getError();

        // set the response status code
        $errorHttpCode = (int)$requestHandler->getHttpCode();
        $this->responseStatusCode = $errorHttpCode;
        // get the response code

        if ($curlChannelErrorMessage) {

            /*
             * set the response exception with details of the error
             */
            $responseException = TwitterManagerRequestException::createWithResponse($curlChannelErrorMessage,
                $errorHttpCode,
                $response);


            $this->setResponseException($responseException);
        }

        // close the channel
        $requestHandler->endRequest();

        return $response;
    }

    /**
     * This method will create and parse the newly constructed query string
     */
    private function createQueryStringFromAttributes(): void
    {
        $attributes = [
            'screen_name' => $this->getTwitterHandle(),
            'count'       => $this->getCount(),
        ];

        $queryString = http_build_query($attributes, '', '&');

        $this->queryString = $queryString;
        $this->rawQueryEntries = $attributes;
        $this->parsedQueryString = "?{$queryString}";
    }

    /**
     * @return string
     */
    public function getTwitterHandle(): string
    {
        return (string) $this->twitterHandle;
    }

    /**
     * @param string $twitterHandle
     *
     * @return TwitterManager
     */
    public function setTwitterHandle(string $twitterHandle): TwitterManager
    {
        $twitterHandle = trim($twitterHandle);
        $this->twitterHandle = '@' . ltrim($twitterHandle, '@');
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return TwitterManager
     */
    public function setCount(int $count): TwitterManager
    {

        // if count is too low, set to default
        $count = $count < self::MIN_ALLOWABLE_COUNT ? self::DEFAULT_COUNT : $count;


        // if count is too high, set to max allowable
        $count = min($count, self::MAX_ALLOWABLE_COUNT);
        $this->count = $count;
        return $this;
    }


    /**
     * @return string
     */
    private function buildRequestHeader(): string
    {

        /*
         *  LOGIC FOR BUILDING THE OAUTH URL AS PICKED FROM TWITTER AT
         *  https://developer.twitter.com/en/docs/basics/authentication/guides/creating-a-signature.html
         *  ---
         *  To encode the HTTP method, base URL, and parameter string into a single string:

            - Convert the HTTP Method to uppercase and set the output string equal to this value.
            - Append the ‘&’ character to the output string.
            - Percent encode the URL and append it to the output string.
            - Append the ‘&’ character to the output string.
            - Percent encode the parameter string and append it to the output string.
         */

        $now = time();

        $params = [
            'oauth_consumer_key'     => $this->consumer_key,
            'oauth_nonce'            => $now,
            'oauth_signature_method' => self::OAUTH_SIGNATURE_METHOD,
            'oauth_timestamp'        => $now,
            'oauth_token'            => $this->oauth_access_token,
            'oauth_version'          => '1.0',
        ];

        $oauthSettings = array_merge($params, $this->rawQueryEntries);
        $requestString = $this->createRequestString($this->url, $oauthSettings);
        $keyDataSet = [$this->consumer_secret, $this->oauth_access_token_secret];
        $twitterSigningKey = implode('&', array_map($this->getCallableForUrlSignature(), $keyDataSet));
        $oauth_signature = base64_encode(hash_hmac('sha1', $requestString, $twitterSigningKey, true));
        $oauthSettings['oauth_signature'] = $oauth_signature;


        /**
         * Build the header string
         */
        $header = array_only_these_keys($oauthSettings, [
            'oauth_consumer_key',
            'oauth_nonce',
            'oauth_signature',
            'oauth_signature_method',
            'oauth_timestamp',
            'oauth_token',
            'oauth_version',
        ]);


        $dataResult = [];
        array_walk($header, function ($item, $key) use (&$dataResult) {
            $value = rawurlencode($item);
            $dataResult[] = "$key=\"$value\"";
        });

        $result = implode(', ', $dataResult);
        return "Authorization: OAuth {$result}";
    }

    /**
     * @param string $tweeterEndpointUrl
     *
     * @param array $oauthSettings
     *
     * @return string
     */
    private function createRequestString(string $tweeterEndpointUrl, array $oauthSettings = []): string
    {
        $result = [];

        // sort the keys
        ksort($oauthSettings);


        array_walk($oauthSettings, function ($value, $key) use (&$result) {
            $part1 = rawurlencode($key);
            $part2 = rawurlencode($value);
            $result[] = "{$part1}={$part2}";
        });


        $result = implode('&', $result);
        $data = [$tweeterEndpointUrl, $result];
        // remove empty entry
        $data = array_filter($data);

        $data = array_map('rawurlencode', $data);
        array_unshift($data, self::HTTP_METHOD);
        return implode('&', $data);

    }

    /**
     * @return string
     */
    private function getCallableForUrlSignature(): string
    {
        return $this->callableForUrlSignature;
    }

    /**
     * @return bool
     */
    public function hasResponseException(): bool
    {
        return null !== $this->getResponseException();
    }

    /**
     * @return null|TwitterManagerRequestException
     */
    public function getResponseException(): ?TwitterManagerRequestException
    {
        return $this->responseException;
    }

    /**
     * @param null|TwitterManagerRequestException $responseException
     *
     * @return TwitterManager
     */
    public function setResponseException(
        ?TwitterManagerRequestException $responseException
    ): TwitterManager {
        $this->responseException = $responseException;
        return $this;
    }

    /**
     * Get the HTTP status code for the previous request
     *
     * @return integer
     */
    public function getResponseStatusCode(): int
    {
        return (int)$this->responseStatusCode;
    }

    /**
     * @return CanInteractWithTwitterApiContract
     */
    public function getRequestHandler(): CanInteractWithTwitterApiContract
    {
        return $this->requestHandler;
    }

    /**
     * @param CanInteractWithTwitterApiContract $requestHandler
     * @return TwitterManager
     */
    public function setRequestHandler(CanInteractWithTwitterApiContract $requestHandler): TwitterManager
    {
        $this->requestHandler = $requestHandler;
        return $this;
    }


}