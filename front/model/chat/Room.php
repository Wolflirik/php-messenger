<?php

namespace front\model\common;

class Room extends \system\Model{

    public function getRooms($user_id, $filter_data = []) {
        $sql = "SELECT r.name, r.room_id, r.user_ids, r.date_updated, IFNULL((SELECT c.text FROM message m LEFT JOIN content c ON (m.content_id=m.content_id) WHERE m.room_id=r.room_id ORDER BY m.date_added DESC LIMIT 1), 'Нет сообщений') as last_message FROM room r WHERE r.user_ids LIKE '%\"user_id\":" . (int)$user_id . "%'";
        
        if(isset($filter_data['start']) && isset($filter_data['limit'])) {
            $sql .= " ORDER BY r.date_updated DESC LIMIT " . (int)$filter_data['start'] . ", " . (int)$filter_data['limit'];
        }else{
            $sql .= ' ORDER BY r.date_updated DESC';
        }

        $query = $this->db->query($sql);

        $rooms = [];

        if($query->num_rows) {
            foreach($query->rows as $row) {
                $rooms[] = [
                    'name'          => $row['name'],
                    'room_id'       => $row['room_id'],
                    'user_ids'      => json_decode($row['user_ids']),
                    'date_updated'  => $row['date_updated'],
                    'last_message'  => $row['last_message']
                ];
            }
        }

        return $rooms;
    }

    public function getRoom($room_id) {
        $query = $this->db->query("SELECT * FROM room WHERE room_id='" . (int)$room_id . "'");

        $room = null;

        if($query->num_rows) {
            $room = [
                'name'          => $query->row['name'],
                'room_id'       => $query->row['room_id'],
                'user_ids'      => json_decode($query->row['user_ids']),
                'date_updated'  => $query->row['date_updated'],
                'last_message'  => $query->row['last_message']
            ];
        }

        return $room;
    }

    public function getRoomCount($user_id) {
        $query = $this->db->query("SELECT COUNT(room_id) as room_count FROM room WHERE user_ids LIKE '%\"user_id\":" . (int)$user_id . "%'");
        return $query->num_rows ? $query->row['room_count'] : 0;
    }

    public function add($data) {
        $this->db->query("INSERT INTO room SET name='" . $this->db->escape($data['name']) . "', user_ids='" . $this->db->escape(json_encode($data['user_ids'])) . "'");
        return $this->db->getLastId();
    }

    public function update($room_id, $data) {
        $this->db->query("UPDATE room SET name='" . $this->db->escape($data['name']) . "', user_ids='" . $this->db->escape(json_encode($data['user_ids'])) . "' WHERE room_id='" . (int)$room_id . "'");
        return (int)$room_id;
    }
}