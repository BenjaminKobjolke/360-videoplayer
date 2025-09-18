<?php
session_start();

// Load configuration
require_once '../config.php';

// Use configuration variables
$password = $uploadPassword;
$maxFileSize = $maxFileSize;
$allowedExtensions = $allowedExtensions;

// Media directory is one level up
$mediaDir = '../' . $mediaDirectory;

// Check if user is logged in
$isAuthenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

// Handle login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['authenticated'] = true;
        $isAuthenticated = true;
        $message = 'Login successful!';
        $messageType = 'success';
    } else {
        $message = 'Invalid password!';
        $messageType = 'error';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Handle file deletion
if ($isAuthenticated && isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']); // Sanitize filename
    $filePath = $mediaDir . $fileToDelete;

    if (file_exists($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'mp4') {
        if (unlink($filePath)) {
            $message = 'File deleted successfully: ' . $fileToDelete;
            $messageType = 'success';
        } else {
            $message = 'Failed to delete file: ' . $fileToDelete;
            $messageType = 'error';
        }
    } else {
        $message = 'File not found: ' . $fileToDelete;
        $messageType = 'error';
    }

    // Redirect to remove delete parameter from URL
    header('Location: index.php');
    exit;
}

// Handle file upload
if ($isAuthenticated && isset($_FILES['video'])) {
    $uploadedFile = $_FILES['video'];

    if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
        $fileName = $uploadedFile['name'];
        $fileSize = $uploadedFile['size'];
        $fileTmp = $uploadedFile['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file extension
        if (!in_array($fileExt, $allowedExtensions)) {
            $message = 'Invalid file format! Only MP4, WebM, and OGG files are allowed.';
            $messageType = 'error';
        }
        // Validate file size
        elseif ($fileSize > $maxFileSize) {
            $message = 'File too large! Maximum size is ' . ($maxFileSize / 1024 / 1024) . 'MB.';
            $messageType = 'error';
        }
        // Upload file
        else {
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
            $destination = $mediaDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destination)) {
                $message = 'File uploaded successfully: ' . $newFileName;
                $messageType = 'success';
            } else {
                $message = 'Upload failed! Please try again.';
                $messageType = 'error';
            }
        }
    } else {
        $message = 'Upload error: ' . $uploadedFile['error'];
        $messageType = 'error';
    }
}

// Get list of uploaded videos
$videos = glob($mediaDir . '*.mp4');
usort($videos, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>360° Video Upload</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="password"]:focus,
        input[type="file"]:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 12px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            position: relative;
        }
        button:hover:not(:disabled) {
            background: #5a67d8;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .upload-progress {
            display: none;
            margin-top: 10px;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s;
            position: relative;
        }
        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .upload-status {
            text-align: center;
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .message {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .logout-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
        .video-list {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }
        .video-list h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .video-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .video-info {
            flex-grow: 1;
        }
        .video-name {
            color: #555;
            word-break: break-all;
            margin-bottom: 4px;
        }
        .video-date {
            color: #999;
            font-size: 14px;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-left: 10px;
            transition: background 0.3s;
        }
        .delete-btn:hover {
            background: #c82333;
        }
        .no-videos {
            color: #999;
            text-align: center;
            padding: 20px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>360° Video Upload</h1>

        <?php if (isset($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!$isAuthenticated): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required autofocus>
                </div>
                <button type="submit">Login</button>
            </form>
        <?php else: ?>
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="form-group">
                    <label for="video">Select 360° Video File:</label>
                    <input type="file" id="video" name="video" accept=".mp4,.webm,.ogg" required>
                </div>
                <button type="submit" id="uploadBtn">Upload Video</button>

                <div class="upload-progress" id="uploadProgress">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="upload-status" id="uploadStatus">
                        <span class="spinner"></span>
                        Uploading video... Please wait
                    </div>
                </div>
            </form>

            <div class="video-list">
                <h2>Uploaded Videos</h2>
                <?php if (empty($videos)): ?>
                    <div class="no-videos">No videos uploaded yet.</div>
                <?php else: ?>
                    <?php foreach ($videos as $video): ?>
                        <div class="video-item">
                            <div class="video-info">
                                <div class="video-name"><?php echo htmlspecialchars(basename($video)); ?></div>
                                <div class="video-date"><?php echo date('Y-m-d H:i', filemtime($video)); ?></div>
                            </div>
                            <button class="delete-btn" onclick="confirmDelete('<?php echo htmlspecialchars(basename($video)); ?>')">Delete</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <a href="?logout" class="logout-link">Logout</a>
            <br>
            <a href="../" class="back-link">← Back to Player</a>
        <?php endif; ?>
    </div>

    <script>
        function confirmDelete(filename) {
            if (confirm('Are you sure you want to delete "' + filename + '"?\n\nThis action cannot be undone.')) {
                window.location.href = '?delete=' + encodeURIComponent(filename);
            }
        }

        // Upload progress handling
        document.addEventListener('DOMContentLoaded', function() {
            const uploadForm = document.getElementById('uploadForm');
            const uploadBtn = document.getElementById('uploadBtn');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressFill = document.getElementById('progressFill');
            const uploadStatus = document.getElementById('uploadStatus');
            const videoInput = document.getElementById('video');

            if (uploadForm) {
                uploadForm.addEventListener('submit', function(e) {
                    const fileInput = videoInput.files[0];

                    if (!fileInput) {
                        return;
                    }

                    // Show progress UI immediately
                    uploadBtn.disabled = true;
                    uploadBtn.textContent = 'Uploading...';
                    uploadProgress.style.display = 'block';

                    // Simulate progress for large files
                    let progress = 0;
                    const progressInterval = setInterval(() => {
                        if (progress < 90) { // Stop at 90% and wait for server response
                            progress += Math.random() * 15;
                            if (progress > 90) progress = 90;
                            progressFill.style.width = progress + '%';

                            if (progress < 30) {
                                uploadStatus.innerHTML = '<span class="spinner"></span>Starting upload...';
                            } else if (progress < 60) {
                                uploadStatus.innerHTML = '<span class="spinner"></span>Uploading video... Please wait';
                            } else {
                                uploadStatus.innerHTML = '<span class="spinner"></span>Almost done... Processing video';
                            }
                        }
                    }, 200);

                    // Store the interval so it can be cleared when the page reloads
                    window.uploadProgressInterval = progressInterval;
                });

                // Show file size when file is selected
                videoInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        console.log('Selected file size: ' + sizeMB + ' MB');

                        // Reset any previous progress
                        uploadProgress.style.display = 'none';
                        uploadBtn.disabled = false;
                        uploadBtn.textContent = 'Upload Video';
                        progressFill.style.width = '0%';
                    }
                });
            }
        });

        // Clear progress interval if page is about to unload
        window.addEventListener('beforeunload', function() {
            if (window.uploadProgressInterval) {
                clearInterval(window.uploadProgressInterval);
            }
        });
    </script>
</body>
</html>