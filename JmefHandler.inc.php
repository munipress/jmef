<?php

/**
 * @file pages/jmef/JmefHandler.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class JmefHandler
 * @ingroup pages_jmef
 *
 * @brief Produce a Journal Metadata Exchange Format in XML format for submitting to aggregators.
 */
class JmefHandler extends Handler {

    var $_oecdClassification = array('1' => 'Natural Sciences',
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

    /**
     * Generate an XML sitemap for webcrawlers
     * Creates a sitemap index if in site context, else creates a sitemap
     * @param $args array
     * @param $request Request
     */
    function index($args, $request) {
        $context = $request->getContext();
        if ($context) {
            $doc = $this->_createContextJmef($request);
            header("Content-Type: application/xml");
            header("Cache-Control: private");
            header("Content-Disposition: inline; filename=\"jmef.xml\"");
            echo $doc;
        }
    }

    /**
     * @copydoc 
     */
    function _createContextJmef($request) {
        $doc = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $context = $request->getJournal();
        $baseUrl = $request->getDispatcher()->url(
                $request,
                ROUTE_PAGE,
                $context->getPath()
        );

        $doc .= "<journal xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n";

        /* Journal IDs */
        if ($journalDDH = trim($context->getData('journalDDH'))) {
            $doc .= "<id type=\"ddh\">" . $journalDDH . "</id>";
        }
        if ($journalDOAJ = trim($context->getData('journalDOAJ'))) {
            $doc .= "<id type=\"doaj\">" . $journalDOAJ . "</id>";
        }
        if ($journalDOI = trim($context->getData('journalDOI'))) {
            $doc .= "<id type=\"doi\">" . $journalDOI . "</id>";
        }

        /* Journal title */
        $doc .= "\t<title-group>\n";

        foreach ($context->getSupportedFormLocales() AS $supportedLocale) {
            $titleLanguages = "";
            if ($languages = $this->getLanguage($supportedLocale)) {
                if (sizeof($languages) >= 2 && $languages[1]) {
                    $titleLanguages .= "language-iso2=\"" . $languages[1] . "\" ";
                }
                if (sizeof($languages) == 3 && $languages[2]) {
                    $titleLanguages .= "language-iso1=\"" . $languages[2] . "\"";
                }
            }
            if ($supportedLocale == $context->getPrimaryLocale()) {
                if ($title = $context->getName($supportedLocale)) {
                    $doc .= "\t\t<title " . $titleLanguages . ">" . $title . "</title>\n";
                }
                if ($subtitle = $context->getSetting("subname", $supportedLocale)) {
                    $doc .= "\t\t<other-title type=\"subtitle\" " . $titleLanguages . ">" . $subtitle . "</other-title>\n";
                }
            } else {
                if ($title = $context->getName($supportedLocale)) {
                    $doc .= "\t\t<other-title type=\"translation\" " . $titleLanguages . ">" . $title . "</other-title>\n";
                }
            }
        }
        $doc .= "\t</title-group>\n";

        /* Diamond criteria */
        $doc .= "<diamond-criteria>";
        if ($context->getData('scholarlyJournal')) {
            $doc .= "\t\t<scholarly-journal value=\"true\"/>\n";
        } else {
            $doc .= "\t\t<scholarly-journal value=\"false\"/>\n";
        }
        if ($context->getData('communityOwned')) {
            $doc .= "\t\t<community-owned value=\"true\"/>\n";
        } else {
            $doc .= "\t\t<community-owned value=\"false\"/>\n";
        }
        if ($context->getData('publishingMode') == 0 && $context->getData('paymentsEnabled') == 0 && $this->checkCClicence($context->getData('licenseUrl'))) {
            $doc .= "\t\t<open-access-with-open-licenses value=\"true\"/>\n";
        } else {
            $doc .= "\t\t<open-access-with-open-licenses value=\"false\"/>\n";
        }

        if ($context->getData('noFees')) {
            $doc .= "\t\t<no-fees value=\"true\"/>\n";
        } else {
            $doc .= "\t\t<no-fees value=\"false\"/>\n";
        }

        if ($context->getData('openAuthorship')) {
            $doc .= "\t\t<open-to-all-authors value=\"true\" />\n";
        } else {
            $doc .= "\t\t<open-to-all-authors value=\"false\" />\n";
        }

        $doc .= "</diamond-criteria>";

        /* ISSNs */
        if ($printIssn = $context->getData('printIssn')) {
            $doc .= "\t<issn publication-format=\"print\">" . $printIssn . "</issn>\n";
        }
        if ($onlineIssn = $context->getData('onlineIssn')) {
            $doc .= "\t<issn publication-format=\"electronic\">" . $onlineIssn . "</issn>\n";
        }

        /* Publisher and other organisations */

        if ($publisher = $context->getData('publisherInstitution')) {
            $doc .= "\t<publisher>\n" .
                    "\t\t<publisher-name>" . $publisher . "</publisher-name>\n";
            if ($countryCode = $context->getData('publisherLocation')) {
                $isoCodes = new \Sokil\IsoCodes\IsoCodesFactory();
                $country = $isoCodes->getCountries()->getByAlpha2($countryCode);
                $doc .= "\t\t<location>\n" .
                        "\t\t\t<country iso2=\"" . $countryCode . "\" iso3=\"" . $country->getAlpha3() . "\">" . $country->getLocalName() . "</country>\n" .
                        "\t\t</location>\n";
            }
            $doc .= "\t</publisher>\n";
            if ($otherOrganisations = trim($context->getData('otherOrganisations'))) {
                $otherOrganisationsExploded = explode(";", $otherOrganisations);
                foreach ($otherOrganisationsExploded as $organisation) {
                    $doc .= "\t<other-organization>\n";
                    if (trim($organisation)) {
                        $doc .= "\t\t<name>" . $organisation . "</name>\n";
                    }
                    $doc .= "\t</other-organization>\n";
                }                
            }
        }
        $doc .= "\t<publication-policy>\n";

        if ($reviewType = $context->getData('reviewType')) {
            $doc .= "\t\t<review-process type=\"" . $reviewType . "\" />\n";
        }

        if ($allLanguages = $context->getSupportedSubmissionLocales()) {
            $doc .= "\t\t<languages>\n";
            foreach ($allLanguages AS $code) {
                $languages = $this->getLanguage($code);
                $doc .= "\t\t\t<language ";
                if (sizeof($languages) >= 2 && $languages[1]) {
                    $doc .= "iso2=\"" . $languages[1] . "\" ";
                }
                if (sizeof($languages) == 3 && $languages[2]) {
                    $doc .= "iso1=\"" . $languages[2] . "\"";
                }
                $doc .= ">" . $languages[0] . "</language>\n";
            }
            $doc .= "\t\t</languages>\n";
        }


        if ($license = $context->getData('licenseUrl')) {
            $doc .= "\t\t<licenses>\n" .
                    "\t\t\t<license xlink:href=\"" . $license . "\" />\n" .
                    "\t\t</licenses>\n";
        }

        $doc .= "\t</publication-policy>\n";

        $doc .= "\t<self-uri xlink:href=\"" . $baseUrl . "\" />\n";

        if ($journalKeywords = trim($context->getData('journalKeywords', $context->getPrimaryLocale()))) {
            $keywords = explode(";", $journalKeywords);
            $doc .= "\t<keywords>\n";
            foreach ($keywords as $keyword) {
                if (trim($keyword)) {
                    $doc .= "\t\t<keyword>" . $keyword . "</keyword>\n";
                }
            }
            $doc .= "\t</keywords>\n";
        }


        if ($oecdClassification = $context->getData('oecdClassification')) {
            $doc .= "\t<classifications>" .
                    "\t\t<classification type=\"oecd-2007\">" .
                    "\t\t\t<class code=\"" . $oecdClassification . "\" value=\"" . $this->_oecdClassification[$oecdClassification] . "\" /> " .
                    "\t\t</classification>" .
                    "\t</classifications>";
        }

        $doc .= "</journal>";
        return $doc;
    }

    function getLanguage($string) {
        $key = trim($string);

        $languages = array(
            "ca_ES" => array("Catalan", "CAT", "CA"),
            "da_DK" => array("Danish", "DAN", "DA"),
            "de_DE" => array("German", "GER", "DE"),
            "el_GR" => array("Greek", "ELL", "EL"),
            "en_US" => array("English", "ENG", "EN"),
            "es_AR" => array("Spanish (Argentina)"),
            "es_ES" => array("Spanish", "SPA", "ES"),
            "eu_ES" => array("Basque (Spain)"),
            "fr_CA" => array("French (Canada)"),
            "it_IT" => array("Italian", "ITA", "IT"),
            "nl_NL" => array("Dutch", "NLD", "NL"),
            "pt_BR" => array("Portuguese (Brazil)"),
            "tr_TR" => array("Turkish", "TUR", "TR"),
            "uk_UA" => array("Ukrainian", "UKR", "UK"),
            "zh_CN" => array("Chinese", "ZHO", "ZH"),
            "cs_CZ" => array("Czech", "CES", "CS"),
            "fa_IR" => array("Persian", "FAS", "FA"),
            "gl_ES" => array("Galician (Spain)", "GLG", "GL"),
            "hr_HR" => array("Croatian", "HRV", "HR"),
            "id_ID" => array("Indonesian", "ID", "IND"),
            "ja_JP" => array("Japanese", "JAP", "JA"),
            "mk_MK" => array("Macedonian", "MKD", "MK"),
            "ml_IN" => array("Malayalam", "MAL", "ML"),
            "no_NO" => array("Norwegian", "NOR", "NO"),
            "pl_PL" => array("Polish", "POL", "PL"),
            "pt_PT" => array("Portuguese", "POR", "PT"),
            "ro_RO" => array("Romanian", "RON", "RO"),
            "ru_RU" => array("Russian", "RUS", "RU"),
            "sr_SR" => array("Serbian", "SRP", "SR"),
            "sv_SE" => array("Swedish", "SWE", "SV"),
            "vi_VN" => array("Vietnamese", "VIE", "VI"),
            "zh_TW" => array("Chinese - TAIWAN"),
            "sk_SK" => array("Slovak", "SLK", "SK"),
            "fr_FR" => array("French", "FRA", "FR")
        );
        if (key_exists($key, $languages)) {
            return $languages[$key];
        } else {
            return false;
        }
    }

    public function checkCClicence($ccLicenseURL) {
        $licenseKeyMap = array(
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-nc-nd/4.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-nc/4.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-nc-sa/4.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-nd/4.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by/4.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-sa/4.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-nc-nd/3.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-nc/3.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-nc-sa/3.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-nd/3.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by/3.0[/]?|',
            '|http[s]?://(www\.)?creativecommons.org/licenses/by-sa/3.0[/]?|'
        );

        foreach ($licenseKeyMap as $pattern) {
            if (preg_match($pattern, $ccLicenseURL)) {
                return true;
            }
        }
        return false;
    }

}
