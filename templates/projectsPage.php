<link href="<?=$this->plugin->assetsPath; ?>styles/projectItem.css" media="screen" type="text/css" rel="stylesheet">
<link href="<?=$this->plugin->assetsPath; ?>libs/apple-gallery-slideshow/apple-gallery-slideshow.css" media="screen" type="text/css" rel="stylesheet">
<link href="<?=$this->plugin->assetsPath; ?>libs/imageCaption/imageCaption.css" media="screen" type="text/css" rel="stylesheet">
<script type="text/javascript" src="<?=$this->plugin->assetsPath; ?>libs/apple-gallery-slideshow/apple-gallery-slideshow.js"></script>
<script type="text/javascript" src="<?=$this->plugin->assetsPath; ?>libs/imageCaption/imageCaption.js"></script>

<div id="BeastxWPPRojectItem">
    <?
        if (!empty($this->filterByCategoryId)) {
            echo $this->printCategoryPage();
        } else {
            echo $this->printCategoriesList();
        }
    ?>
</div>
