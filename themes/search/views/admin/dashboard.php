<?php
defined('FIR') OR exit();
/**
 * The template for displaying the Admin Panel Dashboard section
 */
?>
<?=$this->message()?>
<div class="section">
    <div><strong><?=$lang['site_info']?></strong></div>
    <div>
        <a href="<?=SOFTWARE_URL?>" target="_blank" data-nd><?=SOFTWARE_NAME?></a> <?=SOFTWARE_VERSION?>
    </div>
</div>