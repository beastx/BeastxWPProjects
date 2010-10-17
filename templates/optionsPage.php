<? $this->printHeader(); ?>
<? $this->printSaveOrErrorMessage(); ?>
<form id= "myForm" method="post">
    <? $this->printFormNonce(); ?>
    <div class="postbox-container postbox-container-left">
        <div class="metabox-holder">
            <div class="meta-box-sortables ui-sortable">
                <? $this->getMainBox(); ?>
                <? $this->getCategoriesBox(); ?>
                <? $this->getFoldersBox(); ?>
                <? $this->getLicencessBox(); ?>
                <? $this->getStatsBox(); ?>
                <? $this->getItemsBox(); ?>
            </div>
        </div>
    </div>
    <div class="postbox-container postbox-container-right">
        <div class="metabox-holder">
            <div class="meta-box-sortables ui-sortable">
                <?
                    $this->saveAndResetBox();
                    $this->likeThisPluginBox();
                    $this->needSupportBox();
                    $this->aboutBeastxBox();
                ?>
            </div>
        </div>
    </div>
</form>
