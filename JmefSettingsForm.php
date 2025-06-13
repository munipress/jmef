<?php

/**
 * @file JmefSettingsForm.php
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class JmefSettingsForm
 * @ingroup plugins_generic_jmef
 *
 * @brief Form for journal managers to modify Jmef plugin settings
 */

namespace APP\plugins\generic\jmef;

use APP\core\Application;
use APP\journal\JournalDAO;
use APP\template\TemplateManager;
use PKP\db\DAORegistry;
use PKP\form\Form;

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
        '1.01' => 'Natural sciences - Mathematics',
        '1.02' => 'Natural sciences - Computer and information sciences',
        '1.03' => 'Natural sciences - Physical sciences',
        '1.04' => 'Natural sciences - Chemical sciences',
        '1.05' => 'Natural sciences - Earth and related environmental sciences',
        '1.06' => 'Natural sciences - Biological sciences',
        '1.07' => 'Natural sciences - Other natural sciences',
        '2' => 'Engineering and Technology',
        '2.01' => 'Engineering and technology - Civil engineering',
        '2.02' => 'Engineering and technology - Electrical engineering, electronic engineering, information engineering',
        '2.03' => 'Engineering and technology - Mechanical engineering',
        '2.04' => 'Engineering and technology - Chemical engineering',
        '2.05' => 'Engineering and technology - Materials engineering',
        '2.06' => 'Engineering and technology - Medical engineering',
        '2.07' => 'Engineering and technology - Environmental engineering',
        '2.08' => 'Engineering and technology - Environmental biotechnology',
        '2.09' => 'Engineering and technology - Industrial biotechnology',
        '2.1' => 'Engineering and technology - Nano-technology',
        '2.11' => 'Engineering and technology - Other engineering and technologies',
        '3' => 'Medical and Health Sciences',
        '3.01' => 'Medical and health sciences - Basic medical research',
        '3.02' => 'Medical and health sciences - Clinical medicine',
        '3.03' => 'Medical and health sciences - Health sciences',
        '3.04' => 'Medical and health sciences - Medical biotechnology',
        '3.05' => 'Medical and health sciences - Other medical science',
        '4' => 'Agricultural sciences',
        '4.01' => 'Agricultural sciences - Agriculture, forestry, fisheries',
        '4.02' => 'Agricultural sciences - Animal and dairy science',
        '4.03' => 'Agricultural sciences - Veterinary science',
        '4.04' => 'Agricultural sciences - Agricultural biotechnology',
        '4.05' => 'Agricultural sciences - Other agricultural science',
        '5' => 'Social Sciences',
        '5.01' => 'Social science - Psychology and cognitive science',
        '5.02' => 'Social science - Economics and business',
        '5.03' => 'Social science - Educational sciences',
        '5.04' => 'Social science - Sociology',
        '5.05' => 'Social science - Law',
        '5.06' => 'Social science - Political science',
        '5.07' => 'Social science - Social and economic geography',
        '5.08' => 'Social science - Media and communication',
        '5.09' => 'Social science - Other social sciences',
        '6' => 'Humanities',
        '6.01' => 'Humanities - History and archeology',
        '6.02' => 'Humanities - Languages and literature',
        '6.03' => 'Humanities - Philosophy, ethics and religion',
        '6.04' => 'Humanities - Arts (arts, history of arts, performing arts, music)',
        '6.05' => 'Humanities - Other Humanities');
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
        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
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

        foreach (self::CONFIG_VARS as $configVar => $settings) {
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
