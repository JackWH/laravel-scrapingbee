<?php

namespace Ziming\LaravelScrapingBee;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class LaravelScrapingBee
{
    protected string $baseUrl;
    protected string $apiKey;

    protected array $params = [];
    protected array $headers = [];

    public static function make(): self
    {
        return new static();
    }

    private function __construct()
    {
        $this->apiKey = config('scrapingbee.api_key');
        $this->baseUrl = config(
            'scrapingbee.base_url',
            'https://app.scrapingbee.com/api/v1/'
        );
    }

    /**
     * Matching it with ScrapingBee Python Library
     */
    private function request(string $method, string $url, array $params = [], array $data = [], array $headers = [], array $cookies = []): Response
    {
        if ($url) {
            $this->url($url);
        }

        if ($params) {
            $this->params = array_merge_recursive($this->params, $params);
        }

        if ($headers) {
            $this->setHeaders($headers);
        }

        if ($cookies) {
            $this->setCustomCookies($cookies);
        }

        $this->params['api_key'] = $this->apiKey;


        $http = Http::withHeaders($this->headers);

        if ($method === 'POST') {

            // Saw this in the docs but not sure if it is needed. Commenting it for now
            // If anyone using scrapingbee with POST URLs let me know if this is required.

            // $http->withHeaders([
            //     'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            // ]);

            $response = $http->post($this->baseUrl . '?' . http_build_query($this->params), $data);
        } else {
            // else assume is a GET request
            $response = $http->get($this->baseUrl, $this->params);
        }

        // Reset the params and headers
        $this->reset();

        return $response;
    }

    /**
     * Matching it with ScrapingBee Python Library
     */
    public function get(string $url, array $params = [], array $headers = [], array $cookies = []): Response
    {
        return $this->request('GET', $url, $params, [], $headers, $cookies);
    }

    /**
     * Matching it with ScrapingBee Python Library
     */
    public function post(string $url, array $params = [], array $data = [], array $headers = [], array $cookies = []): Response
    {
        return $this->request('POST', $url, $params, $data, $headers, $cookies);
    }

    /**
     * https://www.scrapingbee.com/documentation/#encoding-url
     */
    public function url(string $url): self
    {
        // urlencode make things fail somehow. Maybe urlencode is run at the Http::get() / Http::post() level
        $this->params['url'] = $url;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#block-ads
     */
    public function blockAds(): self
    {
        $this->params['block_ads'] = true;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#block-resources
     */
    public function allowResources(): self
    {
        $this->params['block_resources'] = false;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#custom-cookies
     */
    public function setCustomCookies(array $cookies): self
    {
        $this->params['cookies'] = http_build_query($cookies, '', ';');

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#geolocation
     */
    public function countryCode(string $countryCode): self
    {
        $this->params['country_code'] = $countryCode;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#custom-google
     */
    public function customGoogle(): self
    {
        $this->params['custom_google'] = true;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#device
     */
    public function device(string $device): self
    {
        $this->params['device'] = $device;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#json_css
     */
    public function extractDataFromCssRules(array $cssRules): self
    {
        $this->params['extract_rules'] = json_encode($cssRules);

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#json_response
     */
    public function jsonResponse(): self
    {
        $this->params['json_response'] = true;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#javascript-execution
     */
    public function executeJsSnippet(string $jsCodeSnippet): self
    {
        $this->params['js_snippet'] = base64_encode($jsCodeSnippet);

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#javascript-scroll
     */
    public function jsScrollToEndOfScreen(int $jsScrollWaitMs = 1000, int $jsScrollCount = 1): self
    {
        $this->params['js_scroll'] = true;
        $this->params['js_scroll_wait'] = $jsScrollWaitMs;
        $this->params['js_scroll_count'] = $jsScrollCount;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#premium-proxy
     */
    public function premiumProxy(): self
    {
        $this->params['premium_proxy'] = true;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#javascript-rendering
     */
    public function disableJs(): self
    {
        $this->params['render_js'] = false;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#page-source
     */
    public function pageSource(): self
    {
        $this->params['return_page_source'] = true;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#screenshot
     */
    public function screenshot(): self
    {
        $this->params['screenshot'] = true;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#screenshot
     */
    public function screenshotFullPage(): self
    {
        $this->params['screenshot_full_page'] = true;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#transparent_status_code
     */
    public function transparentHttpStatusCode(): self
    {
        $this->params['transparent_status_code'] = true;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#wait
     */
    public function wait(int $milliseconds): self
    {
        $this->params['wait'] = $milliseconds;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#wait-for
     */
    public function waitForCssSelector(string $cssSelector): self
    {
        $this->params['wait_for'] = $cssSelector;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#width_or_height
     */
    public function windowWidth(int $windowWidth): self
    {
        $this->params['window_width'] = $windowWidth;

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#width_or_height
     */
    public function windowHeight(int $windowHeight): self
    {
        $this->params['window_height'] = $windowHeight;

        return $this;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = array_combine(
            array_map(function ($key) {
                return 'Spb-'. $key;
            }, array_keys($headers)),
            $headers
        );

        $this->params['forward_headers'] = true;

        return $this;
    }

    private function reset(): self
    {
        $this->params = [];
        $this->headers = [];

        return $this;
    }

    /**
     * https://www.scrapingbee.com/documentation/#UsageEndpoint
     */
    public function usageStatistics(): Response
    {
        return Http::get($this->baseUrl . 'usage', [
            'api_key' => $this->apiKey,
        ]);
    }
}