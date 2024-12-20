<?php
session_start();
include("php/config.php");
if (isset($_GET['articleID'])) {
    $id = $_GET['articleID'];
    mysqli_query($con, "UPDATE statistiques SET views = views + 1 WHERE articleID = $id");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Page</title>
    <link rel="stylesheet" href="style/article.css">
</head>

<body>
    <header class="page-header">
        <nav class="navbar">
            <a href="homepage.php" class="logo">MyBlog</a>
            <?php
            include("php/config.php");
            if (isset($_SESSION['id'])) {
                $userID = $_SESSION['id'];
                $query = mysqli_query($con, "SELECT author_name FROM authors where authorID = $userID");

                if ($result = mysqli_fetch_assoc($query)) {
                    echo "
                    <div class='user-profile'>{$result['author_name']}</div>
                    <ul class='nav-links'>
                
                <li><a  href='dashboard.php'><button>Dashboard</button></a></li>
                <li><a href='logout.php'><button >Logout</button></a></li>
            </ul>";
                }
            } else {
                echo "<ul class='nav-links'>
            <li><button class='sign-in-btn' onclick=\"showContainer('.sign-in-container')\">Sign-in</button></li>
            <li><button class='sign-up-btn' onclick=\"showContainer('.sign-up-container')\">Sign-up</button></li>
        </ul>";
            }
            ?>
        </nav>
    </header>

    <main class="content">
        <section class="article-section">
            <?php
            include("php/config.php");

            if (isset($_GET['articleID'])) {
                $articleID = $_GET['articleID'];
                $query = mysqli_query($con, "SELECT articles.article_title , articles.article_content , authors.author_name , statistiques.views , statistiques.likes
                               FROM articles
                               JOIN authors on articles.authorID = authors.authorID
                               JOIN statistiques on articles.articleID = statistiques.articleID
                               WHERE articles.articleID = $articleID");
                if ($result = mysqli_fetch_assoc($query)) {
                    echo "<header class='article-header'>
                <h1>{$result['article_title']}</h1>
                <p class='author'>By <strong>{$result['author_name']}</strong> - Published on </p>
            </header>
            
            <article class='article-body'>
                <p>{$result['article_content']}</p>
               
            </article>

            <div class='article-stats'>
                <button class='like-btn' onclick='toggleLike()'>Like</button>
                <p id='like-status'>{$result['likes']} Likes</p>
                <p class='views'>Views: <span id='view-count'>{$result['views']}</span></p>
            </div>";
                }
            }
            ?>
        </section>

        <section class="comments-section" id="comments">

            <div class="comments-container">
                <?php
                include("php/config.php");
                if (isset($_POST['add-comment']) && isset($_GET['articleID'])) {
                    $articleID = $_GET['articleID'];
                    $visitor = $_POST['comment-name'];
                    $content = $_POST['comment-content'];
                   
                    if(mysqli_query($con, "INSERT INTO comments(visitor_name,comment_content,articleID,comment_date) VALUES('$visitor','$content',$articleID,NOW())")){

                        header("location: article.php?articleID={$articleID}");
                        exit();
                    }
                }
                ?>
                <form class="comment-form" action="" method="POST">
                    <h2>Leave a Comment</h2>
                    <div class="form-group">
                        <label for="comment-name">Name:</label>
                        <input type="text" id="comment-name" placeholder="Enter your name" name="comment-name" required>
                    </div>
                    <div class="form-group">
                        <label for="comment-content">Comment:</label>
                        <textarea id="comment-content" placeholder="Write your comment here..." name="comment-content" required></textarea>
                    </div>
                    <button class="submit-btn" name="add-comment" type="submit">Submit</button>
                </form>

                <div class="comments-list">
                    <h3>Recent Comments</h3>
                    <?php
                    include("php/config.php");
                    if(isset($_GET['articleID'])){
                        $articleID = $_GET['articleID'];
                        $query = mysqli_query($con,"SELECT visitor_name , comment_content , comment_date FROM comments WHERE articleID = $articleID");
                        while($result = mysqli_fetch_assoc($query)){
                            echo "<div class='comment'>
                            <p><strong>{$result['visitor_name']} :</strong>{$result['comment_content']}</p>
                            <p class='comment-date'>Posted on {$result['comment_date']}</p>
                        </div>";
                        }

                    }
                    ?>
                    
                </div>
            </div>
        </section>
    </main>

    <footer class="page-footer">
        <p>&copy; 2024 MyBlog. All rights reserved.</p>
    </footer>

    <script>
        let likeCount = 0;

        function toggleLike() {
            likeCount++;
            document.getElementById("like-status").textContent = `${likeCount} Likes`;
        }

        function submitComment(event) {
            event.preventDefault();
            const name = document.getElementById("comment-name").value.trim();
            const content = document.getElementById("comment-content").value.trim();

            if (name && content) {
                const commentList = document.querySelector(".comments-list");
                const newComment = document.createElement("div");
                newComment.classList.add("comment");
                newComment.innerHTML = `
                    <p><strong>${name}:</strong> ${content}</p>
                    <p class="comment-date">Posted on: ${new Date().toISOString().split('T')[0]}</p>
                `;
                commentList.appendChild(newComment);

                document.getElementById("comment-name").value = "";
                document.getElementById("comment-content").value = "";
            } else {
                alert("Please fill out both fields.");
            }
        }
    </script>
</body>

</html>