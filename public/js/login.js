document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/api/check-login', {
        method: 'POST',
        body: formData,
        // headers: {
        //     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        // }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/dashboard';
        } else {
            alert('Login failed!');
        }
    })
    .catch(error => console.error('Error:', error));
});
