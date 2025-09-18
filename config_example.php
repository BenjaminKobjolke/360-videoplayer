<?php
// 360° Video Player Configuration Example
// Copy this file to config.php and modify the settings below

// Upload authentication password
$uploadPassword = 'change-this-password';

// Upload settings
$maxFileSize = 500 * 1024 * 1024; // 500MB max file size
$allowedExtensions = ['mp4', 'webm', 'ogg'];

// Media directory (relative to project root)
$mediaDirectory = 'media/';

// Session timeout (in seconds) - 30 days
$sessionTimeout = 30 * 24 * 60 * 60;
?>