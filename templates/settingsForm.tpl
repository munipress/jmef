{**
* plugins/importexport/jmef/templates/settingsForm.tpl
*
* Copyright (c) 2014-2020 Simon Fraser University
* Copyright (c) 2003-2020 John Willinsky
* Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
*
* JMEF plugin settings
*
*}
<script type="text/javascript">
    $(function () {ldelim}
            // Attach the form handler.
            $('#jmefSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
    {rdelim});
</script>
<form class="pkp_form" id="jmefSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
    {csrf}
    {fbvFormArea id="jmefSettingsFormArea"}

            {fbvFormSection for="ownerType" title="plugins.generic.jmef.manager.settings.ownerType"}
                    {fbvElement type="select" label="plugins.generic.jmef.manager.settings.ownerType.description" name="ownerType" id="ownerType" defaultLabel="" defaultValue="" from=$ownerTypes selected=$ownerType translate="0" size=$fbvStyles.size.MEDIUM}
            {/fbvFormSection}  
            
            {fbvFormSection for="journalDOI" title="plugins.generic.jmef.manager.settings.journalDoi"}
                    {fbvElement type="text" id="journalDOI" value=$journalDOI label="plugins.generic.jmef.manager.settings.journalDoi.description" size=$fbvStyles.size.MEDIUM}     
            {/fbvFormSection} 
            
            
            {fbvFormSection for="publisherLocation" title="plugins.generic.jmef.manager.settings.publisherLocation"}
            {translate key="plugins.generic.jmef.manager.settings.publisherName" publisherName=$publisherName}
                    {fbvElement type="select" label="plugins.generic.jmef.manager.settings.publisherLocation.description" name="publisherLocation" id="publisherLocation" defaultLabel="" defaultValue="" from=$countries selected=$publisherLocation translate="0" size=$fbvStyles.size.MEDIUM}
            {/fbvFormSection}
            
            {fbvFormSection for="journalKeywords" title="plugins.generic.jmef.manager.settings.keywords"}
                {fbvElement type="text" label="plugins.generic.jmef.manager.settings.keywords.description" multilingual="true" name="journalKeywords" id="journalKeywords" value=$journalKeywords  size=$fbvStyles.size.LARGE}               
            {/fbvFormSection}
            
             
            {fbvFormSection list=true title="plugins.generic.jmef.manager.settings.journalPolicy"}   
                {if $peerReviewUsed}
                        {assign var="checked" value=true}
                {else}
                        {assign var="checked" value=false}
                {/if}
                {fbvElement type="checkbox" name="peerReviewUsed" id="peerReviewUsed" checked=$checked label="plugins.generic.jmef.manager.settings.peerReviewUsed"}
            
                {if $openAuthorship}
                        {assign var="checked" value=true}
                {else}
                        {assign var="checked" value=false}
                {/if}                
                {fbvElement type="checkbox" name="openAuthorship" id="openAuthorship" checked=$checked label="plugins.generic.jmef.manager.settings.openAuthorship"} 
            {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormButtons submitText="common.save"}
    <p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</form>
