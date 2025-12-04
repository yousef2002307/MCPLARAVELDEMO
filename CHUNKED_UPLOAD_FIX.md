# Chunked Video Upload - Configuration Fix

## Issue Fixed

**Error**: `File has a size of 13.64 MB which is greater than the maximum allowed 10 MB`

**Root Cause**: Spatie Media Library had a default maximum file size limit of 10 MB.

**Solution**: Updated `config/media-library.php` to increase the limit to **500 MB**.

---

## Configuration Changes

### File: `config/media-library.php`

```php
// Before (Line 15)
'max_file_size' => 1024 * 1024 * 10, // 10MB

// After (Line 15)
'max_file_size' => 1024 * 1024 * 500, // 500MB
```

This allows the chunked upload system to handle large video files up to 500 MB.

---

## Additional PHP Configuration (Optional)

If you plan to upload very large files (> 100 MB), you may also need to adjust PHP settings:

### Option 1: Via `.htaccess` (Apache)

Create or edit `.htaccess` in your Laravel root:

```apache
php_value upload_max_filesize 500M
php_value post_max_size 500M
php_value max_execution_time 300
php_value max_input_time 300
```

### Option 2: Via `php.ini` (Recommended for Laragon)

Edit your `php.ini` file (in Laragon: `laragon/bin/php/php-x.x.x/php.ini`):

```ini
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

**Note**: With chunked upload, these PHP limits are less critical since each chunk is only 1 MB. However, setting them higher ensures smooth operation for the final merge process.

---

## Test Again

Your 13.64 MB video should now upload successfully! Try the test page again:

1. Open: `http://localhost:8000/chunked-upload-test.html`
2. Select a post
3. Upload your video
4. Watch it complete successfully! ✅

---

## What Changed

✅ **Media Library Config**: Increased from 10 MB to 500 MB  
✅ **Supports Large Videos**: Now handles videos up to 500 MB  
✅ **Chunked Upload**: Still works efficiently with 1 MB chunks  
✅ **No Code Changes**: Only configuration update needed  

---

## Why This Works

The chunked upload system:
1. **Uploads in 1 MB chunks** - Each request is small
2. **Merges chunks** - Creates final file in temp storage
3. **Spatie Media Library** - Processes the merged file
4. **Previous limit** - 10 MB blocked the merged file
5. **New limit** - 500 MB allows large videos through

The error occurred at step 3, when Spatie tried to process the 13.64 MB merged file but hit the 10 MB limit.
