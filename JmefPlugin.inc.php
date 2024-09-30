<?php

/**
 * @file plugins/generic/jmef/JmefPlugin.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class JmefPlugin
 * @ingroup plugins_generic_jmef
 *
 * @brief Journal Metadata Exchange Format plugin class
 */
import('lib.pkp.classes.plugins.GenericPlugin');

class JmefPlugin extends GenericPlugin {

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled($mainContextId)) {

            HookRegistry::register('Schema::get::context', [$this, 'addToSchema']);
            
            // Intercept the LoadHandler hook to present
            // jmef when requested.
            HookRegistry::register('LoadHandler', array($this, 'callbackHandleContent'));
        }
        return $success;
    }

    /**
     * Extend the context entity's schema with an aditionals properties
     */
    public function addToSchema(string $hookName, array $args) {
        $schema = $args[0];/** @var stdClass */
        $schema->properties->journalKeywords = (object) [
                    'type' => 'string',
                    'multilingual' => true,
                    'validation' => ['nullable']
        ];
        $schema->properties->ownerType = (object) [
                    'type' => 'string',
                    'multilingual' => false,
                    'validation' => ['nullable']
        ];
        $schema->properties->journalDOI = (object) [
                    'type' => 'string',
                    'validation' => ['nullable']
        ];
        $schema->properties->publisherLocation = (object) [
                    'type' => 'string',
                    'validation' => ['nullable']
        ];
        $schema->properties->peerReviewUsed = (object) [
                    'type' => 'boolean',
                    'validation' => ['nullable']
        ];
        $schema->properties->openAuthorship = (object) [
                    'type' => 'boolean',
                    'validation' => ['nullable']
        ];


        return false;
    }

    /**
     * Declare the handler function to process the actual page 
     * @param $hookName string The name of the invoked hook
     * @param $args array Hook parameters
     * @return boolean Hook handling status
     */
    function callbackHandleContent($hookName, $args) {
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);

        $page = & $args[0];
        $op = & $args[1];

        if ($page == 'jmef') {
            // Construct a path to look for
            $path = $page;
            if ($op !== 'index')
                $path .= "/$op";
            if ($ops = $request->getRequestedArgs())
                $path .= '/' . implode('/', $ops);

            // It is -- attach the jmef handler.
            define('HANDLER_CLASS', 'JmefHandler');
            $this->import('JmefHandler');

            return true;
        }
        return false;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    function getDisplayName() {
        return __('plugins.generic.jmef.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    function getDescription() {
        return __('plugins.generic.jmef.description');
    }

    /**
     * @copydoc Plugin::getActions()
     */
    function getActions($request, $verb) {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array_merge(
                $this->getEnabled() ? array(
            new LinkAction(
                    'settings',
                    new AjaxModal(
                            $router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
                            $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
            ),
                ) : array(),
                parent::getActions($request, $verb)
        );
    }

    /**
     * @copydoc Plugin::manage()
     */
    function manage($args, $request) {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();

                AppLocale::requireComponents(LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_MANAGER);
                $templateMgr = TemplateManager::getManager($request);
                $templateMgr->registerPlugin('function', 'plugin_url', array($this, 'smartyPluginUrl'));

                $this->import('JmefSettingsForm');
                $form = new JmefSettingsForm($this, $context);
                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(true);
                    }
                } else {
                    $form->initData();
                }
                return new JSONMessage(true, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }

}
