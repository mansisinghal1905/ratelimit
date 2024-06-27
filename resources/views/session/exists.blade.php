<!DOCTYPE html>
<html>
<head>
    <title>Session Exists</title>
</head>
<body>
    <p>Another session is already active for your IP address.</p>
    <button id="continue">Continue and Close Previous Session</button>

    <script>
        document.getElementById('continue').addEventListener('click', function() {
    fetch('/close-session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ip: '{{ $ip }}',
            session_id: '{{ $session_id }}'
        })
    }).then(response => {
        if (response.ok) {
            // Notify other windows to close
            localStorage.setItem('closePreviousSession', 'true');
            window.location.reload();
        }
    });
});

window.addEventListener('storage', function(event) {
    if (event.key === 'closePreviousSession' && event.newValue === 'true') {
        window.close();
    }
});

    </script>
</body>
</html>
