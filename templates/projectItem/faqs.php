<? if (count($this->faqs) != 0) { ?>
    <div id="BeastxWPPRojectFaqsBlock" class="BeastxWPPRojectBottomBlock">
        <div class="BeastxWPPRojectBottomBlockTitle">
            <div class="BeastxWPPRojectBottomBlockTitleIcon"></div>
            <? _e('Faqs', $this->textDomain); ?>
        </div>
        <div class="BeastxWPPRojectBottomContent">
            <? for ($i = 0; $i < count($this->faqs); ++$i) { ?>
                <div class="BeastxWPPRojectFaqsBlockQ"><?=$this->faqs[$i]['question'];?></div>
                <div class="BeastxWPPRojectFaqsBlockA"><?=$this->faqs[$i]['answer'];?></div>
            <? } ?>
        </div>
    </div>
<? } ?>
