document.addEventListener('DOMContentLoaded', () => {
    const otpInputs = document.querySelectorAll('[id^="ci-otpCode-"]');
    const submitButton = document.getElementById('securityCodeSubmit');
    const resendLink = document.querySelector('.resend-link a');

    // Focus automatico sul campo successivo quando viene inserito un numero
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            // Only allow numbers
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            // Limit to single digit
            if(e.target.value.length > 1) {
                e.target.value = e.target.value[0];
            }
            
            // Keep existing focus/navigation logic
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

    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if OTP is complete before submitting
        if (!isOtpComplete()) {
            return false;
        }
        
        fetch(this.action, {
            method: this.method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(new FormData(this)).toString()
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Basic error feedback
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
