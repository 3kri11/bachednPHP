<?php

namespace Framework\controllers;
use Framework\db\Database;
use Throwable;

class ActionsController {
    private Database $db;
    public function __construct() {
        $config = require getPath() . "helpers/config.php";
        $this->db = new Database($config);
    }

    public function addNews(): array|string
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $content = htmlspecialchars($data['content'] ?? '');
        $title = htmlspecialchars($data['title'] ?? '');
        $author_name = htmlspecialchars($data['author_name'] ?? '');
        $created_at = htmlspecialchars($data['created_at'] ?? '');
        $category = htmlspecialchars($data['category'] ?? '');
        $author_id = htmlspecialchars($data['author_id'] ?? '');
        $status = 'pending';

        $options = [
            'content' => $content,
            'title' => $title,
            'author_name' => $author_name,
            'created_at' => $created_at,
            'category' => $category,
            'author_id' => $author_id,
            'status' => $status,
        ];

        $errors = [];

        try {
            $this->db->query(
                "INSERT INTO posts (content,news_title,author_name,created_at,category,author_id,status) VALUES (:content,:title,:author_name, :created_at, :category, :author_id, :status)",
                $options
            );
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
            return $errors;
        }

        return 'News Post Added Successfully!';
    }

    public function editNews(array $params): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userID = htmlspecialchars($data['user_id'] ?? '');
        $newsID = (int) htmlspecialchars($params['id'] ?? '');

        $options = [
            'userID' => $userID,
            'newsID' => $newsID,
        ];

        $post = null;

        $errors = [];

        try {
            $post = $this->db->query(
                "SELECT * FROM posts WHERE post_id = :newsID AND author_id = :userID",
                $options,
            );
        } catch (Throwable $e) {
            http_response_code(404);
            $errors[] = $e->getMessage();
            return ['errors' => $errors,'message' => 'Invalid news ID or author ID!'];
        };

        if (!$post) {
            http_response_code(404);
            return ['errors' => $errors,'message' => 'Invalid news ID or author ID!'];
        }

        $content = htmlspecialchars($data['content'] ?? '');
        $title = htmlspecialchars($data['title'] ?? '');
        $author_name = htmlspecialchars($data['author_name'] ?? '');
//        $created_at = htmlspecialchars($data['created_at'] ?? '');
        $category = htmlspecialchars($data['category'] ?? '');
//        $author_id = htmlspecialchars($data['author_id'] ?? '');
        $status = 'pending';

        $options = [
            'content' => $content,
            'title' => $title,
            'author_name' => $author_name,
            'category' => $category,
            'status' => $status,
            'newsID' => $newsID,
        ];

        try {
            $this->db->query("UPDATE posts SET content = :content,news_title = :title,author_name = :author_name,category = :category,status = :status 
             WHERE post_id = :newsID",$options);
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
            return ['errors' => $errors,'message' => 'Invalid news ID or author ID!'];
        }

        return ['status' => 'success', 'message' => 'News post updated successfully!'];
    }

    public function deleteNews(array $params) {
        $data = json_decode(file_get_contents('php://input'), true);
        $userID = htmlspecialchars($data['user_id'] ?? '');
        $newsID = htmlspecialchars($params['id'] ?? '');

        $options = [
            'userID' => $userID,
            'newsID' => $newsID,
        ];

        $post = null;

        $errors = [];

        try {
            $post = $this->db->query(
                "SELECT * FROM posts WHERE post_id = :newsID AND author_id = :userID",
                $options
            );
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
            return ['errors' => $errors,'message' => 'Invalid news ID or author ID!'];
        };

        if (!$post) {
            return ['errors' => $errors,'message' => 'Invalid news ID or author ID!'];
        }

        try {
            $this->db->query("DELETE FROM posts WHERE post_id = :newsID",['newsID' => $newsID]);
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
            return ['errors' => $errors,'message' => 'Invalid news ID or author ID!'];
        }

        return ['status' => 'success', 'message' => 'News post deleted successfully!'];
    }

    public function approveNews(array $params) {
        $data = json_decode(file_get_contents('php://input'), true);
        $userID = htmlspecialchars($data['user_id'] ?? '');
        $status = htmlspecialchars($data['status'] ?? '');
        $newsID = htmlspecialchars($params['id'] ?? '');

        $user = null;

        $options = [
            'userID' => $userID,
        ];
        try {
            $user = $this->db->query("SELECT * FROM users WHERE ID = :userID AND is_admin = 1",$options);
        } catch (Throwable $e) {
            return ['errors' => $e->getMessage(),'message' => 'Current user is not an admin!'];
        }

        if (!$user) {
            return ['message' => 'Current user is not an admin!'];
        }

        $options = [
            'newsID' => $newsID,
            'status' => $status
        ];

        try {
            $this->db->query("UPDATE posts SET status = :status WHERE post_ID = :newsID ",$options);
        } catch (Throwable $e) {
            return ['errors' => $e->getMessage(),'message' => 'Invalid news ID!'];
        }

        return 'News Post Approved Successfully!';
    }
}