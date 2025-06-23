<?php

namespace Framework\controllers;
use Framework\db\Database;
use Throwable;

class HomeController {
    private $db;
    public function __construct() {
        $config = require getPath() . "helpers/config.php";
        $this->db = new Database($config);
    }

    public function news() {
        $userID = htmlspecialchars($_GET['userID'] ?? '');
        $limit = (int) htmlspecialchars($_GET['limit'] ?? 50);
        $status = htmlspecialchars($_GET['status'] ?? '');

        $options = [
            'userID' => $userID,
        ];

        $user = null;

        try {
            $user = $this->db->query("SELECT * FROM users WHERE ID = :userID", $options, ['count' => 'single']);
        } catch (Throwable $e) {
            return ['errors' => $e->getMessage()];
        }

        if (!$user) {
            return ['message' => 'User with that ID does not exist!'];
        }

        $options = [
            'status' => $status,
            'limit' => 50,
        ];

        $controls = [
            'count' => 'all',
        ];

        try {
            $result = $this->db->query("SELECT * FROM posts WHERE status = :status LIMIT :limit", $options, $controls);
        } catch (Throwable $e) {
            return ['message' => 'Problem with database!'];
        }

        if (!empty($result)) {
            return $result;
        } else {
            return 'No news found!';
        }
    }


    public function getNewsPost($params) {
        $ID = (int) htmlspecialchars($params['id']);
        $userID = htmlspecialchars($_GET['userID'] ?? '');

        $controls = [
            'count' => 'single'
        ];

        $user = null;

        try {
            $user = $this->db->query("SELECT * FROM users WHERE ID = :userID", ['userID' => $userID],$controls);

            if (!$user) {
                return ['message' => 'User with that ID does not exist!'];
            }
        } catch (Throwable $e) {
            return ['errors' => $e->getMessage()];
        }

        try {
            if ($user['is_admin'] === 0) {
                return $this->db->query(
                    "SELECT * FROM posts WHERE post_id = :id AND status = 'approved'",
                    ['id' => $ID],
                    $controls
                );
            } else {
                return $this->db->query(
                    "SELECT * FROM posts WHERE post_id = :id",
                    ['id' => $ID],
                    $controls
                );
            }
        } catch (Throwable $e) {
            return ['errors' => $e->getMessage()];
        }
    }
}