/**
 * SuiteCRM Deal Documentation Suite - JavaScript Functions
 * 
 * This file contains JavaScript functions for the Deal Documents module
 * including PDF generation, signature capture, and UI interactions.
 */

/**
 * Initialize the Deal Documents JavaScript functionality
 */
function initDealDocuments() {
    // Initialize any required components
    setupEventListeners();
    checkDocumentStatus();
}

/**
 * Set up event listeners for the module
 */
function setupEventListeners() {
    // Listen for document type changes
    var documentTypeField = document.querySelector('[name="document_type"]');
    if (documentTypeField) {
        documentTypeField.addEventListener('change', updateTemplateOptions);
    }
    
    // Listen for deal changes
    var dealField = document.querySelector('[name="deal_id"]');
    if (dealField) {
        dealField.addEventListener('change', populateCustomerFromDeal);
    }
}

/**
 * Check document status and show/hide appropriate sections
 */
function checkDocumentStatus() {
    var statusField = document.querySelector('[name="document_status"]');
    var pdfContent = document.querySelector('[name="pdf_content"]');
    var signatureData = document.querySelector('[name="signature_data"]');
    
    if (statusField) {
        var status = statusField.value;
        
        // Show/hide PDF preview section
        var pdfPreviewContainer = document.getElementById('pdf_preview_container');
        if (pdfPreviewContainer) {
            pdfPreviewContainer.style.display = (pdfContent && pdfContent.value) ? 'block' : 'none';
        }
        
        // Show/hide signature section
        var signatureSection = document.getElementById('signature_section');
        if (signatureSection) {
            var showSignature = (pdfContent && pdfContent.value) && (!signatureData || !signatureData.value);
            signatureSection.style.display = showSignature ? 'block' : 'none';
        }
    }
}

/**
 * Generate PDF document
 */
function generateDocumentPDF() {
    if (!validateRequiredFields()) {
        return false;
    }
    
    // Show loading indicator
    var generateBtn = document.getElementById('generate_pdf_btn');
    if (generateBtn) {
        generateBtn.value = 'Generating...';
        generateBtn.disabled = true;
    }
    
    // Create form data
    var formData = new FormData();
    formData.append('module', 'DM_DealDocuments');
    formData.append('action', 'generate_pdf');
    formData.append('record', getFieldValue('record') || '');
    formData.append('deal_id', getFieldValue('deal_id') || '');
    formData.append('customer_id', getFieldValue('customer_id') || '');
    formData.append('document_type', getFieldValue('document_type') || '');
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPDFPreview(data.pdf_url);
            showSignatureSection();
            showGenerationMessage();
            updateDocumentStatus('Generated');
        } else {
            alert('Error generating PDF: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating PDF');
    })
    .finally(() => {
        if (generateBtn) {
            generateBtn.value = 'Generate PDF';
            generateBtn.disabled = false;
        }
    });
}

/**
 * Validate required fields before PDF generation
 */
function validateRequiredFields() {
    var dealId = getFieldValue('deal_id');
    var customerId = getFieldValue('customer_id');
    var documentType = getFieldValue('document_type');
    
    if (!dealId) {
        alert('Please select an F&I Deal');
        return false;
    }
    if (!customerId) {
        alert('Please select a Customer');
        return false;
    }
    if (!documentType) {
        alert('Please select a Document Type');
        return false;
    }
    return true;
}

/**
 * Show PDF preview in iframe
 */
function showPDFPreview(pdfUrl) {
    var container = document.getElementById('pdf_preview_container');
    var iframe = document.getElementById('pdf_preview');
    
    if (container && iframe) {
        iframe.src = pdfUrl;
        container.style.display = 'block';
    }
}

/**
 * Show signature section
 */
function showSignatureSection() {
    var section = document.getElementById('signature_section');
    if (section) {
        section.style.display = 'block';
        initializeSignaturePad();
    }
}

