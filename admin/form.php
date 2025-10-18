<?php
include("con.php");
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
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SingIT - Admin </title>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />

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
            /* background: #e2e0fe; */
            background: #7159BD;
            font-family: Roboto, Arial, sans-serif;
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
            background: url(https://i.imgur.com/789bjsg.png);
            background-repeat: no-repeat;
            background-size: cover;
        }

        form .input-block {
            margin-bottom: 15px;
        }

        .input-block input {
            padding-left: 30px;
        }

        form .input-block input {
            width: 300px;
            height: 40px;
            display: block;
            margin-top: 8px;
            padding: 20px;
            font-size: 14px;
            color: #685efd;
            border-radius: 25px;
            border: 1px solid #dddddd;
            outline: #685efd;
            transition: 250ms;
        }

        form .input-block input:hover {
            border: 1px solid #685efd;
        }

        ::placeholder {
            color: #c8c6c6;
        }

        form .btn-login {
            display: block;
            min-width: 120px;
            font-size: 16px;
            font-weight: 500;
            border: none;
            background-color: #685efd;
            color: white;
            border-radius: 35px;
            margin: auto;
            width: 200px;
            height: 50px;
            margin-top: 25px;
            box-shadow: 3px 6px 15px -6px #685efd;
            transition: 250ms;
        }

        form .btn-login:hover {
            filter: brightness(90%);
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
                </div>
                <button type="submit" name="login" id="login" class="btn-login">Sign In</button>
            </form>
        </div>
    </section>

    <ul class="squares"></ul>


    <script>
        const btnLogin = document.querySelector('.btn-login');
        const form = document.querySelector("form");


        form.addEventListener("animationstart", event => {
            if (event.animationName === "down") {
                document.querySelector("body").style.overflow = "hidden"
            }
        })

        form.addEventListener("animationend", () => {
            if (event.animationName === "down")
                form.style.display = "none";
            document.querySelector("body").style.overflow = "none"

        });

        // Background squares
        const ulSquares = document.querySelector("ul.squares")

        for (let i = 0; i < 11; i++) {
            const li = document.createElement("li");

            const random = (min, max) => Math.random() * (max - min) + min

            const size = Math.floor(random(10, 120));
            const position = random(1, 99);
            const delay = random(5, 0.1);
            const duration = random(24, 12);

            li.style.width = `${size}px`
            li.style.height = `${size}px`
            li.style.bottom = `-${size}px`

            li.style.left = `${position}%`;

            li.style.animationDelay = `${delay}s`
            li.style.animationDuration = `${duration}s`

            opacity: 0;
            li.style.animationTimingFunction =
                `cubic-bezier(${Math.random()}, ${Math.random()}, ${Math.random()}, ${Math.random()}, ${Math.random()}, )`

            ulSquares.appendChild(li);

        }
    </script>

</body>

</html>