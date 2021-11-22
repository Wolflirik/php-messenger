<?php

namespace system\messenger_server;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use system\messenger_server\AbstractMessenger;

class Messenger extends AbstractMessenger implements MessageComponentInterface
{

    protected $connections;
    protected $model;
    protected $clients = [];
    protected $clientIds = [];

    public function __construct($container)
    {
        parent::__construct($container);
        $this->model = new MessengerModel($container);
        $this->connections = new \SplObjectStorage;
        $this->model->setOfflineAll();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $params_string = $conn->httpRequest->getUri()->getQuery();
        parse_str($params_string, $params);
        if(isset($params['a_token']) && $user_id = $this->checkAccess($params['a_token'])) {
            $this->connections->attach($conn);

            $this->model->updateUser($user_id, [
                'status' => 1
            ]);

            $user_data = $this->model->getUser($user_id);
            echo 'Подключился Id:' . $user_data['user_id'] . ' ФИО:' . $user_data['full_name'] . PHP_EOL;

            if ($this->clients) {
                foreach ($this->clients as $client) {
                    $this->send($client, 'userStatusUpdate', $user_data);
                }
            }

            $this->clientIds[$conn->resourceId] = $user_id;
            $this->clients[$user_id] = $conn;

            $this->sendAllRooms($conn, $user_id);
        } else {
            $this->send($conn, 'errorAuth', [
                'message'   => 'Пользователь не авторизован, сервер ответил отказом!'
            ]);
            $conn->close();
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $params = json_decode($msg, true);
        try {
            if (!isset($params['route'])) throw new \Exception('Обязательные данные, не указан путь запроса!');
            if (!isset($params['data'])) throw new \Exception('Обязательные данные, не указано тело запроса!');
            $routes = $this->config->get('main', 'ws_routes');
            if (array_key_exists($params['route'], $routes)) {
                $this->{$routes[$params['route']]}($from, $params['data']);
            } else {
                throw new \Exception('Указан не верный путь запроса!');
            }
        } catch (\Exception $e) {
            $this->send($from, 'error', [
                'message' => $e->getMessage(),
            ]);
            var_dump($e->getMessage());
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if(isset($this->clientIds[$conn->resourceId])) {
            $user_id = $this->clientIds[$conn->resourceId];
            unset($this->clients[$this->clientIds[$conn->resourceId]]);
            unset($this->clientIds[$conn->resourceId]);
            $this->connections->detach($conn);

            $this->model->updateUser($user_id, [
                'status'    => 0,
                'last_seen' => date("Y-m-d H:i:s")
            ]);

            $user_data = $this->model->getUser($user_id);

            echo 'Отключился Id:' . $user_data['user_id'] . ' ФИО:' . $user_data['full_name'] . PHP_EOL;

            if ($this->clients) {
                foreach ($this->clients as $client) {
                    $this->send($client, 'userStatusUpdate', $user_data);
                }
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    private function sendAllRooms(ConnectionInterface $conn, $user_id)
    {
        $rooms = $this->model->getRoomList($user_id);
        $this->send($conn, 'allRooms', $rooms);
    }

    private function addRoom(ConnectionInterface $conn, $params)
    {
        if (empty($params['user_ids'])) {
            throw new \Exception('Необходимо выбрать пользователей перед созданием комнаты!');
        }

        array_push($params['user_ids'], $this->clientIds[$conn->resourceId]);

        $room_id = $this->model->checkExistRoom($params['user_ids']);
        if (!$room_id) {
            $room_name = [];
            foreach ($params['user_ids'] as $user_id) {
                if ($user = $this->model->getUser($user_id)) {
                    $room_name[] = $user['full_name'];
                } else {
                    throw new \Exception('Нет пользователя с id ' . (int)$user_id . '!');
                }
            }

            $room_name = implode(', ', $room_name);

            $room_id = $this->model->addRoom([
                'name' => $room_name
            ]);

            $room_data = $this->model->getRoom($room_id);

            foreach ($params['user_ids'] as $user_id) {
                $this->model->addUserInRoom($room_id, $user_id);
                if (isset($this->clients[$user_id])) {
                    $this->send($this->clients[$user_id], 'addRoom', $room_data);
                }
            }

            $this->send($conn, 'selectRoom', [
                'room_id'   => $room_data['room_id']
            ]);
        } else {
            $room_data = $this->model->getRoom($room_id);
            $this->send($conn, 'existedRoom', $room_data);
        }
    }

    private function getMessages(ConnectionInterface $conn, $params) {
        if(empty($params['room_id'])) {
            throw new \Exception('Не указан идентификатор чата!');
        }

        $room = $this->model->getRoom($params['room_id']);

        if(!$room) {
            throw new \Exception('Чата с идентификатором ' . (int)$params['room_id'] . ' не существует!');
        }

        if (isset($params['page'])) {
            $page = $params['page'];
        }else{
            $page = 1;
        }

        if(isset($params['last_date'])) {
            $start_date = $params['last_date'];
        } else {
            $start_date = '';
        }

        $filter_data = [
            'start_date' => $start_date,
            'limit' => $this->config->get('settings', 'messages_limit')
        ];

        $messages = $this->model->getMessages($room['room_id'], $filter_data);
        $total_messages = $this->model->getTotalMessages($room['room_id']);
        var_dump($total_messages);

        $count_pages = ceil($total_messages / $this->config->get('settings', 'messages_limit'));

        if($page == $count_pages) {
            $next_page = 0;
        } else {
            $next_page = $page + 1;
        }

        $users = $this->model->getUsers([
            'room_id' => $room['room_id']
        ]);

        $users_in_room = [];

        foreach($users as $user) {
            $users_in_room[$user['user_id']] = $user;
        }

        unset($users);

        $this->send($conn, 'getMessages', [
            'client_id'     => (int)$this->clientIds[$conn->resourceId],
            'users_in_room' => $users_in_room,
            'room_data'     => $room,
            'messages'      => $messages,
            'total_messages'=> $total_messages,
            'next_page'     => $next_page,
            'current_page'  => $page
        ]);
    }

    private function addMessage(ConnectionInterface $conn, $params) {
        if(empty($params['room_id'])) {
            throw new \Exception('Не указан идентификатор чата!');
        }

        if(!$this->model->getRoom($params['room_id'])) {
            throw new \Exception('Чата с идентификатором ' . (int)$params['room_id'] . ' не существует!');
        }

        if(empty($params['text'])) {
            throw new \Exception('Сообщение должно содержать минимум 1 символ!');
        }

        $message_id = $this->model->addMessage($params['room_id'], $this->clientIds[$conn->resourceId], $params['text']);
        $message_data = $this->model->getMessage($message_id);

        $users = $this->model->getUsers([
            'room_id' => $params['room_id']
        ]);

        $this->model->setMissed($users, $params['room_id'], $this->clientIds[$conn->resourceId]);

        foreach($users as $user){
            if(isset($this->clients[$user['user_id']])) {
                $this->send($this->clients[$user['user_id']], 'addMessage', [
                    'missed'        => $user['user_id'] != $this->clientIds[$conn->resourceId],
                    'message_data'  => $message_data
                ]);
            }
        }
    }

    private function searchUser(ConnectionInterface $conn, $params) {
        if(empty($params['query']) || empty(str_replace('@', '', $params['query']))){
            throw new \Exception('Введите никнейм или ФИО в строку поиска!');
        }

        $filter_data = [
            'limit' => $this->config->get('settings', 'base_limit'),
            'query' => $params['query'],
            'not_in'    => (int)$this->clientIds[$conn->resourceId]
        ];

        $users = $this->model->getUsers($filter_data);

        $this->send($conn, 'searchUser', $users);
    }

    private function removeMissed(ConnectionInterface $conn, $params) {
        if(empty($params['room_id'])) {
            throw new \Exception('Не указан идентификатор чата!');
        }

        $room = $this->model->getRoom($params['room_id']);

        if(!$room) {
            throw new \Exception('Чата с идентификатором ' . (int)$params['room_id'] . ' не существует!');
        }

        $this->model->removeMissed($room['room_id'], $this->clientIds[$conn->resourceId]);
    }
}
