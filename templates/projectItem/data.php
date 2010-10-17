<div class="BeastxWPPRojectDataBlock">
    <div class="BeastxWPPRojectDataBlockTitle"><? _e('Project Info', $this->textDomain); ?></div>
    <? if (!empty($this->version)) {?>
        <div class="BeastxWPPRojectDataBlockItem">
            <label class="BeastxWPPRojectDataBlockItemLabel"><? _e('Version', $this->textDomain); ?>:</label>
            <?=$this->version;?>
        </div>
    <? } ?>
    
    <? if (!empty($this->licence)) {?>
        <div class="BeastxWPPRojectDataBlockItem">
            <label class="BeastxWPPRojectDataBlockItemLabel"><? _e('Licence', $this->textDomain); ?>:</label>
            <a href="<?=$this->licence['licenceUrl'];?>" title="Got to <?=$this->licence['licenceName'];?> Website"><?=$this->licence['licenceName'];?></a>
        </div>
    <? } ?>
    
    <div class="BeastxWPPRojectDataBlockItem">
        <label class="BeastxWPPRojectDataBlockItemLabel"><? _e('Category', $this->textDomain); ?>:</label>
        <?=$this->category['categoryName'];?>
    </div>
    
    <? if (!empty($this->tags)) {?>
        <div class="BeastxWPPRojectDataBlockItem">
            <label class="BeastxWPPRojectDataBlockItemLabel"><? _e('Tags', $this->textDomain); ?>:</label>
            <? foreach ($this->tags as $tag) { ?>
                <a href="/tag/<?=$tag->slug;?>/" title="Got to <?=$tag->name;?> page"><?=$tag->name;?></a>
            <? } ?>
        </div>
    <? } ?>
    
    <div class="BeastxWPPRojectDataBlockItem">
        <label class="BeastxWPPRojectDataBlockItemLabel"><? _e('Last Update', $this->textDomain); ?>:</label>
        <?=$this->lastUpdate;?>
    </div>
    
    <? if (!empty($this->status)) {?>
        <div class="BeastxWPPRojectDataBlockItem">
            <label class="BeastxWPPRojectDataBlockItemLabel"><? _e('Status', $this->textDomain); ?>:</label>
            <?=$this->status;?>
        </div>
    <? } ?>
    
    <? if (!empty($this->contributors)) {?>
        <div class="BeastxWPPRojectDataBlockItem">
            <label class="BeastxWPPRojectDataBlockItemLabel"><? _e('Contributors', $this->textDomain); ?>:</label>
            <? for ($i = 0; $i < count($this->contributors); ++$i) { ?>
                <a href="<?=$this->contributors[$i]['url'];?>" title="Got to <?=$this->contributors[$i]['name'];?> Website" target="_blank"><?=$this->contributors[$i]['name'];?></a>
            <? } ?>
        </div>
    <? } ?>
</div>