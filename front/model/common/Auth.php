<?php

namespace front\model\common;

class Auth extends \system\Model{

    public function hasUser($data){
        return $this->db->query("SELECT COUNT(user_id) AS `count` FROM user WHERE nickname = '" . $this->db->escape($data['nickname']) . "' AND password = '" . $this->db->escape(md5(md5($data['password']))) . "'")->row['count'];
    }

    public function getUser($data){
        $query = $this->db->query("SELECT user_id FROM user WHERE nickname = '" . $this->db->escape($data['nickname']) . "' AND password = '" . $this->db->escape(md5(md5($data['password']))) . "'");
        return $query->row;
    }
}