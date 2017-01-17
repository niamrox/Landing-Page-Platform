<?php


class MockResponse {

    public $body;
    public $headers;

}

class MockData {

    public static $oauth = true;
    public static $host = true;

    public static function load($resource) {
        if (!MockData::$host) return '';
        if (!MockData::$oauth) $resource = 'error';
        $dir = dirname(__FILE__);

        if(file_exists($dir."/data/{$resource}.json")) {
            $data = file_get_contents($dir."/data/{$resource}.json");
        }
        else {
            $data = NULL;
        }

        $mock_data = new MockData();
        $mock_data->body = $data;
        return $mock_data;
    }
}
