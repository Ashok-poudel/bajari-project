document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  const signupForm = document.getElementById('signupForm');
  const errorElement = document.getElementById('formError');

  const setError = (message, input) => {
    if (!errorElement) return;
    errorElement.innerText = message;
    errorElement.style.display = 'block';
    if (input) {
      input.classList.add('input-error');
      input.focus();
    }
  };

  const clearError = () => {
    if (!errorElement) return;
    errorElement.innerText = '';
    errorElement.style.display = 'none';
    document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
  };

  const isValidEmail = (email) => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  };

  if (loginForm) {
    loginForm.addEventListener('submit', (event) => {
      clearError();
      const email = loginForm.querySelector('input[name="email"]');
      const password = loginForm.querySelector('input[name="password"]');

      if (!email.value.trim()) {
        event.preventDefault();
        setError('Please enter your email address.', email);
        return;
      }
      if (!isValidEmail(email.value.trim())) {
        event.preventDefault();
        setError('Please enter a valid email address.', email);
        return;
      }
      if (!password.value) {
        event.preventDefault();
        setError('Please enter your password.', password);
        return;
      }
      if (password.value.length < 6) {
        event.preventDefault();
        setError('Password must be at least 6 characters.', password);
      }
    });
  }

  if (signupForm) {
    signupForm.addEventListener('submit', (event) => {
      clearError();
      const name = signupForm.querySelector('input[name="name"]');
      const email = signupForm.querySelector('input[name="email"]');
      const password = signupForm.querySelector('input[name="password"]');
      const confirmPassword = signupForm.querySelector('input[name="confirmPassword"]');

      if (!name.value.trim()) {
        event.preventDefault();
        setError('Please enter your full name.', name);
        return;
      }
      if (!email.value.trim()) {
        event.preventDefault();
        setError('Please enter your email address.', email);
        return;
      }
      if (!isValidEmail(email.value.trim())) {
        event.preventDefault();
        setError('Please enter a valid email address.', email);
        return;
      }
      if (!password.value) {
        event.preventDefault();
        setError('Please enter a password.', password);
        return;
      }
      if (password.value.length < 6) {
        event.preventDefault();
        setError('Password must be at least 6 characters.', password);
        return;
      }
      if (password.value !== confirmPassword.value) {
        event.preventDefault();
        setError('Passwords do not match.', confirmPassword);
      }
    });
  }
});
