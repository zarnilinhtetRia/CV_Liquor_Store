<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <h2>API Documentation</h2>

        {{-- Customers --}}
        <h4>* Customer APIs</h4>
        <ul>
            <li>
                <strong> /api/v1/users</strong>
                - Retrieve a list of all users. <strong> => GET</strong><br>

            </li>
            <li>
                <strong>/api/v1/user_register</strong>
                Store a new user. <strong> => POST</strong> <br>
                <code>Request Sample:</code>
                <pre style="font-weight: bold;font-style: italic;">
                {
                    "name": "John Doe",
                    "email": "jonhndoe@example.com",
                    "password": "password123",
                    "type": "staff"

                }</pre>
            </li>

            <li>
                <strong> /api/v1/user_login</strong>
                - User login. <strong> => POST</strong><br>
                <code>Request Sample:</code>
                <pre style="font-weight: bold;font-style: italic;">
                {
                    "email": "jonhndoe@example.com",
                    "password": "password123"
                }</pre>
            </li>




        </ul>


    </div>

</body>

</html>
