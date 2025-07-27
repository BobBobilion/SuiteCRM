<?php
/**
 * SuiteCRM Deal Documentation Suite - Signature Capture View
 * 
 * Popup window for signature capture
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_DealDocumentsViewSignature_capture extends SugarView
{
    public function __construct()
    {
        parent::__construct();
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
    }
    
    public function display()
    {
        $recordId = $_REQUEST['record'] ?? '';
        
        if (empty($recordId)) {
            echo 'Invalid record ID';
            return;
        }
        
        $document = BeanFactory::getBean('DM_DealDocuments', $recordId);
        
        if (!$document) {
            echo 'Document not found';
            return;
        }
        
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Digital Signature</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    text-align: center;
                }
                #signature_pad {
                    border: 2px solid #333;
                    border-radius: 4px;
                    cursor: crosshair;
                    margin: 20px auto;
                    display: block;
                }
                .button {
                    background-color: #007cba;
                    color: white;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    margin: 5px;
                }
                .button:hover {
                    background-color: #005a87;
                }
                .button.secondary {
                    background-color: #6c757d;
                }
                .button.secondary:hover {
                    background-color: #545b62;
                }
                .document-info {
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                    padding: 15px;
                    margin-bottom: 20px;
                }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        </head>
        <body>
            <div class="document-info">
                <h3>Digital Signature Required</h3>
                <p><strong>Document:</strong> ' . htmlspecialchars($document->name) . '</p>
                <p><strong>Type:</strong> ' . htmlspecialchars($document->document_type) . '</p>
                <p>Please sign below to complete the document:</p>
            </div>
            
            <canvas id="signature_pad" width="500" height="200"></canvas>
            
            <div>
                <button class="button secondary" onclick="clearSignature()">Clear</button>
                <button class="button" onclick="saveSignature()">Save Signature</button>
                <button class="button secondary" onclick="window.close()">Cancel</button>
            </div>
            
            <script>
                var signaturePad = new SignaturePad(document.getElementById("signature_pad"), {
                    backgroundColor: "rgba(255, 255, 255, 1)",
                    penColor: "rgb(0, 0, 0)"
                });
                
                function clearSignature() {
                    signaturePad.clear();
                }
                
                function saveSignature() {
                    if (signaturePad.isEmpty()) {
                        alert("Please provide a signature");
                        return;
                    }
                    
                    var signatureData = signaturePad.toDataURL();
                    
                    var formData = new FormData();
                    formData.append("module", "DM_DealDocuments");
                    formData.append("action", "save_signature");
                    formData.append("record", "' . $recordId . '");
                    formData.append("signature_data", signatureData);
                    
                    fetch("index.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Signature saved successfully");
                            window.close();
                        } else {
                            alert("Error saving signature: " + (data.error || "Unknown error"));
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Error saving signature");
                    });
                }
            </script>
        </body>
        </html>';
    }
}