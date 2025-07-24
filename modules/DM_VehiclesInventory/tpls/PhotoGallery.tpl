{*
 * SuiteCRM Vehicle Inventory System - Photo Gallery Template
 *
 * This template displays vehicle photos in a responsive gallery with lightbox functionality.
 * Supports multiple photo formats and provides admin controls for photo management.
 *}

<div class="vehicle-photo-gallery">
    <div class="photo-gallery-header">
        <h4>{$MOD.LBL_VEHICLE_PHOTOS}</h4>
        {if $fields.assigned_user_id.value == $USER_ID || $IS_ADMIN}
        <button type="button" class="btn btn-primary btn-sm" onclick="openPhotoUploader('{$fields.id.value}')">
            <i class="fa fa-plus"></i> {$MOD.LBL_ADD_PHOTOS}
        </button>
        {/if}
    </div>
    
    <div class="photo-gallery-container">
        {assign var="photos" value=$fields.photos.value}
        {if $photos && $photos != '[]'}
            {assign var="photoArray" value=$photos|@json_decode:true}
            <div class="row photo-grid">
                {foreach from=$photoArray item=photo name=photoLoop}
                <div class="col-md-3 col-sm-4 col-xs-6 photo-item" data-photo-id="{$smarty.foreach.photoLoop.index}">
                    <div class="photo-thumbnail">
                        <img src="{$photo.url}" 
                             alt="{$photo.description|default:'Vehicle Photo'}" 
                             class="img-responsive vehicle-photo"
                             onclick="openPhotoLightbox('{$photo.url}', '{$photo.description|escape}')"
                             loading="lazy">
                        
                        <div class="photo-overlay">
                            <div class="photo-actions">
                                <button type="button" class="btn btn-xs btn-info" 
                                        onclick="viewPhotoDetails('{$smarty.foreach.photoLoop.index}')">
                                    <i class="fa fa-eye"></i>
                                </button>
                                {if $fields.assigned_user_id.value == $USER_ID || $IS_ADMIN}
                                <button type="button" class="btn btn-xs btn-danger" 
                                        onclick="deletePhoto('{$fields.id.value}', '{$smarty.foreach.photoLoop.index}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                                {/if}
                            </div>
                            
                            {if $photo.description}
                            <div class="photo-description">
                                {$photo.description|truncate:30}
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        {else}
            <div class="no-photos-message">
                <div class="text-center">
                    <i class="fa fa-camera fa-3x text-muted"></i>
                    <p class="text-muted">{$MOD.LBL_NO_PHOTOS_AVAILABLE}</p>
                    {if $fields.assigned_user_id.value == $USER_ID || $IS_ADMIN}
                    <button type="button" class="btn btn-primary" onclick="openPhotoUploader('{$fields.id.value}')">
                        <i class="fa fa-plus"></i> {$MOD.LBL_ADD_FIRST_PHOTO}
                    </button>
                    {/if}
                </div>
            </div>
        {/if}
    </div>
</div>

{* Photo Lightbox Modal *}
<div id="photoLightboxModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="photoModalTitle">{$MOD.LBL_VEHICLE_PHOTO}</h4>
            </div>
            <div class="modal-body text-center">
                <img id="photoModalImage" src="" alt="" class="img-responsive" style="max-width: 100%; height: auto;">
                <div id="photoModalDescription" class="photo-description-full"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$APP.LBL_CLOSE_BUTTON_LABEL}</button>
            </div>
        </div>
    </div>
</div>

{* Photo Upload Modal *}
<div id="photoUploadModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">{$MOD.LBL_UPLOAD_PHOTOS}</h4>
            </div>
            <div class="modal-body">
                <div id="photoDropZone" class="photo-drop-zone">
                    <div class="drop-zone-content">
                        <i class="fa fa-cloud-upload fa-3x"></i>
                        <p>{$MOD.LBL_DRAG_DROP_PHOTOS}</p>
                        <p class="text-muted">{$MOD.LBL_OR_CLICK_TO_SELECT}</p>
                        <input type="file" id="photoFileInput" multiple accept="image/*" style="display: none;">
                    </div>
                </div>
                
                <div id="photoUploadProgress" class="upload-progress" style="display: none;">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="upload-status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$APP.LBL_CANCEL_BUTTON_LABEL}</button>
                <button type="button" class="btn btn-primary" onclick="uploadSelectedPhotos()" id="uploadPhotosBtn" disabled>
                    <i class="fa fa-upload"></i> {$MOD.LBL_UPLOAD_PHOTOS}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.vehicle-photo-gallery {
    margin: 15px 0;
}

