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
   DELETE POST
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_post_id"])) {

    $delete_post_id = intval($_POST["delete_post_id"]);
    $user_id = $_SESSION["user_id"];

    $delete_stmt = $conn->prepare(
        "DELETE FROM posts WHERE id = ? AND user_id = ?"
    );

    if (!$delete_stmt) {
        die("Delete prepare failed: " . $conn->error);
    }

    $delete_stmt->bind_param("ii", $delete_post_id, $user_id);

    if (!$delete_stmt->execute()) {
        die("Delete failed: " . $delete_stmt->error);
    }

    $delete_stmt->close();

    header("Location: dashboard.php?deleted=1");
    exit();
}


/* =========================================
   PREVENT BROWSER CACHE
========================================= */

header(
    "Cache-Control: no-store, no-cache, must-revalidate, max-age=0"
);

header(
    "Cache-Control: post-check=0, pre-check=0",
    false
);

header("Pragma: no-cache");


/* =========================================
   GET USER POSTS
========================================= */

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT id, content, created_at
     FROM posts
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$posts = $stmt->get_result();

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
        Dashboard | Student System
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >


    <style>

        /* =========================================
           CREATE POST
        ========================================= */

        .cp-section {

            width: 100%;

            margin-top: 25px;

        }


        .cp-card {

            width: 100%;

            box-sizing: border-box;

            padding: 20px;

            border-radius: 20px;

            background:
                rgba(7, 25, 19, 0.88);

            border:
                1px solid
                rgba(70, 255, 175, 0.20);

            box-shadow:
                0 10px 35px rgba(0,0,0,0.20);

            backdrop-filter: blur(18px);

        }


        /* =========================================
           USER
        ========================================= */

        .cp-user {

            display: flex;

            align-items: center;

            gap: 13px;

        }


        .cp-avatar {

            width: 48px;

            height: 48px;

            min-width: 48px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #19e6a1,
                    #087b59
                );

            color: white;

            font-size: 18px;

            font-weight: bold;

            box-shadow:
                0 0 18px
                rgba(0,255,170,0.20);

        }


        .cp-user-info strong {

            display: block;

            color: white;

            font-size: 14px;

        }


        .cp-user-info span {

            color: #71877e;

            font-size: 11px;

        }


        /* =========================================
           MIND BUTTON
        ========================================= */

        .cp-mind {

            width: 100%;

            height: 48px;

            margin-top: 18px;

            padding: 0 20px;

            box-sizing: border-box;

            border-radius: 25px;

            border:
                1px solid
                rgba(255,255,255,0.08);

            background:
                rgba(255,255,255,0.035);

            color: #899e95;

            text-align: left;

            font-size: 14px;

            cursor: pointer;

            transition: 0.3s;

        }


        .cp-mind:hover {

            background:
                rgba(0,255,170,0.07);

            border-color:
                rgba(0,255,170,0.35);

            color: white;

        }


        /* =========================================
           ACTIONS
        ========================================= */

        .cp-actions {

            display: flex;

            gap: 10px;

            margin-top: 16px;

            padding-top: 15px;

            border-top:
                1px solid
                rgba(255,255,255,0.07);

        }


        .cp-action-btn {

            flex: 1;

            padding: 12px;

            border: none;

            border-radius: 10px;

            background:
                rgba(255,255,255,0.035);

            color: #9aada6;

            cursor: pointer;

            transition: 0.3s;

        }


        .cp-action-btn:hover {

            background:
                rgba(0,255,170,0.08);

            color: #69ffb0;

        }


        /* =========================================
           POSTS
        ========================================= */

        .cp-posts {

            margin-top: 20px;

        }


        .cp-post {

            padding: 22px;

            margin-bottom: 15px;

            border-radius: 20px;

            background:
                rgba(7,25,19,0.88);

            border:
                1px solid
                rgba(70,255,175,0.16);

            box-shadow:
                0 8px 30px rgba(0,0,0,0.18);

        }


        .cp-post-header {

            display: flex;

            align-items: center;

            gap: 12px;

            margin-bottom: 15px;

        }


        .cp-post-header strong {

            display: block;

            color: white;

            font-size: 14px;

        }


        .cp-post-date {

            display: block;

            color: #71877e;

            font-size: 11px;

            margin-top: 4px;

        }


        .cp-post-content {

            color: #d2e0da;

            font-size: 15px;

            line-height: 1.7;

            white-space: pre-wrap;

            word-wrap: break-word;

        }


        /* =========================================
           NO POSTS
        ========================================= */

        .cp-no-post {

            padding: 40px 20px;

            text-align: center;

            border-radius: 20px;

            background:
                rgba(7,25,19,0.70);

            border:
                1px solid
                rgba(70,255,175,0.12);

        }


        .cp-no-post-icon {

            font-size: 38px;

            margin-bottom: 10px;

        }


        .cp-no-post h3 {

            margin: 0 0 6px;

            color: #69ffb0;

        }


        .cp-no-post p {

            margin: 0;

            color: #71857d;

            font-size: 13px;

        }


        /* =========================================
           MODAL
        ========================================= */

        .cp-modal {

            position: fixed;

            inset: 0;

            width: 100%;

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;

            box-sizing: border-box;

            background:
                rgba(0,0,0,0.75);

            backdrop-filter: blur(8px);

            z-index: 99999;

            opacity: 0;

            visibility: hidden;

            pointer-events: none;

            transition: 0.3s;

        }


        .cp-modal.active {

            opacity: 1;

            visibility: visible;

            pointer-events: auto;

        }


        .cp-modal-box {

            width: 100%;

            max-width: 560px;

            border-radius: 22px;

            overflow: hidden;

            background:
                linear-gradient(
                    135deg,
                    rgba(10,35,26,0.98),
                    rgba(3,15,11,0.98)
                );

            border:
                1px solid
                rgba(82,255,180,0.30);

            box-shadow:
                0 30px 80px
                rgba(0,0,0,0.70);

            transform:
                translateY(20px)
                scale(0.96);

            transition: 0.3s;

        }


        .cp-modal.active .cp-modal-box {

            transform:
                translateY(0)
                scale(1);

        }


        /* =========================================
           MODAL HEADER
        ========================================= */

        .cp-modal-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 18px 22px;

            border-bottom:
                1px solid
                rgba(255,255,255,0.08);

        }


        .cp-modal-header h2 {

            margin: 0;

            color: white;

        }


        .cp-close {

            width: 36px;

            height: 36px;

            border: none;

            border-radius: 50%;

            background:
                rgba(255,255,255,0.08);

            color: white;

            font-size: 23px;

            cursor: pointer;

        }


        .cp-close:hover {

            background:
                rgba(255,60,60,0.20);

            color: #ff7777;

        }


        /* =========================================
           MODAL USER
        ========================================= */

        .cp-modal-user {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 18px 22px 5px;

        }


        .cp-modal-user strong {

            display: block;

            color: white;

            font-size: 14px;

        }


        .cp-public {

            display: inline-block;

            margin-top: 5px;

            color: #8da098;

            font-size: 10px;

        }


        /* =========================================
           TEXTAREA
        ========================================= */

        .cp-textarea {

            width: 100%;

            min-height: 180px;

            box-sizing: border-box;

            padding: 20px 22px;

            border: none;

            outline: none;

            resize: vertical;

            background: transparent;

            color: white;

            font-family: Arial, sans-serif;

            font-size: 18px;

        }


        .cp-textarea::placeholder {

            color: #61766d;

        }


        /* =========================================
           MODAL FOOTER
        ========================================= */

        .cp-modal-footer {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 15px 22px 20px;

            border-top:
                1px solid
                rgba(255,255,255,0.07);

        }


        .cp-counter {

            color: #62766e;

            font-size: 11px;

        }


        .cp-publish {

            min-width: 110px;

            height: 42px;

            border: none;

            border-radius: 9px;

            background:
                linear-gradient(
                    90deg,
                    #19e5a3,
                    #69f5c6
                );

            color: #00150e;

            font-weight: 800;

            cursor: pointer;

        }


        .cp-publish:hover {

            box-shadow:
                0 0 18px
                rgba(0,255,170,0.50);

            transform: translateY(-2px);

        }


        /* =========================================
           SUCCESS MESSAGE
        ========================================= */

        .post-success {

            margin-top: 15px;

            padding: 12px 15px;

            border-radius: 10px;

            background:
                rgba(0,255,170,0.08);

            border:
                1px solid
                rgba(0,255,170,0.25);

            color: #69ffb0;

            font-size: 13px;

        }


        /* =========================================
           DELETE POST BUTTON
        ========================================= */

        .cp-delete-form {
            margin-top: 15px;
        }

        .cp-delete-btn {
            background: transparent;
            color: #ff7777;
            border: 1px solid rgba(255, 90, 90, 0.45);
            padding: 8px 15px;
            border-radius: 9px;
            cursor: pointer;
            font-size: 12px;
            transition: 0.3s;
        }

        .cp-delete-btn:hover {
            background: rgba(255, 70, 70, 0.12);
            border-color: #ff7777;
            color: #ff9999;
        }

        .post-deleted {
            margin-top: 15px;
            padding: 12px 15px;
            border-radius: 10px;
            background: rgba(255, 90, 90, 0.08);
            border: 1px solid rgba(255, 90, 90, 0.25);
            color: #ff9999;
            font-size: 13px;
        }


        /* =========================================
           MOBILE
        ========================================= */

        @media (max-width: 700px) {

            .cp-actions {

                flex-direction: column;

            }

            .cp-modal {

                padding: 12px;

            }

        }

        /* =========================================
   NAVBAR WITH NORSU LOGOS
========================================= */

