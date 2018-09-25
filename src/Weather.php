<?php

namespace Hanfuyin\Weather;

use GuzzleHttp\Client;
use Hanfuyin\Weather\Exceptions\HttpException;
use Hanfuyin\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    protected $key;
    protected $guzzleOptions = [];

    public function __construct($key)
    {
         $this->key = $key;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions($option)
    {
        $this->guzzleOptions = $option;
    }

    /**
     *  获取天气
     *  @return json
     */
    public function getWeather($city, $type='base', $format='json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        if(!\in_array(\strtolower($format), ['xml', 'json'])){
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }


        if(!\in_array(\strtolower($type), ['base', 'all'])){
            throw new InvalidArgumentException('Invalid type value(base/all): '.$type);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => strtolower($format),
            'extensions' => strtolower($type),
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            return 'json' === $format ? \json_decode($response, true) : $response;
        }catch (\Exception $e){
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}