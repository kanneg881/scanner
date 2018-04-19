<?php
/**
 * 網址掃瞄器
 *
 * @author Enjoy
 */

namespace TaiwanEnjoy\Url;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class Scanner
{
    /** @var array 網址陣列 */
    protected $urls;

    /** @var Client 用戶端 */
    protected $httpClient;

    /**
     * 建構子
     *
     * @param array $urls 要掃瞄網址的陣列
     */
    public function __construct(array $urls)
    {
        $this->urls = $urls;
        $this->httpClient = new Client();
    }

    /**
     * 取得無效的網址
     *
     * @return array 無效的網址
     * @throws GuzzleException
     */
    public function getInvalidUrls()
    {
        /** @var array $invalidUrls 無效的網址 */
        $invalidUrls = [];

        foreach ($this->urls as $url) {
            try {
                /** @var int $statusCode http 狀態碼 */
                $statusCode = $this->getStatusCodeForUrl($url);
            } catch (Exception $exception) {
                $statusCode = 500;
            }

            if ($statusCode >= 400) {
                array_push($invalidUrls, [
                    'url' => $url,
                    'status' => $statusCode
                ]);
            }
        }

        return $invalidUrls;
    }

    /**
     * 取得網址http 狀態碼
     *
     * @param string $url 網址
     * @return int http 狀態碼
     * @throws GuzzleException
     */
    protected function getStatusCodeForUrl($url)
    {
        /** @var Response $httpResponse http 回應 */
        $httpResponse = $this->httpClient->request('GET', $url);

        return $httpResponse->getStatusCode();
    }
}
