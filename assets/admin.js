
window.addEventListener('load', evt => {
    const dismissMessage = document.querySelector('button.notice-dismiss');
    if (dismissMessage) {
        dismissMessage.addEventListener('click', evt => {
            const notice = evt.target.closest('.notice');
            notice.parentNode.removeChild(notice);
        });
    }
});