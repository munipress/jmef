<?php

/**
 * @file JmefSettingsForm.inc.php
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class JmefSettingsForm
 * @ingroup plugins_generic_jmef
 *
 * @brief Form for journal managers to modify Jmef plugin settings
 */

// $Id$


import('lib.pkp.classes.form.Form');

class JmefSettingsForm extends Form {

	/** @var int */
	var $_contextId;

	/** @var object */
	var $_plugin;
        
        /** @var context **/
        var $_context;
        
	/**
	 * Constructor
	 * @param $plugin object
	 * @param $contextId int
	 */
	function __construct($plugin, $context) {
		$this->_plugin = $plugin;
                $this->_context = $context;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
                $this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
                $context = $this->_context;
		$this->_data = array(		
                    'ownerType' => $context->getSetting('ownerType'),
                    'journalDOI' => $context->getSetting('journalDOI'),
                    'publisherLocation' => $context->getSetting('publisherLocation'),
                    'peerReviewUsed' => $context->getSetting('peerReviewUsed'),
                    'openAuthorship' => $context->getSetting('openAuthorship'),
                    'journalKeywords' => $context->getSetting('journalKeywords')
		);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array( 
                    'ownerType',
                    'journalDOI',
                    'publisherLocation',
                    'peerReviewUsed',
                    'openAuthorship',
                    'journalKeywords'));
	}

        /**
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
                
                
                $isoCodes = new \Sokil\IsoCodes\IsoCodesFactory();
		$countries = array();
		foreach ($isoCodes->getCountries() as $country) {
			$countries[$country->getAlpha2()] = $country->getLocalName();
		}
		asort($countries);
                                
                $ownerTypes = array('community');
                
                $templateMgr->assign('publisherName', $this->_context->getData('publisherInstitution'));  
                $templateMgr->assign('ownerTypes', $ownerTypes);     
		$templateMgr->assign('countries', $countries);                
		$templateMgr->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request, $template, $display);
	}
        
        /**
	 * @copydoc Form::execute()
	 */
	function execute(...$functionArgs) {
                $this->_context->updateSetting('ownerType', trim($this->getData('ownerType'), "\"\';"), 'string');
                $this->_context->updateSetting('journalDOI', trim($this->getData('journalDOI'), "\"\';"), 'string');
                $this->_context->updateSetting('publisherLocation', trim($this->getData('publisherLocation'), "\"\';"), 'string');
                $this->_context->updateSetting('peerReviewUsed', $this->getData('peerReviewUsed'), 'bool');
                $this->_context->updateSetting('openAuthorship', $this->getData('openAuthorship'), 'bool');
                $this->_context->updateSetting('journalKeywords', $this->getData('journalKeywords', null), 'string', true);

		parent::execute(...$functionArgs);
	}
}

?>
