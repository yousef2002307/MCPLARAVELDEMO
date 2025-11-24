# Post API Documentation

This documentation covers all the CRUD operations for the Post model with file upload support using Spatie Media Library.

## Base URL
```
http://localhost:8000/api/posts
```

## Features
- ✅ Full CRUD operations (Create, Read, Update, Delete)
- ✅ File upload support (main image + gallery)
- ✅ Multi-language support (translatable title and body)
- ✅ Image conversions (thumbnails)
- ✅ Pagination support
- ✅ Individual media deletion
- ✅ Proper error handling

---

## API Endpoints

### 1. Get All Posts (with pagination)
**GET** `/api/posts`

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 15)

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/posts?per_page=10" \
  -H "Accept-Language: en"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Post Title",
        "body": "Post content...",
        "created_at": "2025-11-24T10:00:00.000000Z",
        "updated_at": "2025-11-24T10:00:00.000000Z",
        "main_image": {
          "id": 1,
          "url": "http://localhost:8000/storage/1/main-image.jpg",
          "thumb_url": "http://localhost:8000/storage/1/conversions/main-image-thumb.jpg",
          "name": "main-image.jpg",
          "size": 102400
        },
        "gallery": [
          {
            "id": 2,
            "url": "http://localhost:8000/storage/2/gallery-1.jpg",
            "thumb_url": "http://localhost:8000/storage/2/conversions/gallery-1-thumb.jpg",
            "name": "gallery-1.jpg",
            "size": 204800
          }
        ]
      }
    ],
    "per_page": 10,
    "total": 50
  },
  "message": "Posts retrieved successfully"
}
```

---

### 2. Get Single Post
**GET** `/api/posts/{id}`

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/posts/1" \
  -H "Accept-Language: en"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Post Title",
    "body": "Post content...",
    "created_at": "2025-11-24T10:00:00.000000Z",
    "updated_at": "2025-11-24T10:00:00.000000Z",
    "main_image": {
      "id": 1,
      "url": "http://localhost:8000/storage/1/main-image.jpg",
      "thumb_url": "http://localhost:8000/storage/1/conversions/main-image-thumb.jpg",
      "name": "main-image.jpg",
      "size": 102400
    },
    "gallery": []
  },
  "message": "Post retrieved successfully"
}
```

---

### 3. Create New Post
**POST** `/api/posts`

**Content-Type:** `multipart/form-data`

**Form Fields:**

#### Simple Format (Single Language):
- `title` (required): Post title as string
- `body` (required): Post content as string
- `main_image` (optional): Main image file (max 5MB, formats: jpeg, png, jpg, gif, webp)
- `gallery[]` (optional): Array of gallery images (max 5MB each)

**Example Request (Simple):**
```bash
curl -X POST "http://localhost:8000/api/posts" \
  -H "Accept-Language: en" \
  -F "title=My First Post" \
  -F "body=This is the content of my first post" \
  -F "main_image=@/path/to/main-image.jpg" \
  -F "gallery[]=@/path/to/gallery-1.jpg" \
  -F "gallery[]=@/path/to/gallery-2.jpg"
```

#### Multi-Language Format:
- `title[en]` (required): English title
- `title[ar]` (required): Arabic title
- `body[en]` (required): English content
- `body[ar]` (required): Arabic content

**Example Request (Multi-Language):**
```bash
curl -X POST "http://localhost:8000/api/posts" \
  -H "Accept-Language: en" \
  -F "title[en]=My First Post" \
  -F "title[ar]=منشوري الأول" \
  -F "body[en]=This is the content" \
  -F "body[ar]=هذا هو المحتوى" \
  -F "main_image=@/path/to/main-image.jpg" \
  -F "gallery[]=@/path/to/gallery-1.jpg"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "My First Post",
    "body": "This is the content of my first post",
    "created_at": "2025-11-24T10:00:00.000000Z",
    "updated_at": "2025-11-24T10:00:00.000000Z",
    "main_image": {
      "id": 1,
      "url": "http://localhost:8000/storage/1/main-image.jpg",
      "thumb_url": "http://localhost:8000/storage/1/conversions/main-image-thumb.jpg",
      "name": "main-image.jpg",
      "size": 102400
    },
    "gallery": [
      {
        "id": 2,
        "url": "http://localhost:8000/storage/2/gallery-1.jpg",
        "thumb_url": "http://localhost:8000/storage/2/conversions/gallery-1-thumb.jpg",
        "name": "gallery-1.jpg",
        "size": 204800
      }
    ]
  },
  "message": "Post created successfully"
}
```

---

### 4. Update Post
**POST** `/api/posts/{id}` (Using POST for file upload support)

**Content-Type:** `multipart/form-data`

