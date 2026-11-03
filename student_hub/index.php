<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = $_SESSION['error'] ?? "";
$success = $_SESSION['success'] ?? "";

unset($_SESSION['error']);
unset($_SESSION['success']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login | NORSU Student Hub</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="login-body">

    <!-- =========================================
         NEON CURSOR EFFECT
         ========================================= -->

    <div class="cursor-glow"></div>
    <div class="cursor-dot"></div>


    <!-- =========================================
         BACKGROUND EFFECTS
         ========================================= -->

    <div class="background-overlay"></div>

    <div class="rain rain-one"></div>
    <div class="rain rain-two"></div>
    <div class="rain rain-three"></div>


    <!-- =========================================
         LOGIN CONTAINER
         ========================================= -->

    <div class="login-wrapper">

        <div class="glass-login-card">


            <!-- =====================================
                 LEFT SIDE - VISUAL
                 ===================================== -->

            <div class="visual-side">

                <!-- Hanging Lamp -->

                <div class="lamp">

                    <div class="lamp-light"></div>

                </div>


                <!-- Robot -->

                <div class="robot">

                    <div class="robot-face">

                        <div class="robot-eye"></div>

                        <div class="robot-eye"></div>

                    </div>

                    <div class="robot-smile">
                        ◡
                    </div>

                </div>


                <!-- Welcome Text -->

                <div class="visual-text">

                    <span>
                        SECURE ACCESS
                    </span>

                    <h2>

                        Welcome to<br>

                        <strong>
                            NORSU Student Hub
                        </strong>

                    </h2>

                    <p>
                        Your secure digital workspace.
                    </p>

                </div>

            </div>


            <!-- =====================================
                 RIGHT SIDE - LOGIN FORM
                 ===================================== -->

            <div class="form-side">


                <!-- FORM HEADER -->

                <div class="form-header">

                    <div class="mini-logo">
                        🔐
                    </div>

                    <h1>
                        Welcome <span>Back.</span>
                    </h1>

                    <p>
                        Sign in to continue to your account
                    </p>

                </div>


                <!-- =================================
                     SUCCESS MESSAGE
                     ================================= -->

                <?php if (!empty($success)): ?>

                    <div class="message success-message">

                        ✓

                        <?php
                        echo htmlspecialchars($success);
                        ?>

                    </div>

                <?php endif; ?>


                <!-- =================================
                     ERROR MESSAGE
                     ================================= -->

                <?php if (!empty($error)): ?>

                    <div class="message error-message">

                        !

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </div>

                <?php endif; ?>


                <!-- =================================
                     LOGIN FORM
                     ================================= -->

                <form
                    action="login.php"
                    method="POST"
                    id="loginForm"
                >


                    <!-- USERNAME / EMAIL -->

                    <div class="neon-input">

                        <label for="login">
                            Username or Email
                        </label>

                        <div class="input-wrapper">

                            <span class="input-icon">
                                👤
                            </span>

                            <input
                                type="text"
                                id="login"
                                name="login"
                                placeholder="Enter username or email"
                                autocomplete="username"
                                required
                            >

                        </div>

                    </div>


                    <!-- PASSWORD -->

                    <div class="neon-input">

                        <label for="loginPassword">
                            Password
                        </label>

                        <div class="input-wrapper">

                            <span class="input-icon">
                                🔒
                            </span>

                            <input
                                type="password"
                                id="loginPassword"
                                name="password"
                                placeholder="Enter password"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="eye-button"
                                onclick="togglePassword('loginPassword', this)"
                            >
                                👁
                            </button>

                        </div>

                    </div>


                    <!-- OPTIONS -->

                    <div class="form-options">

                        <label class="remember">

                            <input
                                type="checkbox"
                                name="remember"
                            >

                            <span>
                                Remember me
                            </span>

                        </label>


                        <a href="#">
                            Forgot password?
                        </a>

                    </div>


                    <!-- SIGN IN BUTTON -->

                    <button
                        type="submit"
                        class="neon-button"
                    >

                        <span>
                            SIGN IN
                        </span>

                        <span>
                            →
                        </span>

                    </button>

                </form>


                <!-- =================================
                     DIVIDER
                     ================================= -->

                <div class="or-divider">

                    <span></span>

                    <p>
                        OR
                    </p>

                    <span></span>

                </div>


                <!-- =================================
                     SOCIAL BUTTONS
                     ================================= -->

                <div class="social-buttons">

                    <button type="button">

                        <b>
                            G
                        </b>

                        Google

                    </button>


                    <button type="button">

                        <b>
                            GH
                        </b>

                        GitHub

                    </button>

                </div>


                <!-- =================================
                     REGISTER
                     ================================= -->

                <div class="register-area">

                    <p>
                        Don't have an account?
                    </p>

                    <a href="register.php">
                        CREATE ACCOUNT
                    </a>

                </div>


                <!-- SECURITY -->

                <div class="security-note">

                    🛡
                    Your information is protected

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================
         JAVASCRIPT
         ========================================= -->

    <script src="script.js"></script>

</body>

</html>