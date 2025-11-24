# How to Import and Use the Postman Collection

## 📥 Import Instructions

### Method 1: Import from File
1. Open **Postman**
2. Click on **Import** button (top left corner)
3. Click **Choose Files** or drag and drop
4. Select the file: `Post_API_Postman_Collection.json`
5. Click **Import**

### Method 2: Import via Raw Text
1. Open **Postman**
2. Click on **Import** button
3. Select **Raw text** tab
4. Copy and paste the entire content of `Post_API_Postman_Collection.json`
5. Click **Continue** → **Import**

---

## 🎯 Collection Overview

The collection includes **8 pre-configured requests**:

1. ✅ Get All Posts (Paginated)
2. ✅ Get Single Post
3. ✅ Create Post (Simple - Single Language)
4. ✅ Create Post (Multi-Language)
5. ✅ Update Post (Simple)
6. ✅ Update Post (With Media Management)
7. ✅ Delete Post
8. ✅ Delete Specific Media

---

## 🚀 Quick Start Guide

### Step 1: Start Your Laravel Server
Make sure your Laravel application is running:
```bash
php artisan serve
```
The server should be running at `http://localhost:8000`

### Step 2: Test "Get All Posts"
1. In Postman, open the collection: **Laravel Post API - Complete Collection**
2. Click on **"1. Get All Posts (Paginated)"**
3. Click **Send**
4. You should see a response (might be empty if no posts exist yet)

### Step 3: Create Your First Post

#### Option A: Simple Post (No Files)
1. Click on **"3. Create Post (Simple - Single Language)"**
2. Go to **Body** tab
3. You'll see form-data fields:
   - `title`: Already filled with "My First Post"
   - `body`: Already filled with sample content
   - `main_image`: **Disabled by default** (enable and select a file)
   - `gallery[]`: **Disabled by default** (enable and select files)
4. Click **Send**

#### Option B: Post with Images
1. Click on **"3. Create Post (Simple - Single Language)"**
2. Go to **Body** tab
3. For `main_image`:
   - **Enable** the checkbox (if disabled)
   - Click **Select Files**
   - Choose an image from your computer (jpeg, png, jpg, gif, webp)
4. For `gallery[]`:
   - **Enable** the checkbox for each gallery image
   - Click **Select Files** for each
   - Choose images from your computer
5. Click **Send**

---

## 📝 Detailed Request Examples

### 1️⃣ Create Post (Simple - Single Language)

**What it does:** Creates a post with English content only

**Required Fields:**
- `title` (text): "My First Post"
- `body` (text): "This is the content..."

**Optional Fields:**
- `main_image` (file): Select an image file
- `gallery[]` (file): Select multiple images (add multiple `gallery[]` fields)

