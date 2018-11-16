<link rel="stylesheet" type="text/css" href="/ci_media/css/preapprove/step4.css" />

<div class="main">
  <div class="col-main">
    <div id="preapp-content">
    	<div id="preapp-top">
    		<h2 id="preapp-saying"><?=lang('preapp_congrats')?> <?=$fname?>,</h2>
    		<span id="preapp-approved"><?=lang('preapp_saying5')?></span>
    		<p id="preapp-greeting"><?=lang('preapp_saying6')?></p>
    	</div>

    	<div id="preapp-acc">
    		<p id="preapp-msg"><?=lang('preapp_saying7')?></p>
    		<label class="preapp-label"><?=lang('preapp_accn')?></label>
    		<span class="preapp-data no-mouseflow"><?=$accountnumber?></span>
    		<label class="preapp-label"><?=lang('preapp_credit')?></label>
    		<span class="preapp-data"><?=$creditline?></span>
    		<label class="preapp-label no-mouseflow"><?=lang('preapp_code')?></label>
    		<span class="preapp-data no-mouseflow"><?=$ccv?></span>
    	</div>

    	<div id="preapp-coupons">
    		<div id="preapp-shop-title1"><?=lang('preapp_texttitle1')?></div>
    		<div id="preapp-shop-title2"><div id="preapp-shop-title2b"><?=$couponvalue?></div><div id="preapp-shop-title2c">OFF*</div></div>
    		<div id="preapp-shop-title3"><?=lang('preapp_texttitle2')?></div>
    		<div id="preapp-shop-title4"><?=lang('preapp_texttitle3')?> <?=$couponcode?></div>
    		<div id="preapp-shopnow" onclick="show_loader(); window.parent.location.href='<?=lang('preapp_shopnow')?>';"><?=lang('preapp_texttitle4')?></div>
    	</div>

    	<div id="preapp-terms-of"></div>

    	<img id="preapp-back" src="/ci_media/images/preapprove/preapp_back_tv.png" />

    	<div id="preapp-video">
    		<!-- <iframe  id="preapp-iframe"></iframe> -->
    	</div>

    	<div id="preapp-refresh" onclick="playvideo();"></div>

    </div>
  </div>
</div>

<div id="landing-footer" class="mobile-only page-footer">
  <div id="landing-phone" class="footer-phone">
    <img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
    <a class="phone-number" href="tel:1-877-287-2266">1(877)287 2266</a>
  </div>
</div>