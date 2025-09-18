# 360° Video Player

A web-based 360-degree video player with upload functionality, built with Video.js and videojs-vr plugin.

## Features

- **360° Video Playback**: Mouse/touch controls to look around
- **Video Selection**: Burger menu to switch between uploaded videos
- **Upload Interface**: Password-protected video upload with progress indicator
- **File Management**: Delete uploaded videos through the interface
- **Responsive Design**: Works on desktop and mobile devices

## Installation

1. **Clone or download** this repository to your web server
2. **Copy configuration file**:
   ```bash
   cp config_example.php config.php
   ```
3. **Edit configuration** in `config.php`:
   - Change `$uploadPassword` to your desired password
   - Adjust file size limits and allowed extensions if needed
4. **Create media directory** (if it doesn't exist):
   ```bash
   mkdir media
   chmod 755 media
   ```

## Configuration

Edit `config.php` to customize:

- **`$uploadPassword`**: Password for upload interface (default: 'change-this-password')
- **`$maxFileSize`**: Maximum upload file size in bytes (default: 500MB)
- **`$allowedExtensions`**: Allowed video formats (default: ['mp4', 'webm', 'ogg'])
- **`$mediaDirectory`**: Directory for storing videos (default: 'media/')
- **`$sessionTimeout`**: Login session duration in seconds (default: 30 days)

## Usage

### Playing Videos

1. **Access the player**: Navigate to `http://yoursite.com/360_player/`
2. **Look around**: Drag with mouse or touch to explore the 360° video
3. **Switch videos**: Click the hamburger menu (☰) in the top-right corner
4. **Select video**: Click any video from the dropdown to switch to it

### Uploading Videos

1. **Access upload interface**: Navigate to `http://yoursite.com/360_player/upload/`
2. **Login**: Enter the password you set in `config.php`
3. **Select video**: Choose an MP4, WebM, or OGG file (max 500MB by default)
4. **Upload**: Click "Upload Video" and wait for the progress bar to complete
5. **Manage videos**: View all uploaded videos and delete unwanted ones

### File Requirements

- **Supported formats**: MP4 (recommended), WebM, OGV
- **360° videos**: Should be equirectangular projection
- **File size**: Configurable, default maximum is 500MB
- **Resolution**: Any resolution supported by the browser

## Directory Structure

```
360_player/
├── index.php              # Main video player
├── config.php             # Configuration (create from config_example.php)
├── config_example.php     # Configuration template
├── README.md              # This file
├── .gitignore            # Git ignore file
├── css/
│   ├── player.css        # Player styles
│   └── video-js.css      # Video.js styles
├── js/
│   ├── player.js         # Player functionality
│   ├── video.min.js      # Video.js library
│   └── videojs-vr.min.js # VR plugin
├── media/                # Video storage directory
└── upload/
    └── index.php         # Upload interface
```

## Requirements

- **Web server** with PHP 7.0+ (Apache, Nginx, etc.)
- **PHP modules**: Standard PHP installation
- **Modern browser** with WebGL support
- **HTTPS recommended** for better performance and security

## Browser Support

- **Chrome/Edge**: Full support
- **Firefox**: Full support
- **Safari**: Full support (iOS 13.4+ for best experience)
- **Mobile browsers**: Touch controls supported

## Troubleshooting

### Videos won't play
- Check that video files are in the `media/` directory
- Ensure video files are in supported format (MP4 recommended)
- Verify file permissions are correct (readable by web server)

### Upload not working
- Check that `config.php` exists and contains correct password
- Verify `media/` directory has write permissions
- Check PHP upload limits in `php.ini` if files are large
- Ensure the upload directory has sufficient disk space

### 360° view not working
- Verify browser supports WebGL
- Check browser console for JavaScript errors
- Ensure video files are properly encoded 360° content

## Security

- **Password protection**: Upload interface requires authentication
- **Session management**: Configurable session timeout
- **File validation**: Only allowed file types can be uploaded
- **Path sanitization**: Prevents directory traversal attacks
- **Configuration security**: Sensitive settings in separate config file

## License

This project uses the following open-source libraries:
- [Video.js](https://videojs.com/) - Apache License 2.0
- [videojs-vr](https://github.com/videojs/videojs-vr) - Apache License 2.0

## Support

For issues and feature requests, please check the browser console for errors and ensure all requirements are met.