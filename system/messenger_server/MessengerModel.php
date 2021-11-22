<?php

namespace system\messenger_server;

class MessengerModel extends \system\Model
{
    //проверка авторизации
    public function getUserByCredentials($fingerprint, $access_token)
    {
        $query = $this->db->query("SELECT user_id FROM user_session WHERE fingerprint='" . $this->db->escape($fingerprint) . "' AND a_token='" . $this->db->escape($access_token) . "'");
        return $query->num_rows ? $query->row['user_id'] : false;
    }

    //подгрузка всех комнат
    public function getRoomList($user_id)
    {
        $query = $this->db->query("SELECT r.room_id, r.name, r.date_updated, IFNULL((SELECT c.text FROM message m LEFT JOIN content c ON(m.message_id=c.message_id) WHERE m.room_id=r.room_id ORDER BY m.date_added DESC LIMIT 1), 'Еще нет сообщений') as last_message, (SELECT COUNT(*) > 0 FROM missed ms WHERE ms.room_id=r.room_id AND ms.user_id=uir.user_id) as missed FROM room r LEFT JOIN user_in_room uir ON(r.room_id=uir.room_id) WHERE uir.user_id='" . (int)$user_id . "' ORDER BY r.date_updated DESC");
        return $query->num_rows ? $query->rows : [];
    }

    //получает одну комнату по id
    public function getRoom($room_id) {
        $query = $this->db->query("SELECT r.room_id, r.name, r.date_updated, IFNULL((SELECT c.text FROM message m LEFT JOIN content c ON(m.message_id=c.message_id) WHERE m.room_id=r.room_id ORDER BY m.date_added DESC LIMIT 1), 'Еще нет сообщений') as last_message FROM room r WHERE room_id='" . $room_id . "'");
        return $query->num_rows ? $query->row : null;
    }

    //dыгрузка пользователя по id
    public function getUser($user_id) {
        $query = $this->db->query("SELECT CONCAT(surname, ' ', LEFT(name, 1), '. ', LEFT(patronymic, 1), '.') as full_name, user_id, name, surname, patronymic, nickname, status, last_seen FROM user WHERE user_id='" . (int)$user_id . "'");
        return $query->num_rows ? $query->row : null;
    }

