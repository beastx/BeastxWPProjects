<div class="BeastxWPPRojectButtonsBlock">
    <table class="BeastxWPPRojectButtonsBlockTable">
        <tbody>
            <tr>
                <td>
                    <div class="BeastxWPPRojectItemButton">
                        <div class="BeastxWPProjectButtonIcon" id="BeastxWPProjectDownloadButtonIcon"></div>
                        <a href="<?=$this->downloadLink;?>" target="_blank" id="ProjectDownloadLink" title=""><? _e('Download', $this->textDomain); ?></a>
                    </div>
                    <script>
                    jQuery('#ProjectDownloadLink').click(function() {
                        var link = this;
                        jQuery.ajax({
                            url: '<?=$this->plugin->pluginBaseUrl;?>/stats.php?itemId=<?=$this->post->ID;?>&download=1',
                            success: function(data) {
                                location.href = link.href;
                            }
                        });
                        return false;
                    });
                </script>
                </td>
    
                <? if (!empty($this->supportLink)) { ?>
                    <td>
                        <div class="BeastxWPPRojectItemButton">
                            <div class="BeastxWPProjectButtonIcon" id="BeastxWPProjectSoporteButtonIcon"></div>
                            <a href="<?=$this->supportLink;?>" title=""><? _e('Support', $this->textDomain); ?></a>
                        </div>
                    </td>
                <? } ?>
                
                <? if (!empty($this->viewDemoLink)) { ?>
                    <td>
                        <div class="BeastxWPPRojectItemButton">
                            <div class="BeastxWPProjectButtonIcon" id="BeastxWPProjectDemoButtonIcon"></div>
                            <a href="<?=$this->viewDemoLink;?>" title=""><? _e('View Demo', $this->textDomain); ?></a>
                        </div>
                    </td>
                <? } ?>
                
                <? if (!empty($this->donateLink)) { ?>
                    <td>
                        <div class="BeastxWPPRojectItemButton">
                            <div class="BeastxWPProjectButtonIcon" id="BeastxWPProjectDonateButtonIcon"></div>
                            <a href="<?=$this->donateLink;?>" title=""><? _e('Donate', $this->textDomain); ?></a>
                        </div>
                    </td>
                <? } ?>
                
                
            </tr>
        </tbody>
    </table>
</div>