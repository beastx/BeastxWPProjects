<?

$BeastxWPProjectsPostTypeArgs = array(
    'labels' => array(
        'name' => __('Projects', $this->textDomain),
        'singular_name' => __('Project', $this->textDomain),
        'add_new' => __('Add New', $this->textDomain),
        'add_new_item' => __('Add New Project', $this->textDomain),
        'edit' => __('Edit', $this->textDomain),
        'edit_item' => __('Edit Project', $this->textDomain),
        'new_item' => __('New Project', $this->textDomain),
        'view' => __('View Project', $this->textDomain),
        'view_item' => __('View Project', $this->textDomain),
        'search_items' => __('Search Projects', $this->textDomain),
        'not_found' => __('No Projects found', $this->textDomain),
        'not_found_in_trash' => __('No Projects found in Trash', $this->textDomain),
        'parent' => __('Parent Project', $this->textDomain),
    ),
    'description' => __('A super duper is a type of content that is the most wonderful content in the world. There are no alternatives that match how insanely creative and beautiful it is.', $this->textDomain),
    'public' => true,
    'show_ui' => true,
    'publicly_queryable' => true,
    'exclude_from_search' => false,
    'menu_position' => 5,
    'menu_icon' => $this->pluginBaseUrl . '/assets/images/logoSmall.png',
    'hierarchical' => false,
    'query_var' => true,
    'supports' => array('title', 'editor'),
    'taxonomies' => array('post_tag'),
    'can_export' => true
);
?>