    //выгрузка пользователей
    public function getUsers($filter_data)
    {
        $sql = "SELECT CONCAT(u.surname, ' ', LEFT(u.name, 1), '. ', LEFT(u.patronymic, 1), '.') as full_name, u.name, u.surname, u.patronymic, u.nickname, u.status, u.user_id, u.last_seen FROM user u";

        if (isset($filter_data['room_id'])) {
            $sql .= " RIGHT JOIN user_in_room uir ON(u.user_id=uir.user_id) WHERE room_id='" . (int)$filter_data['room_id'] . "'";
        }

        if(isset($filter_data['query'])) {
            $sql .= " WHERE";
            if(preg_match("/^@/", $filter_data['query'])){
                $query = str_replace('@', '', $filter_data['query']);
                $sql .= " nickname LIKE '%" . $this->db->escape($query) . "%'";
            } else {
                $sql .= " MATCH(u.name, u.surname, u.patronymic) AGAINST('*" . $this->db->escape($filter_data['query']) . "*' IN BOOLEAN MODE)";
            }

            if(isset($filter_data['not_in'])) {
                $sql .= " AND u.user_id NOT IN(" . (int)$filter_data['not_in'] . ")";
            }
        }

        $sql .= " ORDER BY u.last_seen DESC";

        if (isset($filter_data['start']) && isset($filter_data['limit'])) {
            $sql .= " LIMIT " . (int)$filter_data['start'] . ", " . (int)$filter_data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->num_rows ? $query->rows : [];
    }

    //редактирование пользователя
    public function updateUser($user_id, $data)
    {
        if (!empty($data)) {
            $sql = "UPDATE user SET";

            $params = [];

            if (isset($data['last_seen'])) {
                $params[] = " last_seen='" . $data['last_seen'] . "'";
            }

            if (isset($data['status'])) {
                $params[] = " status='" . $data['status'] . "'";
            }

            $sql .= implode(',', $params);

            $sql .= " WHERE user_id='" . (int)$user_id . "'";
            $this->db->query($sql);
        }
    }

    //добавление комнаты
    public function addRoom($data)
    {
        $this->db->query("INSERT INTO room SET name='" . $data['name'] . "', date_updated=CURRENT_TIMESTAMP()");
        return $this->db->getLastId();
    }

    public function checkExistRoom($user_ids) {
        $user_count = count($user_ids);
        $user_ids = implode(',', $user_ids);
        $query = $this->db->query("SELECT rooms.room_id FROM (SELECT r.room_id, (SELECT COUNT(uir.user_id) FROM user_in_room uir WHERE uir.room_id=r.room_id AND uir.user_id IN(" . $this->db->escape($user_ids) . ")) as u1, (SELECT COUNT(uir.user_id) FROM user_in_room uir WHERE uir.room_id=r.room_id) as u2 FROM room r) rooms WHERE u1=u2 AND u1='" . $user_count . "'");

        return $query->num_rows ? $query->row['room_id'] : false;
    }

    //добавление пользователя в комнату
    public function addUserInRoom($room_id, $user_id)
    {
        $in_room_count = $this->db->query("SELECT COUNT(*) as in_room_count FROM user_in_room WHERE room_id='" . (int)$room_id . "' AND user_id='" . (int)$user_id . "'")->row['in_room_count'];
        if (!$in_room_count) {
            $this->db->query("INSERT INTO user_in_room SET room_id='" . (int)$room_id . "', user_id='" . (int)$user_id . "'");

            return $user_id;
        } else {
            return false;
        }
    }

    //редактирование комнаты
    public function updateRoom($room_id)
    {
        $this->db->query("UPDATE room SET date_updated=CURRENT_TIMESTAMP() WHERE room_id='" . (int)$room_id . "'");
    }

    public function addMessage($room_id, $user_id, $text) {
        $this->db->query("INSERT INTO message SET author_id='" . (int)$user_id . "', room_id='" . (int)$room_id . "', date_added=CURRENT_TIMESTAMP()");
        $message_id = $this->db->getLastId();
        $this->db->query("INSERT INTO content SET text='" . $this->db->escape($text) . "', message_id='" . (int)$message_id . "'");
        $this->updateRoom($room_id);
        return $message_id;
    }

    //постраничная подгрузка сообщений в чате
    public function getMessages($room_id, $filter_data)
    {
        $sql = "SELECT m.room_id, m.author_id, m.date_added, (SELECT c.text FROM content c WHERE c.message_id=m.message_id) as text FROM message m WHERE m.room_id='" . (int)$room_id . "'";

        if (isset($filter_data['limit'])) {
            if(empty($filter_data['start_date'])) {
                $start_date = "CURRENT_TIMESTAMP()";
            } else {
                $start_date = "'" . $filter_data['start_date'] . "'";
            }
            $sql .= " AND m.date_added < " . $start_date . " ORDER BY m.date_added DESC LIMIT " . (int)$filter_data['limit'];
        } else {
            $sql .= ' ORDER BY m.date_added DESC';
        }

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->rows : [];
    }

    public function getTotalMessages($room_id) {
        $count_messages = $this->db->query("SELECT COUNT(message_id) as count_messages FROM message WHERE room_id='" . (int)$room_id . "'")->row['count_messages'];
        return $count_messages;
    }

    public function getMessage($message_id) {
        $query = $this->db->query("SELECT m.room_id, m.author_id, m.date_added, (SELECT text FROM content c WHERE c.message_id=m.message_id) as text FROM message m WHERE m.message_id='" . (int)$message_id . "'");
        return $query->num_rows ? $query->row : null;
    }

    public function setOfflineAll() {
        $this->db->query("UPDATE user SET status='0'");
    }

    public function setMissed($users, $room_id, $author_id) {
        $sql = "INSERT INTO missed (user_id, room_id) VALUES";
        $values = [];
        foreach($users as $user) {
            if($user['user_id'] != $author_id) {
                $values[] = " ('" . (int)$user['user_id'] . "', '" . (int)$room_id . "')";
            }
        }

        $sql .= implode(",", $values);

        $this->db->query($sql);
    }

    public function removeMissed($room_id, $user_id) {
        $this->db->query("DELETE FROM missed WHERE user_id='" . (int)$user_id . "' AND room_id='" . (int)$room_id . "'");
    }
}
