<?php

class Url {
    /**
     * @param string $url
     * @return string
     */
    public function createTinyUrl($url)
    {  
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, 'https://tinyurl.com/api-create.php?url=' . $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        $data = curl_exec($ch);  
        curl_close($ch);  
        return $data;
    }

    /**
     * @param string $base_url
     * @param array $phrases
     * @return string
     */
    public function createShareUrl($base_url, array $phrases)
    {  
        $encPhrases = [];
        foreach ($phrases as $p) {
            $encPhrases[] = urlencode($p);
        }
        return $base_url . '?q='. implode('_', $encPhrases);
    }

    /**
     * @param string $url
     * @return array List of phrases
     */
    public function decodeShareUrlPhrases($url)
    {
        $phrases = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        if (isset($params['q']) && is_string($params['q'])) {
            $q = explode('_', $params['q']);
            if (is_array($q) && count($q) >= 2 && count($q) <= 3) {
                foreach ($q as $p) {
                    if (is_string($p)) {
                        $phrases[] = htmlspecialchars(trim($p));
                    }
                }
            }
        }
        return $phrases;
    }
}