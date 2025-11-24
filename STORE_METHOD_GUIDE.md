# Store Method - Postman Step-by-Step Guide

## 📋 Quick Reference for Store (Create Post) Method

### Endpoint Details
- **Method:** `POST`
- **URL:** `http://localhost:8000/api/posts`
- **Content-Type:** `multipart/form-data` (automatically set by Postman)
- **Headers Required:**
  - `Accept: application/json`
  - `Accept-Language: en` (or your preferred language)

---

## 🎯 Step-by-Step Instructions

### Step 1: Open the Request
1. Import the collection `Post_API_Postman_Collection.json`
2. Navigate to: **Laravel Post API - Complete Collection** → **Posts** → **"3. Create Post (Simple - Single Language)"**

### Step 2: Configure Headers
The headers are already configured, but verify:
```
Accept: application/json
Accept-Language: en
```

### Step 3: Configure Body (Form-Data)

Click on the **Body** tab, then select **form-data**

You'll see these fields:

#### Text Fields (Always Required):

| KEY | VALUE | TYPE | DESCRIPTION |
|-----|-------|------|-------------|
| `title` | My First Post | Text | Post title |
| `body` | This is the content of my first post. It can be a long text with multiple paragraphs. | Text | Post content |

#### File Fields (Optional):

| KEY | VALUE | TYPE | DESCRIPTION |
|-----|-------|------|-------------|
| `main_image` | [Select File] | File | Main image (max 5MB) |
| `gallery[]` | [Select File] | File | Gallery image 1 |
| `gallery[]` | [Select File] | File | Gallery image 2 |

---

## 📝 Example 1: Create Post WITHOUT Images

### Configuration:
```
✅ title = "My First Blog Post"
✅ body = "Welcome to my blog! This is my first post."
❌ main_image = (disabled/unchecked)
❌ gallery[] = (disabled/unchecked)
```

### Steps:
1. Fill in `title` field: "My First Blog Post"
2. Fill in `body` field: "Welcome to my blog! This is my first post."
3. **Uncheck** (disable) the `main_image` field
4. **Uncheck** (disable) all `gallery[]` fields
5. Click **Send**

