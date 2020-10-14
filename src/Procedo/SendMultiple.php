<?php

namespace Procedo;

use Exception;
use stdClass;

class SendMultiple
{
    private $ret;
    private $environment = 'production';
    private $accessToken = '';

    public function __construct()
    {

        if(defined('ENVIRONMENT')){
            $this->environment = ENVIRONMENT;
        }

        $this->ret = new stdClass();
        $this->ret->status = false;
        $this->ret->mensagem = '';
    }

    public function sendSms($body)
    {
        try {
            $this->body = $this->_defineBody($body);
            
            if(empty($this->body))
                throw new Exception('Array de dados Vazio');

            $result = $this->_send();
            
            // Converte resposta em json
            $result = json_decode($result);
           

            if ($result->status == FALSE) {
                throw new Exception($result->mensagem);
            }

            $this->ret->status = true;
            $this->ret->mensagem = $result->mensagem;
            $this->ret->logs = $result->logs;
        } catch (Exception $e) {
            $this->ret->status = false;
            $this->ret->mensagem = $e->getMessage();
        }

        return $this->ret;
    }

    public function setEnvironment($environment){
        $this->environment = $environment;
    }

    public function setAccessToken($accessToken){
        $this->accessToken = $accessToken;
    }

    private function _defineBody($body)
    {

        $result = [
            'sms' => ''
        ];

        if (!empty($body) && is_array($body)) {
            foreach ($body as $b) {
                if (empty($b['celular']) && empty($b['mensagem']))
                    continue;

                $result['sms'][] = [
                    'to' => $b['celular'],
                    'msg' => $b['mensagem'],
                    'link' => $b['link'],
                ];
            }
        }

        return $result;
    }

    private function _send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_getApiHost());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "x-access-token: {$this->accessToken}"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->body, JSON_NUMERIC_CHECK) );

        $result = curl_exec($ch);

        $errors = curl_error($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if($response != 200)
            throw new Exception("[{$response}] {$errors}");

        return $result;
    }

    private function _getApiHost()
    {
        $url = '';
        switch ($this->environment) {
            case 'development':
                $url = 'http://localhost:3100/send';
                break;
            case 'testing':
            case 'production':
                $url = 'http://sms.procedo.com.br/send';
                break;
        }
        return $url;
    }
}
