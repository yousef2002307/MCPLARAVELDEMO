# Complete API Guide - Posts with Video Gallery Support

## 📋 Table of Contents
1. [Overview](#overview)
2. [Media Collections](#media-collections)
3. [API Endpoints](#api-endpoints)
4. [Request Examples](#request-examples)
5. [Response Format](#response-format)
6. [Validation Rules](#validation-rules)
7. [Postman Collection](#postman-collection)

---

## 🎯 Overview

The Post API now supports **complete media management** including:
- ✅ Main image (single)
- ✅ Image gallery (multiple)
- ✅ Main video (single)
- ✅ **Video gallery (multiple)** ⭐ NEW

All media types can be uploaded, updated, and deleted independently.

---

## 📁 Media Collections

### 1. **Main Image** (`main_image`)
- **Type**: Single file
- **Formats**: JPEG, PNG, JPG, GIF, WebP
- **Max Size**: 5MB
- **Conversions**: Thumbnail (300x200)

### 2. **Image Gallery** (`gallery`)
- **Type**: Multiple files
- **Formats**: JPEG, PNG, JPG, GIF, WebP
- **Max Size**: 5MB per image
- **Conversions**: Thumbnail (300x200)

### 3. **Main Video** (`video`)
- **Type**: Single file
- **Formats**: MP4, MPEG, MOV, AVI, WebM
- **Max Size**: 50MB

### 4. **Video Gallery** (`video_gallery`) ⭐ NEW
- **Type**: Multiple files
- **Formats**: MP4, MPEG, MOV, AVI, WebM
- **Max Size**: 50MB per video

---

## 🔌 API Endpoints

### Posts
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/posts` | Get all posts (paginated) |
| GET | `/api/posts/{id}` | Get single post |
| POST | `/api/posts` | Create new post |
| POST | `/api/posts/{id}` | Update post (using POST for file uploads) |
| DELETE | `/api/posts/{id}` | Delete post |
| DELETE | `/api/posts/{postId}/media/{mediaId}` | Delete specific media |

### Notifications
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notifications` | Get all notifications |
| GET | `/api/notifications/unread` | Get unread notifications |
| GET | `/api/notifications/stats` | Get notification stats |
| PATCH | `/api/notifications/{id}/read` | Mark as read |
| POST | `/api/notifications/mark-all-read` | Mark all as read |
| DELETE | `/api/notifications/{id}` | Delete notification |
| DELETE | `/api/notifications/read/all` | Delete all read |

---

## 📝 Request Examples

### 1. Create Post with All Media Types

```http
POST /api/posts
Content-Type: multipart/form-data

{
  "title": "Complete Post",
  "body": "Post with all media types",
  "main_image": [image file],
  "gallery[]": [image file 1],
  "gallery[]": [image file 2],
  "video": [video file],
  "video_gallery[]": [video file 1],
  "video_gallery[]": [video file 2],
  "video_gallery[]": [video file 3]
}
```

### 2. Create Translatable Post

```http
POST /api/posts
Content-Type: multipart/form-data

{
  "title[en]": "English Title",
  "title[ar]": "عنوان عربي",
  "body[en]": "English content",
  "body[ar]": "محتوى عربي",
  "main_image": [image file],
  "video": [video file]
}
```

### 3. Update Post - Add Videos to Gallery

```http
POST /api/posts/1
Content-Type: multipart/form-data

{
  "title": "Updated Post",
  "body": "Updated content",
  "video_gallery[]": [new video 1],
  "video_gallery[]": [new video 2],
  "replace_video_gallery": false  // false = add to existing
}
```

### 4. Update Post - Replace Entire Video Gallery

```http
POST /api/posts/1
Content-Type: multipart/form-data

{
  "title": "Post Title",
  "body": "Post content",
  "video_gallery[]": [video 1],
  "video_gallery[]": [video 2],
  "replace_video_gallery": true  // true = replace all existing
}
```

### 5. Update Post - Delete Specific Videos

```http
POST /api/posts/1
Content-Type: multipart/form-data

{
  "title": "Post Title",
  "body": "Post content",
  "delete_video_gallery_ids[]": 12,  // Media ID
  "delete_video_gallery_ids[]": 15   // Media ID
}
```

### 6. Update Post - Delete Main Video

```http
POST /api/posts/1
Content-Type: multipart/form-data

{
  "title": "Post Title",
  "body": "Post content",
  "delete_video": true
}
```

### 7. Update Post - Replace Main Video

```http
POST /api/posts/1
Content-Type: multipart/form-data

{
  "title": "Post Title",
  "body": "Post content",
  "video": [new video file]  // Automatically replaces existing
}
```

### 8. Update Post - Complex Update

```http
POST /api/posts/1
Content-Type: multipart/form-data

{
  "title": "Updated Title",
  "body": "Updated content",
  
  // Replace main image
  "main_image": [new image],
  
  // Add to image gallery
  "gallery[]": [new image 1],
  "gallery[]": [new image 2],
  "replace_gallery": false,
  
  // Delete specific gallery images
  "delete_gallery_ids[]": 5,
  "delete_gallery_ids[]": 7,
  
  // Replace main video
  "video": [new video],
  
  // Add to video gallery
  "video_gallery[]": [new video 1],
  "video_gallery[]": [new video 2],
  "replace_video_gallery": false,
  
  // Delete specific videos
  "delete_video_gallery_ids[]": 12,
  "delete_video_gallery_ids[]": 15
}
```

---

## 📤 Response Format

### Success Response (Create/Update/Show)

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Post Title",
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
    
    "gallery": [
      {
        "id": 2,
        "url": "http://example.com/storage/2/gallery1.jpg",
        "thumb_url": "http://example.com/storage/2/conversions/gallery1-thumb.jpg",
        "name": "gallery1.jpg",
        "size": 204800
      },
      {
        "id": 3,
        "url": "http://example.com/storage/3/gallery2.jpg",
        "thumb_url": "http://example.com/storage/3/conversions/gallery2-thumb.jpg",
        "name": "gallery2.jpg",
        "size": 153600
      }
    ],
    
    "video": {
      "id": 4,
      "url": "http://example.com/storage/4/main-video.mp4",
      "name": "main-video.mp4",
      "size": 5242880,
      "mime_type": "video/mp4"
    },
    
    "video_gallery": [
      {
        "id": 5,
        "url": "http://example.com/storage/5/video1.mp4",
        "name": "video1.mp4",
        "size": 10485760,
        "mime_type": "video/mp4"
      },
      {
        "id": 6,
        "url": "http://example.com/storage/6/video2.webm",
        "name": "video2.webm",
        "size": 8388608,
        "mime_type": "video/webm"
      }
    ]
  },
  "message": "Post created successfully"
}
```

### Success Response (Index - Paginated)

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Post 1",
        "body": "Content 1",
        "main_image": {...},
        "gallery": [...],
        "video": {...},
        "video_gallery": [...]
      },
      {
        "id": 2,
        "title": "Post 2",
        "body": "Content 2",
        "main_image": {...},
        "gallery": [...],
        "video": {...},
        "video_gallery": [...]
      }
    ],
    "first_page_url": "http://example.com/api/posts?page=1",
    "from": 1,
    "last_page": 3,
    "last_page_url": "http://example.com/api/posts?page=3",
    "next_page_url": "http://example.com/api/posts?page=2",
    "path": "http://example.com/api/posts",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 45
  },
  "message": "Posts retrieved successfully"
}
```

### Error Response (Validation)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "video": ["The video must be a file of type: mp4, mpeg, mov, avi, webm."],
    "video_gallery.0": ["The video gallery.0 may not be greater than 51200 kilobytes."]
  }
}
```

### Error Response (Not Found)

```json
{
  "success": false,
  "message": "Post not found"
}
```

---

## ✅ Validation Rules

### Required Fields
- `title` (string or array for translations)
- `body` (string or array for translations)

### Optional Fields - Images
| Field | Type | Rules |
|-------|------|-------|
| `main_image` | File | image, mimes:jpeg,png,jpg,gif,webp, max:5120KB |
| `gallery` | Array | array |
| `gallery.*` | File | image, mimes:jpeg,png,jpg,gif,webp, max:5120KB |
| `replace_gallery` | Boolean | Replace all gallery images |
| `delete_gallery_ids` | Array | Array of media IDs to delete |

### Optional Fields - Videos
| Field | Type | Rules |
|-------|------|-------|
| `video` | File | file, mimes:mp4,mpeg,mov,avi,webm, max:51200KB |
| `delete_video` | Boolean | Delete main video |
| `video_gallery` | Array | array |
| `video_gallery.*` | File | file, mimes:mp4,mpeg,mov,avi,webm, max:51200KB |
| `replace_video_gallery` | Boolean | Replace all video gallery items |
| `delete_video_gallery_ids` | Array | Array of video media IDs to delete |

### Translation Format
You can send translatable fields in two ways:

**Option 1: Simple String**
```
title: "My Post Title"
body: "Post content"
```

**Option 2: Translation Array**
```
title[en]: "English Title"
title[ar]: "عنوان عربي"
body[en]: "English content"
body[ar]: "محتوى عربي"
```

---

## 📮 Postman Collection

### Import Instructions

1. **Open Postman**
2. Click **Import** button
3. Select **File** tab
4. Choose `Complete_API_Postman_Collection.json`
5. Click **Import**

### Collection Structure

The collection includes **27 requests** organized in 2 folders:

#### 📁 Posts API (20 requests)
- Get All Posts
- Get Single Post
- Create Post (Basic)
- Create Post (With All Media)
- Create Post (Translatable)
- Update Post (Basic)
- Update Post (Replace Main Image)
- Update Post (Add to Gallery)
- Update Post (Replace Entire Gallery)
- Update Post (Delete Specific Gallery Images)
- Update Post (Replace Main Video)
- Update Post (Delete Main Video)
- Update Post (Add to Video Gallery) ⭐
- Update Post (Replace Video Gallery) ⭐
- Update Post (Delete Specific Videos from Gallery) ⭐
- Update Post (Complete Update)
- Delete Post
- Delete Specific Media

#### 📁 Notifications API (7 requests)
- Get All Notifications
- Get Unread Notifications
- Get Notification Stats
- Mark Notification as Read
- Mark All as Read
- Delete Notification
- Delete All Read Notifications

### Environment Variables

The collection uses one variable:
- `base_url`: Default is `http://127.0.0.1:8000`

To change the base URL:
1. Click on the collection
2. Go to **Variables** tab
3. Update `base_url` value
4. Save

---

## 🎯 Common Use Cases

### Use Case 1: Create a Blog Post with Featured Video
```http
POST /api/posts
{
  "title": "My Video Blog Post",
  "body": "Check out this amazing video!",
  "main_image": [thumbnail.jpg],
  "video": [featured-video.mp4]
}
```

### Use Case 2: Create a Video Gallery Post
```http
POST /api/posts
{
  "title": "Video Collection",
  "body": "Multiple videos showcase",
  "video_gallery[]": [video1.mp4],
  "video_gallery[]": [video2.mp4],
  "video_gallery[]": [video3.mp4]
}
```

### Use Case 3: Update Post - Remove Old Videos, Add New Ones
```http
POST /api/posts/1
{
  "title": "Updated Video Collection",
  "body": "Fresh videos",
  "delete_video_gallery_ids[]": 5,
  "delete_video_gallery_ids[]": 6,
  "video_gallery[]": [new-video1.mp4],
  "video_gallery[]": [new-video2.mp4]
}
```

### Use Case 4: Multilingual Post with Media
```http
POST /api/posts
{
  "title[en]": "Product Launch",
  "title[ar]": "إطلاق المنتج",
  "body[en]": "Watch our new product video",
  "body[ar]": "شاهد فيديو منتجنا الجديد",
  "main_image": [product.jpg],
  "video": [product-demo.mp4],
  "gallery[]": [feature1.jpg],
  "gallery[]": [feature2.jpg]
}
```

---

## 🔧 Tips & Best Practices

### 1. **File Size Optimization**
- Compress images before upload (recommended: under 2MB)
- Compress videos before upload (recommended: under 20MB)
- Use appropriate video codecs (H.264 for MP4 is widely supported)

### 2. **Batch Operations**
- When updating multiple items, combine operations in one request
- Use `replace_gallery` or `replace_video_gallery` for complete replacements
- Use `delete_*_ids` arrays for selective deletions

### 3. **Error Handling**
- Always check the `success` field in responses
- Handle validation errors by checking the `errors` object
- Log media upload failures for debugging

### 4. **Performance**
- Use pagination for listing posts (`per_page` parameter)
- Consider lazy loading for video galleries
- Implement thumbnail previews for videos when possible

### 5. **Language Support**
- Set `Accept-Language` header (en or ar)
- Use translation arrays for multilingual content
- Fallback to default language if translation missing

---

## 📊 Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Post created successfully |
| 404 | Not Found | Post or media not found |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Server Error | Internal server error |

---

## 🆕 What's New in This Update

### Video Gallery Feature ⭐
- Added `video_gallery` media collection
- Support for multiple videos per post
- Individual video deletion by media ID
- Replace entire video gallery option
- Add to existing video gallery option

### Enhanced Validation
- Video gallery validation rules
- Custom error messages for video gallery
- Media ID validation for deletions

### Complete Postman Collection
- 20 Post API requests
- 7 Notification API requests
- Detailed descriptions for each endpoint
- Ready-to-use examples

---

## 📞 Support

For issues or questions:
1. Check validation error messages
2. Review this documentation
3. Test with Postman collection
4. Check Laravel logs for server errors

---

**Last Updated**: December 3, 2025  
**Version**: 2.0 (with Video Gallery Support)
