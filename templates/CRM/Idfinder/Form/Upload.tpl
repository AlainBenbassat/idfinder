<h2>Upload an Excel File</h2>
<div class="help">The Excel file you upload here should contain contact data with at least first name, last name, and email. Based on these three columns, the contact ID will be added to an existing ID column. If not present, a new column contact_id will be added.</div>

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