/**
 * Show generation success message
 */
function showGenerationMessage() {
    var message = document.getElementById('pdf_generation_message');
    if (message) {
        message.style.display = 'block';
    }
}

/**
 * Initialize signature pad
 */
var signaturePad;
function initializeSignaturePad() {
    var canvas = document.getElementById('signature_pad');
    if (canvas && typeof SignaturePad !== 'undefined' && !signaturePad) {
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
    }
}

/**
 * Clear signature pad
 */
function clearSignature() {
    if (signaturePad) {
        signaturePad.clear();
    }
}

/**
 * Save signature
 */
function saveSignature() {
    if (!signaturePad || signaturePad.isEmpty()) {
        alert('Please provide a signature');
        return;
    }
    
    var signatureData = signaturePad.toDataURL();
    var recordId = getFieldValue('record');
    
    var formData = new FormData();
    formData.append('module', 'DM_DealDocuments');
    formData.append('action', 'save_signature');
    formData.append('record', recordId);
    formData.append('signature_data', signatureData);
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Signature saved successfully');
            updateDocumentStatus('Signed');
            hideSignatureSection();
        } else {
            alert('Error saving signature: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving signature');
    });
}

/**
 * Hide signature section after signing
 */
function hideSignatureSection() {
    var section = document.getElementById('signature_section');
    if (section) {
        section.style.display = 'none';
    }
}

/**
 * Download PDF
 */
function downloadPDF() {
    var recordId = getFieldValue('record');
    if (recordId) {
        window.open('index.php?module=DM_DealDocuments&action=download_pdf&record=' + recordId, '_blank');
    }
}

/**
 * Update template options based on document type
 */
function updateTemplateOptions() {
    var documentType = getFieldValue('document_type');
    var templateField = document.querySelector('[name="template_name"]');
    
    if (templateField && documentType) {
        templateField.value = documentType;
    }
}

/**
 * Populate customer from selected deal
 */
function populateCustomerFromDeal() {
    var dealId = getFieldValue('deal_id');
    if (dealId) {
        fetch('index.php?module=DM_FIDeals&action=get_customer&record=' + dealId)
        .then(response => response.json())
        .then(data => {
            if (data.customer_id) {
                setFieldValue('customer_id', data.customer_id);
                setFieldValue('customer_name', data.customer_name);
            }
        })
        .catch(error => console.error('Error fetching customer:', error));
    }
}

/**
 * Update document status field
 */
function updateDocumentStatus(status) {
    setFieldValue('document_status', status);
}

/**
 * Helper function to get field value
 */
function getFieldValue(fieldName) {
    var field = document.querySelector('[name="' + fieldName + '"]');
    return field ? field.value : '';
}

/**
 * Helper function to set field value
 */
function setFieldValue(fieldName, value) {
    var field = document.querySelector('[name="' + fieldName + '"]');
    if (field) {
        field.value = value;
    }
}

/**
 * Show signature modal for detail view
 */
function showSignatureModal() {
    var recordId = getFieldValue('record') || document.location.search.match(/record=([^&]*)/)[1];
    
    var signatureWindow = window.open(
        'index.php?module=DM_DealDocuments&action=signature_capture&record=' + recordId,
        'signature',
        'width=600,height=500,scrollbars=no,resizable=no'
    );
    
    // Refresh page when signature window closes
    var checkClosed = setInterval(function() {
        if (signatureWindow.closed) {
            clearInterval(checkClosed);
            window.location.reload();
        }
    }, 1000);
}

/**
 * Regenerate PDF document
 */
function regenerateDocumentPDF() {
    if (confirm('Regenerate PDF document? This will overwrite the existing PDF.')) {
        var recordId = getFieldValue('record') || document.location.search.match(/record=([^&]*)/)[1];
        window.location.href = 'index.php?module=DM_DealDocuments&action=generate_pdf&record=' + recordId + '&regenerate=1';
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initDealDocuments();
});