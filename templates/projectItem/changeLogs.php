<? if (count($this->changeLog) != 0) { ?>
    <div id="BeastxWPPRojectChangeLogBlock" class="BeastxWPPRojectBottomBlock">
        <div class="BeastxWPPRojectBottomBlockTitle">
            <div class="BeastxWPPRojectBottomBlockTitleIcon"></div>
            <? _e('Change Log', $this->textDomain); ?>
        </div>
        <div class="BeastxWPPRojectBottomContent">
            <? for ($i = 0; $i < count($this->changeLog); ++$i) { ?>
                <div class="BeastxWPPRojectFaqsBlockQ"><? _e('Version', $this->textDomain); ?> <?=$this->changeLog[$i]['version'];?></div>
                <div class="BeastxWPPRojectFaqsBlockA"><?=$this->changeLog[$i]['changes'];?></div>
            <? } ?>
        </div>
    </div>
<? } ?>
