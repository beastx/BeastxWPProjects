<? if (count($this->screenshots['full']) > 0) { ?>
    <div class="BeastxWPPRojectScreenshotsBlock">
        <div class="BeastxWPPRojectBottomBlockTitle">
            <div class="BeastxWPPRojectBottomBlockTitleIcon"></div>
            <? _e('Screenshots', $this->textDomain); ?>
        </div>
        <div class="galleryContent">
            <div id="gallery">
                <div id="slides">
                    <?
                    for ($i = 0; $i < count($this->screenshots['full']); ++$i) {
                        echo '<div class="slide" style="width: 480px; text-align:center; height: 360px;"><table cellpadding="0" cellspacing="0"><tbody><tr><td style="width: 480px; text-align:center; height: 360px;"><img class="' . (empty($this->screenshots['full'][$i][4]) ? '' : 'hascaption') . '" src="' . $this->screenshots['full'][$i][0] . '" width="' . $this->screenshots['full'][$i][1] . '" height="' . $this->screenshots['full'][$i][2] . '"  title="' . $this->screenshots['full'][$i][4] . '" /></td></tr></tbody></table></div>';
                    } ?>
                </div>
                <div id="menu">
                    <ul>
                        <?
                        echo '<li class="fbar">&nbsp;</li>';
                        for ($i = 0; $i < count($this->screenshots['tumbs']); ++$i) {
                            echo '<li class="menuItem"><a href="" title="' . $this->screenshots['tumbs'][$i][4] . '"><img src="' . $this->screenshots['tumbs'][$i][0] . '" width="24" height="24" alt="' . $this->screenshots['tumbs'][$i][4] . '" /></a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<? } ?>