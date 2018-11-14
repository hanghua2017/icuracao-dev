<div id="decline" class="step-container">
  <div class="page-header">
    <div class="header-center">
  		<img class="header-image" src="/ci_media/images/logo.png" alt="Curacao"/>		
  		<div class="desktop-only">
  			<div class="header-right">
  				<span class="header-text">Apply by Phone</span><span class="header-phone"> 1 <?= lang('oca_contact') ?></span><br/>
  				<?php $lang = $this->session->userdata('site_lang');?>
  				<?php if($lang != 'spanish'){ ?>
  					<a class="language-switch" href='/credit-app/langswitch/switchLanguage?esp'>ESPAÑOL</a>
  				<?php }else{ ?>
  					<a class="language-switch" href='/credit-app/langswitch/switchLanguage?eng'>ENGLISH</a>
  				<?php } ?>
  			</div>
  		</div>
  	</div>
  	<div class="mobile-only">
  	<?php $lang = $this->session->userdata('site_lang');?>
  	<?php if($lang != 'spanish'){ ?>
  		<a class="language-switch" href='/credit-app/langswitch/switchLanguage?esp'>ESPAÑOL</a>
  	<?php }else{ ?>
  		<a class="language-switch" href='/credit-app/langswitch/switchLanguage?eng'>ENGLISH</a>
  	<?php } ?>
  	</div>
  </div>
  <div class="page-body">
    <div class="desktop-only">
      <img class="image1" src="/ci_media/images/credit_app/desktop_landing_title.png" alt="Curacao"/>
      <span class="desktop-title">Credit Application</span>
    </div>
  
    <div class="content-body decision-page">
      <div class="decline">
        <?= lang('oca_pending_1') ?>
        </div>
        <div class="decline text1">
          <?= lang('oca_pending_2') ?>
        </div>
        <div class="decline text2">
          <?= lang('oca_pending_3') ?>
        </div>
        <div class="decline text3">
          <?= lang('oca_pending_4') ?>
        </div>
        <div class="decline text4">
          <?= lang('oca_pending_5') ?> <br/>   <?= lang('oca_pending_6') ?>
        </div>
        <button class="decline-b button" onclick="show_store()">
          <?= lang('oca_pending_7') ?>
        </button>	
    </div>  
  </div>

  <div class="page-footer">
    <div class="footer-phone decision-last">
      <img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
      <a class="phone-number" href="tel:1-800-990-3422">1<?= lang('oca_contact') ?></a>
    </div>
  </div>
  
</div>	