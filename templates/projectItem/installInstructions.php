<? if ($this->installInstructions) { ?>
    <div id="BeastxWPPRojectInstallInstructionsBlock" class="BeastxWPPRojectBottomBlock">
        <div class="BeastxWPPRojectBottomBlockTitle">
            <div class="BeastxWPPRojectBottomBlockTitleIcon"></div>
            <? _e('Install Instructions', $this->textDomain); ?>
        </div>
        <div class="BeastxWPPRojectBottomContent">
            <?=$this->installInstructions;?>
        </div>
    </div>
<? } ?>