### Expected Response:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "My First Blog Post",
    "body": "Welcome to my blog! This is my first post.",
    "created_at": "2025-11-24T12:00:00.000000Z",
    "updated_at": "2025-11-24T12:00:00.000000Z",
    "main_image": null,
    "gallery": []
  },
  "message": "Post created successfully"
}
```

---

## 📸 Example 2: Create Post WITH Images

### Configuration:
```
✅ title = "My Post with Images"
✅ body = "This post includes a main image and gallery images."
✅ main_image = [Your image file]
✅ gallery[] = [Gallery image 1]
✅ gallery[] = [Gallery image 2]
```

### Steps:

#### 1. Fill Text Fields:
- `title`: "My Post with Images"
- `body`: "This post includes a main image and gallery images."

#### 2. Add Main Image:
- **Check** (enable) the `main_image` checkbox
- In the VALUE column, you'll see "Select Files"
- Click **Select Files**
- Choose an image from your computer (e.g., `main-photo.jpg`)
- The file name will appear in Postman

#### 3. Add Gallery Images:
- **Check** (enable) the first `gallery[]` checkbox
- Click **Select Files**
- Choose an image (e.g., `gallery-1.jpg`)
- **Check** (enable) the second `gallery[]` checkbox
- Click **Select Files**
- Choose another image (e.g., `gallery-2.jpg`)

#### 4. Send Request:
- Click **Send** button
- Wait for the response

### Expected Response:
```json
{
  "success": true,
  "data": {
    "id": 2,
    "title": "My Post with Images",
    "body": "This post includes a main image and gallery images.",
    "created_at": "2025-11-24T12:05:00.000000Z",
    "updated_at": "2025-11-24T12:05:00.000000Z",
    "main_image": {
      "id": 1,
      "url": "http://localhost:8000/storage/1/main-photo.jpg",
      "thumb_url": "http://localhost:8000/storage/1/conversions/main-photo-thumb.jpg",
      "name": "main-photo.jpg",
      "size": 245760
    },
    "gallery": [
      {
        "id": 2,
        "url": "http://localhost:8000/storage/2/gallery-1.jpg",
        "thumb_url": "http://localhost:8000/storage/2/conversions/gallery-1-thumb.jpg",
        "name": "gallery-1.jpg",
        "size": 189440
      },
      {
        "id": 3,
        "url": "http://localhost:8000/storage/3/gallery-2.jpg",
        "thumb_url": "http://localhost:8000/storage/3/conversions/gallery-2-thumb.jpg",
        "name": "gallery-2.jpg",
        "size": 201728
      }
    ]
  },
  "message": "Post created successfully"
}
```

---

## 🌍 Example 3: Create Multi-Language Post

### Use Request: "4. Create Post (Multi-Language)"

### Configuration:
```
✅ title[en] = "My First Post"
✅ title[ar] = "منشوري الأول"
✅ body[en] = "This is the content of my first post in English."
✅ body[ar] = "هذا هو محتوى منشوري الأول باللغة العربية."
✅ main_image = [Your image file]
✅ gallery[] = [Gallery image]
```

### Steps:
1. Click on **"4. Create Post (Multi-Language)"**
2. The fields are already configured with examples
3. You can modify the values:
   - `title[en]`: English title
   - `title[ar]`: Arabic title (or any other language code)
   - `body[en]`: English content
   - `body[ar]`: Arabic content
4. Optionally add images (same as Example 2)
5. Click **Send**

### How to Retrieve in Different Languages:

#### Get in English:
```
GET http://localhost:8000/api/posts/1
Header: Accept-Language: en
```
Response will show English content.

#### Get in Arabic:
```
GET http://localhost:8000/api/posts/1
Header: Accept-Language: ar
```
Response will show Arabic content.

---

## 🎨 How to Add More Gallery Images

If you want to add more than 2 gallery images:

### Method 1: Duplicate Existing Row
1. Hover over an existing `gallery[]` row
2. Click the **"..."** menu (or right-click)
3. Select **Duplicate**
4. A new `gallery[]` row will appear
5. Select a different image file

### Method 2: Add New Row Manually
1. Scroll to the bottom of the form-data table
2. In the empty row:
   - **KEY:** Type `gallery[]`
   - **TYPE:** Select `File` from dropdown
   - **VALUE:** Click "Select Files" and choose an image
3. Repeat for as many images as you want

---

## ✅ Validation Rules Reference

When creating a post, these validation rules apply:

| Field | Required | Type | Rules |
|-------|----------|------|-------|
| `title` | ✅ Yes | String/Array | Max 255 characters |
| `body` | ✅ Yes | String/Array | No max length |
| `main_image` | ❌ No | File | Image, max 5MB, formats: jpeg,png,jpg,gif,webp |
| `gallery[]` | ❌ No | File | Image, max 5MB per file, formats: jpeg,png,jpg,gif,webp |

---

## ❌ Common Errors and Solutions

### Error 1: "The title field is required"
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```
**Solution:** Make sure the `title` field is checked (enabled) and has a value.

---

### Error 2: "The main image must be an image"
```json
{
  "errors": {
    "main_image": ["The main image must be an image."]
  }
}
```
**Solution:** You uploaded a non-image file (e.g., PDF, TXT). Use only: jpeg, png, jpg, gif, webp.

---

### Error 3: "The main image may not be greater than 5120 kilobytes"
```json
{
  "errors": {
    "main_image": ["The main image may not be greater than 5120 kilobytes."]
  }
}
```
**Solution:** Your image is larger than 5MB. Compress it or use a smaller image.

---

### Error 4: 404 Not Found
```json
{
  "message": "Not Found"
}
```
**Solution:** 
- Check the URL is correct: `http://localhost:8000/api/posts`
- Make sure your Laravel server is running: `php artisan serve`
- Verify the route exists: `php artisan route:list --path=api/posts`

---

### Error 5: 500 Internal Server Error
```json
{
  "success": false,
  "message": "Error creating post",
  "error": "..."
}
```
**Solution:**
- Check Laravel logs: `storage/logs/laravel.log`
- Make sure database is connected
- Run: `php artisan storage:link`
- Check file permissions on `storage` directory

---

## 🔍 Testing Checklist

Before sending the request, verify:

