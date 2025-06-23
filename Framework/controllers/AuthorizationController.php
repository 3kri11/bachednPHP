<?php

namespace Framework\controllers;

use Framework\db\Database;

class AuthorizationController {
    private $db;

    public function __construct() {
        $config = require getPath() . "helpers/config.php";
        $this->db = new Database($config);
    }

    public function login(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = htmlspecialchars($data['username'] ?? '');
        $email = htmlspecialchars($data['email'] ?? '');
        $password = htmlspecialchars($data['password'] ?? '');

        $options = [
            'username' => $username,
            'email' => $email,
        ];

        $controls = [
            'count' => 'single'
        ];

        $user = $this->db->query(
            "SELECT * FROM users WHERE korisnicko_ime = :username AND email = :email",
            $options,
            $controls
        );

        $errors = [];

        if (!$user) {
            $errors[] = "Invalid username,email or password!";
            http_response_code(401);
            return ['errors' => $errors];
        }

        if (!password_verify($password, $user['sifra'])) {
            $errors[] = "Invalid username,email or password!";
            return ['errors' => $errors];
        }

        http_response_code(200);
        return [$user,'message' => 'User successfully logged in!'];
    }

    public function register(): array|false|string
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $firstName = htmlspecialchars($data['firstName'] ?? '');
        $lastName = htmlspecialchars($data['lastName'] ?? '');
        $username = htmlspecialchars($data['username'] ?? '');
        $email = htmlspecialchars($data['email'] ?? '');
        $password = htmlspecialchars($data['password'] ?? '');
        $is_admin = htmlspecialchars($data['is_admin'] ?? '');

        $options = [
            'username' => $username,
            'email' => $email,
        ];

        $controls = [
            'count' => 'single'
        ];

        $user = $this->db->query(
            "SELECT * FROM users WHERE korisnicko_ime = :username OR email = :email",
            $options,
            $controls
        );

        $errors = [];

        if ($user) {
            $errors[] = "User with that email or username already exists!";
            http_response_code(409);
            return ['errors' => $errors];
        } else {


        $options = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password,PASSWORD_DEFAULT),
            'firstName' => ucfirst($firstName),
            'lastName' => ucfirst($lastName),
            'is_admin' => $is_admin,
        ];

        $this->db->query(
            "INSERT INTO users (ime,prezime,korisnicko_ime, email, sifra,is_admin) VALUES (:firstName,:lastName,:username, :email, :password, :is_admin)",
            $options,
            $controls
        );

        http_response_code(201);
        return json_encode("User successfully registered!");
        }
    }
    public function changePassword() {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = htmlspecialchars($data['email'] ?? '');
        $password = htmlspecialchars($data['password'] ?? '');
        $newPassword = htmlspecialchars($data['newPassword'] ?? '');

        if ($password === $newPassword) {
            $errors[] = "New password cannot be the same as the old one!";
            return ['errors' => $errors];
        }

        $options = [
            'email' => $email,
        ];

        $controls = [
            'count' => 'single'
        ];

        $user = $this->db->query(
            "SELECT * FROM users WHERE email = :email",
            $options,
            $controls
        );

        $options = [
            'email' => $email,
            'password' => password_hash($newPassword,PASSWORD_DEFAULT),
        ];

        if (!$user) {
            http_response_code(404);
            $errors[] = "User not found!";
            return ['errors' => $errors];
        } else if (!password_verify($password,$user['sifra'])) {
            http_response_code(401);
            $errors[] = "Invalid old password!";
            return ['errors' => $errors];
        }
        else {
            $this->db->query(
                "UPDATE users SET sifra = :password WHERE email = :email",
                $options,
                $controls
            );
            http_response_code(200);
            return json_encode("Password successfully changed!");
        }
    }

}