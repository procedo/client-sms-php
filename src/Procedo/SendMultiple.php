<?php

namespace Procedo;

use Exception;
use stdClass;

class SendMultiple
{
    private $ret;
    private $environment = 'production';

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

            $result = $this->_send();
            // Converte resposta em json
            $result = json_decode($result, true);

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


    private function _defineBody($body)
    {
        try {
            $result = [
                'sms' => ''
            ];

            if (!empty($body) && is_array($body)) {
                foreach ($body as $b) {
                    if (empty($b['celular']) && empty($b['mensagem']))
                        continue;

                    $result['sms'][] = [
                        'to' => $b['celular'],
                        'msg' => $b['mensagem']
                    ];
                }
            }

            if(empty($result))
                throw new Exception('Array de dados Vazio');

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    private function _send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_getApiHost());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);

        return curl_exec($ch);
    }

    private function _getApiHost()
    {
        $url = '';
        switch ($this->environment) {
            case 'development':
                $url = 'http://localhost:3100/send';
                break;
            case 'production':
                $url = 'http://api-sms.procedo.com.br/send';
                break;
        }
        return $url;
    }
}
