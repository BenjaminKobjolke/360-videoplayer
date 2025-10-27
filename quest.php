<?php
// Get all MP4 files from media folder
$mediaDir = 'media/';
$files = glob($mediaDir . '*.mp4');
$newestVideo = '';
$allVideos = [];

if (!empty($files)) {
    // Sort files by modification time (newest first)
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    $newestVideo = $files[0];
    $allVideos = $files;
} else {
    // Fallback to default if no videos found
    $newestVideo = 'media/360.mp4';
}

// Set page-specific variables for VR mode
$pageTitle = '360° Video Player - VR Mode';
$pageDescription = 'Interactive 360-degree video player for Meta Quest';
$playerScript = 'js/player-vr.js';
$showControls = true;  // Enable video controls for fullscreen/VR button

// Include shared layout
require 'player-layout.php';
