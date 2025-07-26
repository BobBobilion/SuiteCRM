{* SuiteCRM AutoInventory Photo Gallery Detail Template *}
<div id="photo_gallery_detail_container">
    {if $fields.photo_gallery.value && $fields.photo_gallery.value != '[]'}
        {assign var="photos" value=$fields.photo_gallery.value|@json_decode:true}
        {if $photos && is_array($photos) && count($photos) > 0}
            <div class="photo-gallery-grid">
                {foreach from=$photos item=photo key=index}
                    <div class="photo-item">
                        <img src="{$photo}" alt="Auto Photo {$index+1}" 
                             onclick="openPhotoModal('{$photo}', {$index+1})"
                             style="width: 120px; height: 120px; object-fit: cover; cursor: pointer; 
                                    border: 2px solid #ddd; border-radius: 8px; margin: 5px;
                                    transition: transform 0.2s;">
                    </div>
                {/foreach}
            </div>
            <small style="color: #666; margin-top: 10px; display: block;">
                Click on any photo to view full size. Total: {count($photos)} photo(s)
            </small>
        {else}
            <span style="color: #999; font-style: italic;">No photos uploaded</span>
        {/if}
    {else}
        <span style="color: #999; font-style: italic;">No photos uploaded</span>
    {/if}
</div>

{* Photo Modal for Full Size View *}
<div id="photoModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; 
                            width: 100%; height: 100%; background-color: rgba(0,0,0,0.9);">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                max-width: 90%; max-height: 90%;">
        <img id="modalPhoto" src="" alt="" style="max-width: 100%; max-height: 100%; border-radius: 8px;">
        <div style="text-align: center; margin-top: 10px;">
            <span id="modalCaption" style="color: white; font-size: 16px;"></span>
            <br>
            <button onclick="closePhotoModal()" 
                    style="margin-top: 10px; padding: 8px 16px; background: #fff; border: none; 
                           border-radius: 4px; cursor: pointer;">Close</button>
        </div>
    </div>
</div>

<script type="text/javascript">
{literal}
function openPhotoModal(photoSrc, photoNumber) {
    document.getElementById('photoModal').style.display = 'block';
    document.getElementById('modalPhoto').src = photoSrc;
    document.getElementById('modalCaption').textContent = 'Photo ' + photoNumber;
}

function closePhotoModal() {
    document.getElementById('photoModal').style.display = 'none';
}

// Close modal when clicking outside the image
document.getElementById('photoModal').onclick = function(event) {
    if (event.target === this) {
        closePhotoModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePhotoModal();
    }
});
{/literal}
</script>

<style type="text/css">
{literal}
.photo-gallery-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.photo-item img:hover {
    transform: scale(1.05);
    border-color: #007cba;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

#photoModal {
    cursor: pointer;
}

#modalPhoto {
    box-shadow: 0 8px 32px rgba(0,0,0,0.5);
}
{/literal}
</style> 