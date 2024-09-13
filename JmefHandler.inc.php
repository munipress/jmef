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
        $contextId = $context->getId();

        $doc .= "<journal xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n";
        
        /* Journal ID */
        if($journalDOI = trim($context->getData('journalDOI'))){
            $doc .= "<journal-id journal-id-type=\"doi\">" . $journalDOI . "</journal-id>";
        }
        
        /* Journal title */
        if ($title = $context->getName($context->getPrimaryLocale())) {
            $doc .= "\t<journal-title-group>\n" .
                    "\t\t<journal-title>" . $title . "</journal-title>\n" .
                    "\t</journal-title-group>\n";
        }
        
        /* ISSNs */
        if ($printIssn = $context->getData('printIssn')){
            $doc .= "\t<issn publication-format=\"print\">" . $printIssn . "</issn>\n";
        }
        if ($onlineIssn = $context->getData('onlineIssn')){
            $doc .= "\t<issn publication-format=\"online\">" . $onlineIssn . "</issn>\n";
        }
        
        /* Publisher */
        if ($publisher = $context->getData('publisherInstitution')) {
            $doc .= "\t<publisher>\n" .
                    "\t\t<publisher-name>" . $publisher . "</publisher-name>\n";
            if($countryCode = $context->getData('publisherLocation')) {
                $isoCodes = new \Sokil\IsoCodes\IsoCodesFactory();
                $country = $isoCodes->getCountries()->getByAlpha2($countryCode);
                $doc .=  "\t\t<publisher-location>\n" .
                  "\t\t\t<country iso2=\"" . $countryCode . "\" iso3=\"" . $country->getAlpha3() ."\">". $country->getLocalName() ."</country>\n" .
                  "\t\t</publisher-location>\n";
            }
            $doc .="\t</publisher>\n";
        }
        $doc .= "\t<publication-policy>\n";
        
        if ($context->getData('publishingMode') == 1 || $context->getData('paymentsEnabled') == 1) {
            $doc .= "\t\t<fees free=\"false\"/>\n";
        } else {
            $doc .= "\t\t<fees free=\"true\"/>\n";
        }
        if ($context->getData('peerReviewUsed')) {
            $doc .= "\t\t<review-process peer-review=\"true\" />\n";
        } else {
            $doc .= "\t\t<review-process peer-review=\"false\" />\n";
        }
       
        if($languages = $this->getLanguage($context->getPrimaryLocale())){
            $doc .= "\t\t<languages>\n" . 
                    "\t\t\t<language ";
            if(sizeof($languages) >= 2 && $languages[1]) {
                $doc .= "iso2=\"" . $languages[1] . "\" ";
            } 
            if (sizeof($languages) == 3 && $languages[2]) {
                $doc .= "iso1=\"" . $languages[2] . "\"";
            }
            $doc .= ">" . $languages[0] . "</language>\n" . 
                    "\t\t</languages>\n";
        }
        
        
        if ($license = $context->getData('licenseUrl')){
            $doc .= "\t\t<licenses>\n" . 
                    "\t\t\t<license xlink:href=\"" . $license . "\" />\n" .
                    "\t\t</licenses>\n";
        }
        
        if ($openAuthorship = $context->getData('openAuthorship')){
            $doc .= "\t\t<authorship open=\"true\" />\n";
        } else {
            $doc .= "\t\t<authorship open=\"false\" />\n";
        }
        
        $doc .= "\t</publication-policy>\n";
        
        
        $doc .="\t<self-uri xlink:href=\"" . $baseUrl . "\" />\n";
        
        if($journalKeywords = trim($context->getData('journalKeywords',$context->getPrimaryLocale()))){
            $keywords = explode(";", $journalKeywords);
            $doc .= "\t<kwd-group>\n";
            foreach($keywords as $keyword){
                if(trim($keyword)){
                    $doc .= "\t\t<kwd>" . $keyword . "</kwd>\n";
                }
            }
            $doc .= "\t</kwd-group>\n";
        }
        $doc .= "</journal>";
        return $doc;
    }

    function getLanguage($string){
        $key = trim($string);
    
        $languages = array(
            "ca_ES" => array("Catalan","CAT","CA"),
            "da_DK" => array("Danish","DAN","DA"), 
            "de_DE" => array("German","GERL","DE"), 
            "el_GR" => array("Greek","ELL", "EL"), 
            "en_US" => array("English","ENG","EN"), 
            "es_AR" => array("Spanish (Argentina)"), 
            "es_ES" => array("Spanish","SPA","ES"), 
            "eu_ES" => array("Basque (Spain)"), 
            "fr_CA" => array("French (Canada)"), 
            "it_IT" => array("Italian", "ITA", "IT"), 
            "nl_NL" => array("Dutch","NLD","NL"), 
            "pt_BR" => array("Portuguese (Brazil)"), 
            "tr_TR" => array("Turkish","TUR","TR"), 
            "uk_UA" => array("Ukrainian","UKR","UK"), 
            "zh_CN" => array("Chinese","ZHO","ZH"), 
            "cs_CZ" => array("Czech","CES","CS"), 
            "fa_IR" => array("Persian","FAS","FA"), 
            "gl_ES" => array("Galician (Spain)","GLG","GL"), 
            "hr_HR" => array("Croatian","HRV","HR"), 
            "id_ID" => array("Indonesian","ID","IND"), 
            "ja_JP" => array("Japanese","JAP","JA"), 
            "mk_MK" => array("Macedonian","MKD","MK"), 
            "ml_IN" => array("Malayalam","MAL","ML"), 
            "no_NO" => array("Norwegian","NOR","NO"), 
            "pl_PL" => array("Polish","POL","PL"), 
            "pt_PT" => array("Portuguese","POR","PT"), 
            "ro_RO" => array("Romanian","RON","RO"), 
            "ru_RU" => array("Russian","RUS","RU"), 
            "sr_SR" => array("Serbian","SRP","SR"), 
            "sv_SE" => array("Swedish","SWE","SV"), 
            "vi_VN" => array("Vietnamese","VIE","VI"), 
            "zh_TW" => array("Chinese - TAIWAN"), 
            "sk_SK" => array("Slovak","SLK","SK"), 
            "fr_FR" => array("French","FRA","FR")
        );
        if(key_exists($key, $languages)){
            return $languages[$key];
        } else {
            return false;
        }
    }
}
