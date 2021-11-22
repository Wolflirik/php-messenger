<?php

namespace front\model\common;

class Message extends \system\Model{

    public function getMessages($room_id, $filter_data = []) {
        $sql = "SELECT * FROM message WHERE room_id='" . (int)$room_id . "'";

        if(isset($filter_data['start']) && isset($filter_data['limit'])) {
            $sql .= " ORDER BY r.date_added DESC LIMIT " . (int)$filter_data['start'] . ", " . (int)$filter_data['limit'];
        }else{
            $sql .= ' ORDER BY r.date_added DESC';
        }

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->rows : [];
    }

    public function add($data) {
        $this->db->query("INSERT INTO content SET text='" . (int)$data['text'] . "'");
        $content_id = $this->db->getLastId();
        $this->db->query("INSERT INTO message SET room_id='" . (int)$data['room_id'] . "', user_id='" . (int)$data['user_id'] . "', content_id='" . $content_id . "', date_added=NOW()");
        $message_id = $this->db->getLastId();
        $this->db->query("UPDATE room SET date_updated=NOW() WHERE room_id='" . (int)$data['room_id'] . "'");
        return $message_id;
    }
}