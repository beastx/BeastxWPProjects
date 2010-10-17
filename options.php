<?
$BeastxWPProjectsOptions = array( //  la propiedad options se delcrara y la clasa BeastxPlugin se encargar de manejarla con los metodos, get, save, reset, etc.
    'main' => array(
        'label' => __('Main options', $this->textDomain),
        'description' => '',
        'type' =>'section',
        'options' => array(
            'enabled' => array(
                'label' => __('Enabled plugin?', $this->textDomain),
                'description' => '',
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            ),
            'basePageName' => array(
                'label' => __('URL Base Name', $this->textDomain),
                'description' => __('This is the part of the url to use form accedd to one published project.<br>This word most be singular. Becosa its are converted to plural for items list pages.'),
                'validators' => array(),
                'type' => 'text',
                'defaultValue' => 'Project'
            ),
            'showAsNormalPostInFrontEnd' => array(
                'label' => __('Show as normal Post in the Front End?', $this->textDomain),
                'description' => '',
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            )
        )
    ),
    'categories' => array(
        'label' => __('Projects categories', $this->textDomain),
        'description' => '',
        'type' =>'section',
        'options' => array(
            'categories' => array(
                'label' => __('Categories', $this->textDomain),
                'description' => '',
                'validators' => array(),
                'type' => 'rowEditor'
            )
        )
    ),
    'licences' => array(
        'label' => __('Projects licences', $this->textDomain),
        'description' => '',
        'type' =>'section',
        'options' => array(
            'licences' => array(
                'label' => __('Licences', $this->textDomain),
                'description' => '',
                'validators' => array(),
                'type' => 'rowEditor'
            )
        )
    ),
    'folders' => array(
        'label' => __('Uploads options', $this->textDomain),
        'description' => '',
        'type' =>'section',
        'options' => array(
            'uploadFolder' => array(
                'label' => __('Uploads folder', $this->textDomain),
                'description' => __('Set the folder use to upload the projects files. (Into /wp-content/)'),
                'validators' => array(),
                'type' => 'text',
                'defaultValue' => $this->folders['uploads']
            ),
            'templateFolder' => array(
                'label' => __('Templates folder', $this->textDomain),
                'description' => __('Set the folder use to save the templates files. (Into /wp-content/)'),
                'validators' => array(),
                'type' => 'text',
                'defaultValue' => $this->folders['templates']
            )
        )
    ),
    'stats' => array(
        'label' => __('Stats options', $this->textDomain),
        'description' => '',
        'type' =>'section',
        'options' => array(
            'registerViews' => array(
                'label' => __('Register views', $this->textDomain),
                'description' => __('Count every time that one project is viewed.'),
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            ),
            'registerDownloads' => array(
                'label' => __('Register downloads', $this->textDomain),
                'description' => __('Count every time that one project is downloaded.'),
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            )
        )
    ),
    'items' => array(
        'label' => __('Items options', $this->textDomain),
        'description' => '',
        'type' =>'section',
        'options' => array(
            'useVersioning' => array(
                'label' => __('Use Versions', $this->textDomain),
                'description' => __('Enabled this to permit set the version of the project file.'),
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            ),
            'maxScreenshots' => array(
                'label' => __('Max Screenshots', $this->textDomain),
                'description' => __('Set the maximun number of the screenshot that can be attached to one project.'),
                'validators' => array(),
                'type' => 'text',
                'defaultValue' => 5
            ),
            'useOtherNothes' => array(
                'label' => __('Use Other Nothes', $this->textDomain),
                'description' => '',
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            ),
            'useInstallIntructions' => array(
                'label' => __('Use Install Intructions', $this->textDomain),
                'description' => '',
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            ),
            'useChangeLog' => array(
                'label' => __('Use Change Log', $this->textDomain),
                'description' => '',
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            ),
            'useFAQ' => array(
                'label' => __('Use FAQ', $this->textDomain),
                'description' => '',
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 1
            ),
            'canPublishWithoutCategory' => array(
                'label' => __('Can publish without category', $this->textDomain),
                'description' => __('Enabled this to permit publish one project whiout link to any category.'),
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 0
            ),
            'canPublishWithoutTags' => array(
                'label' => __('Can publish without Tags', $this->textDomain),
                'description' => __('Enabled this to permit publish one project whiout link to any tag.'),
                'validators' => array(),
                'type' => 'checkbox',
                'defaultValue' => 0
            )
        )
    )
);
?>