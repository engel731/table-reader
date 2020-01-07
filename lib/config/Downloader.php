<?php
    namespace TableReader\Config;
    
    class Downloader 
    {
        public static function download($url, $filepath) 
        {
            $fp = fopen($filepath, "w");

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FILE, $fp);
            $data = curl_exec($curl);

            if($data === false) {
                var_dump(curl_error($curl));
                die();
            } 

            curl_close($curl);
            fclose($fp);

            return $filepath;
        }
    }