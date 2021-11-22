<?php

namespace system\messenger_server;
use Ratchet\ConnectionInterface;

abstract class AbstractMessenger
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($key)
    {
        return $this->container->get($key);
    }

    public function __set($key, $val)
    {
        $this->container->set($key, $val);
    }

    protected function send(ConnectionInterface $conn, $type, $data)
    {
        $send_data = [
            'route'  => $type,
            'data'  => $data,
            'timestamp' => time()
        ];

        $conn->send(json_encode($send_data, JSON_UNESCAPED_SLASHES));
    }

    protected function checkAccess($access_token)
    {
        if ($access_token) {
            try {
                $token = \system\helper\JWT::decode($access_token, $this->config->get('settings', 'encrypt_key'), ['HS512']);
                if ($user_id = $this->model->getUserByCredentials($token->data->enc, $access_token)) {
                    return $user_id;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }
}
