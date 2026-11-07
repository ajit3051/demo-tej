<?php

/**
 * Tejas ERP - Authentication API
 * 
 * Endpoints:
 *   POST /api/auth.php?action=login    - Authenticate user, returns user details + JWT token
 *   POST /api/auth.php?action=logout   - Logout / terminate session
 *   GET  /api/auth.php?action=me       - Current authenticated user profile
 *   POST /api/auth.php?action=change_password - Change user password
 */

require_once __DIR__ . '/config.php';

$action = Request::input('action') ?? Request::input('route');
$method = Request::method();

switch ($action) {
    case 'login':
        if ($method !== 'POST') {
            Response::error("Method not allowed. Use POST.", 405);
        }

        $username = trim((string)Request::input('username'));
        $password = (string)Request::input('password');

        if (empty($username) || empty($password)) {
            Response::error("Username and password are required.", 422);
        }

        // Query user from tbl_user_master with user_type = 'api' condition
        $user = ApiDB::fetchOne(
            "SELECT id, user_code, user_name, mobile_no, store_name, location, user_type, password, status 
             FROM tbl_user_master 
             WHERE (user_name = :username OR user_code = :usercode) 
               AND LOWER(user_type) = 'api' 
             LIMIT 1",
            [':username' => $username, ':usercode' => $username]
        );

        if (!$user) {
            // Check if user exists with a different user_type for clear feedback
            $existing = ApiDB::fetchOne(
                "SELECT id, user_type FROM tbl_user_master 
                 WHERE user_name = :username OR user_code = :usercode LIMIT 1",
                [':username' => $username, ':usercode' => $username]
            );
            if ($existing && strtolower(trim((string)$existing['user_type'])) !== 'api') {
                Response::error("Access denied for this role.", 403);
            }
            Response::error("Invalid username or password.", 401);
        }

        if (isset($user['status']) && strtolower($user['status']) === 'inactive') {
            Response::error("Your account has been deactivated. Please contact administrator.", 403);
        }

        // Verify password (supports bcrypt hash or plain text legacy fallback)
        $dbPass = $user['password'];
        $valid = password_verify($password, $dbPass) || ($password === $dbPass);

        if (!$valid) {
            Response::error("Invalid username or password.", 401);
        }

        // Generate token and session
        unset($user['password']);
        $token = Auth::generateToken($user);

        // Sync with session for browser compatibility
        $_SESSION['login_user'] = $user['user_name'];
        $_SESSION['user_code']  = $user['user_code'];
        $_SESSION['user_type']  = $user['user_type'];
        $_SESSION['store_name'] = $user['store_name'];
        $_SESSION['IS_LOGIN']   = 'yes';

        Response::success([
            'token' => $token,
            'user'  => $user,
        ], "Login successful.");
        break;

    case 'logout':
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        Response::success(null, "Logged out successfully.");
        break;

    case 'me':
    case 'profile':
        $user = Auth::requireAuth();
        // Fetch full profile from database
        $profile = ApiDB::fetchOne(
            "SELECT id, user_code, user_name, mobile_no, store_name, location, user_type, gender, status, created_date 
             FROM tbl_user_master 
             WHERE user_code = :code OR user_name = :name LIMIT 1",
            [':code' => $user['user_code'] ?? '', ':name' => $user['user_name'] ?? '']
        );
        Response::success($profile ?: $user, "User profile retrieved.");
        break;

    case 'change_password':
        $user = Auth::requireAuth();
        $oldPass = (string)Request::input('old_password');
        $newPass = (string)Request::input('new_password');

        if (empty($oldPass) || empty($newPass)) {
            Response::error("Old password and new password are required.", 422);
        }

        $row = ApiDB::fetchOne(
            "SELECT password FROM tbl_user_master WHERE user_code = :code LIMIT 1",
            [':code' => $user['user_code']]
        );

        if (!$row || (!password_verify($oldPass, $row['password']) && $oldPass !== $row['password'])) {
            Response::error("Incorrect old password.", 400);
        }

        $hashed = password_hash($newPass, PASSWORD_BCRYPT);
        ApiDB::update('tbl_user_master', ['password' => $hashed], 'user_code = :code', [':code' => $user['user_code']]);

        Response::success(null, "Password changed successfully.");
        break;

    default:
        Response::error("Unknown action. Available actions: login, logout, me, change_password", 400);
        break;
}
