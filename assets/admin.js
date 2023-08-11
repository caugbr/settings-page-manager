
window.addEventListener('load', evt => {
    const dismissMessage = document.querySelector('button.notice-dismiss');
    if (dismissMessage) {
        dismissMessage.addEventListener('click', evt => {
            const notice = evt.target.closest('.notice');
            notice.parentNode.removeChild(notice);
        });
    }

    const tabEl = document.querySelector('.tabs');
    if (tabEl) {
        const tabs = tabEl.querySelectorAll('.tab-links a');
        Array.from(tabs).forEach(tab => {
            tab.addEventListener('click', evt => {
                evt.preventDefault();
                const action = evt.target.getAttribute('data-action');
                document.querySelector('input[name="action"]').value = action;
                const name = evt.target.getAttribute('data-tab');
                tabEl.setAttribute('data-tab', name);
                window.location.hash = `#${name}`;
            });
        });
        if (window.location.hash) {
            const hash = window.location.hash.replace('#', '');
            const link = document.querySelector(`.tab-links a[data-tab="${hash}"]`);
            console.log('tab-links', link)
            if (link) {
                link.dispatchEvent(new Event('click'));
            }
        }
    }
});