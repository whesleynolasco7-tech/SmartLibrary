<?php
/**
 * User class
 * Handles authentication for both Admin/Librarian and Student accounts.
 */
class User
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Attempt login.
     */
    public function login(string $email, string $password): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            'email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }

        return null;
    }

    /**
     * Register user
     */
    public function register(string $name, string $email, string $password, string $role = 'student'): int|false
    {
        $existing = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $existing->execute([
            'email' => $email
        ]);

        if ($existing->fetch()) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO users
            (name, email, password, role, created_at)
            VALUES
            (:name, :email, :password, :role, NOW())
        ");

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hash,
            'role' => $role
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Change password
     */
    public function changePassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
        ");

        return $stmt->execute([
            'password' => $hash,
            'id' => $id
        ]);
    }

    /**
 * Update profile picture
 */
public function updateProfilePicture(int $id, string $filename): bool
{
    $stmt = $this->db->prepare("
        UPDATE users
        SET profile_picture = :picture
        WHERE id = :id
    ");

    return $stmt->execute([
        'picture' => $filename,
        'id'      => $id
    ]);
}

    /**
     * Start Session
     */
    public static function startSession(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Default avatar
        $_SESSION['profile_picture'] = '';

        // Admin avatar comes from users table
        if (!empty($user['profile_picture'])) {
            $_SESSION['profile_picture'] = $user['profile_picture'];
        }

        // Student avatar comes from students table
        if ($user['role'] === 'student') {

            $db = Database::getInstance()->getConnection();

            $stmt = $db->prepare("
                SELECT profile_picture
                FROM students
                WHERE user_id = :user_id
                LIMIT 1
            ");

            $stmt->execute([
                'user_id' => $user['id']
            ]);

            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student && !empty($student['profile_picture'])) {
                $_SESSION['profile_picture'] = $student['profile_picture'];
            }
        }
    }

    /**
     * Logout
     */
    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}