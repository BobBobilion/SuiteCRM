<?php
/**
 * SuiteCRM Deal Documentation Suite - Edit View
 * 
 * Custom edit view for Deal Documents with PDF generation and signature capabilities
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.edit.php');

class DM_DealDocumentsViewEdit extends ViewEdit
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function preDisplay()
    {
        parent::preDisplay();
        
        // Add CSS for edit view
        echo '<style>
            .pdf-preview-container {
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 15px;
                margin: 10px 0;
                background-color: #f9f9f9;
            }
            .signature-pad-container {
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 15px;
                margin: 10px 0;
                background-color: white;
                text-align: center;
            }
            #signature_pad {
                border: 2px solid #333;
                border-radius: 4px;
                cursor: crosshair;
            }
            .generation-message {
                padding: 10px;
                margin: 10px 0;
                border-radius: 4px;
                background-color: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
            }
        </style>';
    }
    
    public function display()
    {
        // Add JavaScript for document generation and signature
        echo '<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>';
        echo '<script>
            var signaturePad;
            
            function generateDocumentPDF() {
                if (!validateRequiredFields()) {
                    return false;
                }
                
                // Show loading indicator
                document.getElementById("generate_pdf_btn").value = "Generating...";
                document.getElementById("generate_pdf_btn").disabled = true;
                
                // Create form data
                var formData = new FormData();
                formData.append("module", "DM_DealDocuments");
                formData.append("action", "generate_pdf");
                formData.append("record", document.querySelector("[name=record]").value || "");
                formData.append("deal_id", document.querySelector("[name=deal_id]").value || "");
                formData.append("customer_id", document.querySelector("[name=customer_id]").value || "");
                formData.append("document_type", document.querySelector("[name=document_type]").value || "");
                
                fetch("index.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showPDFPreview(data.pdf_url);
                        showSignatureSection();
                        document.getElementById("pdf_generation_message").style.display = "block";
                    } else {
                        alert("Error generating PDF: " + (data.error || "Unknown error"));
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error generating PDF");
                })
                .finally(() => {
                    document.getElementById("generate_pdf_btn").value = "Generate PDF";
                    document.getElementById("generate_pdf_btn").disabled = false;
                });
            }
            
            function validateRequiredFields() {
                var dealId = document.querySelector("[name=deal_id]").value;
                var customerId = document.querySelector("[name=customer_id]").value;
                var documentType = document.querySelector("[name=document_type]").value;
                
                if (!dealId) {
                    alert("Please select an F&I Deal");
                    return false;
                }
                if (!customerId) {
                    alert("Please select a Customer");
                    return false;
                }
                if (!documentType) {
                    alert("Please select a Document Type");
                    return false;
                }
                return true;
            }
            
            function showPDFPreview(pdfUrl) {
                var container = document.getElementById("pdf_preview_container");
                var iframe = document.getElementById("pdf_preview");
                iframe.src = pdfUrl;
                container.style.display = "block";
            }
            
            function showSignatureSection() {
                var section = document.getElementById("signature_section");
                section.style.display = "block";
                initializeSignaturePad();
            }
            
            function initializeSignaturePad() {
                var canvas = document.getElementById("signature_pad");
                if (canvas && !signaturePad) {
                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: "rgba(255, 255, 255, 0)",
                        penColor: "rgb(0, 0, 0)"
                    });
                }
            }
            
            function clearSignature() {
                if (signaturePad) {
                    signaturePad.clear();
                }
            }
            
            function saveSignature() {
                if (!signaturePad || signaturePad.isEmpty()) {
                    alert("Please provide a signature");
                    return;
                }
                
                var signatureData = signaturePad.toDataURL();
                var recordId = document.querySelector("[name=record]").value;
                
                var formData = new FormData();
                formData.append("module", "DM_DealDocuments");
                formData.append("action", "save_signature");
                formData.append("record", recordId);
                formData.append("signature_data", signatureData);
                
                fetch("index.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Signature saved successfully");
                        window.location.reload();
                    } else {
                        alert("Error saving signature: " + (data.error || "Unknown error"));
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error saving signature");
                });
            }
            
            function downloadPDF() {
                var recordId = document.querySelector("[name=record]").value;
                window.open("index.php?module=DM_DealDocuments&action=download_pdf&record=" + recordId, "_blank");
            }
            
            function updateTemplateOptions() {
                var documentType = document.querySelector("[name=document_type]").value;
                var templateField = document.querySelector("[name=template_name]");
                
                if (templateField && documentType) {
                    templateField.value = documentType;
                }
            }
            
            function populateCustomerFromDeal() {
                var dealId = document.querySelector("[name=deal_id]").value;
                if (dealId) {
                    // AJAX call to get customer from deal
                    fetch("index.php?module=DM_FIDeals&action=get_customer&record=" + dealId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.customer_id) {
                            var customerField = document.querySelector("[name=customer_id]");
                            var customerNameField = document.querySelector("[name=customer_name]");
                            if (customerField) customerField.value = data.customer_id;
                            if (customerNameField) customerNameField.value = data.customer_name;
                        }
                    })
                    .catch(error => console.error("Error fetching customer:", error));
                }
            }
        </script>';
        
        parent::display();
    }
}