<li class="menu-item menu-search-item d-none d-lg-block">
	<form role="search" class="searchform main-search-form search-form" method="get" action="<?php do_action("wpqa_search_permalink")?>">
		<div class="search-wrapper">
			<input type="search" class="form-control<?php echo ("on" == $live_search?" live-search live-search-icon":"")?>"<?php echo ("on" == $live_search?" autocomplete='off'":"")?> placeholder="<?php esc_attr_e('Type Search Words','himer')?>" name="search" value="<?php echo do_action("wpqa_get_search")?>">
			<?php if ($live_search == "on") {?>
				<div class="loader_2 search_loader"></div>
				<div class="live-search-results mt-2 search-results results-empty"></div>
			<?php }?>
			<input type="hidden" name="search_type" class="search_type" value="<?php do_action("wpqa_search_type")?>">
			<div class="search-click"></div>
			<button type="submit" class="search-form__btn"><i class="icon-ios-search-strong"></i></button>
		</div>
	</form>
</li>