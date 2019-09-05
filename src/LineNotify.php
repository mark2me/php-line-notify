<?php

namespace SIG\Line;

use Curl\Curl;

/**
 * Line notify
 *
 * https://notify-bot.line.me/my/services/
 *
 */
class LineNotify {

    const AUTHORIZE_URI = 'https://notify-bot.line.me/oauth/authorize';

    const TOKEN_URL = 'https://notify-bot.line.me/oauth/token';

    const ENDPOINT_BASE = 'https://notify-api.line.me/api';

    private $client_id = '';

    private $client_secret = '';

    private $callback_url = '';

    private $token = null;

    private $endpointBase = null;

    private $http = null;

    /**
     * Initialize class
     *
     * @param array
     */
    public function __construct($params)
    {
        $this->client_id = (isset($params['client_id'])) ? $params['client_id'] : '';
        $this->client_secret = (isset($params['client_secret'])) ? $params['client_secret'] : '';
        $this->callback_url =  (isset($params['callback_url'])) ? $params['callback_url'] : '';
        $this->token = (isset($params['token'])) ? $params['token'] : '';
        $this->endpointBase = LineNotify::ENDPOINT_BASE;
        $this->http = new Curl();
    }

    /**
     * Set token Line notify
     *
     * @param string
     */
    public function setToken($token) {
        $this->token = $token;
    }


    /**
     * make token auth link
     *
     * @return string the auth link of Line notify
     */
    public function authLink() {

        $param = array(
            'response_type' => 'code',
            'scope'         => 'notify',
            'state'         => 'NO_STATE',
            'client_id'     => $this->client_id,
            'redirect_uri'  => $this->callback_url
        );

        return LineNotify::AUTHORIZE_URI .'?'. http_build_query($param);
    }

   /**
    * Get current token Line Notify
    *
    * @return string the token of Line notify
    */
    public function getToken($code) {

        $param = array(
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->callback_url,
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret
        );

        $this->http->post(LineNotify::TOKEN_URL,$param);
        $response = json_decode($this->http->response,true);

        return (!empty($response['access_token'])) ? $response['access_token'] : false;
    }

    /**
     * For checking connection status.
     *
     * @return boolean
     */
    public function status() {

        $this->http->setHeader('Authorization', 'Bearer '.$this->token);
        $this->http->get($this->endpointBase.'/status');

        $response = json_decode($this->http->response,true);
        return ($response['status']==200) ? true : false;
    }

    /**
     * revoke notification configurations.
     *
     * @return boolean
     */
    public function revoke() {

        $this->http->setHeader('Authorization', 'Bearer '.$this->token);
        $this->http->post($this->endpointBase.'/revoke');

        $response = json_decode($this->http->response,true);
        return ($response['status']==200) ? true : false;
    }

    /**
     * Send text message on Line notify
     *
     * @param string $text
     * @return boolean
     */
    public function sendText($text) {

        $this->http->setHeader('Authorization', 'Bearer '.$this->token);
        $this->http->post($this->endpointBase.'/notify',array(
            'message' => $text
        ));

        $response = json_decode($this->http->response,true);
        return ($response['status']==200) ? true : false;

    }

    /**
     * Send Image message on Line notify
     *
     * @param string $text
     * @param string $thum_img
     * @param string $full_img
     * @return boolean
     */
    public function sendImage($text,$thum_img,$full_img) {

        $this->http->setHeader('Authorization', 'Bearer '.$this->token);
        $this->http->post($this->endpointBase.'/notify',array(
            'message' => $text,
            'imageThumbnail' => $thum_img,
            'imageFullsize'  => $full_img
        ));

        $response = json_decode($this->http->response,true);
        return ($response['status']==200) ? true : false;

    }

}