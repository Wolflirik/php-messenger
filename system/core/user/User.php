<?php

namespace system\core\user;


class User {
    private $name;
    private $surname;
    private $patronymic;
    private $nickname;
    private $email;
    private $user_id;

    private $db;
    private $config;
    private $request;
    private $key;
    private $fingerprint;

    public function __construct($container)
    {
        $this->db = $container->get('db');
        $this->config = $container->get('config');
        $this->request = $container->get('request');
        $this->key = $this->config->get('settings', 'encrypt_key');
        $this->fingerprint = hash('sha256', md5($this->request->server['HTTP_ACCEPT_LANGUAGE']) . md5($this->request->server['HTTP_ACCEPT_ENCODING']) . md5($this->request->server['HTTP_USER_AGENT']));
        $user_id = $this->checkAccess();
        if($user_id) {
            $this->login($user_id);
        }
    }

    /**
     * @param $type
     * @return array
     */
    private function generateToken($type){
        $time_start = time();
        $time_end = $time_start + 60;
        if($type == 'access')
        {
            $time_end = $time_start + 1800;
        }
        if ($type == 'refresh')
        {
            $time_end = $time_start + 2592000;
        }

        $data = [
            'iat'  => $time_start,
            'jti'  => base64_encode(random_bytes(32) . md5($this->request->server['HTTP_USER_AGENT'] . $time_start)),
            'iss'  => $this->config->get('settings', 'system_name'),
            'nbf'  => $time_start,
            'exp'  => $time_end,
            'data' => [
                'enc'        => $this->fingerprint
            ]
        ];

        $jwt = \system\helper\JWT::encode(
            $data,
            $this->key,
            'HS512'
        );

        return ['token' => $jwt, 'exp' => $time_end];
    }

    /**
     * @return int/bool
     */
    private function checkAccess(){
        $access_token = isset($this->request->cookie['a_token']) ? $this->request->cookie['a_token'] : false;
        if($access_token) {
            try {
                $token = \system\helper\JWT::decode($access_token, $this->key, ['HS512']);
                $session = $this->db->query("SELECT user_id FROM user_session WHERE fingerprint='" . $this->db->escape($token->data->enc) . "' AND a_token='" . $this->db->escape($access_token) . "'");
                if($session->num_rows && $token->data->enc == $this->fingerprint){
                    return $session->row['user_id'];
                }else{
                    return $this->checkRefresh();
                }
            } catch (\Exception $e) {
                return $this->checkRefresh();
            }
        }else {
            return $this->checkRefresh();
        }
    }

    /**
     * @return int/bool
     */
    private function checkRefresh() {
        $refresh_token = isset($this->request->cookie['r_token']) ? $this->request->cookie['r_token'] : false;
        if($refresh_token) {
            try {
                $token = \system\helper\JWT::decode($refresh_token, $this->key, ['HS512']);
                $session = $this->db->query("SELECT user_id FROM user_session WHERE fingerprint='" . $this->db->escape($token->data->enc) . "' AND r_token='" . $this->db->escape($refresh_token) . "'");
                if($session->num_rows && $token->data->enc == $this->fingerprint){
                    $this->setToken($session->row['user_id']);
                    return $session->row['user_id'];
                } else {
                    return $this->removeToken();
                }
            } catch (\Exception $e) {
                return $this->removeToken();
            }
        }else{
            return $this->removeToken();
        }
    }

    private function removeToken(){
        if(isset($this->request->cookie['a_token'])) {
            setcookie('a_token', '', time() - 42000, '/', str_replace('//', '', $this->config->get('main', 'domain')), false, false);
            unset($this->reqiest->cookie['a_token']);
            $this->db->query("DELETE FROM user_session WHERE a_token='" . $this->db->escape($this->request->cookie['a_token']) . "'");
        }
        if(isset($this->request->cookie['r_token'])) {
            setcookie('r_token', '', time() - 42000, '/', str_replace('//', '', $this->config->get('main', 'domain')), false, true);
            unset($this->reqiest->cookie['r_token']);
            $this->db->query("DELETE FROM user_session WHERE r_token='" . $this->db->escape($this->request->cookie['r_token']) . "'");
        }
        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->surname . ' ' . mb_substr($this->name, 0, 1) . '. ' . mb_substr($this->patronymic, 0, 1) . '.';
    }

    /**
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function isLogged()
    {
        return $this->user_id;
    }

    /**
     * @param $user_id
     * @return bool
     */
    private function setToken($user_id) {
        $result = $this->db->query("SELECT COUNT(*) as count_sessions FROM user_session WHERE user_id='" . (int)$this->user_id . "'");
        if($result->row['count_sessions'] >= 5) {
            $this->db->query("DELETE FROM user_session WHERE user_id='" . (int)$user_id . "'");
        }

        $access = $this->generateToken('access');
        $refresh = $this->generateToken('refresh');

        $this->db->query("INSERT INTO user_session SET user_id='" . (int)$user_id . "', a_token='" . $this->db->escape($access['token']) . "', r_token='" . $this->db->escape($refresh['token']) . "', fingerprint='" . $this->db->escape($this->fingerprint) . "'");
        setcookie('a_token', $access['token'], time() + 3600, '/', str_replace('//', '', $this->config->get('main', 'domain')), false, false);
        setcookie('r_token', $refresh['token'], time() + (86400 * 60), '/', str_replace('//', '', $this->config->get('main', 'domain')), false, true);
    }

    /**
     * @param $login
     * @param $password
     * @param $remember
     * @return bool
     */
    public function login($user_id = false, $login = '', $password = '')
    {
        if($user_id !== false) {
            $user_data = $this->db->query("SELECT user_id, name, surname, patronymic, nickname, email FROM user WHERE user_id='" . (int)$user_id . "'");
        } else {
            $user_data = $this->db->query("SELECT user_id, name, surname, patronymic, nickname, email FROM user WHERE (nickname = '" . $this->db->escape($login) . "' OR email = '" . $this->db->escape($login) . "') AND (password = SHA1(CONCAT(hash, SHA1(CONCAT(hash, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "')");
        }

        if($user_data->num_rows) {

            $this->name = $user_data->row['name'];
            $this->surname = $user_data->row['surname'];
            $this->patronymic = $user_data->row['patronymic'];
            $this->nickname = $user_data->row['nickname'];
            $this->email = $user_data->row['email'];
            $this->user_id = $user_data->row['user_id'];
            if($user_id === false) {
                $this->setToken($this->user_id);
            }

            return true;
        }else{
            return false;
        }
    }

    public function logout()
    {
        $this->name = '';
        $this->surname = '';
        $this->patronymic = '';
        $this->nickname = '';
        $this->email = '';
        $this->user_id = '';

        $this->removeToken();
    }
}
