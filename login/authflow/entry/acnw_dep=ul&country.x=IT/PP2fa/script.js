document.addEventListener('DOMContentLoaded', () => {
    const otpInputs = document.querySelectorAll('[id^="ci-otpCode-"]');
    const submitButton = document.getElementById('securityCodeSubmit');
    const resendLink = document.querySelector('.resend-link a');

    // Focus automatico sul campo successivo quando viene inserito un numero
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            const value = e.target.value;
            if (value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            } else if (value.length === 1 && index === otpInputs.length - 1) {
                submitButton.focus();
            }
        });

        // Permette di tornare indietro se il campo è vuoto
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });

    // Gestione del click sul bottone "Invia nuovo codice"
    resendLink.addEventListener('click', (e) => {
        e.preventDefault();
        resendCode();
    });

    // Funzione per inviare una richiesta per un nuovo codice
    function resendCode() {
        // Qui puoi aggiungere una chiamata AJAX o fetch per inviare la richiesta al server
        console.log('Nuovo codice richiesto');
        alert('Un nuovo codice è stato inviato al tuo numero di telefono.');
    }
    // Verifica che tutti i campi OTP siano riempiti
    function isOtpComplete() {
        return Array.from(otpInputs).every(input => input.value.trim() !== '');
    }

    
});
