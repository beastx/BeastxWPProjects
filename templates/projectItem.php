<link href="<?=$this->plugin->assetsPath; ?>styles/projectItem.css" media="screen" type="text/css" rel="stylesheet">
<link href="<?=$this->plugin->assetsPath; ?>libs/apple-gallery-slideshow/apple-gallery-slideshow.css" media="screen" type="text/css" rel="stylesheet">
<link href="<?=$this->plugin->assetsPath; ?>libs/imageCaption/imageCaption.css" media="screen" type="text/css" rel="stylesheet">
<script type="text/javascript" src="<?=$this->plugin->assetsPath; ?>libs/apple-gallery-slideshow/apple-gallery-slideshow.js"></script>
<script type="text/javascript" src="<?=$this->plugin->assetsPath; ?>libs/imageCaption/imageCaption.js"></script>

<div id="BeastxWPPRojectItem">
    <table class="BeastxWPPRojectItemFirstTable" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="BeastxWPPRojectLeftTD">
                    <div class="BeastxWPPRojectDescriptionBlock">
                        <?=$this->description ?>
                    </div>
                </td>
                <td class="BeastxWPPRojectRightTD">
                    <? $this->includeTemplateFile('data.php'); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <? $this->includeTemplateFile('mainButtons.php'); ?>
    <? $this->includeTemplateFile('screenshots.php'); ?>
    <div class="BeastxWPPRojectBottom">
        <? $this->includeTemplateFile('faqs.php'); ?>
        <? $this->includeTemplateFile('installInstructions.php'); ?>
        <? $this->includeTemplateFile('changeLogs.php'); ?>
        <? $this->includeTemplateFile('otherNotes.php'); ?>
    </div>
</div>
