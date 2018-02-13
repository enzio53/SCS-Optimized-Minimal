<?php
/**
 * Class SCS
 *
 * (work with GPA)
 * @link https://github.com/enzio53/GPA
 */
class SCS{

    public function __construct($https = false, $country = false){
        if($https){
            $this->httpsRedirect();
        }

        if($country){
            $this->BlockedCountry();
        }
    }

    private function httpsRedirect(){
        if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
            header('Location: https://'.$_SERVER['SERVER_NAME']);
        }
    }

    private function BlockedCountry(){
        if(file_exists(__DIR__.'/../inc/config/blocked_country.xml')){
            $ip = $this->ProxyLookup();

            if(file_exists(__DIR__.'/../inc/data/scs_storage/'.$this->bin_encode($ip).'-scs.xml')){
                $xml_freegeoip  = simplexml_load_file(__DIR__.'/../inc/data/scs_storage/'.$this->bin_encode($ip).'-scs.xml');
            } else {
                $xml_freegeoip  = simplexml_load_string(file_get_contents('https://freegeoip.net/xml/'.$ip));

                $handle = fopen(__DIR__.'/../inc/data/scs_storage/'.$this->bin_encode($ip).'-scs.xml', 'a+');
                fwrite($handle, '<?xml version="1.0" encoding="UTF-8"?><client><CountryCode>'.$xml_freegeoip->CountryCode.'</CountryCode></client>');
                fclose($handle);
            }

            $xml_config     = simplexml_load_file(__DIR__.'/../inc/config/blocked_country.xml');
            $xml_extract    = explode(',', $xml_config->blocked_country_code);

            if(in_array($xml_freegeoip->CountryCode, $xml_extract)){
                die(require(__DIR__.'/../inc/view/blocked.html'));
            }
        } else {
            $this->JsonReplySCS(['error' => '"inc/config/blocked_country.xml" not found !']);
        }
    }

    private function ProxyLookup(){
        if(isset($_SERVER['HTTP_CLIENT_IP'])){
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    private function JsonReplySCS($array){
        echo json_encode($array);
    }

    private function bin_encode($str){
        return array_shift(unpack('H*', $str));
    }

}

/**
 *
 * SCS-Optimized-Minimal (work with GPA)
 * @author Enzo Poker <enzio@garryhost.com>
 * @link https://github.com/enzio53/GPA
 * @version 1.0
 *
 */
