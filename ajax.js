document.getElementById("login_btn").addEventListener("click", loginAjax, false);

function loginAjax(event) {
    const username = document.getElementById("username").value; // Get the username from the form
    const password = document.getElementById("password").value; // Get the password from the form

    // Make a URL-encoded string for passing POST data:
    const data = { 'username': username, 'password': password };

    fetch("login_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("You've been logged in!");
            } else {
                console.log(`You were not logged in: ${data.message}`);
            }
        })
        .catch(err => console.error(err));
}
