<?php
include("connection.php");
session_start();

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $pwd = $_POST["password"];
    $tmp = 0;

    if (!$email && !$pwd) {
        echo "<script>alert('Email & Password Is Required');</script>";
        $tmp = 2;
    } elseif (!$email) {
        echo "<script>alert('Email Is Required');</script>";
        $tmp = 2;
    } elseif (!$pwd) {
        echo "<script>alert('Password Is Required');</script>";
        $tmp = 2;
    }

    if ($tmp != 2) {
        // SECURITY NOTE: Please secure this query with prepared statements!
        $qry = mysqli_query($con, "SELECT * FROM admin WHERE email='$email'");
        if ($qry && mysqli_num_rows($qry) > 0) {
            $row = mysqli_fetch_assoc($qry);
            if (password_verify($pwd, $row['password'])) {
                $_SESSION["id"] = $row['id'];
                header("location:home.php");
                exit;
            } else {
                echo "<script>alert('Invalid Password');</script>";
            }
        } else {
            echo "<script>alert('Invalid Email');</script>";
        }
    }
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SingIT - Admin </title>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />

    <!-- font awsome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- iconscout -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <!-- google -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100vh;
        }

        body {
            /* background: #7159BD;
            font-family: Roboto, Arial, sans-serif; */
            background: linear-gradient(135deg, #9f98ec, #f294cc);
            font-family: Roboto, Arial, sans-serif;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        form {
            margin: 20px 0;
            background-color: white;
            padding: 50px 100px;
            border-radius: 35px;
            -webkit-box-shadow: 0px 0px 57px -37px rgba(104, 94, 253, 1);
            -moz-box-shadow: 0px 0px 57px -37px rgba(104, 94, 253, 1);
            box-shadow: 0px 0px 57px -37px rgba(104, 94, 253, 1);
        }

        /* Centalizar ao meio */
        .form-wrapper {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        div .photo-info img {
            width: 140px;
            display: block;
            margin: 0 auto;
            border-radius: 50%;
            align-items: center;
            -webkit-box-shadow: 0px 0px 56px -33px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: 0px 0px 56px -33px rgba(0, 0, 0, 0.75);
            box-shadow: 0px 0px 56px -33px rgba(0, 0, 0, 0.75);

        }

        .photo-info h3 {
            display: block;
            font-size: 23px;
            margin: 30px;
            text-align: center;
            font-weight: 300;
            color: #616161;
        }

        form {
            /* ઈમેજની સાથે સફેદ કે અન્ય કલર આપો જેથી ઈમેજ લોડ ન થાય ત્યાં સુધી તે દેખાય */
            background: #ffffff url(https://i.imgur.com/789bjsg.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            /* ઈમેજ વચ્ચે રહે તે માટે */
        }

        form .input-block {
            margin-bottom: 15px;
            position: relative;
        }

        .input-block input {
            padding-left: 30px;
        }

        form .input-block input {
            width: 300px;
            height: 40px;
            display: block;
            margin-top: 8px;
            /* padding: 20px; */
            font-size: 14px;
            color: #685efd;
            border-radius: 25px;
            border: 1px solid #dddddd;
            outline: #685efd;
            transition: 250ms;
            /* padding-right: 50px; */
        }

        form .input-block input:hover {
            border: 1px solid #685efd;
        }

        ::placeholder {
            color: #c8c6c6;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #c8c6c6;
            font-size: 16px;
            /* Reduced font size */
            z-index: 10;
        }

        .toggle-password:hover {
            color: #685efd;
        }

        form .btn-login {
            display: block;
            min-width: 120px;
            font-size: 16px;
            font-weight: 500;
            border: none;

            background: linear-gradient(135deg, #6259ca, #ff6ec4);
            background-size: 200% 100%;
            color: white;
            border-radius: 35px;
            margin: auto;
            width: 200px;
            height: 50px;
            margin-top: 25px;

            box-shadow: 0 4px 15px rgba(255, 110, 196, 0.4);
            transition: all 0.4s ease;
        }

        form .btn-login:hover {
            background-position: 100% 0;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 110, 196, 0.6);
            cursor: pointer;
            filter: brightness(110%);
        }

        form .btn-login:active {
            transform: translateY(1px);
        }

        form p .span {
            color: #685efd;
            text-decoration: none;
        }

        form p .span:hover {
            transition: 200ms;
            filter: brightness(70%);
        }

        form p {
            font-size: 16px;
            text-align: center;
            padding-top: 15px;
        }

        /*=== APARIÇÃO DO FORM ===*/
        form {
            overflow: hidden;
            animation: fade 0.2s;
        }

        form .input-block:nth-child(2) {
            animation: move 500ms;
        }

        form .input-block:nth-child(3) {
            animation: move 400ms;
            animation-delay: 150ms;
            animation-fill-mode: backwards;
        }

        form .btn-login {
            animation: move 400ms;
            animation-delay: 250ms;
            animation-fill-mode: backwards;
        }

        @keyframes move {
            from {
                opacity: 0;
                transform: translate(-35%);
            }

            to {
                opacity: 1;
                transform: translate(0);
            }
        }

        @keyframes fade {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Quando clicar no botão, sumir com o form */
        .form-hide {
            animation: down 500ms;
            animation-fill-mode: 1.2s forwards;
            animation-timing-function: cubic-bezier(0.075, 0.82, 0.165, 1);
        }

        @keyframes down {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(100vh);
            }
        }

        /*=== FORM NO-NO ===*/
        form.validate-error {
            animation: nono 200ms linear, fade paused;
            animation-iteration-count: 2;
        }

        @keyframes nono {

            0%,
            100% {
                transform: translateX(0);
            }

            35% {
                transform: translateX(-15%);
            }

            70% {
                transform: translateX(15%);
            }
        }

        /*=== SQUARES ===*/
        body {
            overflow: hidden;
        }

        .squares li {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.15);
            display: block;
            position: absolute;
            bottom: -40px;
            animation: up 2s infinite, alternate;
        }

        /* જૂનું .squares li કાઢી નાખો અને આ નવું ઉમેરો */
        .squares .moving-icon {
            position: absolute;
            bottom: -60px;
            color: rgba(255, 255, 255, 0.4);
            /* આઈકોનનો આછો કલર */
            display: block;
            animation: up 2s infinite, alternate;
            pointer-events: none;
            /* જેથી ક્લિક ન થાય */
        }

        @keyframes up {
            from {
                opacity: 0;
                transform: translateY(0);
            }

            50% {
                opacity: 1;

            }

            to {
                opacity: 0;
                transform: translateY(-800px) rotate(960deg);
            }
        }
    </style>

</head>

<body>

    <section class="form-section">
        <div class="form-wrapper">
            <form action="" method="post">
                <div class="photo-info">
                    <img src="Profile.png" alt="">
                    <h3>Welcome</h3>
                </div>
                <div class="input-block">
                    <input id="email" name="email" type="email" placeholder="Email" />
                </div>
                <div class="input-block">
                    <input type="password" id="password" name="password" placeholder="Password" />
                    <span class="toggle-password fa-solid fa-eye-slash" id="togglePassword"></span>
                </div>
                <button type="submit" name="login" id="login" class="btn-login">Sign In</button>
            </form>
        </div>
    </section>

    <ul class="squares"></ul>


    <script>
        const btnLogin = document.querySelector('.btn-login');
        const form = document.querySelector("form");

        // Password visibility toggle logic
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        if (togglePassword) {
            togglePassword.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                // toggle the eye icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }


        // Existing animation listeners
        form.addEventListener("animationstart", event => {
            if (event.animationName === "down") {
                document.querySelector("body").style.overflow = "hidden"
            }
        })

        form.addEventListener("animationend", (event) => {
            if (event.animationName === "down")
                form.style.display = "none";
            document.querySelector("body").style.overflow = "none"

        });

        // Background squares
        const ulSquares = document.querySelector("ul.squares");

        // 1. આઈકોનનું લિસ્ટ બનાવો
        const iconsList = [{
            type: 'class',
            name: 'uil uil-music'
        },
        {
            type: 'class',
            name: 'uil uil-music-note'
        },
        {
            type: 'class',
            name: 'fa-brands fa-itunes-note'
        },
        {
            type: 'class',
            name: 'fa-solid fa-music'
        },
        {
            type: 'text',
            name: 'music_note'
        }, // Google
            // {
            //     type: 'text',
            //     name: 'music_note_2'
            // }, // Google
        ];

        for (let i = 0; i < 30; i++) {
            const icon = document.createElement("i");
            const randomIcon = iconsList[Math.floor(Math.random() * iconsList.length)];

            if (randomIcon.type === 'class') {
                icon.className = `${randomIcon.name} moving-icon`;
            } else {
                icon.className = 'material-icons moving-icon';
                icon.innerText = randomIcon.name; // Google icon માટે આ જરૂરી છે
            }

            const random = (min, max) => Math.random() * (max - min) + min;
            const size = Math.floor(random(20, 60));
            const position = random(1, 99);
            const delay = random(15, 0.1);;
            const duration = random(24, 12);

            icon.style.fontSize = `${size}px`;
            icon.style.left = `${position}%`;
            icon.style.animationDelay = `${delay}s`;
            icon.style.animationDuration = `${duration}s`;

            ulSquares.appendChild(icon);
        }
    </script>

</body>

</html>