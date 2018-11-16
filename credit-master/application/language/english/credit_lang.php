<?php 
  
  //oca stands for organic credit app 
  defined('BASEPATH') OR exit('No direct script access allowed');
  
  //landing page
  $lang['oca_contact'] = '(800)990-3422';
  $lang['oca_landing_title1'] = "Welcome to Curacao's Online Credit Application";
  $lang['oca_landing_button1'] = "GET STARTED!";
  $lang['oca_landing_row1_title'] = "Applying is fast";
  $lang['oca_landing_row1_text'] = "We value your time. Apply anywhere in minutes";
  $lang['oca_landing_row2_title'] = "Applying is easy";
  $lang['oca_landing_row2_text'] = "Getting credit is effortless. Basic information is all you need";
  $lang['oca_landing_row3_title'] = "Applying is secure";
  $lang['oca_landing_row3_text'] = "Your privacy is our main goal. Your info is never shared";
  $lang['oca_view_terms'] = "View Pricing and Terms for";
  $lang['oca_terms_ca'] = "/credit-app/ci_media/pdf/terms_ca_033018.pdf";
  $lang['oca_terms_az'] = "/credit-app/ci_media/pdf/terms_az_033018.pdf";
  $lang['oca_terms_nv'] = "/credit-app/ci_media/pdf/terms_nv_033018.pdf";
  
  //step1
  $lang['oca_step1_title'] = "Please tell us about yourself";
  $lang['oca_fname'] = "First name";
  $lang['oca_empty_err'] = " can not be empty";
  $lang['oca_valid_err'] = " needs to be valid";
  $lang['oca_mname'] = "middle name";
  $lang['oca_lname'] = "last name";
  $lang['oca_phone'] = "Mobile Phone";
  $lang['oca_email'] = "email";
  $lang['oca_email_duplicate_err'] = "email duplicate in our system, please change to another one. ";
  $lang['oca_referal_code'] = "referral code";
  $lang['oca_optional'] = "optional";
  $lang['oca_tcpa'] = "I agree to TCPA";
  $lang['oca_read'] = "Read";
  $lang['oca_terms'] = "I agree to Terms & Conditions";
  $lang['oca_agree_terms_err'] = "Please Agree to our terms to continue application";
  $lang['oca_next'] = "next";
  $lang['oca_pchoice_1'] = "Phone Verification Method";
  $lang['oca_pchoice_err'] = "Please choose at least one verification method";
  $lang['oca_refcode_info'] = "If someone referred you and gave you a referral code please type it in";
  
  $lang['oca_terms_content'] = "<p><strong>Credit Application Disclosure:</strong></p><p>By signing below, I certify that I am 18 years or older and my application is true and complete. I agree that Adir International, L C C dba Curacao (&ldquo;Curacao&rdquo;) may verify my information (including my spouse&rsquo;s if I live in a community property state). I agree that Curacao may furnish information about me as permitted by law, including this application, verification interrogatory data and my credit performance with Curacao, to financial institutions or third parties with whom it has a cross or joint marketing agreement so they may consider offering me other products and services. I authorize Curacao to share information about me with any or its affiliates, associates, or subsidiaries. I expressly consent to be contacted concerning my credit relationship with Curacao by Curacao itself, anyone acting on behalf of Curacao (whether a Curacao affiliate or a third party, such as a collection agency), or anyone who has acquired my loan or account from Curacao. This consent is irrevocable and extends to contact by autodialed or prerecorded voice calls or text messages to my residential telephone and/or cell phone, whether or not I am charged for the call or message under my telephone calling plan. I give my consent to receive legal disclosures and any other information related to my account electronically. I understand that I may request a paper copy of the disclosures. I have read and agree to the credit terms and other disclosures relating to the loan or account for which I am applying, and I understand if Curacao extend credit to me, a copy will be provided to me and will govern my loan or account. If more than one person signs below, each agrees that he or she will have full and equal access to any credit lines and will be individually and jointly liable for repayment of all indebtedness incurred.</p>";
  
  $lang['oca_tcpa_content'] = "<p>Communicating with Curacao: I hereby expressly consent to receive future call, emails or text messages that deliver prerecorded messages (concerning, for example, sales or other special offers by or on behalf of Curacao at the following telephone number(s): ____________________ (land line) and/or ____________________ (cell), or, if both are left blank, at the number Curacao has on file for me at the time of the call. I understand that my signing this consent is completely voluntary and is not a condition for purchasing any good or service from Curacao.</p>";

  
  //phone verify page
  $lang['oca_pverify_title'] = "Phone Verification";
  $lang['oca_pverify_text1'] = "please choose one of the options to receive your verification code: text or call";
  $lang['oca_pverify_text2'] = "Please check your phone number ending in";
  $lang['oca_pverify_text3'] = "for a six digit code and enter it below to continue";
  $lang['oca_pverify_text_option'] = "Text";
  $lang['oca_pverify_call_option'] = "Call";
  $lang['oca_enter_code'] = "Enter code";
  $lang['oca_verify_code'] = "Verify code";
  $lang['oca_continue1'] = "continue";
  
  //step2
  $lang['oca_step2_title'] = "Please share more info with us";
  $lang['oca_address'] = "Street Addres";
  $lang['oca_apt'] = "APT,SUITE,ETC";
  $lang['oca_zip'] = "Zip Code";
  $lang['oca_zip_valid'] = "ZipCode needs to be valid among CA, NV and AZ";
  $lang['oca_city'] = "City";
  $lang['oca_select'] = "Select";
  $lang['oca_state'] = "State";
  $lang['oca_income'] = "Monthly Income";
  $lang['oca_income_confirm'] = "Please confirm this is montly income not annual";
  $lang['oca_income_info'] = "Include all income available to you before taxes and deductions (Gross Income)";
  $lang['oca_dob'] = "Date of Birth";
  $lang['oca_month'] = "Month";
  $lang['oca_date'] = "Date";
  $lang['oca_year'] = "Year";

  
  //step3
  $lang['oca_step3_title'] = "Please select one";
  $lang['oca_step3_text1'] = " Apply with my Social Security Number and Valid State ID";
  $lang['oca_step3_text2'] = "Why Do you Need This?";
  $lang['oca_step3_text3'] = " Apply with my Matricula or International Passport";
  $lang['oca_doca_info'] = "This is required to verify and protect your identity ";
  $lang['oca_docb_info'] = "If you do not have an SSN and a valid State ID this is required to verify and protect your identity ";
  
  //step3 us
  $lang['oca_step3_us_title'] = "Please enter the last pieces of info";
  $lang['oca_ssn'] = "Social Security Number";
  $lang['oca_id_type'] = "ID Type";
  $lang['oca_dl'] = "Driver's License";
  $lang['oca_si'] = "State ID";
  $lang['oca_id_number'] = "ID Number";
  $lang['oca_maiden_name'] = "Mother's Maiden Name";
  $lang['oca_applynow'] = "Apply Now";
  $lang['oca_maiden_info'] = "This is required to verify and protect your identity and maybe use for authentication purpose";
  
  //step3 International
  $lang['oca_step3_int_title'] = "Please enter the last pieces of info";
  $lang['oca_step3_int_country'] = "Country of Issuance";
  $lang['oca_step3_not_accept'] = "Sorry we don't accept other id type online please come to our stores to apply for credit, or if you have any questions please call our toll free number at 1-866-410-1611. Monday - Sunday, 10AM - 7PM PST.";
  
  //step4 
  $lang['oca_step4_title'] = "We're almost there";
  $lang['oca_step4_text1'] = "Please answer the following questions to complete your application";
  $lang['oca_bank_type'] = "Type of bank account";
  $lang['oca_bank_1'] = "Checking";
  $lang['oca_bank_2'] = "Savings";
  $lang['oca_bank_3'] = "Both";
  $lang['oca_bank_none'] = "None";
  $lang['oca_select_bank'] = "Please select your bank account type";
  $lang['oca_live_length'] = "Time living at your current address?";
  $lang['oca_live_1'] = "No more than 2 years";
  $lang['oca_live_2'] = "3 to 5 years";
  $lang['oca_live_3'] = "6 to 7 years";
  $lang['oca_live_4'] = "8 years or more";
  $lang['oca_choice_empty'] = "Please answer this question";
  $lang['oca_employment_type'] = "Employment type";
  $lang['oca_emp_1'] = "Construct";
  $lang['oca_emp_2'] = "Domestic";
  $lang['oca_emp_3'] = "Self-Employed";
  $lang['oca_emp_4'] = "Medical";
  $lang['oca_emp_5'] = "Office";
  $lang['oca_emp_6'] = "Management";
  $lang['oca_emp_7'] = "Retail/Fashion";
  $lang['oca_emp_8'] = "Service";
  $lang['oca_emp_9'] = "Retired";
  $lang['oca_emp_10'] = "Unemployed";
  $lang['oca_select_emp'] = "Please select your employment type";
  $lang['oca_company_name'] = "Company Name";
  $lang['oca_work_length'] = "Work length at this company";
  $lang['oca_work_1'] = "Less than 1 year";
  $lang['oca_work_2'] = "1 year";
  $lang['oca_work_3'] = "2 years";
  $lang['oca_work_4'] = "3 years";
  $lang['oca_work_5'] = "4 to 5 years";
  $lang['oca_work_6'] = "6 to 10 year";
  $lang['oca_work_7'] = "More than 10 years";
  $lang['oca_select_work'] = "Please select your work length";
  $lang['oca_company_phone'] = "Company's Phone number";
  
  //step5 
  $lang['oca_step5_title'] = "Final step, please provide us with two references";
  $lang['oca_ref_1'] = "Reference1";
  $lang['oca_ref_fname'] = "First Name";
  $lang['oca_ref_lname'] = "Last Name";
  $lang['oca_ref_phone'] = "Phone Number";
  $lang['oca_relation'] = "Relationship";
  $lang['oca_ref1'] = "Son, Daughter, Parent";
  $lang['oca_ref2'] = "Brother, Sister";
  $lang['oca_ref3'] = "Aunt, Uncle, Cousin, Grandparent, In-Laws";
  $lang['oca_ref4'] = "Friends";
  $lang['oca_ref5'] = "Acquaintance";
  $lang['oca_choice_rel'] = "Please choose the relationship";
  $lang['oca_ref_2'] = "Reference2";
  $lang['oca_submit'] = "SUBMIT";
  
  //approve page
  $lang['oca_approve_title'] = "Congratulations!";
  $lang['oca_approve_text1'] = "You're Approved!";
  $lang['oca_approve_text2'] = "Welcome to the Curacao family.";
  $lang['oca_approve_text3'] = "Please see your account details below";
  $lang['oca_approve_text4'] = "Your credit application has been approved. You will receiving an email shortly.  Print out the email and follow the instructions on how to activate your account. ";
  $lang['oca_account'] = "Account Number";
  $lang['oca_credit_limit'] = "Credit Limit";
  $lang['oca_start_shopping'] = "Start Shopping";
  
  //pending and decline page
  $lang['oca_pending_1'] = "Thank you for applying for Curacao credit.";
  $lang['oca_pending_2'] = "  We are reviewing your application and will notify you of our decision within 7 to 10 business days.";
  $lang['oca_pending_3'] = "If you have any question, contact us at (800) 990-3422";
  $lang['oca_pending_4'] = "Monday thru Sunday, 10:00 am to 9:00 pm PST.";
  $lang['oca_pending_5'] = "Thank you,";
  $lang['oca_pending_6'] = "Curacao Family";
  $lang['oca_pending_7'] = "go to store";
  
  //duplicate page
  $lang['oca_duplicate_1'] = "Our records indicate that you may already have an account with us and we would like to verify some information so that you can begin shopping today. ";
  $lang['oca_duplicate_2'] = "Contact us at (800)990-3422 Monday thru Sunday, 10:00 am to 9:00 pm PST or come visit our new Credit Lounge at any Curacao store near you.";
  $lang['oca_duplicate_3'] = "STORE LOCATOR";
  
  $lang['oca_already_exist_email'] = 'It looks like you already have account with us. If any quesiton please call (800)990-3422 ';

  //wrong address messages: for both credit app and preapprove
  $lang['incomplete_address'] = 'Incomplete Address';
  $lang['incorrect_zip'] = 'Incorrect (City, Zip, State) combination';
  $lang['incorrect_address'] = 'Incorrect Street Address';