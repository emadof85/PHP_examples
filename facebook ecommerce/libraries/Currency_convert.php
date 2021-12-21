<?php
class Currency_convert {
    public $client;

    public function __construct()
    {
        $this->initClient();
    }

    public function getExchangeRate($base, $target)
    {
        curl_setopt($this->client, CURLOPT_URL, 'https://api.exchangeratesapi.io/latest?'.http_build_query(array(
            'base'      =>  $base,
            'symbols'   =>  $target
        )));
        $response = curl_exec($this->client);
        $currency_data = json_decode($response, true);

        return $currency_data['rates'][$target];
    }

    protected function initClient() {
        $this->client = curl_init();
        curl_setopt($this->client, CURLOPT_RETURNTRANSFER, 1);
    }
}