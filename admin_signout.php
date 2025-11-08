<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session completely
session_destroy();

// Optional: clear session cookie (extra security)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect with a friendly message
echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="refresh" content="2;url=index.php">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Logging Out...</title>
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="text-center">
        <div class="spinner-border text-success mb-3" role="status"></div>
        <h4 class="text-success">Logging you out...</h4>
        <p class="text-muted">Redirecting to homepage in 2 seconds.</p>
    </div>
</body>
</html>';
exit;
?>
