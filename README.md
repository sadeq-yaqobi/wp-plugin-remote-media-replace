# Remote Media Replacer

A WordPress plugin that intelligently replaces local media URLs with remote ones when available, with automatic fallback to local images if the remote version doesn't exist.

## Description

Remote Media Replacer is designed for WordPress developers and site administrators who work with multiple environments (local, staging, production) and need to seamlessly handle media files across different servers. The plugin automatically detects when you're on a local environment and attempts to serve images from a remote server, falling back to local images when the remote version isn't available.

## Features

- **Smart URL Replacement**: Automatically replaces local upload URLs with remote ones
- **Fallback Protection**: Falls back to local images if remote images don't exist
- **Local Environment Detection**: Only activates on localhost/127.0.0.1 environments
- **Performance Optimized**: Uses HTTP HEAD requests to check image existence with minimal overhead
- **Easy Configuration**: Simple admin interface for setting remote URL
- **Non-Destructive**: Doesn't modify your database or actual file paths

## Installation

1. Download the plugin files
2. Upload the plugin folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the remote URL in **Settings > Remote Media Replacer**

## Configuration

After activation, navigate to **Settings > Remote Media Replacer** in your WordPress admin panel.

### Settings

- **Remote Uploads Base URL**: Enter the base URL of your main site's uploads directory
    - Example: `https://yourproductionsite.com/wp-content/uploads`
    - This should point to where your media files are stored on the remote server

## How It Works

1. **Environment Detection**: The plugin checks if you're running on a local environment (localhost or 127.0.0.1)
2. **URL Scanning**: On local environments, it scans all `<img>` tags in the final HTML output
3. **Remote Check**: For each local upload URL found, it checks if a corresponding remote image exists
4. **Smart Replacement**: If the remote image exists, it replaces the local URL; otherwise, it keeps the local URL
5. **Fallback Safety**: If remote images are unavailable, local images are served automatically

## Use Cases

- **Local Development**: Work with a local WordPress installation while accessing media from your live site
- **Staging Environments**: Test changes without needing to sync large media libraries
- **Content Migration**: Gradually transition media files between servers
- **Bandwidth Optimization**: Reduce local storage requirements during development

## Technical Details

### Functions

- `rmr_get_main_upload_url()`: Retrieves the configured remote upload URL
- `rmr_is_local()`: Detects if the current environment is local
- `rmr_replace_final_output()`: Processes HTML output and replaces image URLs
- `rmr_remote_image_exists()`: Checks if remote images exist using HTTP HEAD requests

### Performance Considerations

- Uses WordPress's `wp_remote_head()` function for efficient image existence checks
- 2-second timeout on remote requests to prevent hanging
- Only processes output on local environments
- Minimal overhead through targeted regex processing

## Requirements

- WordPress 4.0 or higher
- PHP 5.6 or higher
- `allow_url_fopen` enabled (for remote URL checking)

## Limitations

- Only activates on local environments (localhost/127.0.0.1)
- Only processes `<img>` tags in final HTML output
- Requires remote server to respond to HTTP HEAD requests
- 2-second timeout may not be sufficient for slow remote servers

## Troubleshooting

### Images Not Loading from Remote
- Verify the remote URL is correct and accessible
- Check if the remote server allows HEAD requests
- Ensure remote images have the same directory structure as local

### Plugin Not Working
- Confirm you're on a local environment (localhost/127.0.0.1)
- Verify the remote URL is configured in settings
- Check if WordPress can make external HTTP requests

### Performance Issues
- Consider the network latency to your remote server
- Monitor the number of remote requests being made
- Adjust timeout values if needed

## Security Notes

- Plugin only activates on local environments for security
- Uses WordPress's built-in HTTP functions for remote requests
- Properly escapes and validates all user inputs
- No database modifications are made

## Support

For issues, feature requests, or contributions, please contact the plugin author or refer to your development team's guidelines.

## Changelog

### Version 1.0
- Initial release
- Basic URL replacement functionality
- Admin settings interface
- Local environment detection
- Remote image existence checking

## License

This plugin is released under the same license as WordPress itself (GPL v2 or later).