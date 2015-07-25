
<form method="get" id="searchform" action="<?php echo esc_url(home_url());?>">
<div class="sbox">
<input class="search-textbox" type="text" value="<?php if(trim(wp_specialchars($s,1))!="") echo trim(wp_specialchars($s,1)); else echo '';?>" placeholder="Enter Keywords" name="s" id="s" />
<input type="submit" id="searchsubmit" name="Submit" value="SEARCH"/>
</div>
</form>