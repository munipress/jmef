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

    const CONFIG_VARS = array('reviewType' => array('string', false),
        'journalDOI' => array('string', false),
        'journalDDH' => array('string', false),
        'journalDOAJ' => array('string', false),
        'publisherLocation' => array('string', false),
        'otherOrganisations' => array('string', false),
        'scholarlyJournal' => array('bool', false),
        'communityOwned' => array('bool', false),
        'noFees' => array('bool', false),
        'openAuthorship' => array('bool', false),
        'journalKeywords' => array('string', true),
        'oecdClassification' => array('string', false)
    );
    const REVIEW_TYPE = array(
        'peer' => 'peer'
    );
    const OECD_CLASSIFICATION_LIST = array('1' => 'Natural Sciences',
        '1.01' => 'Mathematics',
        '1.02' => 'Computer and information sciences',
        '1.03' => 'Physical sciences',
        '1.04' => 'Chemical sciences',
        '1.05' => 'Earth and related environmental sciences',
        '1.06' => 'Biological sciences',
        '1.07' => 'Other natural sciences',
        '2' => 'Engineering and Technology',
        '2.01' => 'Civil engineering',
        '2.02' => 'Electrical engineering, electronic engineering, information engineering',
        '2.03' => 'Mechanical engineering',
        '2.04' => 'Chemical engineering',
        '2.05' => 'Materials engineering',
        '2.06' => 'Medical engineering',
        '2.07' => 'Environmental engineering',
        '2.08' => 'Environmental biotechnology',
        '2.09' => 'Industrial biotechnology',
        '2.1' => 'Nano-technology',
        '2.11' => 'Other engineering and technologies',
        '3' => 'Medical and Health Sciences',
        '3.01' => 'Basic medical research',
        '3.02' => 'Clinical medicine',
        '3.03' => 'Health sciences',
        '3.04' => 'Medical biotechnology',
        '3.05' => 'Other medical science',
        '4' => 'Agricultural and Veterinary Sciences',
        '4.01' => 'Agriculture, forestry, fisheries',
        '4.02' => 'Animal and dairy science',
        '4.03' => 'Veterinary science',
        '4.05' => 'Other agricultural science',
        '5' => 'Social Sciences',
        '5.01' => 'Psychology and cognitive science',
        '5.02' => 'Economics and business',
        '5.03' => 'Educational sciences',
        '5.04' => 'Sociology',
        '5.05' => 'Law',
        '5.06' => 'Political science',
        '5.07' => 'Social and economic geography',
        '5.08' => 'Media and communication',
        '5.09' => 'Other social sciences',
        '6' => 'Humanities and the arts',
        '6.01' => 'History and archeology',
        '6.02' => 'Languages and literature',
        '6.03' => 'Philosophy, ethics and religion',
        '6.04' => 'Art',
        '6.05' => 'Other Humanities');

    /** @var int */
    var $_contextId;

    /** @var object */
    var $_plugin;

    /** @var context * */
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
        $this->_data = array();
        foreach (self::CONFIG_VARS as $metadata => $settings) {
            $this->_data[$metadata] = $this->_context->getSetting($metadata);
        }
    }

    /**
     * Assign form data to user-submitted data.
     */
    function readInputData() {
        $this->readUserVars(array_keys(self::CONFIG_VARS));
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

        $templateMgr->assign('publisherName', $this->_context->getData('publisherInstitution'));
        $templateMgr->assign('reviewTypes', self::REVIEW_TYPE);
        $templateMgr->assign('oecdClassificationsList', self::OECD_CLASSIFICATION_LIST);
        $templateMgr->assign('countries', $countries);
        $templateMgr->assign('pluginName', $this->_plugin->getName());
        $templateMgr->assign('applicationName', Application::get()->getName());
        return parent::fetch($request, $template, $display);
    }

    /**
     * @copydoc Form::execute()
     */
    function execute(...$functionArgs) {

        $context = $this->_context;

        foreach (self::CONFIG_VARS as $configVar => $setting) {
            if ($settings[1]) {
                $context->setData($configVar, $this->getData($configVar, null));
            } else {
                $context->setData($configVar, $this->getData($configVar));
            }
        }
        parent::execute(...$functionArgs);

        $contextDao = DAORegistry::getDAO('JournalDAO'); /* @var $contextDao JournalDAO */
        $contextDao->updateObject($context);
    }

}

?>
