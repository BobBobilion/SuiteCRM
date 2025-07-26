{* SuiteCRM AutoInventory Photo Gallery Edit Template *}
<div id="photo_gallery_container">
    <div class="photo-upload-section">
        <label for="photo_upload">{$MOD.LBL_UPLOAD_PHOTOS}:</label>
        <input type="file" id="photo_upload" name="photo_upload[]" multiple accept="image/*" 
               onchange="handlePhotoUpload(this);" style="margin-bottom: 10px;">
        <small style="display: block; color: #666; margin-bottom: 10px;">
            Select multiple images (JPG, PNG, GIF). Hold Ctrl/Cmd to select multiple files.
        </small>
    </div>
    
    <div class="current-photos">
        <label>{$MOD.LBL_PHOTO_GALLERY}:</label>
        <div id="photo_preview_container" style="margin-top: 10px;">
            {if $fields.photo_gallery.value && $fields.photo_gallery.value != '[]'}
                {assign var="photos" value=$fields.photo_gallery.value|@json_decode:true}
                {if $photos && is_array($photos)}
                    {foreach from=$photos item=photo key=index}
                        <div class="photo-item" style="display: inline-block; margin: 5px; position: relative;">
                            <img src="{$photo}" alt="Auto Photo {$index+1}" 
                                 style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ccc;">
                            <button type="button" onclick="removePhoto({$index})" 
                                    style="position: absolute; top: -5px; right: -5px; background: red; color: white; 
                                           border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button>
                        </div>
                    {/foreach}
                {/if}
            {/if}
        </div>
    </div>
    
    <textarea name="photo_gallery" id="photo_gallery" style="display: none;">{$fields.photo_gallery.value}</textarea>
</div>

<script type="text/javascript">
{literal}
var currentPhotos = [];

// Initialize photos from existing data
document.addEventListener('DOMContentLoaded', function() {
    var photoData = document.getElementById('photo_gallery').value;
    if (photoData && photoData !== '[]') {
        try {
            currentPhotos = JSON.parse(photoData);
        } catch(e) {
            currentPhotos = [];
        }
    }
    updatePhotoDisplay();
});

function handlePhotoUpload(input) {
    if (input.files && input.files.length > 0) {
        for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];
            
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select only image files.');
                continue;
            }
            
            // Validate file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                alert('File ' + file.name + ' is too large. Please select files under 5MB.');
                continue;
            }
            
            // Create file reader
            var reader = new FileReader();
            reader.onload = function(e) {
                currentPhotos.push(e.target.result);
                updatePhotoDisplay();
                updateHiddenField();
            };
            reader.readAsDataURL(file);
        }
        
        // Clear the input
        input.value = '';
    }
}

function removePhoto(index) {
    if (confirm('Are you sure you want to remove this photo?')) {
        currentPhotos.splice(index, 1);
        updatePhotoDisplay();
        updateHiddenField();
    }
}

function updatePhotoDisplay() {
    var container = document.getElementById('photo_preview_container');
    var html = '';
    
    for (var i = 0; i < currentPhotos.length; i++) {
        html += '<div class="photo-item" style="display: inline-block; margin: 5px; position: relative;">';
        html += '<img src="' + currentPhotos[i] + '" alt="Auto Photo ' + (i+1) + '" ';
        html += 'style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ccc;">';
        html += '<button type="button" onclick="removePhoto(' + i + ')" ';
        html += 'style="position: absolute; top: -5px; right: -5px; background: red; color: white; ';
        html += 'border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button>';
        html += '</div>';
    }
    
    container.innerHTML = html;
}

function updateHiddenField() {
    document.getElementById('photo_gallery').value = JSON.stringify(currentPhotos);
}
{/literal}
</script>

<style type="text/css">
{literal}
.photo-upload-section {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9;
}

.current-photos {
    margin-top: 15px;
}

.photo-item img {
    transition: transform 0.2s;
}

.photo-item img:hover {
    transform: scale(1.1);
    cursor: pointer;
}

#photo_upload {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
    max-width: 400px;
}
{/literal}
</style> 