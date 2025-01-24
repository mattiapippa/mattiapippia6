document.addEventListener('DOMContentLoaded', () => {
    // Fetch security challenge
    fetch('security-check.php')
        .then(r => r.json())
        .then(data => {
            localStorage.setItem('challenge', data.math);
            localStorage.setItem('server_fp', data.fp);
        });
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

    document.addEventListener('submit', function(e) {
        e.preventDefault();
        const mathChallenge = localStorage.getItem('challenge');
        const serverFp = localStorage.getItem('server_fp');
        
        e.target.querySelector('input[name="website"]').value = '';
        
        fetch(e.target.action, {
            method: e.target.method,
            headers: {
                'X-Fingerprint': serverFp,
                'X-Math-Challenge': mathChallenge,
                'X-Local-Storage': localStorage.getItem('fp') ? 'exists' : 'missing'
            },
            body: new FormData(e.target)
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
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
