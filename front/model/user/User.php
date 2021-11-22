<?php

namespace front\model\user;


class User extends \system\Model {

    public function getUser($user_id) {
        $result = $this->db->query("SELECT * FROM user WHERE user_id='" . (int)$user_id . "'");

        if($result->num_rows){
            return [
                'name'          => $result->row['name'],
                'surname'       => $result->row['surname'],
                'patronymic'    => $result->row['patronymic'],
                'nickname'      => $result->row['nickname'],
                'email'         => $result->row['email'],
                'user_id'       => $result->row['user_id'],
            ];
        }

        return [];
    }

    public function checkExistNickname($nickname) {
        $result = $this->db->query("SELECT user_id FROM user WHERE nickname='" . $this->db->escape($nickname) . "'");
        return $result->num_rows ? $result->row['user_id'] : false;
    }

    public function checkExistEmail($email) {
        $result = $this->db->query("SELECT user_id FROM user WHERE email='" . $this->db->escape($email) . "'");

        return $result->num_rows ? $result->row['user_id'] : false;
    }

    public function add($data) {
        $this->db->query("INSERT INTO user SET 
        nickname='" . $this->db->escape($data['nickname']) . "',
        name='" . $this->db->escape($data['name']) . "',
        surname='" . $this->db->escape($data['surname']) . "', 
        patronymic='" . $this->db->escape($data['patronymic']) . "', 
        hash='" . ($hash = \system\helper\Common::generateToken(10)) . "',
        password='" . $this->db->escape(sha1($hash . sha1($hash . sha1($data['password'])))) . "', 
        email='" . $this->db->escape($data['email']) . "'");
    }

    public function update($user_id, $data) {
        $this->db->query("UPDATE user SET 
        nickname='" . $this->db->escape($data['nickname']) . "',
        hash='" . ($hash = \system\helper\Common::generateToken(10)) . "',
        email='" . $this->db->escape($data['email']) . "'
        WHERE user_id='" . (int)$user_id . "'");

        if(isset($data['password']) && !empty($data['password'])) {
            $this->db->query("UPDATE user SET hash = '" . $this->db->escape($hash = \system\helper\Common::generateToken(10)) . "', password = '" . $this->db->escape(sha1($hash . sha1($hash . sha1($data['password'])))) . "' WHERE user_id='" . (int)$user_id . "'");
        }
    }

    public function remove($user_id) {
        $this->db->query("DELETE FROM user WHERE user_id='" . (int)$user_id . "'");
    }
}