<? if (!empty($this->otherNotes)) { ?>
    <div id="BeastxWPPRojectOtherNotesBlock" class="BeastxWPPRojectBottomBlock">
        <div class="BeastxWPPRojectBottomBlockTitle">
            <div class="BeastxWPPRojectBottomBlockTitleIcon"></div>
            <? _e('Other Notes', $this->textDomain); ?>
        </div>
        <div class="BeastxWPPRojectBottomContent">
            <?=$this->otherNotes;?>
        </div>
    </div>
<? } ?>