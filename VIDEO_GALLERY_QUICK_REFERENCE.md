# 🎬 Video Gallery Quick Reference

## ✨ New Feature: Video Gallery Support

Posts now support **multiple videos** in addition to the main video!

---

## 📋 Quick Overview

| Feature | Main Video | Video Gallery |
|---------|-----------|---------------|
| **Quantity** | 1 video | Multiple videos |
| **Field Name** | `video` | `video_gallery[]` |
| **Formats** | MP4, MPEG, MOV, AVI, WebM | MP4, MPEG, MOV, AVI, WebM |
| **Max Size** | 50MB | 50MB per video |
| **Delete Flag** | `delete_video` | `delete_video_gallery_ids[]` |
| **Replace Flag** | Auto (upload new) | `replace_video_gallery` |

---

## 🚀 Quick Examples

### 1️⃣ Create Post with Video Gallery
```bash
POST /api/posts

title: "My Video Collection"
body: "Multiple videos"
video_gallery[]: video1.mp4
video_gallery[]: video2.mp4
video_gallery[]: video3.mp4
```

### 2️⃣ Add Videos to Existing Gallery
```bash
POST /api/posts/1

title: "Updated Post"
body: "More videos added"
video_gallery[]: new-video1.mp4
video_gallery[]: new-video2.mp4
replace_video_gallery: false  ← Keep existing videos
```

### 3️⃣ Replace Entire Video Gallery
```bash
POST /api/posts/1

title: "Fresh Videos"
body: "All new videos"
video_gallery[]: video-a.mp4
video_gallery[]: video-b.mp4
replace_video_gallery: true  ← Delete old, add new
```

### 4️⃣ Delete Specific Videos
```bash
POST /api/posts/1

title: "Post Title"
body: "Post content"
delete_video_gallery_ids[]: 12  ← Media ID
delete_video_gallery_ids[]: 15  ← Media ID
```

### 5️⃣ Complete Media Upload
```bash
POST /api/posts

# Text
title: "Complete Post"
body: "All media types"

# Images
main_image: thumbnail.jpg
gallery[]: img1.jpg
gallery[]: img2.jpg

# Videos
video: main-video.mp4
video_gallery[]: vid1.mp4
video_gallery[]: vid2.mp4
video_gallery[]: vid3.mp4
```

---

## 📤 Response Structure

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Post Title",
    "body": "Post content",
    
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
  }
}
```

---

## ⚙️ All Video-Related Fields

### For Create & Update Requests

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `video` | File | No | Main video (single) |
| `delete_video` | Boolean | No | Delete main video |
| `video_gallery` | Array | No | Multiple videos |
| `video_gallery.*` | File | No | Individual video file |
| `replace_video_gallery` | Boolean | No | Replace all videos in gallery |
| `delete_video_gallery_ids` | Array | No | Media IDs to delete |
| `delete_video_gallery_ids.*` | Integer | No | Specific media ID |

---

## 🎯 Common Scenarios

### Scenario 1: Product Showcase
```
Main Video: Product overview (30 sec)
Video Gallery: 
  - Feature demo 1
  - Feature demo 2
  - Customer testimonial
  - Unboxing video
```

### Scenario 2: Tutorial Series
```
Main Video: Introduction
Video Gallery:
  - Step 1
  - Step 2
  - Step 3
  - Bonus tips
```

### Scenario 3: Event Coverage
```
Main Video: Event highlights
Video Gallery:
  - Opening ceremony
  - Keynote speech
  - Panel discussion
  - Closing remarks
```

---

## ✅ Validation Rules

### Video Gallery
```php
'video_gallery' => 'nullable|array'
'video_gallery.*' => 'file|mimes:mp4,mpeg,mov,avi,webm|max:51200'
```

### Delete Video Gallery IDs
```php
'delete_video_gallery_ids' => 'nullable|array'
'delete_video_gallery_ids.*' => 'integer|exists:media,id'
```

### Replace Video Gallery
```php
'replace_video_gallery' => 'nullable|boolean'
```

---

## 🔍 How to Get Media IDs

### From Response
When you create or retrieve a post, each media item has an `id`:

```json
"video_gallery": [
  {
    "id": 12,  ← Use this ID to delete
    "url": "...",
    "name": "video1.mp4"
  },
  {
    "id": 15,  ← Use this ID to delete
    "url": "...",
    "name": "video2.mp4"
  }
]
```

### To Delete Specific Videos
```bash
delete_video_gallery_ids[]: 12
delete_video_gallery_ids[]: 15
```

---

## 💡 Pro Tips

### 1. **Batch Upload**
Upload multiple videos in one request:
```
video_gallery[]: video1.mp4
video_gallery[]: video2.mp4
video_gallery[]: video3.mp4
video_gallery[]: video4.mp4
```

### 2. **Selective Update**
Keep some videos, delete others, add new ones:
```
delete_video_gallery_ids[]: 5    ← Delete old
delete_video_gallery_ids[]: 7    ← Delete old
video_gallery[]: new-video.mp4   ← Add new
replace_video_gallery: false     ← Keep remaining
```

### 3. **Complete Refresh**
Replace everything at once:
```
video_gallery[]: fresh1.mp4
video_gallery[]: fresh2.mp4
replace_video_gallery: true  ← Deletes all old videos
```

### 4. **Organize by Type**
- **Main Video**: Overview/trailer
- **Video Gallery**: Detailed content/series

---

## 🐛 Troubleshooting

### Issue: "Video too large"
**Solution**: Ensure video is under 50MB (51200 KB)

### Issue: "Invalid video format"
**Solution**: Use MP4, MPEG, MOV, AVI, or WebM

### Issue: "Media ID not found"
**Solution**: Check the media ID exists and belongs to this post

### Issue: "Videos not appearing"
**Solution**: Ensure you're using `video_gallery[]` with brackets

---

## 📊 Comparison: Before vs After

### Before (Single Video Only)
```
✅ Main video
❌ Video gallery
```

### After (Full Video Support)
```
✅ Main video
✅ Video gallery (multiple)
✅ Individual video deletion
✅ Batch video upload
✅ Replace all videos
✅ Add to existing videos
```

---

## 🎓 Learning Path

1. **Start Simple**: Upload one video to gallery
2. **Add More**: Upload multiple videos at once
3. **Update**: Add videos to existing gallery
4. **Delete**: Remove specific videos by ID
5. **Replace**: Replace entire gallery
6. **Master**: Combine all operations in one request

---

## 📝 Checklist for Video Gallery Implementation

- [ ] Import Postman collection
- [ ] Test creating post with video gallery
- [ ] Test adding videos to existing gallery
- [ ] Test replacing entire video gallery
- [ ] Test deleting specific videos
- [ ] Test combined operations
- [ ] Implement error handling
- [ ] Add loading states for uploads
- [ ] Display video gallery in UI
- [ ] Add video player functionality

---

## 🔗 Related Files

- **Model**: `app/Models/Post.php`
- **Controller**: `app/Http/Controllers/Api/PostController.php`
- **Request**: `app/Http/Requests/PostRequest.php`
- **Postman**: `Complete_API_Postman_Collection.json`
- **Full Guide**: `COMPLETE_API_GUIDE.md`

---

**Quick Start**: Import the Postman collection and try the "Create Post (With All Media)" request! 🚀
