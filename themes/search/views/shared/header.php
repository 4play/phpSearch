<?php
defined('FIR') OR exit();
/**
 * The template for displaying the Header section
 */
?>
<div id="header" class="header<?php if(!$this->url[0]): ?> header-home<?php endif ?>">
    <?php if($this->settings['cookie_policy_url']): ?>
        <?php if(isset($_COOKIE['cookie_law']) && empty($_COOKIE['cookie_law'])): ?>
        <div class="cookie-law-container">
            <div class="cookie-law-content">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,0A12,12,0,1,0,24,12,12,12,0,0,0,12,0Zm0,2A10,10,0,1,1,2,12,10,10,0,0,1,12,2Zm0,3.81a2.69,2.69,0,0,0-.5,0,1.3,1.3,0,0,0-.44.22,1.08,1.08,0,0,0-.28.38,1.32,1.32,0,0,0-.09.56,1.32,1.32,0,0,0,.09.56,1,1,0,0,0,.28.38,1.25,1.25,0,0,0,.44.19,2.63,2.63,0,0,0,.5.06,3,3,0,0,0,.53-.06,1.11,1.11,0,0,0,.41-.19,1,1,0,0,0,.28-.38,1.37,1.37,0,0,0,0-1.12,1.08,1.08,0,0,0-.28-.38,1.16,1.16,0,0,0-.41-.22A3,3,0,0,0,12,5.81ZM10.78,9.16v9h2.44v-9Z"/></svg>
                <div><?=$lang['cookie_text']?></div>
                <a href="javascript:;" onclick="cookieLaw('<?=COOKIE_PATH?>')" data-nd><?=$lang['ok']?></a>
                <a href="<?=e($this->settings['cookie_policy_url'])?>" target="_blank" data-nd><?=$lang['more_info']?></a>
            </div>
        </div>
        <?php endif ?>
    <?php endif ?>

    <div class="header-content">
        <div class="header-col header-col-logo"><a href="<?=$data['url']?>/"><div class="logo-small"><img src="<?=$data['url']?>/<?=UPLOADS_PATH?>/brand/<?php if($data['cookie']['dark_mode']): ?><?=$data['settings']['logo_small_dark']?><?php else: ?><?php if($data['settings']['site_backgrounds'] && $data['cookie']['backgrounds']): ?><?php if(!$this->url[0]): ?><?=$data['settings']['logo_small_dark']?><?php else: ?><?=$data['settings']['logo_small']?><?php endif ?><?php else: ?><?=$data['settings']['logo_small']?><?php endif ?><?php endif ?>"></div></a></div>
        <div class="header-col header-col-content">
            <?php if(isset($data['search_bar_view'])): ?>
                <?=$data['search_bar_view']?>
            <?php elseif(!$this->url[0]): ?>
                <div class="page-menu header-menu">
                    <?php foreach($data['menu'] as $value): ?>
                        <div class="home-search-menu<?=($value[1] == true ? ' home-search-menu-active' : '')?>" id="path-<?=$value[0]?>" data-new-path="<?=$value[0]?>"><?=$lang[$value[0]]?></div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>
        <div class="header-col header-col-menu">
            <div class="header-menu-el">
                <div class="icon header-icon icon-menu menu-button" id="db-menu" data-db-id="menu"></div>
                <div class="menu" id="dd-menu">
                    <div class="menu-content">
                        <?php foreach($data['site_menu'] as $key => $value): ?>
                            <?php if(isset($_SESSION['user']['id'])): ?>
                                <div class="divider"></div>
                            <?php else: ?>
                                <div class="menu-title"><?=$lang[$key]?></div>
                                <div class="divider"></div>
                            <?php endif ?>

                            <?php if(is_array($value)): ?>
                                <?php foreach($value as $list => $meta): ?>
                                    <a href="<?=$data['url']?>/<?=$key?>/<?=$list?>"<?=(($meta[0] == true) ? ' data-nd' : '')?>><div class="menu-icon icon-<?=$list?>"></div><?=$lang[$list]?></a>
                                <?php endforeach ?>
                            <?php endif ?>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>