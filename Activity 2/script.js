function verifyFixes() {
    const card = document.querySelector('.profile-card');
    
    if (!card) {
        alert("Keep trying! The HTML and CSS selectors still don't match.");
        return;
    }

    const cardStyles = window.getComputedStyle(card);
    const bodyStyles = window.getComputedStyle(document.body);

    const isBoxSizingFixed = cardStyles.boxSizing === 'border-box';
    
    const isPaddingFixed = cardStyles.paddingTop !== '0px';

    if (isBoxSizingFixed && isPaddingFixed) {
        document.getElementById('successModal').classList.add('show');
    } else {
        alert("Not quite yet! Double-check your html and css syntax.");
    }
}

function closeModal() {
    document.getElementById('successModal').classList.remove('show');
}