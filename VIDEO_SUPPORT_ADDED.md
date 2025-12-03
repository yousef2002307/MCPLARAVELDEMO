# Video Support Added to Posts

## Summary
Successfully added video upload functionality to the Post model. Posts can now have an associated video file in addition to images.

## Changes Made

### 1. **Post Model** (`app/Models/Post.php`)
- Added a new `video` media collection that accepts a single video file
- Supported video formats: MP4, MPEG, QuickTime (MOV), AVI, WebM
- The video collection is configured as `singleFile()` to allow only one video per post

### 2. **PostController** (`app/Http/Controllers/Api/PostController.php`)

#### **Store Method (Create Post)**
- Added video upload handling when creating a new post
- If a video file is present in the request, it's saved to the `video` media collection

#### **Update Method (Update Post)**
- Added video update handling:
  - If a new video is uploaded, the old video is removed and replaced
  - Added `delete_video` flag to allow removing the video without uploading a new one

#### **Transform Method (API Response)**
- Updated `transformPost()` to include video data in API responses
- Video response includes:
  - `id`: Media ID
  - `url`: Full URL to the video file
  - `name`: Original filename
  - `size`: File size in bytes
  - `mime_type`: Video MIME type

### 3. **PostRequest Validation** (`app/Http/Requests/PostRequest.php`)
- Added validation rules for video uploads:
  - `video`: Optional file, must be mp4, mpeg, mov, avi, or webm format
  - Maximum size: 50MB (51200 KB)
  - `delete_video`: Optional boolean flag for video deletion
- Added custom validation messages:
  - `Videomustsupportedformat`: For invalid video format
  - `Videotoobig`: For videos exceeding size limit

## API Usage Examples

### **Creating a Post with Video**
```http
POST /api/posts
Content-Type: multipart/form-data

{
  "title": "My Post Title",
  "body": "Post content here",
  "main_image": [image file],
  "gallery[]": [image file 1],
  "gallery[]": [image file 2],
  "video": [video file]
}
```

### **Updating a Post - Replace Video**
```http
PUT/PATCH /api/posts/{id}
Content-Type: multipart/form-data

{
  "title": "Updated Title",
  "video": [new video file]  // This will replace the existing video
}
```

### **Updating a Post - Delete Video**
```http
PUT/PATCH /api/posts/{id}
Content-Type: multipart/form-data

{
  "title": "Updated Title",
  "delete_video": true  // This will remove the video
}
```

### **API Response Example**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "My Post",
    "body": "Post content",
    "created_at": "2025-12-03T14:30:00.000000Z",
    "updated_at": "2025-12-03T14:30:00.000000Z",
    "main_image": {
      "id": 1,
      "url": "http://example.com/storage/1/image.jpg",
      "thumb_url": "http://example.com/storage/1/conversions/image-thumb.jpg",
      "name": "image.jpg",
      "size": 102400
    },
    "gallery": [...],
    "video": {
      "id": 5,
      "url": "http://example.com/storage/5/video.mp4",
      "name": "video.mp4",
      "size": 5242880,
      "mime_type": "video/mp4"
    }
  },
  "message": "Post created successfully"
}
```

## Supported Video Formats
- **MP4** (video/mp4)
- **MPEG** (video/mpeg)
- **QuickTime/MOV** (video/quicktime)
- **AVI** (video/x-msvideo)
- **WebM** (video/webm)

## File Size Limits
- **Images**: 5MB per file
- **Video**: 50MB per file

## Notes
- Only one video can be associated with each post (single file collection)
- Videos are automatically deleted when the post is deleted (handled by Spatie Media Library)
- The video field is optional - posts can be created without videos
- When updating, you can either replace the video or delete it using the `delete_video` flag
