<?php

session_start();

require_once "db.php";


/* =========================================
   CHECK LOGIN
========================================= */

if (!isset($_SESSION["user_id"])) {

    header("Location: index.php");
    exit();

}


/* =========================================
   GET CURRENT USER
========================================= */

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT id, full_name, username, email
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();


if (!$user) {

    session_destroy();

    header("Location: index.php");
    exit();

}


$full_name = htmlspecialchars($user["full_name"]);
$username = htmlspecialchars($user["username"]);
$email = htmlspecialchars($user["email"]);


/* =========================================
   GET USER POSTS
========================================= */

$post_stmt = $conn->prepare(
    "SELECT id, content, created_at
     FROM posts
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$post_stmt->bind_param("i", $user_id);

$post_stmt->execute();

$posts = $post_stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo $full_name; ?> | Profile
    </title>

    <link
        rel="stylesheet"
        href="profile.css"
    >

</head>


<body>


<!-- =========================================
     NAVBAR
========================================= -->

<nav class="profile-navbar">

    <div class="profile-logo">
        NORSU Student Hub
    </div>


    <div class="profile-nav-buttons">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</nav>



<!-- =========================================
     PROFILE PAGE
========================================= -->

<main class="profile-container">


    <!-- =====================================
         COVER PHOTO
    ====================================== -->

    <section class="cover-photo">

        <button class="cover-button">
            📷 Add cover photo
        </button>

    </section>



    <!-- =====================================
         PROFILE INFORMATION
    ====================================== -->

    <section class="profile-header">


        <!-- PROFILE PICTURE -->

<div class="profile-picture">

    <section class="welcome-card">
                
            

</div>



        <!-- USER INFORMATION -->

        <div class="profile-info">

            <h1>
                <?php echo $full_name; ?>
            </h1>

            <p class="username">
                @<?php echo $username; ?>
            </p>


            <div class="followers">

                <strong>5M</strong>
                followers

                <span>•</span>

                <strong>1K</strong>
                following

            </div>


            <p class="bio">
                🎥 PARA NI NIMO RENE
            </p>

        </div>



        <!-- PROFILE BUTTONS -->

        <div class="profile-actions">

            <a
                href="dashboard.php"
                class="dashboard-button"
            >
                📊 Dashboard
            </a>


            <button
                class="edit-button"
                onclick="openEditProfile()"
            >
                ✏️ Edit
            </button>


            <button class="more-button">
                ⋮
            </button>

        </div>

    </section>



    <!-- =====================================
         PROFILE TABS
    ====================================== -->

    <div class="profile-tabs">

        <a href="#posts" class="active">
            All
        </a>

        <a href="#about">
            About
        </a>

        <a href="#reels">
            Reels
        </a>

        <a href="#photos">
            Photos
        </a>

        <a href="#friends">
            Friends
        </a>

        <a href="#more">
            More ▼
        </a>

    </div>



    <!-- =====================================
         PROFILE CONTENT
    ====================================== -->

    <section class="profile-content">


        <!-- =================================
             ABOUT
        ================================== -->

        <div
            class="about-card"
            id="about"
        >

            <h2>
                About
            </h2>


            <div class="about-item">

                <span>👤</span>

                <div>

                    <small>
                        Full Name
                    </small>

                    <strong>
                        <?php echo $full_name; ?>
                    </strong>

                </div>

            </div>


            <div class="about-item">

                <span>🔖</span>

                <div>

                    <small>
                        Username
                    </small>

                    <strong>
                        @<?php echo $username; ?>
                    </strong>

                </div>

            </div>


            <div class="about-item">

                <span>📧</span>

                <div>

                    <small>
                        Email
                    </small>

                    <strong>
                        <?php echo $email; ?>
                    </strong>

                </div>

            </div>


            <div class="about-item">

                <span>🎥</span>

                <div>

                    <small>
                        Category
                    </small>

                    <strong>
                        Digital Creator
                    </strong>

                </div>

            </div>

        </div>



        <!-- =================================
             POSTS
        ================================== -->

        <div
            class="posts-card"
            id="posts"
        >

            <h2>
                Posts
            </h2>


            <?php if ($posts->num_rows > 0): ?>


                <?php while ($post = $posts->fetch_assoc()): ?>


                    <div class="profile-post">


                        <div class="post-user">

                            <div class="small-avatar">

                                <?php

                                echo strtoupper(
                                    substr(
                                        $user["full_name"],
                                        0,
                                        1
                                    )
                                );

                                ?>

                            </div>


                            <div>

                                <strong>
                                    <?php echo $full_name; ?>
                                </strong>

                                <small>

                                    Public ·

                                    <?php

                                    echo date(
                                        "M d, Y h:i A",
                                        strtotime(
                                            $post["created_at"]
                                        )
                                    );

                                    ?>

                                </small>

                            </div>

                        </div>



                        <div class="post-content">

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $post["content"]
                                )
                            );

                            ?>

                        </div>


                        <div class="post-actions">

                            <button>
                                👍 Like
                            </button>

                            <button>
                                💬 Comment
                            </button>

                            <button>
                                ↗ Share
                            </button>

                        </div>


                    </div>

                    


                <?php endwhile; ?>


            <?php else: ?>


                <div class="no-posts">

                    📝

                    <h3>
                        No posts yet
                    </h3>

                    <p>
                        Your posts will appear here.
                    </p>

                </div>


            <?php endif; ?>


        </div>


    </section>


</main>



<!-- =========================================
     EDIT PROFILE MODAL
========================================= -->

<div
    class="modal"
    id="editModal"
>

    <div class="modal-box">

        <button
            class="close-button"
            onclick="closeEditProfile()"
        >
            ×
        </button>


        <h2>
            Edit Profile
        </h2>


        <p>
            Your current account information
        </p>


        <label>
            Full Name
        </label>

        <input
            type="text"
            value="<?php echo $full_name; ?>"
            readonly
        >


        <label>
            Username
        </label>

        <input
            type="text"
            value="@<?php echo $username; ?>"
            readonly
        >


        <label>
            Email
        </label>

        <input
            type="email"
            value="<?php echo $email; ?>"
            readonly
        >


        <div class="modal-note">

            Profile editing can be connected
            to your database later.

        </div>

    </div>

</div>



<script>

function openEditProfile() {

    document
        .getElementById("editModal")
        .classList
        .add("show");

}


function closeEditProfile() {

    document
        .getElementById("editModal")
        .classList
        .remove("show");

}


window.addEventListener(
    "click",
    function(event) {

        const modal =
            document.getElementById("editModal");

        if (event.target === modal) {

            closeEditProfile();

        }

    }
);

</script>


</body>

</html>


<?php

$stmt->close();

$post_stmt->close();

$conn->close();

?>