.navbar {
    width: 100%;
    min-height: 65px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 8px 35px;

    background: #020c09;

    border-bottom: 1px solid rgba(70, 255, 175, 0.25);

    position: sticky;
    top: 0;

    z-index: 9999;
}


/* LEFT SIDE */

.nav-left {
    display: flex;
    align-items: center;
    gap: 12px;
}


/* LOGOS */

.school-logo {
    width: 45px;
    height: 45px;

    object-fit: contain;

    display: block;

    border-radius: 50%;

    background: white;

    padding: 2px;

    flex-shrink: 0;
}


/* TITLE */

.nav-logo {
    color: white;

    font-size: 20px;

    font-weight: 800;

    margin-left: 4px;

    white-space: nowrap;
}


/* RIGHT SIDE */

.nav-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
}


/* PROFILE */

.profile-btn {
    display: inline-block;

    text-decoration: none;

    background: #69ffb0;

    color: #00150e;

    padding: 10px 18px;

    border-radius: 8px;

    font-weight: 700;

    transition: 0.3s;
}


.profile-btn:hover {
    background: #19e5a3;

    box-shadow:
        0 0 18px
        rgba(0, 255, 170, 0.45);
}


/* LOGOUT */

.logout-btn {
    display: inline-block;

    text-decoration: none;

    background: #69ffb0;

    color: #00150e;

    padding: 10px 18px;

    border-radius: 8px;

    font-weight: 700;

    transition: 0.3s;
}