.photo-gallery-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.photo-gallery-container {
    min-height: 200px;
}

.photo-grid .photo-item {
    margin-bottom: 15px;
}

.photo-thumbnail {
    position: relative;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    background: #f9f9f9;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.photo-thumbnail:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.photo-thumbnail img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    opacity: 0;
    transition: opacity 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 8px;
}

.photo-thumbnail:hover .photo-overlay {
    opacity: 1;
}

.photo-actions {
    display: flex;
    gap: 5px;
}

.photo-description {
    color: white;
    font-size: 12px;
    text-align: center;
}

.no-photos-message {
    padding: 40px;
    text-align: center;
}

.photo-drop-zone {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s ease;
}

.photo-drop-zone:hover,
.photo-drop-zone.dragover {
    border-color: #428bca;
    background-color: #f5f5f5;
}

.drop-zone-content i {
    color: #ccc;
    margin-bottom: 15px;
}

.upload-progress {
    margin-top: 15px;
}

.progress {
    margin-bottom: 10px;
}

.upload-status {
    text-align: center;
    font-size: 14px;
}
</style>

<script type="text/javascript">
{literal}
// Photo Gallery JavaScript Functions
var selectedPhotos = [];

function openPhotoLightbox(imageUrl, description) {
    $('#photoModalImage').attr('src', imageUrl);
    $('#photoModalTitle').text(description || 'Vehicle Photo');
    $('#photoModalDescription').text(description || '');
    $('#photoLightboxModal').modal('show');
}

function viewPhotoDetails(photoIndex) {
    // Implementation for viewing photo details
    console.log('Viewing photo details for index:', photoIndex);
}

function deletePhoto(vehicleId, photoIndex) {
    if (confirm('Are you sure you want to delete this photo?')) {
        // AJAX call to delete photo
        console.log('Deleting photo:', vehicleId, photoIndex);
        // TODO: Implement photo deletion via AJAX
    }
}

function openPhotoUploader(vehicleId) {
    selectedPhotos = [];
    $('#uploadPhotosBtn').prop('disabled', true);
    $('#photoUploadProgress').hide();
    $('#photoUploadModal').modal('show');
}

function uploadSelectedPhotos() {
    if (selectedPhotos.length === 0) {
        alert('Please select photos to upload.');
        return;
    }
    
    // TODO: Implement photo upload via AJAX
    console.log('Uploading photos:', selectedPhotos);
}

// Initialize photo upload functionality
$(document).ready(function() {
    // Photo drop zone functionality
    var dropZone = $('#photoDropZone');
    var fileInput = $('#photoFileInput');
    
    dropZone.on('click', function() {
        fileInput.click();
    });
    
    dropZone.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    dropZone.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });
    
    dropZone.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        var files = e.originalEvent.dataTransfer.files;
        handlePhotoSelection(files);
    });
    
    fileInput.on('change', function() {
        var files = this.files;
        handlePhotoSelection(files);
    });
});

function handlePhotoSelection(files) {
    selectedPhotos = [];
    
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        if (file.type.startsWith('image/')) {
            selectedPhotos.push(file);
        }
    }
    
    $('#uploadPhotosBtn').prop('disabled', selectedPhotos.length === 0);
    
    // Update drop zone text
    var dropZoneContent = $('.drop-zone-content');
    if (selectedPhotos.length > 0) {
        dropZoneContent.html(
            '<i class="fa fa-check-circle fa-3x text-success"></i>' +
            '<p>' + selectedPhotos.length + ' photo(s) selected</p>' +
            '<p class="text-muted">Click to select different photos</p>'
        );
    }
}
{/literal}
</script> 