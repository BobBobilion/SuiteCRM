{*
 * SuiteCRM Service & Parts Hub - Parts Lookup Template
 *}
<!DOCTYPE html>
<html>
<head>
    <title>{$MOD.LBL_PARTS_LOOKUP} - {$APP.LBL_BROWSER_TITLE}</title>
    <link rel="stylesheet" type="text/css" href="themes/SuiteP/css/style.css">
    <script src="include/javascript/jquery/jquery-min.js"></script>
    <script src="modules/DM_PartsInventory/js/DM_PartsInventory.js"></script>
</head>
<body class="popup-body">
    <div class="popup-container">
        <div class="popup-header">
            <h3>{$MOD.LBL_PARTS_LOOKUP}</h3>
        </div>
        
        <div class="popup-content">
            <!-- Search Form -->
            <form id="parts_search_form" method="post" action="index.php">
                <input type="hidden" name="module" value="DM_PartsInventory">
                <input type="hidden" name="action" value="parts_lookup">
                
                <table class="search-form" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="20%">
                            <label for="part_number">{$MOD.LBL_PART_NUMBER}:</label>
                        </td>
                        <td width="25%">
                            <input type="text" name="part_number" id="part_number" value="{$search_criteria.part_number}" placeholder="Enter part number">
                        </td>
                        <td width="20%">
                            <label for="description">{$MOD.LBL_DESCRIPTION}:</label>
                        </td>
                        <td width="25%">
                            <input type="text" name="description" id="description" value="{$search_criteria.description}" placeholder="Enter description">
                        </td>
                        <td width="10%">
                            <input type="submit" value="{$APP.LBL_SEARCH_BUTTON_LABEL}" class="button">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="manufacturer">{$MOD.LBL_MANUFACTURER}:</label>
                        </td>
                        <td>
                            <input type="text" name="manufacturer" id="manufacturer" value="{$search_criteria.manufacturer}" placeholder="Enter manufacturer">
                        </td>
                        <td>
                            <label for="category">{$MOD.LBL_CATEGORY}:</label>
                        </td>
                        <td>
                            <select name="category" id="category">
                                <option value="">-- {$APP.LBL_NONE} --</option>
                                {foreach from=$categories key=key item=value}
                                <option value="{$key}" {if $search_criteria.category == $key}selected{/if}>{$value}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td>
                            <input type="button" value="{$APP.LBL_CLEAR_BUTTON_LABEL}" class="button" onclick="clearSearchForm();">
                        </td>
                    </tr>
                </table>
            </form>
            
            <br>
            
            <!-- Parts Results -->
            <div class="parts-results">
                {if $parts}
                <table class="list-view" cellpadding="0" cellspacing="0" width="100%">
                    <thead>
                        <tr class="pagination">
                            <th width="15%">{$MOD.LBL_PART_NUMBER}</th>
                            <th width="30%">{$MOD.LBL_DESCRIPTION}</th>
                            <th width="15%">{$MOD.LBL_MANUFACTURER}</th>
                            <th width="10%">{$MOD.LBL_CATEGORY}</th>
                            <th width="8%">{$MOD.LBL_QUANTITY_ON_HAND}</th>
                            <th width="12%">{$MOD.LBL_RETAIL_PRICE}</th>
                            <th width="10%">{$APP.LBL_ACTION}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$parts item=part}
                        <tr class="{cycle values='oddListRowS1,evenListRowS1'}">
                            <td>{$part.part_number}</td>
                            <td>{$part.description|truncate:50}</td>
                            <td>{$part.manufacturer}</td>
                            <td>{$part.category}</td>
                            <td align="right">{$part.quantity_on_hand}</td>
                            <td align="right">${$part.retail_price|number_format:2}</td>
                            <td align="center">
                                <input type="button" value="{$APP.LBL_SELECT_BUTTON_LABEL}" class="button" 
                                       onclick="selectPart('{$part.id}', '{$part.part_number}', '{$part.description|escape:'javascript'}', '{$part.retail_price}');">
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
                {else}
                <div class="no-results">
                    {if $search_criteria}
                        <p>No parts found matching your search criteria.</p>
                    {else}
                        <p>Enter search criteria to find parts, or browse recent parts below.</p>
                    {/if}
                </div>
                {/if}
            </div>
        </div>
        
        <div class="popup-footer">
            <input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="button" onclick="window.close();">
        </div>
    </div>

    <script type="text/javascript">
        function selectPart(partId, partNumber, description, retailPrice) {
            if (window.opener && window.opener.DM_PartsInventory) {
                window.opener.DM_PartsInventory.selectPart(partId, partNumber, description, retailPrice);
            } else if (window.opener && window.opener.addSelectedPart) {
                // Fallback for other integrations
                window.opener.addSelectedPart(partId, partNumber, description, retailPrice);
            } else {
                // Last resort - just close the window
                alert('Part selected: ' + partNumber + ' - ' + description + ' ($' + retailPrice + ')');
                window.close();
            }
        }
        
        function clearSearchForm() {
            document.getElementById('part_number').value = '';
            document.getElementById('description').value = '';
            document.getElementById('manufacturer').value = '';
            document.getElementById('category').value = '';
        }
        
        // Enable enter key search
        $(document).ready(function() {
            $('#parts_search_form input[type="text"]').keypress(function(e) {
                if (e.which == 13) {
                    $('#parts_search_form').submit();
                    return false;
                }
            });
        });
    </script>
</body>
</html>