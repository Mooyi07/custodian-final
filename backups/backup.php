<?php
require_once '../config/config.php';
require_once '../config/backup_config.php';
require_once '../config/conn.php';
require_once '../includes/sidebar.php';

// Initialize backup history
$backups = glob(BACKUP_DIR . '/' . BACKUP_PREFIX . '*');
$backupHistory = [];
foreach ($backups as $backup) {
    $backupHistory[] = [
        'date' => date("Y-m-d H:i:s", filemtime($backup)),
        'type' => 'System Backup',
        'size' => formatSize(folderSize($backup))
    ];
}
?>

<style>
    body {
        margin: 0;
        padding: 0;
        display: flex;
    }
    .sidebar {
        width: 230px;
        position: fixed;
        height: 100vh;
        top: 0;
        left: 0;
        padding-top: 20px;
    }
    .content {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
        position: absolute;
        top: 0;
    }
</style>

<div class="container mt-4" style="margin-left: 270px; max-width: calc(100% - 290px);">
    <!-- Backup Creation Section -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">System Backup</h5>
            <span class="badge bg-light text-primary" id="backupCount">0 backups stored</span>
        </div>
        <div class="card-body">
            <p class="text-muted">Create a backup of all system data and files. This will backup both the database and important files.</p>
            <form action="backup.php" method="post" class="mt-3" id="backupForm">
                <button type="submit" name="backup" class="btn btn-primary" id="backupBtn">
                    <i class="fas fa-download me-2"></i>Create Backup
                </button>
            </form>
            <!-- Progress Bar (Hidden by default) -->
            <div class="progress mt-3 d-none" id="backupProgress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="progressBar"></div>
            </div>
        </div>
    </div>

    <!-- Backup History Section -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Backup History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="backupHistory">
                        <!-- Backup history will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['backup'])) {
    // Set up the directories and database connection information
    $sourceDir = dirname(__DIR__); // The root directory of the application
    $timestamp = date("Y-m-d_H-i-s"); // Append timestamp for uniqueness
    $backupDir = BACKUP_DIR;
    
    // Create backup directory if it doesn't exist
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0777, true);
    }

    // Clean old backups if MAX_BACKUPS is set
    if (MAX_BACKUPS > 0) {
        $backups = glob($backupDir . '/' . BACKUP_PREFIX . '*');
        if (count($backups) >= MAX_BACKUPS) {
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            $toDelete = array_slice($backups, 0, count($backups) - MAX_BACKUPS + 1);
            foreach ($toDelete as $backup) {
                if (is_dir($backup)) {
                    array_map('unlink', glob("$backup/*.*"));
                    rmdir($backup);
                } else {
                    unlink($backup);
                }
            }
        }
    }

    // 1. Backup Files
    function copyFiles($source, $destination)
    {
        global $BACKUP_EXCLUDE_DIRS, $BACKUP_EXCLUDE_EXTENSIONS;
        
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }
        
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file != "." && $file != "..") {
                $sourcePath = $source . "/" . $file;
                $destPath = $destination . "/" . $file;
                
                // Skip excluded directories
                if (is_dir($sourcePath) && in_array($file, $BACKUP_EXCLUDE_DIRS)) {
                    continue;
                }
                
                // Skip excluded file extensions
                if (is_file($sourcePath)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array('.' . $extension, $BACKUP_EXCLUDE_EXTENSIONS)) {
                        continue;
                    }
                }
                
                if (is_dir($sourcePath)) {
                    copyFiles($sourcePath, $destPath);
                } else {
                    copy($sourcePath, $destPath);
                }
            }
        }
        closedir($dir);
    }

    $backupPath = $backupDir . '/' . BACKUP_PREFIX . $timestamp;
    copyFiles($sourceDir, $backupPath);

    // 2. Backup MySQL Database
    function backupDatabase($host, $username, $password, $database, $backupFile)
    {
        if (!is_dir(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0777, true);
        }

        $command = sprintf('mysqldump --opt -h "%s" -u "%s" -p"%s" "%s" > "%s" 2>&1', $host, $username, $password, $database, $backupFile);
        exec($command, $output, $status);
        
        if ($status !== 0) {
            error_log('Database backup failed. Error: ' . implode("\n", $output));
            return $status;
        }
        
        if (!file_exists($backupFile) || filesize($backupFile) === 0) {
            error_log('Database backup file is empty or does not exist');
            return 1;
        }
        
        return 0;
    }

    $dbBackupFile = $backupDir . '/' . DB_BACKUP_PREFIX . $timestamp . '.sql';
    $status = backupDatabase(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, $dbBackupFile);

    // Success or failure message
    if ($status === 0) {
        // Update backup history array
        $backups = glob(BACKUP_DIR . '/' . BACKUP_PREFIX . '*');
        $backupHistory = [];
        foreach ($backups as $backup) {
            $backupHistory[] = [
                'date' => date("Y-m-d H:i:s", filemtime($backup)),
                'type' => 'System Backup',
                'size' => formatSize(folderSize($backup))
            ];
        }
        
        echo "<div class='alert alert-success mt-3'>
            <h5><i class='fas fa-check-circle me-2'></i>Backup Successful!</h5>
            <p class='mb-0'>Files and database have been backed up successfully.</p>
            <small class='text-muted'>Backup location: " . $backupDir . "</small>
            <script>updateBackupHistory();</script>
        </div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>
            <h5><i class='fas fa-exclamation-circle me-2'></i>Backup Failed!</h5>
            <p class='mb-0'>There was an error backing up the database.</p>
        </div>";
    }
}

