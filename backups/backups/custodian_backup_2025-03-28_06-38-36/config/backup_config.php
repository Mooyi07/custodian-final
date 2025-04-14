<?php
// Backup Configuration Settings

// Define backup directory path
define('BACKUP_DIR', __DIR__ . '/../backups/backups');

// Define which directories to exclude from backup
$BACKUP_EXCLUDE_DIRS = [
    'backups',  // Don't backup the backup directory itself
    'node_modules',  // Skip node modules
    'vendor',  // Skip vendor directory if exists
    '.git'  // Skip git directory if exists
];

// Define which file extensions to exclude from backup
$BACKUP_EXCLUDE_EXTENSIONS = [
    '.log',  // Skip log files
    '.tmp',  // Skip temporary files
    '.cache'  // Skip cache files
];

// Maximum number of backups to keep (0 for unlimited)
define('MAX_BACKUPS', 5);

// Backup file prefix
define('BACKUP_PREFIX', 'custodian_backup_');

// Database backup settings
define('DB_BACKUP_PREFIX', 'db_backup_');