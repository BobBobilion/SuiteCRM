<?php
/**
 * SuiteCRM F&I Deal Center - Approval Tracking View
 * 
 * This view provides basic approval tracking functionality for manual
 * lender management as part of Phase 3 MVP implementation.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class DM_FIDealsViewApproval_tracking extends SugarView
{
    public $type = 'approval_tracking';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $GLOBALS['log']->debug("F&I Deal Center: Approval tracking view initialized");
    }
    
    /**
     * Preprocess the view
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        // Check if user has access to this module
        if (!ACLController::checkAccess('DM_FIDeals', 'edit', true)) {
            ACLController::displayNoAccess(true);
            sugar_cleanup(true);
        }
    }
    
    /**
     * Display the approval tracking interface
     */
    public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        
        $GLOBALS['log']->debug("F&I Deal Center: Displaying approval tracking view");
        
        // Get the deal ID from request
        $dealId = $_REQUEST['record'] ?? '';
        
        if (empty($dealId)) {
            $this->displayError('Deal ID is required for approval tracking');
            return;
        }
        
        // Load the deal
        $deal = BeanFactory::getBean('DM_FIDeals', $dealId);
        if (!$deal || $deal->deleted) {
            $this->displayError('Deal not found or has been deleted');
            return;
        }
        
        // Handle form submission for status updates
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_approval'])) {
            $this->handleApprovalUpdate($deal);
        }
        
        // Load lender information
        $lender = null;
        if (!empty($deal->lender_id)) {
            $lender = BeanFactory::getBean('Accounts', $deal->lender_id);
        }
        
        // Get approval history
        $approvalHistory = $this->getApprovalHistory($dealId);
        
        // Display the tracking interface
        $this->displayTrackingInterface($deal, $lender, $approvalHistory);
    }
    
    /**
     * Handle approval status updates
     */
    private function handleApprovalUpdate($deal)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Processing approval update for deal: " . $deal->id);
        
        try {
            // Get form data
            $newStatus = $_POST['approval_status'] ?? '';
            $approvalNote = $_POST['approval_note'] ?? '';
            $lenderResponse = $_POST['lender_response'] ?? '';
            $followUpDate = $_POST['follow_up_date'] ?? '';
            
            // Update deal status if provided
            if (!empty($newStatus) && $newStatus !== $deal->deal_status) {
                $deal->deal_status = $newStatus;
                
                // Set funding date if approved
                if ($newStatus === 'Approved' && empty($deal->funding_date)) {
                    $deal->funding_date = date('Y-m-d');
                }
            }
            
            // Update lender approval number if provided
            if (!empty($_POST['approval_number'])) {
                $deal->lender_approval_number = $_POST['approval_number'];
            }
            
            // Update stipulations if provided
            if (!empty($_POST['stipulations'])) {
                $deal->stips_required = $_POST['stipulations'];
            }
            
            // Save the deal
            $deal->save();
            
            // Log the approval activity
            $this->logApprovalActivity($deal->id, $newStatus, $approvalNote, $lenderResponse, $followUpDate);
            
            // Show success message
            echo '<div class="alert alert-success" style="margin: 20px;">Approval status updated successfully!</div>';
            
            $GLOBALS['log']->debug("F&I Deal Center: Approval update completed successfully");
            
        } catch (Exception $e) {
            $errorMessage = "Error updating approval status: " . $e->getMessage();
            echo '<div class="alert alert-danger" style="margin: 20px;">' . htmlspecialchars($errorMessage) . '</div>';
            $GLOBALS['log']->error("F&I Deal Center: " . $errorMessage);
        }
    }
    
    /**
     * Log approval activity to Notes
     */
    private function logApprovalActivity($dealId, $status, $note, $lenderResponse, $followUpDate)
    {
        global $current_user;
        
        $GLOBALS['log']->debug("F&I Deal Center: Logging approval activity for deal: " . $dealId);
        
        // Create a note to track this activity
        $noteBean = BeanFactory::newBean('Notes');
        $noteBean->name = 'Approval Update - ' . $status;
        $noteBean->parent_type = 'DM_FIDeals';
        $noteBean->parent_id = $dealId;
        $noteBean->assigned_user_id = $current_user->id;
        
        // Build note description
        $description = "Approval Status Update:\n\n";
        $description .= "Status: " . $status . "\n";
        if (!empty($note)) {
            $description .= "Notes: " . $note . "\n";
        }
        if (!empty($lenderResponse)) {
            $description .= "Lender Response: " . $lenderResponse . "\n";
        }
        if (!empty($followUpDate)) {
            $description .= "Follow-up Date: " . $followUpDate . "\n";
        }
        $description .= "\nUpdated by: " . $current_user->full_name . " on " . date('m/d/Y h:i A');
        
        $noteBean->description = $description;
        $noteBean->save();
        
        $GLOBALS['log']->debug("F&I Deal Center: Approval activity logged as note: " . $noteBean->id);
    }
    
    /**
     * Get approval history from Notes
     */
    private function getApprovalHistory($dealId)
    {
        $GLOBALS['log']->debug("F&I Deal Center: Retrieving approval history for deal: " . $dealId);
        
        $query = "SELECT id, name, description, date_entered, assigned_user_id 
                  FROM notes 
                  WHERE parent_id = '" . $GLOBALS['db']->quote($dealId) . "' 
                  AND parent_type = 'DM_FIDeals' 
                  AND deleted = 0 
                  AND name LIKE 'Approval Update%'
                  ORDER BY date_entered DESC";
        
        $result = $GLOBALS['db']->query($query);
        $history = array();
        
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            // Get user name
            $user = BeanFactory::getBean('Users', $row['assigned_user_id']);
            $row['user_name'] = $user ? $user->full_name : 'Unknown User';
            $history[] = $row;
        }
        
        $GLOBALS['log']->debug("F&I Deal Center: Retrieved " . count($history) . " approval history records");
        
        return $history;
    }
    
    /**
     * Display the approval tracking interface
     */
    private function displayTrackingInterface($deal, $lender, $approvalHistory)
    {
        echo '<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">';
        
        // Page header
        echo '<div style="border-bottom: 2px solid #ddd; padding-bottom: 15px; margin-bottom: 20px;">';
        echo '<h2>Approval Tracking - Deal ' . htmlspecialchars($deal->deal_number) . '</h2>';
        echo '<div style="color: #666;">';
        echo 'Customer: ' . htmlspecialchars($deal->customer_name ?? 'N/A') . ' | ';
        echo 'Amount: $' . number_format($deal->amount_financed, 2) . ' | ';
        echo 'Current Status: <strong>' . htmlspecialchars($deal->deal_status) . '</strong>';
        echo '</div>';
        echo '</div>';
        
        // Two-column layout
        echo '<div style="display: flex; gap: 20px;">';
        
        // Left column - Update form
        echo '<div style="flex: 1; background: #f9f9f9; padding: 20px; border-radius: 5px;">';
        echo '<h3>Update Approval Status</h3>';
        
        echo '<form method="POST" action="index.php?module=DM_FIDeals&action=approval_tracking&record=' . $deal->id . '">';
        echo '<input type="hidden" name="update_approval" value="1">';
        
        // Status dropdown
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">Approval Status:</label>';
        echo '<select name="approval_status" style="width: 100%; padding: 8px;">';
        echo '<option value="">-- No Change --</option>';
        $statuses = array('Draft', 'Submitted', 'Under Review', 'Approved', 'Denied', 'Funded', 'Complete');
        foreach ($statuses as $status) {
            $selected = ($status === $deal->deal_status) ? 'selected' : '';
            echo '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
        }
        echo '</select>';
        echo '</div>';
        
        // Approval number
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">Approval Number:</label>';
        echo '<input type="text" name="approval_number" value="' . htmlspecialchars($deal->lender_approval_number ?? '') . '" style="width: 100%; padding: 8px;">';
        echo '</div>';
        
        // Lender response
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">Lender Response:</label>';
        echo '<textarea name="lender_response" rows="3" style="width: 100%; padding: 8px;" placeholder="Notes from lender communication..."></textarea>';
        echo '</div>';
        
        // Internal notes
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">Internal Notes:</label>';
        echo '<textarea name="approval_note" rows="3" style="width: 100%; padding: 8px;" placeholder="Internal notes about this update..."></textarea>';
        echo '</div>';
        
        // Stipulations
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">Stipulations Required:</label>';
        echo '<textarea name="stipulations" rows="3" style="width: 100%; padding: 8px;" placeholder="List any stipulations required by lender...">' . htmlspecialchars($deal->stips_required ?? '') . '</textarea>';
        echo '</div>';
        
        // Follow-up date
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">Follow-up Date:</label>';
        echo '<input type="date" name="follow_up_date" style="width: 100%; padding: 8px;">';
        echo '</div>';
        
        // Submit button
        echo '<button type="submit" style="background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer;">Update Status</button>';
        echo '</form>';
        echo '</div>';
        
        // Right column - History and lender info
        echo '<div style="flex: 1;">';
        
        // Lender information
        if ($lender) {
            echo '<div style="background: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
            echo '<h3>Lender Information</h3>';
            echo '<div><strong>Lender:</strong> ' . htmlspecialchars($lender->name) . '</div>';
            if ($lender->fi_contact_name) {
                echo '<div><strong>Contact:</strong> ' . htmlspecialchars($lender->fi_contact_name) . '</div>';
            }
            if ($lender->fi_contact_phone) {
                echo '<div><strong>Phone:</strong> ' . htmlspecialchars($lender->fi_contact_phone) . '</div>';
            }
            if ($lender->submission_email) {
                echo '<div><strong>Email:</strong> ' . htmlspecialchars($lender->submission_email) . '</div>';
            }
            if ($lender->dealer_number) {
                echo '<div><strong>Dealer #:</strong> ' . htmlspecialchars($lender->dealer_number) . '</div>';
            }
            echo '</div>';
        }
        
        // Approval history
        echo '<div style="background: #fff; border: 1px solid #ddd; border-radius: 5px;">';
        echo '<div style="background: #f5f5f5; padding: 15px; border-bottom: 1px solid #ddd;">';
        echo '<h3 style="margin: 0;">Approval History</h3>';
        echo '</div>';
        
        if (empty($approvalHistory)) {
            echo '<div style="padding: 20px; text-align: center; color: #666;">No approval history found</div>';
        } else {
            echo '<div style="max-height: 400px; overflow-y: auto;">';
            foreach ($approvalHistory as $item) {
                echo '<div style="padding: 15px; border-bottom: 1px solid #eee;">';
                echo '<div style="font-weight: bold; color: #333;">' . htmlspecialchars($item['name']) . '</div>';
                echo '<div style="font-size: 12px; color: #666; margin: 5px 0;">';
                echo 'By ' . htmlspecialchars($item['user_name']) . ' on ' . date('m/d/Y h:i A', strtotime($item['date_entered']));
                echo '</div>';
                echo '<div style="white-space: pre-wrap; font-size: 13px;">' . htmlspecialchars($item['description']) . '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
        
        echo '</div>'; // Close right column
        echo '</div>'; // Close two-column layout
        
        // Action buttons
        echo '<div style="margin-top: 20px; text-align: center;">';
        echo '<a href="index.php?module=DM_FIDeals&action=DetailView&record=' . $deal->id . '" class="btn btn-secondary">Back to Deal</a> ';
        echo '<a href="index.php?module=DM_FIDeals&action=submission&record=' . $deal->id . '" class="btn btn-primary" target="_blank">View Submission</a>';
        echo '</div>';
        
        echo '</div>'; // Close main container
        
        $GLOBALS['log']->debug("F&I Deal Center: Approval tracking interface displayed successfully");
    }
    
    /**
     * Display error message
     */
    private function displayError($message)
    {
        echo '<div class="alert alert-danger" style="margin: 20px; padding: 15px;">';
        echo '<h3>Error</h3>';
        echo '<p>' . htmlspecialchars($message) . '</p>';
        echo '<a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>';
        echo '</div>';
        
        $GLOBALS['log']->error("F&I Deal Center: Approval tracking error - " . $message);
    }
} 