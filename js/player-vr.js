// Initialize Video.js player with VR plugin (Meta Quest optimized)
const player = videojs('player');

player.ready(() => {
    // Initialize VR plugin with 360 video settings for Meta Quest
    player.vr({
        projection: '360',
        motionControls: true  // Enable device motion for VR headsets
    });

    console.log('360° Video Player initialized (VR Mode)');
    console.log('Put on your Meta Quest to view in VR');
});

// Burger menu functionality
document.addEventListener('DOMContentLoaded', function() {
    const burgerIcon = document.getElementById('burgerIcon');
    const videoMenu = document.getElementById('videoMenu');
    const videoItems = document.querySelectorAll('.video-item[data-video]');

    // Get current video source
    let currentVideoSrc = player.currentSrc();

    // Mark current video in menu
    function updateCurrentVideo() {
        videoItems.forEach(item => {
            const videoPath = item.getAttribute('data-video');
            if (currentVideoSrc.includes(videoPath) || currentVideoSrc.endsWith(videoPath)) {
                item.classList.add('current');
            } else {
                item.classList.remove('current');
            }
        });
    }

    // Initial update
    updateCurrentVideo();

    // Toggle burger menu
    burgerIcon.addEventListener('click', function() {
        burgerIcon.classList.toggle('active');
        videoMenu.classList.toggle('active');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!burgerIcon.contains(event.target) && !videoMenu.contains(event.target)) {
            burgerIcon.classList.remove('active');
            videoMenu.classList.remove('active');
        }
    });

    // Handle video selection
    videoItems.forEach(item => {
        item.addEventListener('click', function() {
            const videoPath = this.getAttribute('data-video');

            // Change video source
            player.src({
                src: videoPath,
                type: 'video/mp4'
            });

            // Update current video tracking
            currentVideoSrc = videoPath;
            updateCurrentVideo();

            // Close menu
            burgerIcon.classList.remove('active');
            videoMenu.classList.remove('active');

            // Optional: Reset VR view to center
            setTimeout(() => {
                if (player.vr && player.vr().camera) {
                    const camera = player.vr().camera;
                    camera.rotation.set(0, 0, 0);
                    camera.updateMatrix();
                    camera.updateMatrixWorld();
                }
            }, 500);

            console.log('Switched to video:', videoPath);
        });
    });

    // Add hover effects
    videoItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            if (!this.classList.contains('current')) {
                this.style.background = 'rgba(255, 255, 255, 0.1)';
            }
        });

        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('current')) {
                this.style.background = '';
            }
        });
    });
});
