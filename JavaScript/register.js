document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleRegister();
        });
    }
});
function handleRegister() {
    const form = document.getElementById('registerForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;
    submitButton.textContent = 'Registering...';
    submitButton.disabled = true;
    clearErrors();
    
    const formData = new FormData(form);
    
    fetch('../Database/process_register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            displayErrors(data.errors);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        submitButton.textContent = originalButtonText;
        submitButton.disabled = false;
    });
}

function displayErrors(errors) {
    for (const field in errors) {
        if (field === 'general') {
            showMessage(errors[field], 'error');
        } else {
            const inputField = document.querySelector(`[name="${field}"]`);
            if (inputField) {
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                errorElement.textContent = errors[field];
                errorElement.style.cssText = 'color: #ff0000; font-size: 14px; margin-top: 5px;';
                
                inputField.parentNode.appendChild(errorElement);
                inputField.classList.add('error-input');
            }
        }
    }
}

function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(error => error.remove());
    const errorInputs = document.querySelectorAll('.error-input');
    errorInputs.forEach(input => input.classList.remove('error-input'));
}

function showMessage(message, type) {
    const existingMessages = document.querySelectorAll('.message');
    existingMessages.forEach(msg => msg.remove());
    
    const messageElement = document.createElement('div');
    messageElement.textContent = message;
    messageElement.className = 'message';
    
    if (type === 'success') {
        messageElement.style.cssText = 'background: #4CAF50; color: white; padding: 15px; border-radius: 4px; margin: 10px 0; text-align: center;';
    } else {
        messageElement.style.cssText = 'background: #ffebee; color: #c62828; padding: 15px; border-radius: 4px; margin: 10px 0; text-align: center; border: 1px solid #f44336;';
    }
    const form = document.getElementById('registerForm');
    form.insertBefore(messageElement, form.firstChild);
}
