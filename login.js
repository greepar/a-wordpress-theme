document.addEventListener('DOMContentLoaded', () => {
  const login = document.getElementById('login');
  if (!login) {
    return;
  }

  const placeCapsWarning = () => {
    const capsWarning = login.querySelector('.caps-warning');
    if (!capsWarning) {
      return false;
    }

    const message = login.querySelector('.message, #login_error, .success');
    if (message && message.parentNode) {
      message.parentNode.insertBefore(capsWarning, message.nextSibling);
      return true;
    }

    const form = login.querySelector('#loginform');
    if (form && form.parentNode) {
      form.parentNode.insertBefore(capsWarning, form);
      return true;
    }

    return false;
  };

  if (placeCapsWarning()) {
    return;
  }

  const observer = new MutationObserver(() => {
    if (placeCapsWarning()) {
      observer.disconnect();
    }
  });

  observer.observe(login, { childList: true, subtree: true });
});
