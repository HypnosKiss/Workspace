<?php

namespace App\Extend;


use GuzzleHttp\Client;

class KonKaWebservice
{

    public static function post($xml, $url)
    {
        info('post nc xml', ['xml' => $xml]);
        $client = new Client();
        try {
            $response = $client->post('http://' . self::serverAddress() . ':' . self::serverPort() . '/CsmProject/services/ShortMsgService.' . $url, [
                    'body' => $xml,
                    'headers' => [
                        'Content-Type' => 'text/xml',
                        'SOAPAction' => 'http://ws.jf.itf.nc/JFyyService'
                    ],
                    'http_errors' => false,
                ]
            );
            return $response;
        } catch (\Exception $e) {
            info($e);
            return false;
        }
    }

    protected static function serverAddress()
    {
        return env('KON_KA_WEB_SERVICE_ADD', '172.40.1.119');
    }

    protected static function serverPort()
    {
        return env('KON_KA_WEB_SERVICE_PORT', '9080');
    }

    protected static function username()
    {
        return env('KON_KA_WEB_SERVICE_USERNAME', 'KonkaYouPin');
    }

    protected static function password()
    {
        return env('KON_KA_WEB_SERVICE_PASSWORD', 'U2FsdGVkX18KjqiLumxGVvqhwCemq6vgeTUT');
    }

    /**
     * @param $body
     * @param $beginHtml
     * @param $endHtml
     * @return bool|string
     */

    public static function getResult($body, $beginHtml, $endHtml)
    {
        if (!strpos($body, $beginHtml)) {
            return false;
        }
        $beginPos = strpos($body, $beginHtml) + strlen($beginHtml);
        $endPos = strpos($body, $endHtml);
        return substr($body, $beginPos, $endPos - $beginPos);
    }

    /**
     * @param $body
     * @return bool|string
     */

    public static function getReturn($body)
    {
        return self::getResult($body, '<ns:return>', '</ns:return');
    }

    /**
     * @param $body
     * @return bool|string
     */

    public static function getMessage($body)
    {
        return self::getResult($body, '<message>', '</message>');
    }

    /**
     * 获取当前用户所有权限
     *
     * @param $phone
     * @param $code
     * @param string $temId
     * @return array|mixed
     */

    public static function postSms($phone, $code, $temId = 'YP')
    {
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://services.pcm.com" xmlns:xsd="http://services.pcm.com/xsd">
   <soapenv:Header/>
   <soapenv:Body>
      <ser:sendShortMsg>
         <ser:username>' . self::username() . '</ser:username>
         <ser:password>' . self::password() . '</ser:password>
         <ser:telNum>' . $phone . '</ser:telNum>
         <ser:templateName>' . $temId . '</ser:templateName>
         <ser:shortMsg>
            <xsd:attr1>' . $code . '</xsd:attr1>
            <xsd:attr2></xsd:attr2>
            <xsd:attr3></xsd:attr3>
            <xsd:attr4></xsd:attr4>
            <xsd:attr5></xsd:attr5>
            <xsd:attr6></xsd:attr6>
         </ser:shortMsg>
      </ser:sendShortMsg>
   </soapenv:Body>
</soapenv:Envelope>';
        $response = self::post($xml, 'ShortMsgServiceHttpSoap11Endpoint');
        if ($response->getStatusCode() !== 200) {
            return [];
        }
        return json_decode(self::getReturn($response->getBody()->getContents()), true);
    }
}
