<?php

namespace front\controller\common;

class Auth extends \system\Controller{

    private $error = [];

    public function login(){
        if($this->user->isLogged()){
            $this->redirect($this->config->get('main', 'domain') . '/');
        }

        $this->data['error'] = 0;

        if($this->request->server['REQUEST_METHOD'] == 'POST'){

            if(isset($this->request->post['login']) && isset($this->request->post['password'])){
                if($this->user->login(false, $this->request->post['login'], $this->request->post['password'])) {
                    $this->redirect($this->config->get('main', 'domain') . '/');
                }
            }

            $this->data['error'] = 1;
        }

        $this->document->setTitle('Авторизация');

        $this->child = [
            'common/header',
            'common/footer'
        ];

        $this->template = 'common/login';
        $this->setOutput();
    }

    public function register() {
        if($this->user->isLogged()){
            $this->redirect($this->config->get('main', 'domain') . '/');
        }

        $this->load->model('user/user');

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateRegiser()){
            $this->model_user_user->add($this->request->post);
            $this->redirect($this->config->get('main', 'domain') . '/login');
        }

        $this->document->setTitle('Регистрация');

        if(isset($this->error['nickname'])) {
            $this->data['error_nickname'] = $this->error['nickname'];
        }else{
            $this->data['error_nickname'] = '';
        }

        if(isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        }else{
            $this->data['error_name'] = '';
        }

        if(isset($this->error['surname'])) {
            $this->data['error_surname'] = $this->error['surname'];
        }else{
            $this->data['error_surname'] = '';
        }

        if(isset($this->error['patronymic'])) {
            $this->data['error_patronymic'] = $this->error['patronymic'];
        }else{
            $this->data['error_patronymic'] = '';
        }

        if(isset($this->error['email'])) {
            $this->data['error_email'] = $this->error['email'];
        }else{
            $this->data['error_email'] = '';
        }

        if(isset($this->error['password'])) {
            $this->data['error_password'] = $this->error['password'];
        }else{
            $this->data['error_password'] = '';
        }

        if (isset($this->request->post['nickname'])) {
            $this->data['nickname'] = $this->request->post['nickname'];
        } else {
            $this->data['nickname'] = '';
        }

        if (isset($this->request->post['name'])) {
            $this->data['name'] = $this->request->post['name'];
        } else {
            $this->data['name'] = '';
        }

        if (isset($this->request->post['surname'])) {
            $this->data['surname'] = $this->request->post['surname'];
        } else {
            $this->data['surname'] = '';
        }

        if (isset($this->request->post['patronymic'])) {
            $this->data['patronymic'] = $this->request->post['patronymic'];
        } else {
            $this->data['patronymic'] = '';
        }

        if (isset($this->request->post['email'])) {
            $this->data['email'] = $this->request->post['email'];
        } else {
            $this->data['email'] = '';
        }

        if (isset($this->request->post['password'])) {
            $this->data['password'] = $this->request->post['password'];
        } else {
            $this->data['password'] = '';
        }

        $this->child = [
            'common/header',
            'common/footer'
        ];

        $this->template = 'common/register';
        $this->setOutput();
    }

    public function logout(){
        $this->user->logout();
        $this->redirect($this->config->get('main', 'domain') . '/login');
    }

    private function validateRegiser() {
        if (!isset($this->request->post['nickname']) || mb_strlen($this->request->post['nickname']) < 5) {
            $this->error['nickname'] = 'Ник должен сожержать минимум 5 символов!';
        } else {
            $user_id = $this->model_user_user->checkExistNickname($this->request->post['nickname']);
            if($user_id) {
                $this->error['nickname'] = 'Ник должен быть уникальный на всю систему!';
            }
        }

        if (!isset($this->request->post['name']) || mb_strlen($this->request->post['name']) < 2) {
            $this->error['name'] = 'Имя пользователя должно cодержать минимум 2 символа!';
        }

        if (!isset($this->request->post['surname']) || mb_strlen($this->request->post['surname']) < 2) {
            $this->error['surname'] = 'Фамилия пользователя должна содержать минимум 2 символа!';
        }

        if (!isset($this->request->post['patronymic']) || mb_strlen($this->request->post['patronymic']) < 2) {
            $this->error['patronymic'] = 'Отчество пользователя должно содержать минимум 2 символа!';
        }

        if (!isset($this->request->post['email']) || empty($this->request->post['email'])) {
            $this->error['email'] = 'Поле email должно быть заполнено!';
        } else if (!preg_match("/^[^@]+@.*.[a-z]{2,15}$/i", $this->request->post['email'])) {
            $this->error['email'] = 'Не верный формат email!';
        } else {
            $user_id = $this->model_user_user->checkExistEmail($this->request->post['email']);
            if($user_id) {
                $this->error['email'] = 'Email должен быть уникальный на всю систему!';
            }
        }

        if (!isset($this->request->post['password']) && empty($this->request->post['password'])) {
            $this->error['password'] = 'Пароль должен быть заполнен!';
        } else if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $this->request->post['password'])) {
            $this->error['password'] = 'Пароль должен состоять как минимум из 8 символов, содержать цифры, малые и заглавные латинские буквы, афроамериканца, представителя ЛГБТ и инвалида! ;}';
        }

        return !$this->error;
    }
}