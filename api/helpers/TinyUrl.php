<?php

class TinyUrl {
    /**
     * @param string $url
     * @return string
     */
    public static function create($url)
    {  
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, 'https://tinyurl.com/api-create.php?url=' . $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        $data = curl_exec($ch);  
        curl_close($ch);  
        return $data;
    }
}