.logout-btn:hover {
    background: #19e5a3;

    box-shadow:
        0 0 18px
        rgba(0, 255, 170, 0.45);
}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 700px) {

    .navbar {
        padding: 8px 15px;
    }

    .school-logo {
        width: 38px;
        height: 38px;
    }

    .nav-logo {
        font-size: 15px;
    }

    .profile-btn,
    .logout-btn {
        padding: 8px 10px;
        font-size: 12px;
    }

}


@media (max-width: 500px) {

    .nav-logo {
        display: none;
    }

    .school-logo {
        width: 40px;
        height: 40px;
    }

}

        /* POST REACTION / COMMENT / SHARE */
        .cp-post-actions { display:flex; gap:8px; margin-top:16px; padding-top:14px; border-top:1px solid rgba(255,255,255,.07); }
        .cp-post-action { position:relative; flex:1; }
        .cp-action-main { width:100%; padding:11px 8px; border:0; border-radius:10px; background:rgba(255,255,255,.035); color:#9aada6; cursor:pointer; transition:.25s; }
        .cp-action-main:hover { background:rgba(0,255,170,.08); color:#69ffb0; }
        .reaction-menu { position:absolute; bottom:48px; left:0; display:none; gap:4px; padding:8px; background:#10251e; border:1px solid rgba(109,255,176,.3); border-radius:30px; z-index:9999; box-shadow:0 10px 30px rgba(0,0,0,.55); }
        .reaction-menu.show { display:flex; }
        .reaction-menu button { width:35px; height:35px; padding:0; border:0; border-radius:50%; background:transparent; color:white; font-size:20px; cursor:pointer; }
        .reaction-menu button:hover { background:rgba(255,255,255,.1); transform:scale(1.2); }
        .comment-section { display:none; margin-top:14px; }
        .comment-section.show { display:block; }
        .comment-form { display:flex; gap:8px; }
        .comment-input { flex:1; min-width:0; height:42px; padding:0 15px; border:1px solid rgba(255,255,255,.1); border-radius:22px; background:rgba(255,255,255,.04); color:white; outline:none; }
        .comment-submit { height:42px; padding:0 17px; border:0; border-radius:22px; background:linear-gradient(90deg,#20e5a6,#63f5c4); color:#00140e; font-weight:800; cursor:pointer; }
        .comments-list { margin-top:12px; }
        .single-comment { margin-bottom:7px; padding:10px 13px; border-radius:12px; background:rgba(255,255,255,.035); border:1px solid rgba(255,255,255,.05); color:#d2ddd8; font-size:13px; }
        .comment-meta { display:block; margin-top:4px; color:#60736c; font-size:10px; }
        @media(max-width:700px){ .cp-post-actions{gap:5px}.cp-action-main{font-size:11px;padding:10px 4px}.comment-form{gap:6px}.reaction-menu{left:50%;transform:translateX(-50%)} }
    </style>

</head>


<body class="dashboard-body">


    <!-- CURSOR -->

    <div class="cursor-glow"></div>

    <div class="cursor-dot"></div>



   <nav class="navbar">

    <div class="nav-left">

        <img
            src="norsu-logo.png"
            alt="NORSU Logo"
            class="school-logo"
        >

        <img
            src="cas-logo.png"
            alt="College Logo"
            class="school-logo"
        >

        <div class="nav-logo ">
          NORSU Student Hub
        </div>

    </div>


    <div class="nav-buttons">

        <a
            href="profile.php"
            class="profile-btn"
        >
            👤 Profile
        </a>

        <a
            href="logout.php"
            class="logout-btn"
        >
            Logout
        </a>

    </div>

</nav>

    <!-- =========================================
         DASHBOARD
    ========================================= -->

    <main class="dashboard-container">


        <!-- WELCOME -->

        <section class="welcome-card">
                <img
            src="jojo.png"
            alt="NORSU Logo"
            class="school-logo"
        >

            </div>


            <div>

                <p class="small-title">
                    Welcome back!
                </p>


                <h1>

                    <?php

                    echo htmlspecialchars(
                        $_SESSION["full_name"]
                    );

                    ?>

                </h1>


                <p>
                    You have successfully logged into your account.
                </p>

            </div>

        </section>


        <!-- =========================================
             ACCOUNT INFORMATION
        ========================================== -->

        <section class="info-card">

            <div class="card-header">

                <h2>
                    Account Information
                </h2>

                <span class="status">
                    ● Active
                </span>

            </div>


            <div class="info-grid">


                <div class="info-item">

                    <span class="info-label">
                        Full Name
                    </span>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $_SESSION["full_name"]
                        );

                        ?>

                    </strong>

                </div>


                <div class="info-item">

                    <span class="info-label">
                        Username
                    </span>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $_SESSION["username"]
                        );

                        ?>

                    </strong>

                </div>


                <div class="info-item">

                    <span class="info-label">
                        Email
                    </span>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $_SESSION["email"]
                        );

                        ?>

                    </strong>

                </div>


                <div class="info-item">

                    <span class="info-label">
                        Account Status
                    </span>

                    <strong>
                        Logged In
                    </strong>

                </div>


            </div>

        </section>


        <!-- =========================================
             DASHBOARD BOXES
        ========================================== -->

        <section class="dashboard-grid">


            <div class="dashboard-box">

                <div class="box-icon">
                    🪪
                </div>

                <h3>
                    Student Profile
                </h3>

                <p>
               view and manage your personal student information.
                </p>

            </div>


            <div class="dashboard-box">

                <div class="box-icon">
                    📝
                </div>

                <h3>
                    My post﹠Update
                </h3>

                <p>
                    Create, view, edit. and delete your posts and updates.
                </p>

            </div>


            <div class="dashboard-box">

                <div class="box-icon">
                    ✉︎
                </div>

                <h3>
                 Notifications
                </h3>

                <p>
                  React,comments, and interact with post from other user.
                </p>

            </div>


        </section>


        <!-- =========================================
             CREATE POST
        ========================================== -->

        <section class="cp-section">


            <div class="cp-card">


                <div class="cp-user">


                    <div class="cp-avatar">

                        <?php

                        echo strtoupper(
                            substr(
                                $_SESSION["full_name"],
                                0,
                                1
                            )
                        );

                        ?>

                    </div>


                    <div class="cp-user-info">

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $_SESSION["full_name"]
                            );

                            ?>

                        </strong>

                        <span>
                            🌐 Public
                        </span>

                    </div>


                </div>


                <!-- CLICK TO OPEN -->

                <button
                    type="button"
                    class="cp-mind"
                    onclick="openCreatePost()"
                >

                    What's on your mind,
                    <?php

                    echo htmlspecialchars(
                        explode(
                            " ",
                            $_SESSION["full_name"]
                        )[0]
                    );

                    ?>?

                </button>


                <!-- ACTIONS -->

                <div class="cp-actions">


                    <button
                        type="button"
                        class="cp-action-btn"
                        onclick="openCreatePost()"
                    >

                        📝 Create Post

                    </button>


                    <button
                        type="button"
                        class="cp-action-btn"
                        onclick="openCreatePost()"
                    >

                        💬 Share an Update

                    </button>


                </div>


            </div>







            
            <!-- ===================================== SQL SELEC  Query Implementation:  As seen in  get_commentes.php  and get_react.php ,
              the system uses prepared statements with  SELECT  queries to retrieve related records. For example, comments are fetched using:
                  SELECT pc.comment, pc.created_at, u.full_name FROM post_comments pc ... WHERE pc.post_id = ? ORDER BY pc.created_at ASC`

            And reactions are queried using:
             SELECT reaction FROM post_reactions WHERE post_id = ? AND user_id = ?`
 
           Displaying Records: The fetched database rows are processed, encoded in JSON format, and dynamically rendered onto the dashboard interface 
           for users to view posts, comments, and reaction lists.

            ====================================== -->

            <!-- =====================================
                 SUCCESS
            ====================================== -->

            <?php if (isset($_GET["posted"])): ?>

                <div class="post-success">

                    ✓ Your post was published successfully!

                </div>

            <?php endif; ?>

            <?php if (isset($_GET["deleted"])): ?>

                <div class="post-deleted">
                    ✓ Your post was deleted successfully!
                </div>

            <?php endif; ?>


            <!-- =====================================
                 POSTS
            ====================================== -->

            <div class="cp-posts">


                <?php if ($posts->num_rows > 0): ?>


                    <?php while ($post = $posts->fetch_assoc()): ?>


                        <div class="cp-post">


                            <div class="cp-post-header">


                                <div class="cp-avatar">

                                    <?php

                                    echo strtoupper(
                                        substr(
                                            $_SESSION["full_name"],
                                            0,
                                            1
                                        )
                                    );

                                    ?>

                                </div>


                                <div>

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $_SESSION["full_name"]
                                        );

                                        ?>

                                    </strong>


                                    <span class="cp-post-date">

                                        🌐 Public ·

                                        <?php

                                        echo date(
                                            "M d, Y h:i A",
                                            strtotime(
                                                $post["created_at"]
                                            )
                                        );

                                        ?>

                                    </span>

                                </div>


                            </div>


                            <div class="cp-post-content">

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $post["content"]
                                    )
                                );

                                ?>

                            </div>

                            <!-- POST ACTIONS -->
                            <div class="cp-post-actions">
                                <div class="cp-post-action reaction-wrap">
                                    <button type="button" class="cp-action-main" onclick="toggleReactionMenu(<?php echo $post["id"]; ?>)">
                                        <span id="reaction-icon-<?php echo $post["id"]; ?>">👍</span>
                                        <span id="reaction-count-<?php echo $post["id"]; ?>">0</span>
                                    </button>
                                    <div class="reaction-menu" id="reaction-menu-<?php echo $post["id"]; ?>">
                                        <button type="button" onclick="sendReaction(<?php echo $post["id"]; ?>, 'like')">👍</button>
                                        <button type="button" onclick="sendReaction(<?php echo $post["id"]; ?>, 'love')">❤️</button>
                                        <button type="button" onclick="sendReaction(<?php echo $post["id"]; ?>, 'haha')">😂</button>
                                        <button type="button" onclick="sendReaction(<?php echo $post["id"]; ?>, 'wow')">😮</button>
                                        <button type="button" onclick="sendReaction(<?php echo $post["id"]; ?>, 'sad')">😢</button>
                                        <button type="button" onclick="sendReaction(<?php echo $post["id"]; ?>, 'angry')">😡</button>
                                    </div>
                                </div>
                                <div class="cp-post-action">
                                    <button type="button" class="cp-action-main" onclick="toggleComments(<?php echo $post["id"]; ?>)">💬 Comment <span id="comment-count-<?php echo $post["id"]; ?>">0</span></button>
                                </div>
                                <div class="cp-post-action">
                                    <button type="button" class="cp-action-main" onclick="sharePost(<?php echo $post["id"]; ?>)">↗ Share</button>
                                </div>
                            </div>

                            <div class="comment-section" id="comment-section-<?php echo $post["id"]; ?>">
                                <form class="comment-form" onsubmit="submitComment(event, <?php echo $post["id"]; ?>)">
                                    <input type="text" class="comment-input" id="comment-input-<?php echo $post["id"]; ?>" maxlength="500" placeholder="Write a comment..." required>
                                    <button type="submit" class="comment-submit">Send</button>
                                </form>
                                <div class="comments-list" id="comments-<?php echo $post["id"]; ?>"></div>
                            </div>

                            <!-- DELETE POST -->

                            <form
                                action="dashboard.php"
                                method="POST"
                                class="cp-delete-form"
                                onsubmit="return confirm('Are you sure you want to delete this post?');"
                            >

                                <input
                                    type="hidden"
                                    name="delete_post_id"
                                    value="<?php echo $post["id"]; ?>"
                                >

                                <button
                                    type="submit"
                                    class="cp-delete-btn"
                                >
                                    🗑️ Delete Post
                                </button>

                            </form>



                        </div>


                    <?php endwhile; ?>


                <?php else: ?>


                    <div class="cp-no-post">


                        <div class="cp-no-post-icon">
                            📝
                        </div>


                        <h3>
                            No posts yet
                        </h3>


                        <p>
                            Be the first to share something!
                        </p>


                    </div>


                <?php endif; ?>


            </div>


        </section>


        <!-- =========================================
             DELETE ACCOUNT
        ========================================== -->

        <section class="account-actions">


            <div class="delete-card">


                <div class="delete-content">

                    <h2>
                        Delete Account
                    </h2>


                    <p>
                        Permanently delete your Student
                        System account and all information
                        associated with it.
                    </p>

                </div>


                <button
                    type="button"
                    class="delete-account-btn"
                    onclick="confirmDeleteAccount()"
                >

                    🗑 Delete Account

                </button>


            </div>


        </section>


    </main>


    <!-- =========================================
         CREATE POST MODAL
    ========================================== -->

    <div
        class="cp-modal"
        id="createPostModal"
    >


        <div class="cp-modal-box">


            <!-- HEADER -->

            <div class="cp-modal-header">

                <h2>
                    Create Post
                </h2>


                <button
                    type="button"
                    class="cp-close"
                    onclick="closeCreatePost()"
                >

                    ×

                </button>

            </div>


            <!-- USER -->

            <div class="cp-modal-user">


                <div class="cp-avatar">

                    <?php

                    echo strtoupper(
                        substr(
                            $_SESSION["full_name"],
                            0,
                            1
                        )
                    );

                    ?>

                </div>


                <div>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $_SESSION["full_name"]
                        );

                        ?>

                    </strong>


                    <span class="cp-public">

                        🌐 Public

                    </span>

                </div>

            </div>


            <!-- =====================================
                 ACTUAL FORM
            ====================================== -->

            <form
                action="create_post.php"
                method="POST"
            >


                <textarea
                    name="content"
                    id="cpPostText"
                    class="cp-textarea"
                    maxlength="1000"
                    placeholder="What's on your mind?"
                    oninput="updatePostCount()"
                    required
                ></textarea>


                <div class="cp-modal-footer">


                    <span
                        class="cp-counter"
                        id="cpCounter"
                    >

                        0 / 1000

                    </span>


                    <button
                        type="submit"
                        class="cp-publish"
                    >

                        PUBLISH

                    </button>


                </div>


            </form>


        </div>

    </div>


    <!-- =========================================
         JAVASCRIPT
    ========================================== -->

    <script>


        /* OPEN MODAL */

        function openCreatePost() {

            document
                .getElementById("createPostModal")
                .classList
                .add("active");

            document
                .getElementById("cpPostText")
                .focus();

        }


        /* CLOSE MODAL */

        function closeCreatePost() {

            document
                .getElementById("createPostModal")
                .classList
                .remove("active");

        }


        /* CHARACTER COUNT */

        function updatePostCount() {

            const text =
                document.getElementById("cpPostText");

            const counter =
                document.getElementById("cpCounter");

            counter.textContent =
                text.value.length + " / 1000";

        }


        /* CLICK OUTSIDE */

        document
            .getElementById("createPostModal")
            .addEventListener(
                "click",
                function(event) {

                    if (
                        event.target === this
                    ) {

                        closeCreatePost();

                    }

                }
            );


        /* ESC KEY */

        document.addEventListener(
            "keydown",
            function(event) {

                if (
                    event.key === "Escape"
                ) {

                    closeCreatePost();

                }

            }
        );


        /* DELETE ACCOUNT */

        function confirmDeleteAccount() {

            const answer = confirm(

                "Are you sure you want to delete your account?\n\n" +

                "This action cannot be undone."

            );


            if (answer) {

                window.location.href =
                    "delete_account.php";

            }

        }


        function toggleReactionMenu(postId) {
            const menu=document.getElementById("reaction-menu-"+postId);
            document.querySelectorAll(".reaction-menu.show").forEach(m=>{if(m!==menu)m.classList.remove("show")});
            menu.classList.toggle("show");
        }

        function sendReaction(postId,reaction){
            const fd=new FormData(); fd.append("post_id",postId); fd.append("reaction",reaction);
            fetch("react_post.php",{method:"POST",body:fd})
            .then(r=>r.json()).then(data=>{
                if(!data.success){alert(data.message||"Failed to react");return;}
                document.getElementById("reaction-count-"+postId).textContent=data.count;
                document.getElementById("reaction-icon-"+postId).textContent=data.user_reaction_icon||"👍";
                document.getElementById("reaction-menu-"+postId).classList.remove("show");
            }).catch(()=>alert("Reaction error. Check react_post.php."));
        }

        function toggleComments(postId){
            const section=document.getElementById("comment-section-"+postId);
            section.classList.toggle("show");
            if(section.classList.contains("show")){ loadComments(postId); document.getElementById("comment-input-"+postId).focus(); }
        }

        function submitComment(event,postId){
            event.preventDefault();
            const input=document.getElementById("comment-input-"+postId); const text=input.value.trim();
            if(!text)return;
            const fd=new FormData(); fd.append("post_id",postId); fd.append("comment",text);
            fetch("add_comment.php",{method:"POST",body:fd})
            .then(r=>r.json()).then(data=>{
                if(!data.success){alert(data.message||"Failed to add comment");return;}
                input.value=""; loadComments(postId);
            }).catch(()=>alert("Comment error. Check add_comment.php."));
        }

        function loadComments(postId){
            fetch("get_comments.php?post_id="+encodeURIComponent(postId))
            .then(r=>r.json()).then(data=>{
                if(!data.success)return;
                const list=document.getElementById("comments-"+postId); list.innerHTML="";
                document.getElementById("comment-count-"+postId).textContent=data.comments.length;
                data.comments.forEach(c=>{
                    const item=document.createElement("div"); item.className="single-comment";
                    const text=document.createElement("div"); text.textContent=(c.full_name||"User")+": "+c.comment;
                    const meta=document.createElement("span"); meta.className="comment-meta"; meta.textContent=c.created_at||"";
                    item.appendChild(text); item.appendChild(meta); list.appendChild(item);
                });
            }).catch(()=>{});
        }

        function sharePost(postId){
            const url=window.location.origin+window.location.pathname+"#post-"+postId;
            if(navigator.share){ navigator.share({title:"Student System Post",url:url}).catch(()=>{}); }
            else if(navigator.clipboard){ navigator.clipboard.writeText(url).then(()=>alert("Post link copied!")); }
            else { prompt("Copy this link:",url); }
        }

        document.addEventListener("click",function(e){
            if(!e.target.closest(".reaction-wrap")) document.querySelectorAll(".reaction-menu.show").forEach(m=>m.classList.remove("show"));
        });

        document.addEventListener("DOMContentLoaded",function(){
            document.querySelectorAll(".cp-post").forEach(post=>{
                const id=post.querySelector(".reaction-menu")?.id.replace("reaction-menu-","");
                if(id) loadReaction(id);
            });
        });

        function loadReaction(postId){
            fetch("get_reaction.php?post_id="+encodeURIComponent(postId))
            .then(r=>r.json()).then(data=>{
                if(!data.success)return;
                document.getElementById("reaction-count-"+postId).textContent=data.count;
                if(data.user_reaction_icon) document.getElementById("reaction-icon-"+postId).textContent=data.user_reaction_icon;
            }).catch(()=>{});
        }

    </script>


    <script src="script.js"></script>


</body>

</html>

<?php

$stmt->close();

$conn->close();

?>