**How to add files:**
1. Go to **Body** tab
2. Find the `main_image` row
3. Check the checkbox to enable it
4. Click **Select Files** in the VALUE column
5. Choose your image file
6. Repeat for `gallery[]` fields

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "My First Post",
    "body": "This is the content...",
    "main_image": {
      "id": 1,
      "url": "http://localhost:8000/storage/1/image.jpg",
      "thumb_url": "http://localhost:8000/storage/1/conversions/image-thumb.jpg"
    },
    "gallery": [...]
  },
  "message": "Post created successfully"
}
```

---

### 2️⃣ Create Post (Multi-Language)

**What it does:** Creates a post with multiple language translations

**Required Fields:**
- `title[en]` (text): "My First Post"
- `title[ar]` (text): "منشوري الأول"
- `body[en]` (text): "English content..."
- `body[ar]` (text): "المحتوى بالعربية..."

**How to test:**
1. Click on **"4. Create Post (Multi-Language)"**
2. All fields are pre-filled with examples
3. Optionally add images (same as above)
4. Click **Send**

**How to retrieve in different languages:**
After creating, test with different `Accept-Language` headers:
- English: Set `Accept-Language: en`
- Arabic: Set `Accept-Language: ar`

---

### 3️⃣ Update Post with Media Management

**What it does:** Updates a post and manages its media files

**Fields:**
- `title` (text): Updated title
- `body` (text): Updated content
- `main_image` (file): New main image (replaces old one)
- `gallery[]` (file): New gallery images
- `replace_gallery` (text): "true" or "false"
  - `true`: Removes all old gallery images and adds new ones
  - `false`: Keeps old images and adds new ones
- `delete_gallery_ids[]` (text): Media IDs to delete (e.g., "2", "3")

**Example Scenario:**
You want to:
- Update the title
- Keep the main image
- Delete gallery images with IDs 2 and 3
- Add 2 new gallery images

**Steps:**
1. Click on **"6. Update Post (With Media Management)"**
2. Change the URL: Replace `1` with your post ID
3. Update `title` and `body` fields
4. **Disable** `main_image` (keep old one)
5. **Enable** `gallery[]` fields and select new images
6. **Enable** `delete_gallery_ids[]` fields
7. Set values to "2" and "3"
8. Set `replace_gallery` to "false"
9. Click **Send**

---

## 🎨 Working with Images

### Supported Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)

### Size Limits
- Maximum: **5MB per image**
- Recommended: 1-2MB for optimal performance

### Image Conversions
All uploaded images automatically generate thumbnails:
- **Width:** 300px
- **Height:** 200px
- **Access:** Use `thumb_url` in the response

### How to Add Multiple Gallery Images
1. In the **Body** tab, you'll see `gallery[]` fields
2. To add more:
   - Hover over a `gallery[]` row
   - Click the **duplicate** icon (or manually add a new row)
   - Set KEY to `gallery[]`
   - Set TYPE to `File`
   - Select your image file

---

## 🔧 Troubleshooting

### Issue 1: "Post not found" (404)
**Solution:** Make sure you're using a valid post ID in the URL

### Issue 2: Validation errors
**Common errors:**
- `title.required`: You forgot to fill the title field
- `main_image.max`: Image is larger than 5MB
- `main_image.mimes`: Wrong file format (use jpeg, png, jpg, gif, webp)

**Solution:** Check the error message and fix the corresponding field

### Issue 3: "Call to undefined method"
**Solution:** Make sure you've run:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Issue 4: Images not uploading
**Solution:** 
1. Check your `.env` file has correct filesystem settings
2. Run: `php artisan storage:link`
3. Make sure `storage/app/public` directory exists and is writable

---

## 📊 Testing Workflow

### Complete Testing Sequence:

1. **Create a post** (Request #3 or #4)
   - Note the returned `id` (e.g., `id: 1`)

2. **Get all posts** (Request #1)
   - Verify your post appears in the list

3. **Get single post** (Request #2)
   - Change URL to use your post ID
   - Verify all data is correct

4. **Update the post** (Request #5 or #6)
   - Change URL to use your post ID
   - Modify some fields
   - Verify changes are saved

5. **Delete specific media** (Request #8)
   - Get a media ID from the post response
   - Change URL: `/posts/{postId}/media/{mediaId}`
   - Verify media is deleted

6. **Delete the post** (Request #7)
   - Change URL to use your post ID
   - Verify post is deleted

---

## 🌍 Multi-Language Testing

### Test Language Switching:

1. **Create a multi-language post** (Request #4)
2. **Get the post in English:**
   - Use Request #2
   - Set header: `Accept-Language: en`
   - You'll see English content

3. **Get the same post in Arabic:**
   - Use Request #2 (same post ID)
   - Change header: `Accept-Language: ar`
   - You'll see Arabic content

---

## 💡 Pro Tips

1. **Save Response Data:**
   - After creating a post, copy the `id` from the response
   - Use it in update/delete requests

2. **Use Variables:**
   - The collection has variables: `base_url` and `api_prefix`
   - You can modify these if your server runs on a different port

3. **Organize Your Tests:**
   - Create a new folder in Postman
   - Duplicate requests for different test scenarios
   - Name them clearly (e.g., "Create Post - With Images", "Create Post - No Images")

4. **Test Error Cases:**
   - Try sending empty title (should fail validation)
   - Try uploading a PDF file (should fail - not an image)
   - Try uploading a 10MB image (should fail - too large)

---

## 📸 Example Test Images

You can use these free image sources for testing:
- **Unsplash:** https://unsplash.com/
- **Pexels:** https://www.pexels.com/
- **Pixabay:** https://pixabay.com/

Download a few images (keep them under 5MB) and use them for testing.

---

## ✅ Checklist

Before testing, make sure:
- [ ] Laravel server is running (`php artisan serve`)
- [ ] Database is set up and migrated
- [ ] Spatie Media Library is configured
- [ ] Storage link is created (`php artisan storage:link`)
- [ ] Postman collection is imported
- [ ] You have some test images ready (under 5MB)

---

## 🎯 Quick Reference

| Request | Method | URL | Purpose |
|---------|--------|-----|---------|
| #1 | GET | `/api/posts` | List all posts |
| #2 | GET | `/api/posts/{id}` | Get one post |
| #3 | POST | `/api/posts` | Create (simple) |
| #4 | POST | `/api/posts` | Create (multi-lang) |
| #5 | POST | `/api/posts/{id}` | Update (simple) |
| #6 | POST | `/api/posts/{id}` | Update (advanced) |
| #7 | DELETE | `/api/posts/{id}` | Delete post |
| #8 | DELETE | `/api/posts/{id}/media/{mediaId}` | Delete media |

---

## 🆘 Need Help?

If you encounter issues:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Check the Postman console (bottom panel) for detailed request/response
3. Verify your `.env` database settings
4. Make sure all migrations are run: `php artisan migrate`

Happy Testing! 🚀
