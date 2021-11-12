<?php


namespace Efrogg\ContentRenderer\Connector\Squidex;


use Efrogg\ContentRenderer\Log\LoggerProxy;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

// TODO : déplacer squidex dans un vendor a part,
//  pour ne pas poluer le cms de base, et pouvoir ajouter guzzle uniquement dans le vendor squidex
class SquidexClient
{
    use LoggerProxy;
    private $hostName;
    private $basePath;
    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * @var bool
     */
    private $acceptUnpublished = false;


    /**
     * SquidexClient constructor.
     * @param  string       $baseUrl
     * @param  string       $basePath
     * @param               $bearerApiToken
     * @param  string|null  $hostName  the host name if different
     * @throws InvalidArgumentException
     */
    public function __construct(string $baseUrl, string $basePath,$bearerApiToken, string $hostName = null,?LoggerInterface $logger=null)
    {
        $this->basePath = $basePath;
        $this->hostName = $hostName;
        $this->initLogger($logger);

        $clientHeaders = [
            'Authorization' => 'Bearer '.$bearerApiToken,
            'Host'          => $this->hostName,
            'Accept'        => '*/*',
            'User-Agent'    => 'ContentRenderer SquidexClient'
        ];

        $this->guzzleClient = new Client(
            [
                RequestOptions::TIMEOUT         => 5,
                'base_uri'                      => $baseUrl,
                RequestOptions::HEADERS         => $clientHeaders,
                RequestOptions::ALLOW_REDIRECTS => false
            ]
        );
    }

    public function setAcceptUnpublished(bool $accept = true): void
    {
        $this->acceptUnpublished = $accept;
    }

    public function call()
    {
    }

    /**
     * @param         $url
     * @param  array  $queryParameters
     * @return mixed
     * @throws BadResponseException
     */
    public function get($url, $queryParameters = [])
    {

        $startTime=microtime(true);
        $finalUrl = $this->basePath.$this->forgeUrl($url, $queryParameters);

        $response = $this->guzzleClient->get(
            $finalUrl,
            $this->getRequestOptions()
        );

        $this->info("get ".$url,['duration'=>microtime(true)-$startTime,"parameters"=>$queryParameters,'title'=>'SquidexClient']);
        return json_decode($response->getBody()->getContents(),true);
    }

    private function forgeUrl($url, $queryParameters)
    {
        if (empty($queryParameters)) {
            return $url;
        }
        return $url.'?'.http_build_query($queryParameters);
    }

    private function getRequestOptions()
    {
        $headers = [];
        if (null !== $this->hostName) {
            $headers['Host'] = $this->hostName;
        }
        if ($this->acceptUnpublished) {
            $headers['X-Unpublished'] = 1;
        }
        return [
            RequestOptions::HEADERS => $headers
        ];
    }

}
