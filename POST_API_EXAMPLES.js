// Example JavaScript code to interact with the Post API

const API_BASE_URL = 'http://localhost:8000/api/posts';

// ============================================
// 1. Get All Posts (with pagination)
// ============================================
async function getAllPosts(page = 1, perPage = 15) {
    try {
        const response = await fetch(`${API_BASE_URL}?page=${page}&per_page=${perPage}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Accept-Language': 'en', // or 'ar' for Arabic
            },
        });

        const data = await response.json();
        console.log('Posts:', data);
        return data;
    } catch (error) {
        console.error('Error fetching posts:', error);
    }
}

// ============================================
// 2. Get Single Post
// ============================================
async function getPost(postId) {
    try {
        const response = await fetch(`${API_BASE_URL}/${postId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Accept-Language': 'en',
            },
        });

        const data = await response.json();
        console.log('Post:', data);
        return data;
    } catch (error) {
        console.error('Error fetching post:', error);
    }
}

// ============================================
// 3. Create New Post (Simple - Single Language)
// ============================================
async function createPost(title, body, mainImage = null, galleryImages = []) {
    try {
        const formData = new FormData();

        // Add text fields
        formData.append('title', title);
        formData.append('body', body);

        // Add main image if provided
        if (mainImage) {
            formData.append('main_image', mainImage);
        }

        // Add gallery images if provided
        galleryImages.forEach((image) => {
            formData.append('gallery[]', image);
        });

        const response = await fetch(API_BASE_URL, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Accept-Language': 'en',
            },
            body: formData,
        });

        const data = await response.json();
        console.log('Created Post:', data);
        return data;
    } catch (error) {
        console.error('Error creating post:', error);
    }
}

// ============================================
// 4. Create Post (Multi-Language)
// ============================================
async function createMultiLanguagePost(translations, mainImage = null, galleryImages = []) {
    try {
        const formData = new FormData();

        // Add translatable fields
        // translations = { title: { en: 'English', ar: 'عربي' }, body: { en: 'Content', ar: 'محتوى' } }
        Object.keys(translations.title).forEach(lang => {
            formData.append(`title[${lang}]`, translations.title[lang]);
        });

        Object.keys(translations.body).forEach(lang => {
            formData.append(`body[${lang}]`, translations.body[lang]);
        });

        // Add images
        if (mainImage) {
            formData.append('main_image', mainImage);
        }

        galleryImages.forEach((image) => {
            formData.append('gallery[]', image);
        });

        const response = await fetch(API_BASE_URL, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Accept-Language': 'en',
            },
            body: formData,
        });

        const data = await response.json();
        console.log('Created Multi-Language Post:', data);
        return data;
    } catch (error) {
        console.error('Error creating post:', error);
    }
}

// ============================================
// 5. Update Post
// ============================================
async function updatePost(postId, title, body, options = {}) {
    try {
        const formData = new FormData();

        // Add text fields
        formData.append('title', title);
        formData.append('body', body);

        // Add new main image if provided
        if (options.mainImage) {
            formData.append('main_image', options.mainImage);
        }

        // Add new gallery images if provided
        if (options.galleryImages && options.galleryImages.length > 0) {
            options.galleryImages.forEach((image) => {
                formData.append('gallery[]', image);
            });
        }

        // Replace all gallery images?
        if (options.replaceGallery) {
            formData.append('replace_gallery', 'true');
        }

        // Delete specific gallery images by ID
        if (options.deleteGalleryIds && options.deleteGalleryIds.length > 0) {
            options.deleteGalleryIds.forEach((id) => {
                formData.append('delete_gallery_ids[]', id);
            });
        }

        const response = await fetch(`${API_BASE_URL}/${postId}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Accept-Language': 'en',
            },
            body: formData,
        });

        const data = await response.json();
        console.log('Updated Post:', data);
        return data;
    } catch (error) {
        console.error('Error updating post:', error);
    }
}

// ============================================
// 6. Delete Post
// ============================================
async function deletePost(postId) {
    try {
        const response = await fetch(`${API_BASE_URL}/${postId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Accept-Language': 'en',
            },
        });

        const data = await response.json();
        console.log('Deleted Post:', data);
        return data;
    } catch (error) {
        console.error('Error deleting post:', error);
    }
}

// ============================================
// 7. Delete Specific Media from Post
// ============================================
async function deleteMedia(postId, mediaId) {
    try {
        const response = await fetch(`${API_BASE_URL}/${postId}/media/${mediaId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Accept-Language': 'en',
            },
        });

        const data = await response.json();
        console.log('Deleted Media:', data);
        return data;
    } catch (error) {
        console.error('Error deleting media:', error);
    }
}

// ============================================
// USAGE EXAMPLES
// ============================================

// Example 1: Get all posts
// getAllPosts(1, 10);

// Example 2: Get single post
// getPost(1);

// Example 3: Create simple post with file upload from input
/*
const fileInput = document.getElementById('main-image-input');
const galleryInput = document.getElementById('gallery-input');

createPost(
  'My Post Title',
  'This is the post content',
  fileInput.files[0], // Main image
  Array.from(galleryInput.files) // Gallery images
);
*/

// Example 4: Create multi-language post
/*
createMultiLanguagePost(
  {
    title: {
      en: 'English Title',
      ar: 'العنوان بالعربية'
    },
    body: {
      en: 'English content here...',
      ar: 'المحتوى بالعربية هنا...'
    }
  },
  mainImageFile,
  [galleryImage1, galleryImage2]
);
*/

// Example 5: Update post with new images and delete some gallery images
/*
updatePost(1, 'Updated Title', 'Updated Content', {
  mainImage: newMainImageFile,
  galleryImages: [newGalleryImage1, newGalleryImage2],
  deleteGalleryIds: [2, 3], // Delete media with IDs 2 and 3
  replaceGallery: false // Add to existing gallery, don't replace
});
*/

// Example 6: Delete post
// deletePost(1);

// Example 7: Delete specific media
// deleteMedia(1, 5); // Delete media ID 5 from post ID 1

// ============================================
// HTML FORM EXAMPLE
// ============================================
/*
<form id="create-post-form" enctype="multipart/form-data">
  <input type="text" name="title" placeholder="Post Title" required>
  <textarea name="body" placeholder="Post Content" required></textarea>
  
  <label>Main Image:</label>
  <input type="file" id="main-image-input" accept="image/*">
  
  <label>Gallery Images:</label>
  <input type="file" id="gallery-input" accept="image/*" multiple>
  
  <button type="submit">Create Post</button>
</form>

<script>
document.getElementById('create-post-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  const title = formData.get('title');
  const body = formData.get('body');
  const mainImage = document.getElementById('main-image-input').files[0];
  const galleryImages = Array.from(document.getElementById('gallery-input').files);
  
  const result = await createPost(title, body, mainImage, galleryImages);
  
  if (result.success) {
    alert('Post created successfully!');
    e.target.reset();
  } else {
    alert('Error creating post');
  }
});
</script>
*/
