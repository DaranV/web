<?php
namespace Usage;

class Curl
{
    /**
     * Curl Get
     *
     * @param string $url
     * @param array $params
     * @return mixed
     */

    public static function get(string $url, array $params = array())
    {
        $url = $url . '?' . http_build_query($params, '', '&');
        $ch  = curl_init();
        
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false
        );
        curl_setopt_array($ch, $options);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }

    /**
     * Curl Post
     *
     * @param string $url
     * @param array $fields
     * @return mixed
     */

    public static function post(string $url, array $fields = array())
    {
        $ch = curl_init();
        
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_POST => true,
            CURLOPT_USERAGENT => "RevoCMS Agent"
        );
        curl_setopt_array($ch, $options);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }

    /**
     * Curl Put
     *
     * @param string $url
     * @param array $fields
     * @return mixed
     */

    public static function put(string $url, array $fields = array())
    {
        $post_field_string = http_build_query($fields);
        $ch                = curl_init($url);
        
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $post_field_string
        );
        curl_setopt_array($ch, $options);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
}
