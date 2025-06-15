<?php

/**
 * Get an environment variable or return default value
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env(string $key, $default = null)
{
    static $env = null;
    if ($env === null) {
        $env = [];
        $envPath = __DIR__ . '/../.env';
        if (file_exists($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if ($line && $line[0] !== '#' && strpos($line, '=') !== false) {
                    [$k, $v] = explode('=', $line, 2);
                    $env[trim($k)] = trim($v);
                }
            }
        }
    }
    return array_key_exists($key, $env) ? env_cast($env[$key]) : $default;
}

function env_cast($value) {
    $l = strtolower($value);
    if ($l === 'true') return true;
    if ($l === 'false') return false;
    if ($l === 'null') return null;
    if (is_numeric($value)) return $value + 0;
    return $value;
}

/**
 * Validate PayTabs redirect signature
 * @param array $post_values
 * @param string $serverKey
 * @return bool
 */
function is_valid_redirect(array $post_values, string $serverKey)
{
    if (empty($post_values) || !array_key_exists('signature', $post_values)) {
        return false;
    }
    $requestSignature = $post_values["signature"];
    unset($post_values["signature"]);
    $fields = array_filter($post_values);
    ksort($fields);
    $query = http_build_query($fields);
    return is_genuine($query, $requestSignature, $serverKey);
}

/**
 * Check if signature matches expected hash
 * @param string $data
 * @param string $requestSignature
 * @param string $serverKey
 * @return bool
 */
function is_genuine(string $data, string $requestSignature, string $serverKey)
{
    $signature = hash_hmac('sha256', $data, $serverKey);
    return hash_equals($signature, $requestSignature);
}

/**
 * Get request parameter
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function request(string $key, $default = null)
{
    return $_GET[$key] ?? $_POST[$key] ?? $default;
}

/**
 * Redirect to another page
 *
 * @param string $url
 * @return void
 */
function redirect(string $url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Go back to previous page
 *
 * @return void
 */
function back()
{
    redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
}

/**
 * Flash message to session
 *
 * @param string $message
 * @param string $type
 * @return void
 */
function flash(string $message, string $type = 'success')
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get flash message
 *
 * @return array|null
 */
function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render a view
 *
 * @param string $view
 * @param array $data
 * @return void
 */
function view(string $view, array $data = [])
{
    extract($data);
    $viewPath = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';
    require $viewPath;
}

/**
 * Render a colored status badge for an order status (Tailwind CSS).
 *
 * @param string $status
 * @return string
 */
function status_badge($status) {
    $status = strtolower($status);
    $badgeClasses = [
        'completed' => 'bg-green-100 text-green-800',
        'success' => 'bg-green-100 text-green-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'processing' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'failed' => 'bg-red-100 text-red-800',
    ];
    $class = $badgeClasses[$status] ?? 'bg-gray-100 text-gray-800';
    return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded ' . $class . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
}
