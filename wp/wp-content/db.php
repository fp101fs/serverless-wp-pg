<?php
/**
 * Hybrid DB drop-in for ServerlessWP.
 * Supports PostgreSQL (via pg4wp) and SQLite (via sqlite-database-integration).
 */

// 1. Check for PostgreSQL (Neon / Vercel Postgres)
if (isset($_ENV['POSTGRES_URL']) || isset($_ENV['DATABASE_URL']) || isset($_ENV['NEON_DATABASE_URL']) || (defined('DB_DRIVER') && DB_DRIVER === 'pgsql')) {
    if (file_exists(__DIR__ . '/pg4wp/db.php')) {
        require_once __DIR__ . '/pg4wp/db.php';
        // pg4wp's db.php will define $wpdb
        return;
    }
}

// 2. Check for SQLite (S3 / Sandbox)
if (isset($_ENV['SQLITE_S3_BUCKET']) || isset($_ENV['SERVERLESSWP_DATA_SECRET']) || (defined('DB_ENGINE') && DB_ENGINE === 'sqlite')) {
    $sqlite_dir = __DIR__ . '/plugins/sqlite-database-integration';
    if (file_exists($sqlite_dir . '/wp-includes/sqlite/db.php')) {
        if (!defined('DATABASE_TYPE')) define('DATABASE_TYPE', 'sqlite');
        if (!defined('DB_ENGINE')) define('DB_ENGINE', 'sqlite');
        require_once $sqlite_dir . '/wp-includes/sqlite/db.php';
        // sqlite plugin's db.php will define $wpdb
        return;
    }
}

// 3. Fallback to default MySQL
// If we reach here, we don't define $wpdb, so WordPress will load the default wpdb class.
