# Chunked Video Upload - Quick Reference

## 🚀 Quick Start

1. **Open Test Page**: http://localhost:8000/chunked-upload-test.html
2. **Select Post**: Choose from dropdown
3. **Choose Collection**: Single video or video gallery
4. **Upload Video**: Drag & drop or click to browse
5. **Start Upload**: Click button and watch progress

## 📡 API Endpoints

### Upload Chunks
```
POST /api/chunked-upload/video
```
- Receives video chunks
- Returns progress or final filename

### Complete Upload
```
POST /api/chunked-upload/video/complete
```
**Body**:
```json
{
  "post_id": 1,
  "filename": "video_123.mp4",
  "collection": "video"
}
```

## 🎯 Key Features

✅ **1 MB chunks** - Optimal performance  
✅ **Resume capability** - Interrupted uploads can continue  
✅ **Real-time progress** - Percentage, speed, chunks  
✅ **Drag & drop** - Modern upload UX  
✅ **Video preview** - See uploaded video immediately  
✅ **Error handling** - Comprehensive validation  

## 📁 Files Created

- `app/Http/Controllers/Api/ChunkedVideoController.php` - Backend controller
- `routes/api.php` - Added chunked upload routes
- `public/chunked-upload-test.html` - Premium test page

## 🧪 Testing Checklist

- [ ] Upload small video (< 10MB)
- [ ] Upload large video (> 50MB)
- [ ] Test drag & drop
- [ ] Test click to browse
- [ ] Verify progress tracking
- [ ] Check video in post via API
- [ ] Test error scenarios

## 🔧 Troubleshooting

**Issue**: "File size greater than maximum allowed"  
**Fix**: Updated `config/media-library.php` max_file_size to 500MB

**Issue**: Posts not loading  
**Fix**: Check Laravel server is running on port 8000

**Issue**: Upload fails  
**Fix**: Check `storage/app/public/temp-videos/` directory exists and is writable

**Issue**: Video not in post  
**Fix**: Verify complete endpoint was called successfully

## 📦 Package Used

**pion/laravel-chunk-upload** (v1.5.6)
- Handles chunk receiving and merging
- Minimal memory footprint
- Production-ready

## 🎨 UI Highlights

- Modern gradient design (purple to violet)
- Glassmorphism effects
- Smooth animations
- Responsive layout
- Premium color scheme
