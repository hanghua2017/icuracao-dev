<?php
$terms = strip_tags(lang('oca_terms_content'));
$terms = preg_replace("/[\r\n]*/","",$terms);

$tcpa = strip_tags(lang('oca_tcpa_content'));
$tcpa = preg_replace("/[\r\n]*/","",$tcpa);
?>

  <!-- Step1: first name, middle name, last name, phone number, email, referal code, tcpa and terms -->
  <div id="step1" class="step-container">
    <!-- Header -->
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
    <!-- Body -->
    <div class="page-body">
      <div class="progress">
        <div class="desktop-only">
          <img class="image1" src="/ci_media/images/credit_app/desktop_landing_title.png" alt="Curacao"/>
          <span class="desktop-title">Credit Application</span>
        </div>
         <img class="progress-image" src="/ci_media/images/credit_app/step1_progress.png" />
      </div>
      
      <!-- Content Body -->
      <div class="content-body">
        <div class="step-title">
          <?= lang('oca_step1_title')  ?>
        </div>
        
        <div class="input-wrapper">
          <!-- first name -->
          <div class="left">
            <div class="step-input">
              <span class="step-label"><?= lang('oca_fname')  ?></span>
              <input type="text" class="input" id="step1-fname"  onfocus="hide_error('step1-fname')" onblur="check_step1_input('step1-fname')" maxlength="15"/>	    
            </div>  
            <span id="step1-fname-empty-error" style="display:none" class="alert error"><?= lang('oca_fname')  ?><?= lang('oca_empty_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id="step1-fname-valid-error" style="display:none" class="alert error"><?= lang('oca_fname')  ?><?= lang('oca_valid_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>
    
            <!-- middle name -->
          <div class="right">
            <div class="step-input">
              <span class="step-label"><?= lang('oca_mname')  ?></span>
              <input type="text" class="input" id="step1-mname" onfocus="hide_error('step1-mname')" onblur="check_step1_input('step1-mname')" maxlength="15"/>
            </div>
            <span id="step1-mname-valid-error" style="display:none" class="alert error"><?= lang('oca_mname')  ?><?= lang('oca_valid_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>
      
          <!-- last name -->
          <div class="left">
            <div class="step-input">
              <span class="step-label"><?= lang('oca_lname')  ?></span>
              <input type="text" class="input" id="step1-lname"  onfocus="hide_error('step1-lname')"	onblur="check_step1_input('step1-lname')" maxlength="15"/>
            </div>
            <span id="step1-lname-empty-error" style="display:none" class="alert error"><?= lang('oca_lname')  ?><?= lang('oca_empty_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id="step1-lname-valid-error" style="display:none" class="alert error"><?= lang('oca_lname')  ?><?= lang('oca_valid_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>
              
          <!-- phone number -->
          <div class="right">
            <div class="step-input">
              <span class="step-label"><?= lang('oca_phone')  ?></span>
              <input type="tel" class="input" id="step1-phone" placeholder="e.g. (123)456-7890" maxlength="13" onfocus="hide_error('step1-phone')" onblur="check_step1_input('step1-phone')" />
            </div>
            <span id="step1-phone-empty-error" style="display:none" class="alert error"><?= lang('oca_phone')  ?><?= lang('oca_empty_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id="step1-phone-valid-error" style="display:none" class="alert error"><?= lang('oca_phone')  ?><?= lang('oca_valid_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>
          
          <!-- Phone verification method -->
          <div class="right">
              <div class="pverify-method step-input">
                <?= lang('oca_pchoice_1') ?>
                <div class="pv-choice-wrapper">
                  <input type="radio" id="text-pverify" class="phone-choice-radio" name="pv-choice"><label id="text-pverify-gap" class="phone-choice-label"><?= lang('oca_pverify_text_option') ?></label> 
                  <input type="radio" id="call-pverify" class="phone-choice-radio" name="pv-choice"><label class="phone-choice-label"><?= lang('oca_pverify_call_option') ?></label>
                </div>
              </div>
                <span id="step1-pchoice-empty-error" style="display:none" class="alert error"><?= lang('oca_pchoice_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>
          
          <!-- email -->
          <div id="email-desktop-gap" class="left">
            <div class="step-input">
            <span class="step-label"><?= lang('oca_email')  ?></span>
            <input type="text" class="input" id="step1-email" onfocus="hide_error('step1-email')" onblur="check_step1_input('step1-email')" maxlength="45"/>
            </div>
            <span id="step1-email-empty-error" style="display:none" class="alert error"><?= lang('oca_email')  ?><?= lang('oca_empty_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id="step1-email-valid-error" style="display:none" class="alert error"><?= lang('oca_email')  ?><?= lang('oca_valid_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
            <span id="step1-email-duplicate-error" style="display:none" class="alert error"><?= lang('oca_email_duplicate_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          </div>
        
          <!-- Referal Code -->
          <div class="step-input right">
            <span class="step-label"><?= lang('oca_referal_code')  ?> <span class="optional">(<?= lang('oca_optional')  ?>)</span><span class="question" onclick="showInfo('refcode-info')">?</span></span>
            <input type="text" class="input" id="step1-refcode" maxlength="6"/>
          </div>
          <span id="refcode-info" style="display:none" class="alert confirm"><?= lang('oca_refcode_info') ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          <span id="step1-refcode-valid-error" style="display:none" class="alert error"><?= lang('oca_referal_code')  ?><?= lang('oca_valid_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
          
        </div>
        
        <div class="step1-center">
          <!-- Agree to TCPA -->
          <div class="tcpa-terms">
            <input class="t_check" type="checkbox" id="step1-tcpa" />
            <?php if($store == 'desktop'){?>
              <label class="t_label"><?= lang('oca_tcpa')  ?></label> <span class="read" onclick="popup_show('TCPA',500,500, '<?= $tcpa ?>')"><?= lang('oca_read')  ?></span>
            <?php }else{?>
                <label class="t_label"><?= lang('oca_tcpa')  ?></label> <span class="read" onclick="popup_show('TCPA',400,300, '<?= $tcpa ?>')"><?= lang('oca_read')  ?></span>
            <?php }?>
          </div>
          <!-- Agree to Terms -->
          <div class="tcpa-terms">
            <input class="t_check" type="checkbox" id="step1-terms" onclick="hide_error('step1-terms')" />
            <?php if($store == 'desktop'){?>
              <label class="t_label"><?= lang('oca_terms')  ?></label> <span class="read" onclick="popup_show('Terms',500,500, '<?= $terms ?>')"><?= lang('oca_read')  ?></span>
            <?php }else{?>
              <label class="t_label"><?= lang('oca_terms')  ?></label> <span class="read" onclick="popup_show('Terms',400,300, '<?= $terms ?>')"><?= lang('oca_read')  ?></span>
            <?php }?>
          </div>
          <span id="step1-terms-empty-error" style="display:none" class="alert error"><?= lang('oca_agree_terms_err')  ?><span onclick="closeCurrent(this)" class="close">X</span></span>
        </div>
        
        <div class="step1-center-pverify">
          <!-- <div class="pverify-method">
            <?= lang('oca_pchoice_1') ?>
            <div class="pv-choice-wrapper">
              <input type="radio" id="text-pverify" class="phone-choice-radio" name="pv-choice"><label class="phone-choice-label"><?= lang('oca_pverify_text_option') ?></label> 
              <input type="radio" id="call-pverify" class="phone-choice-radio" name="pv-choice"><label class="phone-choice-label"><?= lang('oca_pverify_call_option') ?></label>
            </div>
          </div> -->
          <!-- <span id="step1-pchoice-empty-error" style="display:none" class="alert error"><?= lang('oca_pchoice_err') ?><span onclick="closeCurrent(this)" class="close">X</span></span> -->
        </div>

        <button class="step button" onclick="check_step1()" type="button">
          <?= lang('oca_next')  ?>
        </button>	
        <span id="step1-error" style="display:none" class="general-error alert error"></span>
        <img class="center-image" src="/ci_media/images/credit_app/trust_badge.png"/>
      </div>      
    </div>
    
    <div class="page-footer">
      
      <div class="footer-phone">
        <img class="footer-image" src="/ci_media/images/credit_app/phone.png" />
        <a class="phone-number" href="tel:1-800-990-3422">1<?= lang('oca_contact') ?></a>
      </div>
    </div>
      
  </div>