// Get backup history
$backups = glob(BACKUP_DIR . '/' . BACKUP_PREFIX . '*');
$backupHistory = [];
foreach ($backups as $backup) {
    $backupHistory[] = [
        'date' => date("Y-m-d H:i:s", filemtime($backup)),
        'type' => 'System Backup',
        'size' => formatSize(folderSize($backup))
    ];
}

// Helper function to calculate folder size
function folderSize($path) {
    $total_size = 0;
    $files = scandir($path);
    foreach($files as $file) {
        if ($file !== '.' && $file !== '..') {
            if (is_dir($path . '/' . $file)) {
                $total_size += folderSize($path . '/' . $file);
            } else {
                $total_size += filesize($path . '/' . $file);
            }
        }
    }
    return $total_size;
}

// Helper function to format size
function formatSize($size) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}
?>

<script>
// Update backup history table
function updateBackupHistory() {
    const backupHistory = <?php echo json_encode($backupHistory); ?>;
    const tbody = document.getElementById('backupHistory');
    const backupCount = document.getElementById('backupCount');
    
    tbody.innerHTML = '';
    backupCount.textContent = `${backupHistory.length} backup${backupHistory.length !== 1 ? 's' : ''} stored`;
    
    backupHistory.forEach(backup => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${backup.date}</td>
            <td><span class="badge bg-info">${backup.type}</span></td>
            <td>${backup.size}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-2" title="Download Backup">
                    <i class="fas fa-download"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" title="Delete Backup">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Show progress bar during backup
document.getElementById('backupForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    const progressDiv = document.getElementById('backupProgress');
    const progressBar = document.getElementById('progressBar');
    const backupBtn = document.getElementById('backupBtn');
    
    progressDiv.classList.remove('d-none');
    backupBtn.disabled = true;
    
    // Send backup request via AJAX
    const formData = new FormData(this);
    
    fetch('backup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        // Update progress to 100%
        progressBar.style.width = '100%';
        
        // Insert the response message
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const alertDiv = tempDiv.querySelector('.alert');
        if (alertDiv) {
            document.getElementById('backupForm').insertAdjacentElement('afterend', alertDiv);
        }
        
        // Reload the page to refresh backup history
        location.reload();
        
        // Reset progress bar after 1 second
        setTimeout(() => {
            progressDiv.classList.add('d-none');
            progressBar.style.width = '0%';
            backupBtn.disabled = false;
        }, 1000);
    })
    .catch(error => {
        console.error('Backup failed:', error);
        progressDiv.classList.add('d-none');
        progressBar.style.width = '0%';
        backupBtn.disabled = false;
        
        // Show error message
        const errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-danger mt-3';
        errorAlert.innerHTML = `
            <h5><i class='fas fa-exclamation-circle me-2'></i>Backup Failed!</h5>
            <p class='mb-0'>There was an error during the backup process. Please try again.</p>
        `;
        document.getElementById('backupForm').insertAdjacentElement('afterend', errorAlert);
    });

});

// Initialize backup history
updateBackupHistory();
</script>