**Form Fields:**
- `title` (required): Post title (string or array for multi-language)
- `body` (required): Post content (string or array for multi-language)
- `main_image` (optional): New main image file
- `gallery[]` (optional): New gallery images to add
- `replace_gallery` (optional): Boolean - if true, replaces all gallery images
- `delete_gallery_ids[]` (optional): Array of media IDs to delete

**Example Request (Update with new images):**
```bash
curl -X POST "http://localhost:8000/api/posts/1" \
  -H "Accept-Language: en" \
  -F "title=Updated Post Title" \
  -F "body=Updated content" \
  -F "main_image=@/path/to/new-main-image.jpg" \
  -F "gallery[]=@/path/to/new-gallery-1.jpg" \
  -F "delete_gallery_ids[]=2" \
  -F "delete_gallery_ids[]=3"
```

**Example Request (Replace all gallery images):**
```bash
curl -X POST "http://localhost:8000/api/posts/1" \
  -H "Accept-Language: en" \
  -F "title=Updated Post Title" \
  -F "body=Updated content" \
  -F "replace_gallery=true" \
  -F "gallery[]=@/path/to/new-gallery-1.jpg" \
  -F "gallery[]=@/path/to/new-gallery-2.jpg"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Updated Post Title",
    "body": "Updated content",
    "created_at": "2025-11-24T10:00:00.000000Z",
    "updated_at": "2025-11-24T11:00:00.000000Z",
    "main_image": {
      "id": 5,
      "url": "http://localhost:8000/storage/5/new-main-image.jpg",
      "thumb_url": "http://localhost:8000/storage/5/conversions/new-main-image-thumb.jpg",
      "name": "new-main-image.jpg",
      "size": 102400
    },
    "gallery": [
      {
        "id": 6,
        "url": "http://localhost:8000/storage/6/new-gallery-1.jpg",
        "thumb_url": "http://localhost:8000/storage/6/conversions/new-gallery-1-thumb.jpg",
        "name": "new-gallery-1.jpg",
        "size": 204800
      }
    ]
  },
  "message": "Post updated successfully"
}
```

---

### 5. Delete Post
**DELETE** `/api/posts/{id}`

**Example Request:**
```bash
curl -X DELETE "http://localhost:8000/api/posts/1" \
  -H "Accept-Language: en"
```

**Example Response:**
```json
{
  "success": true,
  "message": "Post deleted successfully"
}
```

---

### 6. Delete Specific Media
**DELETE** `/api/posts/{postId}/media/{mediaId}`

**Example Request:**
```bash
curl -X DELETE "http://localhost:8000/api/posts/1/media/5" \
  -H "Accept-Language: en"
```

**Example Response:**
```json
{
  "success": true,
  "message": "Media deleted successfully"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "main_image": ["The main image must be an image."]
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Post not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Error creating post",
  "error": "Detailed error message..."
}
```

---

## Testing with Postman

### Create Post Example:
1. Method: POST
2. URL: `http://localhost:8000/api/posts`
3. Headers:
   - `Accept-Language: en`
4. Body (form-data):
   - `title`: "My Test Post"
   - `body`: "This is a test post content"
   - `main_image`: [Select File]
   - `gallery[]`: [Select File]
   - `gallery[]`: [Select File]

### Update Post Example:
1. Method: POST
2. URL: `http://localhost:8000/api/posts/1`
3. Headers:
   - `Accept-Language: en`
4. Body (form-data):
   - `title`: "Updated Title"
   - `body`: "Updated content"
   - `main_image`: [Select File] (optional)
   - `delete_gallery_ids[]`: 2
   - `delete_gallery_ids[]`: 3

---

## Multi-Language Support

The Post model supports translatable fields (title and body). You can:

1. **Store in multiple languages:**
```bash
curl -X POST "http://localhost:8000/api/posts" \
  -F "title[en]=English Title" \
  -F "title[ar]=العنوان بالعربية" \
  -F "body[en]=English content" \
  -F "body[ar]=المحتوى بالعربية"
```

2. **Retrieve in specific language:**
Set the `Accept-Language` header to get content in that language:
```bash
curl -X GET "http://localhost:8000/api/posts/1" \
  -H "Accept-Language: ar"
```

---

## File Upload Limits

- **Max file size:** 5MB per image
- **Supported formats:** jpeg, png, jpg, gif, webp
- **Main image:** Single file only
- **Gallery:** Multiple files allowed

---

## Image Conversions

All uploaded images automatically generate a thumbnail conversion:
- **Width:** 300px
- **Height:** 200px
- **Access via:** `thumb_url` in the response

You can access both the original image (`url`) and the thumbnail (`thumb_url`) for each media item.

---

## Notes

1. The `AcceptLanguageMiddleware` is applied to all routes for multi-language support
2. All media files are automatically deleted when a post is deleted
3. The update endpoint uses POST instead of PUT/PATCH to support file uploads
4. Pagination is available on the index endpoint with customizable `per_page` parameter
5. All responses follow a consistent JSON structure with `success`, `data`, and `message` fields
