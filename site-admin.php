<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Log In</title>

    <link rel="stylesheet" href="./wp-content/login/styles.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css">


</head>

<body>

    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>

    <form id="loginForm">

        <h2>Admin Login</h2>

        <input type="email" id="email" name="email" class="text-field" placeholder="Username" />
        <input type="password" id="password" name="password" class="text-field" placeholder="Password" />

        <button type="button" class="button" value="Log In" onclick="loginUser()">Log In</button>

    </form>


    <script>
        async function loginUser() {

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;


            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            try {
                const response = await fetch('./backend/admin/adminLogin.php', {
                    method: 'POST',
                    body: formData,
                });


                const result = await response.json();


                if (result.success) {

                    console.log('Login successful!');
                    console.log('User ID:', result.user_id);
                    console.log('Is Admin:', result.is_admin);


                    if (result.is_admin) {
                        alertify.success("Login successful");
                        window.location.href = './site-admin/index.php';
                    } else {
                        alertify.error("Invalid Email and Password");
                    }
                } else {

                    alertify.error(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('There was an error with the login request.');
            }
        }


        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();
            loginUser();
        });
    </script>

</body>

</html>