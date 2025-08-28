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
    {fbvFormArea id="jmefSettingsFormAreaDiamond" title="plugins.generic.jmef.manager.settings.diamondCategory"}    
            {fbvFormSection list=true}   
                {if $scholarlyJournal}
                        {assign var="checked" value=true}
                {else}
                        {assign var="checked" value=false}
                {/if}
                {fbvElement type="checkbox" name="scholarlyJournal" id="scholarlyJournal" checked=$checked label="plugins.generic.jmef.manager.settings.scholarlyJournal"}                
                {if $noFees}
                        {assign var="checked" value=true}
                {else}
                        {assign var="checked" value=false}
                {/if}
                {fbvElement type="checkbox" name="noFees" id="noFees" checked=$checked label="plugins.generic.jmef.manager.settings.noFees"}
            
                {if $openAuthorship}
                        {assign var="checked" value=true}
                {else}
                        {assign var="checked" value=false}
                {/if}                
                {fbvElement type="checkbox" name="openAuthorship" id="openAuthorship" checked=$checked label="plugins.generic.jmef.manager.settings.openAuthorship"} 
                {if $communityOwned}
                        {assign var="checked" value=true}
                {else}
                        {assign var="checked" value=false}
                {/if}
                {fbvElement type="checkbox" name="communityOwned" id="communityOwned" checked=$checked label="plugins.generic.jmef.manager.settings.communityOwned"}
                <br />
                <strong>{translate key="plugins.generic.jmef.diamond.organisationType"}</strong>
                {if $organisationType == "nonprofit"}
                    {assign var=elementPublicChecked value=false}
                    {assign var=elementNonProfitChecked value=true}
                {else}
                    {assign var=elementPublicChecked value=true}
                    {assign var=elementNonProfitChecked value=false}
                {/if}
                {fbvElement type="radio" name="organisationType" id="organisationTypePublic" value="public" checked=$elementPublicChecked label="plugins.generic.jmef.diamond.organisationType.public"}
                 {fbvElement type="radio" name="organisationType" id="organisationTypePublic" value="nonprofit" checked=$elementNonProfitChecked label="plugins.generic.jmef.diamond.organisationType.nonProfit"}
            {/fbvFormSection}
    {/fbvFormArea}     
    {fbvFormArea id="jmefSettingsFormAreaOthers" title="plugins.generic.jmef.manager.settings.additionalMetadata"}
            {fbvFormSection for="journalDDH" title="plugins.generic.jmef.manager.settings.journalDdh"}
                    {fbvElement type="text" id="journalDDH" value=$journalDDH label="plugins.generic.jmef.manager.settings.journalDdh.description" size=$fbvStyles.size.MEDIUM}     
            {/fbvFormSection} 
            {fbvFormSection for="journalDOAJ" title="plugins.generic.jmef.manager.settings.journalDoaj"}
                    {fbvElement type="text" id="journalDOAJ" value=$journalDOAJ label="plugins.generic.jmef.manager.settings.journalDoaj.description" size=$fbvStyles.size.MEDIUM}     
            {/fbvFormSection} 
            {fbvFormSection for="journalDOI" title="plugins.generic.jmef.manager.settings.journalDoi"}
                    {fbvElement type="text" id="journalDOI" value=$journalDOI label="plugins.generic.jmef.manager.settings.journalDoi.description" size=$fbvStyles.size.MEDIUM}     
            {/fbvFormSection} 
            {fbvFormSection for="reviewType" title="plugins.generic.jmef.manager.settings.reviewType"}
                    {fbvElement type="select" label="plugins.generic.jmef.manager.settings.reviewType.description" name="reviewType" id="reviewType" defaultLabel="" defaultValue="" from=$reviewTypes selected=$reviewType translate="0" size=$fbvStyles.size.MEDIUM}
            {/fbvFormSection}  
            
            {fbvFormSection for="publisherLocation" title="plugins.generic.jmef.manager.settings.publisherLocation"}
            {translate key="plugins.generic.jmef.manager.settings.publisherName" publisherName=$publisherName}
                    {fbvElement type="select" label="plugins.generic.jmef.manager.settings.publisherLocation.description" name="publisherLocation" id="publisherLocation" defaultLabel="" defaultValue="" from=$countries selected=$publisherLocation translate="0" size=$fbvStyles.size.MEDIUM}
            {/fbvFormSection}
            
            {fbvFormSection for="journalKeywords" title="plugins.generic.jmef.manager.settings.otherOrganisations"}
                {fbvElement type="text" label="plugins.generic.jmef.manager.settings.otherOrganisations.description" name="otherOrganisations" id="otherOrganisations" value=$otherOrganisations  size=$fbvStyles.size.LARGE}               
            {/fbvFormSection}
            
            {fbvFormSection for="journalKeywords" title="plugins.generic.jmef.manager.settings.keywords"}
                {fbvElement type="text" label="plugins.generic.jmef.manager.settings.keywords.description" multilingual="true" name="journalKeywords" id="journalKeywords" value=$journalKeywords  size=$fbvStyles.size.LARGE}               
            {/fbvFormSection}
            
            {fbvFormSection for="oecdClassification" title="plugins.generic.jmef.manager.settings.oecdClassification"}
                    {fbvElement type="select" label="plugins.generic.jmef.manager.settings.oecdClassification.description" name="oecdClassification" id="oecdClassification" defaultLabel="" defaultValue="" from=$oecdClassificationsList selected=$oecdClassification translate="0" size=$fbvStyles.size.MEDIUM}
            {/fbvFormSection}
            
    {/fbvFormArea}
    {fbvFormButtons submitText="common.save"}
    <p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</form>
