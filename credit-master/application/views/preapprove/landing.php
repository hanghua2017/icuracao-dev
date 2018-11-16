<div class="main">
  <div class="col-main">
    <div id="pa-bc" class="breadcrumbs">
      <div class="link_icons_wrapper">
        <ul>
          <li class="home">
            <div class="category_page_links home_link">
              <a href="https://icuracao.com/" title="Go to Home Page"> <img src="/ci_media/images/homeLinkIcon.jpg">
    					</a>
            </div>
          </li>
          <li class="cms_page">
            <div class="category_page_links last_link">
              <div class="category_page_links_text">Congratulations! You're Pre-Approved</div>
              <img src="/ci_media/images/lastLinkIcon.jpg">
            </div>
          </li>
        </ul>
      </div>
    </div>
    
    <?php if(isset($error)){ ?>
      <div class="alert error">
        <?= $error;?>
      </div>
    <?php } ?>
    
    <div id="preback">
    	
    <form id="pre-apply-now" class="ng-pristine ng-valid" action="/accept/step1" method="post">
    	<div id="title1"><?= lang("preapp_landing_title1"); ?></div>
    	<div id="title2"><?= lang("preapp_landing_title2"); ?></div>
    	<div id="contentright">	
    		<div id="title3"><?= lang("preapp_landing_title3"); ?></div>
    		<div id="step1"><span class="stepnumber">1</span><?= lang("preapp_landing_step1"); ?></div>
    		<div id="step2"><span class="stepnumber">2</span><?= lang("preapp_landing_step2"); ?></div>
    		<div id="step3"><span class="stepnumber">3</span><?= lang("preapp_landing_step3"); ?></div>
    		<div id="authbox">
    			<div id="auth"><?= lang("preapp_landing_auth"); ?></div>
    			<div id="inputholder">
    				<div class="code">
    					<div class="input2"><input class="required three required-entry validate-alpha authcode" style="width: 30px !important;" onkeyup="nextbox(this,'pcode2');" type="text" name="pcode1" maxlength="2" /></div>
    					<div class="input4"><input class="required three required-entry validate-alpha authcode" style="width: 50px !important;" onkeyup="nextbox(this,'pcode3');" type="text" name="pcode2" maxlength="4" /></div>
    					<div class="input6"><input class="required four required-entry validate-number authcode" style="width: 60px !important;" type="text" name="pcode3" maxlength="6" /></div>
    					<input id="tokenField" type="hidden" name="token" value="" />
    					<button id="step0-submit" title="Enter Activation Code" type="submit" onclick="show_loader()"><?= lang("preapp_landing_submit_button");?></button></div>
    			</div>
    		</div>
    		<div id="terms">
    			<?= lang("preapp_landing_terms"); ?> 
    			<a class="termlinks" href="<?= lang("preapp_terms_ca"); ?>">California</a>, 
    			<a class="termlinks" href="<?= lang("preapp_terms_az"); ?>">Arizona</a>
    			<?= lang("preapp_terms_or"); ?>
    			<a class="termlinks" href="<?= lang("preapp_terms_nv");; ?>">Nevada</a>
    		</div>
    	</div>
    	</form>
    </div>
  </div>
</div>

<div id="landing-footer" class="mobile-only page-footer">
  <div id="landing-phone" class="footer-phone">
    <!-- <img class="footer-image" src="/ci_media/images/credit_app/phone.png" /> -->
    <a class="phone-number" href="tel:1-877-287-2266">1(877)287 2266</a>
  </div>
</div>
