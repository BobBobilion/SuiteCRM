{*
/**
 * SuiteCRM AutoInventory EditView Header Template
 * 
 * This template sets up the proper form action and return parameters
 * to ensure saves redirect correctly through our custom controller.
 */
*}

<form name="EditView" id="EditView" method="POST" action="index.php">
<input type="hidden" name="module" value="AutoInventory">
<input type="hidden" name="record" value="{$fields.id.value}">
<input type="hidden" name="action" value="save">
<input type="hidden" name="return_module" value="AutoInventory">
<input type="hidden" name="return_action" value="DetailView">
<input type="hidden" name="return_id" value="{$fields.id.value}">
<input type="hidden" name="isDuplicate" value="{$isDuplicate}">
<input type="hidden" name="offset" value="{$offset}">

{* Include any additional hidden fields for proper form handling *}
{foreach from=$fields key=field_name item=field}
    {if $field.type == 'id' && $field_name != 'id'}
        <input type="hidden" name="{$field_name}" value="{$field.value}">
    {/if}
{/foreach}

{* Add CSRF token if available *}
{if isset($SUGAR_TOKEN_NAME) && isset($SUGAR_TOKEN_VALUE)}
    <input type="hidden" name="{$SUGAR_TOKEN_NAME}" value="{$SUGAR_TOKEN_VALUE}">
{/if}

<script type="text/javascript">
{literal}
// Ensure form submits properly
document.addEventListener('DOMContentLoaded', function() {
    console.log('AutoInventory EditView form initialized');
    
    // Log form action for debugging
    var form = document.getElementById('EditView');
    if (form) {
        console.log('Form action set to:', form.action);
        console.log('Form method:', form.method);
        
        // Validate form before submit
        form.addEventListener('submit', function(e) {
            console.log('AutoInventory form submitting...');
            
            // You can add any pre-submit validation here
            var nameField = document.getElementsByName('name')[0];
            if (nameField && nameField.value.trim() === '') {
                alert('Please enter a name for this auto record.');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    }
});
{/literal}
</script> 