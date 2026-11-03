document.addEventListener("DOMContentLoaded", function () {

    /* =========================================
       LOGIN FORM VALIDATION
       ========================================= */

    const loginForm =
        document.getElementById("loginForm");


    if (loginForm) {

        loginForm.addEventListener(
            "submit",
            function (event) {

                const login =
                    document
                    .getElementById("login")
                    .value
                    .trim();

                const password =
                    document
                    .getElementById("loginPassword")
                    .value;


                /* Check username/email */

                if (login === "") {

                    event.preventDefault();

                    alert(
                        "Please enter your username or email."
                    );

                    return;
                }


                /* Check password */

                if (password === "") {

                    event.preventDefault();

                    alert(
                        "Please enter your password."
                    );

                    return;
                }

            }
        );

    }


    /* =========================================
       NEON CURSOR EFFECT
       ========================================= */

    const cursorGlow =
        document.querySelector(".cursor-glow");

    const cursorDot =
        document.querySelector(".cursor-dot");


    if (cursorGlow && cursorDot) {

        let mouseX =
            window.innerWidth / 2;

        let mouseY =
            window.innerHeight / 2;

        let glowX = mouseX;

        let glowY = mouseY;


        /* =====================================
           GET MOUSE POSITION
           ===================================== */

        document.addEventListener(
            "mousemove",
            function (event) {

                mouseX =
                    event.clientX;

                mouseY =
                    event.clientY;


                /* Small neon dot */

                cursorDot.style.left =
                    mouseX + "px";

                cursorDot.style.top =
                    mouseY + "px";

            }
        );


        /* =====================================
           SMOOTH NEON GLOW
           ===================================== */

        function animateGlow() {

            glowX +=
                (mouseX - glowX) * 0.12;

            glowY +=
                (mouseY - glowY) * 0.12;


            cursorGlow.style.left =
                glowX + "px";

            cursorGlow.style.top =
                glowY + "px";


            requestAnimationFrame(
                animateGlow
            );

        }


        animateGlow();


        /* =====================================
           HOVER EFFECT
           ===================================== */

        const hoverElements =
            document.querySelectorAll(
                "button, a, input, .robot, .lamp, .glass-login-card"
            );


        hoverElements.forEach(
            function (element) {


                /* Mouse enters */

                element.addEventListener(
                    "mouseenter",
                    function () {

                        document.body.classList.add(
                            "cursor-hover"
                        );

                    }
                );


                /* Mouse leaves */

                element.addEventListener(
                    "mouseleave",
                    function () {

                        document.body.classList.remove(
                            "cursor-hover"
                        );

                    }
                );

            }
        );

    }

});


/* =========================================
   SHOW / HIDE PASSWORD
   ========================================= */

function togglePassword(
    inputId,
    button
) {

    const input =
        document.getElementById(inputId);


    /* Make sure input exists */

    if (!input) {
        return;
    }


    /* Show password */

    if (input.type === "password") {

        input.type = "text";

        button.textContent = "🙈";

    }


    /* Hide password */

    else {

        input.type = "password";

        button.textContent = "👁";

 
 
 /* =========================================
   REACTION MENU
========================================= */

function toggleReactionMenu(postId) {

    const menu = document.getElementById(
        "reaction-menu-" + postId
    );

    menu.classList.toggle("show");
}


/* =========================================
   SEND REACTION
========================================= */

function sendReaction(postId, reaction) {

    const formData = new FormData();

    formData.append("post_id", postId);
    formData.append("reaction", reaction);

    fetch("react_post.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {

            document.getElementById(
                "reaction-count-" + postId
            ).textContent = data.count;

            document.getElementById(
                "reaction-menu-" + postId
            ).classList.remove("show");

        } else {

            alert(data.message);

        }

    })
    .catch(error => {

        console.error(error);

        alert("Something went wrong.");

    });
}


/* =========================================
   COMMENT SECTION
========================================= */

function toggleComments(postId) {

    const section = document.getElementById(
        "comment-section-" + postId
    );

    section.classList.toggle("show");

}


/* =========================================
   SUBMIT COMMENT
========================================= */

function submitComment(event, postId) {

    event.preventDefault();

    const input = document.getElementById(
        "comment-input-" + postId
    );

    const comment = input.value.trim();

    if (comment === "") {
        return;
    }

    const formData = new FormData();

    formData.append("post_id", postId);
    formData.append("comment", comment);

    fetch("comment_post.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {

            const comments = document.getElementById(
                "comments-" + postId
            );

            const newComment =
                document.createElement("div");

            newComment.className = "single-comment";

            newComment.textContent =
                data.comment;

            comments.appendChild(newComment);

            input.value = "";

        } else {

            alert(data.message);

        }

    })
    .catch(error => {

        console.error(error);

        alert("Something went wrong.");

    });
}


/* =========================================
   SHARE POST
========================================= */

function sharePost(postId, content) {

    const shareUrl =
        window.location.origin +
        window.location.pathname +
        "?post=" +
        postId;

    if (navigator.share) {

        navigator.share({
            title: "Student System Post",
            text: content,
            url: shareUrl
        });

    } else {

        navigator.clipboard.writeText(shareUrl)
            .then(() => {

                alert(
                    "Post link copied! You can now share it."
                );

            })
            .catch(() => {

                prompt(
                    "Copy this link:",
                    shareUrl
                );

            });

    }
}   }

}