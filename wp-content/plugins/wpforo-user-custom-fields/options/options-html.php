<?php
if (!defined('ABSPATH')) {
    exit();
}
$activeTab = isset($_GET['tab']) && ($tab = trim($_GET['tab'])) ? $tab : WpforoUcfConstants::TAB_FIELDS;
$mainPage = admin_url('admin.php?page=' . WpforoUcfConstants::UCF_PAGE_MAIN);
?>
<style>
    .wpf-addon-header{ padding:10px 10px 5px 10px; margin:10px 0; border-bottom:1px solid #ddd; max-width:240px; font-weight:normal;}
    .wpf-addon-table{width:100%; border:none; padding:0px; float:left; margin:0px auto; box-sizing:border-box;}
    .wpf-addon-table tr:nth-child(2n+1) { background: #f9f9f9 none repeat scroll 0 0; }
    .wpf-addon-table td, .wpf-addon-table th{ padding:10px 15px; text-align:left; vertical-align:top;}
    .wpf-addon-table label{ font-size:14px;}
    /*    #wpf-admin-wrap .nav-tab-wrapper > a.nav-tab:last-child{float: left;}*/
</style>
<?php do_action(WpforoUcfHelper::UCF_PAGE_MAIN); ?>
<?php settings_errors('wpfucf'); ?>
<h3 class="wpf-addon-header"><?php _e('wpForo User Custom Fields', 'wpforo-ucf'); ?></h3>
<div class="wpfucf-wrapper">
    <h2 class="nav-tab-wrapper">
        <?php
        foreach ($this->tabs as $tab) {
            $tabTitle = $tab['tabTitle'];
            $tabKey = $tab['tabKey'];
            if ($activeTab == $tabKey) {
                $this->defaultTabFile = $tabKey . '.php';
            }
            $style = $tabKey == 'member_tabs' ? 'float:right;' : '';
            ?>
            <a style="<?php echo $style; ?>" href="<?php echo $mainPage . '&tab=' . $tabKey; ?>" class="nav-tab <?php echo $activeTab == $tabKey ? 'nav-tab-active' : ''; ?>"><?php echo $tabTitle; ?></a>
            <?php
        }
        ?>
    </h2>
    <?php include_once 'tabs/' . $this->defaultTabFile; ?>
</div>