- [ ] Laravel server is running (`php artisan serve`)
- [ ] URL is correct: `http://localhost:8000/api/posts`
- [ ] Method is **POST**
- [ ] Headers are set:
  - [ ] `Accept: application/json`
  - [ ] `Accept-Language: en`
- [ ] Body type is **form-data**
- [ ] Required fields are filled:
  - [ ] `title` has a value
  - [ ] `body` has a value
- [ ] Image files (if any) are:
  - [ ] Valid image formats (jpeg, png, jpg, gif, webp)
  - [ ] Under 5MB each
  - [ ] Properly selected in Postman

---

## 📊 Response Structure Explained

### Success Response:
```json
{
  "success": true,           // ✅ Request was successful
  "data": {
    "id": 1,                 // 🆔 Post ID (use this for update/delete)
    "title": "...",          // 📝 Post title
    "body": "...",           // 📄 Post content
    "created_at": "...",     // 📅 Creation timestamp
    "updated_at": "...",     // 📅 Last update timestamp
    "main_image": {          // 🖼️ Main image details (null if not uploaded)
      "id": 1,               // Media ID
      "url": "...",          // Full-size image URL
      "thumb_url": "...",    // Thumbnail URL (300x200)
      "name": "...",         // Original file name
      "size": 245760         // File size in bytes
    },
    "gallery": [             // 🎨 Gallery images array
      {
        "id": 2,
        "url": "...",
        "thumb_url": "...",
        "name": "...",
        "size": 189440
      }
    ]
  },
  "message": "Post created successfully"  // ✅ Success message
}
```

---

## 💡 Pro Tips

### Tip 1: Save the Post ID
After creating a post, **copy the `id`** from the response. You'll need it for:
- Getting the post: `GET /api/posts/{id}`
- Updating the post: `POST /api/posts/{id}`
- Deleting the post: `DELETE /api/posts/{id}`

### Tip 2: Test Without Images First
Start by creating a post without images to make sure the basic functionality works. Then add images.

### Tip 3: Use Small Test Images
For testing, use small images (100-500KB) to speed up uploads. You can test with larger images later.

### Tip 4: Check the Response
Always check the response to see:
- The generated `id`
- The image URLs (to verify uploads worked)
- The thumbnail URLs (to verify conversions worked)

### Tip 5: Use Postman Environment Variables
Create a Postman environment with:
- `base_url`: `http://localhost:8000`
- `post_id`: (save after creating a post)

Then use: `{{base_url}}/api/posts/{{post_id}}`

---

## 🎯 Quick Test Scenarios

### Scenario 1: Minimum Valid Request
```
title = "Test"
body = "Test content"
(No images)
```
✅ Should succeed

### Scenario 2: With Main Image Only
```
title = "Test with Image"
body = "Test content"
main_image = [image file]
(No gallery)
```
✅ Should succeed

### Scenario 3: With Gallery Only
```
title = "Test with Gallery"
body = "Test content"
(No main image)
gallery[] = [image1]
gallery[] = [image2]
```
✅ Should succeed

### Scenario 4: Complete Post
```
title = "Complete Post"
body = "Full content"
main_image = [image file]
gallery[] = [image1]
gallery[] = [image2]
gallery[] = [image3]
```
✅ Should succeed

### Scenario 5: Invalid - No Title
```
(title is empty or disabled)
body = "Test content"
```
❌ Should fail with validation error

### Scenario 6: Invalid - Large Image
```
title = "Test"
body = "Test content"
main_image = [10MB image file]
```
❌ Should fail with validation error

---

## 📞 Need More Help?

If you're still having issues:

1. **Check Postman Console:**
   - Click "Console" at the bottom of Postman
   - See the raw request/response details

2. **Check Laravel Logs:**
   - Open: `storage/logs/laravel.log`
   - Look for recent errors

3. **Test with cURL:**
   ```bash
   curl -X POST http://localhost:8000/api/posts \
     -H "Accept: application/json" \
     -H "Accept-Language: en" \
     -F "title=Test Post" \
     -F "body=Test content"
   ```

4. **Verify Routes:**
   ```bash
   php artisan route:list --path=api/posts
   ```

---

Happy Testing! 🚀
