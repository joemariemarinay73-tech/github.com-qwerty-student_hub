<?php

session_start();

require_once "db.php";


/* =========================================
   IF ALREADY LOGGED IN
========================================= */

if (isset($_SESSION["user_id"])) {

    header("Location: dashboard.php");

    exit();
}


/* =========================================
   VARIABLES
========================================= */

$error = "";


/* =========================================
   REGISTER
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    /* Check empty fields */

    if (
        empty($full_name) ||
        empty($username) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all fields.";

    }


    /* Check email */

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }


    /* Check password */

    elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    }


    /* Check password confirmation */

    elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    }


    else {

        /* =========================================
           CHECK DUPLICATE USERNAME / EMAIL
        ========================================== */

        $check = $conn->prepare(
            "SELECT id FROM users
             WHERE username = ? OR email = ?
             LIMIT 1"
        );

        $check->bind_param(
            "ss",
            $username,
            $email
        );

        $check->execute();

        $result = $check->get_result();


        if ($result->num_rows > 0) {

            $error = "Username or email already exists.";

            $check->close();

        }

        else {

            $check->close();


            /* =========================================
               HASH PASSWORD
            ========================================== */

            $hashed_password =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            /* =========================================
               INSERT USER
            ========================================== */

            $stmt = $conn->prepare(
                "INSERT INTO users
                (full_name, username, email, password)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $full_name,
                $username,
                $email,
                $hashed_password
            );


            if ($stmt->execute()) {

                $_SESSION["success"] =
                    "Account created successfully! You can now login.";

                $stmt->close();

                $conn->close();

                header("Location: index.php");

                exit();

            }

            else {

                $error =
                    "Something went wrong. Please try again.";

                $stmt->close();

            }

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Account | Student System</title>


    <!-- IMPORTANT: LOAD CSS -->

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body class="login-body">


    <!-- =========================================
         BACKGROUND EFFECTS
    ========================================== -->

    <div class="cursor-glow"></div>

    <div class="cursor-dot"></div>

    <div class="background-overlay"></div>

    <div class="rain rain-one"></div>

    <div class="rain rain-two"></div>

    <div class="rain rain-three"></div>


    <!-- =========================================
         REGISTER WRAPPER
    ========================================== -->

    <div class="login-wrapper">


        <div class="glass-login-card register-card">


            <!-- =====================================
                 LEFT VISUAL SIDE
            ====================================== -->

            <div class="visual-side">


                <div class="lamp">

                    <div class="lamp-light"></div>

                </div>


                <div class="robot">

                    <div class="robot-face">

                        <div class="robot-eye"></div>

                        <div class="robot-eye"></div>

                    </div>


                    <div class="robot-smile">

                        ◡

                    </div>

                </div>


                <div class="visual-text">

                    <span>
                        JOIN US
                    </span>


                    <h2>

                        Create your<br>

                        <strong>
                            Student Account
                        </strong>

                    </h2>


                    <p>
                        Start your secure digital journey.
                    </p>

                </div>

            </div>


            <!-- =====================================
                 RIGHT REGISTER FORM
            ====================================== -->

            <div class="form-side">


                <div class="form-header">

                    <div class="mini-logo">

                        👤

                    </div>


                    <h1>

                        Create Account<span>.</span>

                    </h1>


                    <p>

                        Register a new student account

                    </p>

                </div>


                <!-- ERROR -->

                <?php if (!empty($error)): ?>

                    <div class="message error-message">

                        !

                        <?php

                        echo htmlspecialchars($error);

                        ?>

                    </div>

                <?php endif; ?>


                <!-- =================================
                     REGISTER FORM
                ================================== -->

                <form
                    action="register.php"
                    method="POST"
                    id="registerForm"
                >


                    <!-- FULL NAME -->

                    <div class="neon-input">

                        <label for="full_name">

                            Full Name

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                👤

                            </span>


                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                placeholder="Enter your full name"
                                value="<?php echo htmlspecialchars($_POST["full_name"] ?? ""); ?>"
                                autocomplete="name"
                                required
                            >

                        </div>

                    </div>


                    <!-- USERNAME -->

                    <div class="neon-input">

                        <label for="username">

                            Username

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                @

                            </span>


                            <input
                                type="text"
                                id="username"
                                name="username"
                                placeholder="Choose a username"
                                value="<?php echo htmlspecialchars($_POST["username"] ?? ""); ?>"
                                autocomplete="username"
                                required
                            >

                        </div>

                    </div>


                    <!-- EMAIL -->

                    <div class="neon-input">

                        <label for="email">

                            Email

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                ✉

                            </span>


                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="Enter your email"
                                value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                                autocomplete="email"
                                required
                            >

                        </div>

                    </div>


                    <!-- PASSWORD -->

                    <div class="neon-input">

                        <label for="registerPassword">

                            Password

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                🔒

                            </span>


                            <input
                                type="password"
                                id="registerPassword"
                                name="password"
                                placeholder="Create a password"
                                autocomplete="new-password"
                                required
                            >


                            <button
                                type="button"
                                class="eye-button"
                                onclick="togglePassword('registerPassword', this)"
                            >

                                👁

                            </button>

                        </div>

                    </div>


                    <!-- CONFIRM PASSWORD -->

                    <div class="neon-input">

                        <label for="confirmPassword">

                            Confirm Password

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                🔒

                            </span>


                            <input
                                type="password"
                                id="confirmPassword"
                                name="confirm_password"
                                placeholder="Confirm your password"
                                autocomplete="new-password"
                                required
                            >


                            <button
                                type="button"
                                class="eye-button"
                                onclick="togglePassword('confirmPassword', this)"
                            >

                                👁

                            </button>

                        </div>

                    </div>


                    <!-- CREATE ACCOUNT BUTTON -->

                    <button
                        type="submit"
                        class="neon-button"
                    >

                        <span>
                            CREATE ACCOUNT
                        </span>


                        <span>
                            →
                        </span>

                    </button>


                </form>


                <!-- =================================
                     LOGIN LINK
                ================================== -->

                <div class="register-area">

                    <p>

                        Already have an account?

                    </p>


                    <a href="index.php">

                        LOGIN

                    </a>

                </div>


                <!-- SECURITY -->

                <div class="security-note">

                    🛡 Your information is protected

                </div>


            </div>

        </div>

    </div>


    <!-- JAVASCRIPT -->

    <script src="script.js"></script>


</body>

</html>