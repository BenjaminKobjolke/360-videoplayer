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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>360° Video Player</title>
    <meta name="description" content="Interactive 360-degree video player">

    <link href="css/video-js.css" rel="stylesheet">
    <link href="css/player.css" rel="stylesheet">

    <script src="js/video.min.js"></script>
    <script src="js/videojs-vr.min.js"></script>
</head>
<body>
    <video id="player" class="video-js vjs-default-skin" playsinline crossorigin muted autoplay loop width="960" height="540">
        <source src="<?php echo htmlspecialchars($newestVideo); ?>" type="video/mp4">
    </video>

    <!-- Burger Menu -->
    <div class="burger-menu">
        <div class="burger-icon" id="burgerIcon">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="video-menu" id="videoMenu">
            <h3>Select Video</h3>
            <?php if (empty($allVideos)): ?>
                <div class="no-videos">No videos available</div>
            <?php else: ?>
                <?php foreach ($allVideos as $video): ?>
                    <div class="video-item" data-video="<?php echo htmlspecialchars($video); ?>">
                        <div class="video-name"><?php echo htmlspecialchars(basename($video)); ?></div>
                        <div class="video-date"><?php echo date('Y-m-d H:i', filemtime($video)); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/player.js"></script>
</body>
</html>