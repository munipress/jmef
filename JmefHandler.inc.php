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

     var $_languages = array(
        "ar_IQ" => array("Arabic (Iraq)", "ARA", "AR"),
        "bs_BA" => array("Bosnian", "BOS", "BS"),
        "ca_ES" => array("Catalan", "CAT", "CA"),
        "cs_CZ" => array("Czech", "CES", "CS"),
        "da_DK" => array("Danish", "DAN", "DA"),
        "de_DE" => array("German", "DEU", "DE"),
        "el_GR" => array("Greek", "ELL", "EL"),
        "en_US" => array("English", "ENG", "EN"),
        "es_ES" => array("Spanish", "SPA", "ES"),
        "eu_ES" => array("Basque", "EUS", "EU"),
        "fa_IR" => array("Persian", "FAS", "FA"),
        "fi_FI" => array("Finnish", "FIN", "FI"),
        "fr_CA" => array("French (Canada)", "FRA", "FR"),
        "fr_FR" => array("French", "FRA", "FR"),
        "gd_GB" => array("Scottish Gaelic", "GLA", "GD"),
        "he_IL" => array("Hebrew", "HEB", "HE"),
        "hi_IN" => array("Hindi", "HIN", "HI"),
        "hr_HR" => array("Croatian", "HRV", "HR"),
        "hu_HU" => array("Hungarian", "HUN", "HU"),
        "hy_AM" => array("Armenian", "HYE", "HY"),
        "id_ID" => array("Indonesian", "IND", "ID"),
        "is_IS" => array("Icelandic", "ISL", "IS"),
        "it_IT" => array("Italian", "ITA", "IT"),
        "ja_JP" => array("Japanese", "JPN", "JA"),
        "ka_GE" => array("Georgian", "KAT", "KA"),
        "kk_KZ" => array("Kazakh", "KAZ", "KK"),
        "ko_KR" => array("Korean", "KOR", "KO"),
        "ku_IQ" => array("Kurdish", "KUR", "KU"),
        "mk_MK" => array("Macedonian", "MKD", "MK"),
        "mn_MN" => array("Mongolian", "MON", "MN"),
        "nb_NO" => array("Norwegian Bokmal", "NOB", "NB"),
        "nl_NL" => array("Dutch", "NLD", "NL"),
        "pl_PL" => array("Polish", "POL", "PL"),
        "pt_BR" => array("Portuguese (Brazil)", "POR", "PT"),
        "pt_PT" => array("Portuguese", "POR", "PT"),
        "ro_RO" => array("Romanian", "RON", "RO"),
        "ru_RU" => array("Russian", "RUS", "RU"),
        "sk_SK" => array("Slovak", "SLK", "SK"),
        "sl_SI" => array("Slovenian", "SLV", "SL"),
        "sr_RS@cyrillic" => array("Serbian (Cyrillic)", "SRP", "SR"),
        "sr_RS@latin" => array("Serbian (Latin)", "SRP", "SR"),
        "sv_SE" => array("Swedish", "SWE", "SV"),
        "tr_TR" => array("Turkish", "TUR", "TR"),
        "uk_UA" => array("Ukrainian", "UKR", "UK"),
        "ur_PK" => array("Urdu", "URD", "UR"),
        "uz_UZ@cyrillic" => array("Uzbek (Cyrillic)", "UZB", "UZ"),
        "uz_UZ@latin" => array("Uzbek (Latin)", "UZB", "UZ"),
        "vi_VN" => array("Vietnamese", "VIE", "VI"),
        "zh_CN" => array("Chinese (Simplified)", "ZHO", "ZH"),
        "zh_TW" => array("Chinese (Traditional)", "ZHO", "ZH"),
        "dsb" => array("Lower Sorbian", "DSB", ""),
        "hsb" => array("Upper Sorbian", "HSB", "")
    );
    
    var $_oecdClassificationsList = array('1' => 'Natural Sciences',
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
        $doc .= "\t<organizations>\n";
        if ($publisher = $context->getData('publisherInstitution')) {
            $doc .= "\t<publisher>\n" .
                    "\t\t<name>" . $publisher . "</name>\n";
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
        $doc .= "\t</organizations>\n";
        $doc .= "\t<publication-policy>\n";

        if ($reviewType = $context->getData('reviewType')) {
            $doc .= "\t\t<review-process type=\"" . $reviewType . "\" />\n";
        }

        if ($allLanguages = $context->getSupportedSubmissionLocales()) {
            $doc .= "\t\t<languages>\n";
            foreach ($allLanguages AS $code) {
                if($languages = $this->getLanguage($code)){
                  $doc .= "\t\t\t<language ";
                  if (sizeof($languages) >= 2 && $languages[1]) {
                      $doc .= "iso2=\"" . $languages[1] . "\" ";
                  }
                  if (sizeof($languages) == 3 && $languages[2]) {
                      $doc .= "iso1=\"" . $languages[2] . "\"";
                  }
                  $doc .= ">" . $languages[0] . "</language>\n";
                }
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
                    "\t\t\t<class code=\"" . $oecdClassification . "\" value=\"" . $this->_oecdClassificationsList[$oecdClassification] . "\" /> " .
                    "\t\t</classification>" .
                    "\t</classifications>";
        }

        $doc .= "</journal>";
        return $doc;
    }

    function getLanguage($string) {
        $key = trim($string);

        $languages = $this->_languages;
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
