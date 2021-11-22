<?php

namespace front\controller\user;

class User extends \system\Controller {
    private $error = [];

    public function index() {
        if(!$this->user->isLogged()){
            $this->redirect($this->config->get('main', 'domain') . '/');
        }

        $this->load->model('user/user');

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateUpdate()){
            $this->model_user_user->update($this->user->getUserId(), $this->request->post);
        }

        if(isset($this->error['nickname'])) {
            $this->data['error_nickname'] = $this->error['nickname'];
        }else{
            $this->data['error_nickname'] = '';
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

        $user_info = $this->model_user_user->getUser($this->user->getUserId());

        if (isset($this->request->post['nickname'])) {
            $this->data['nickname'] = $this->request->post['nickname'];
        } else if($user_info) {
            $this->data['nickname'] = $user_info['nickname'];
        } else {
            $this->data['nickname'] = '';
        }

        if($user_info) {
            $this->data['name'] = $user_info['name'];
        } else {
            $this->data['name'] = '';
        }

        if($user_info) {
            $this->data['surname'] = $user_info['surname'];
        } else {
            $this->data['surname'] = '';
        }

        if($user_info) {
            $this->data['patronymic'] = $user_info['patronymic'];
        } else {
            $this->data['patronymic'] = '';
        }

        if (isset($this->request->post['email'])) {
            $this->data['email'] = $this->request->post['email'];
        } else if($user_info) {
            $this->data['email'] = $user_info['email'];
        } else {
            $this->data['email'] = '';
        }

        if (isset($this->request->post['password'])) {
            $this->data['password'] = $this->request->post['password'];
        } else {
            $this->data['password'] = '';
        }

        $this->template = 'user/user_form';
        $this->setOutput();
    }

    private function validateUpdate() {
        if (!isset($this->request->post['nickname']) || mb_strlen($this->request->post['nickname']) < 5) {
            $this->error['nickname'] = 'Ник должен сожержать минимум 5 символов!';
        } else {
            $user_id = $this->model_user_user->checkExistNickname($this->request->post['nickname']);
            if($user_id !== false && $user_id !== $this->user->getUserId()) {
                $this->error['nickname'] = 'Ник должен быть уникальный на всю систему!';
            }
        }

        if (!isset($this->request->post['email']) || empty($this->request->post['email'])) {
            $this->error['email'] = 'Поле email должно быть заполнено!';
        } else if (!preg_match("/^[^@]+@.*.[a-z]{2,15}$/i", $this->request->post['email'])) {
            $this->error['email'] = 'Не верный формат email!';
        } else {
            $user_id = $this->model_user_user->checkExistEmail($this->request->post['email']);
            if($user_id !== false && $user_id !== $this->user->getUserId()) {
                $this->error['email'] = 'Email должен быть уникальный на всю систему!';
            }
        }

        if (isset($this->request->post['password']) && !empty($this->request->post['password'])) {
            if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $this->request->post['password'])) {
                $this->error['password'] = 'Пароль должен состоять как минимум из 8 символов, содержать цифры, малые и заглавные латинские буквы, афроамериканца, представителя ЛГБТ и инвалида! ;}';
            }
        }

        return !$this->error;
    }
}