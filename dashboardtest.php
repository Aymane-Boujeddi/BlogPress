<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['username'])) {
    header("location: homepage.php");
}
if (isset($_GET['deleteArticle'])) {
    $id = $_GET['deleteArticle'];
    $delete_article = mysqli_query($con, "DELETE FROM articles WHERE articleID = $id");
}
if (isset($_GET['deleteComment'])) {
    $commentID = $_GET['deleteComment'];
    $delete_comment = mysqli_query($con, "DELETE FROM comments WHERE commentID = $commentID");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Dashboard</title>
    <link rel="stylesheet" href="style/dashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Author Dashboard</h2>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a onclick="showPart('.add-article')">Add Article</a></li>
                <li><a onclick="showPart('.manage-articles')">Manage Articles</a></li>
                <li><a onclick="showPart('.manage-comments')">Manage Comments</a></li>
                <li><a onclick="showPart('.stats')">Statistics</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <section class="add-article container">
                <h2>Add New Article</h2>
                <?php

                if (isset($_POST['add-article'])) {
                    $title = mysqli_real_escape_string($con, $_POST['title']);
                    $content = mysqli_real_escape_string($con, $_POST['content']);
                    $authorID = $_SESSION['id'];

                    if (mysqli_query($con, "INSERT INTO articles (article_title,article_content,authorID,creation_date) VALUES ('$title','$content','$authorID',NOW())")) {
                        $articleID = mysqli_query($con, "SELECT articleID FROM articles where article_title ='$title' and article_content = '$content' ");
                        if ($result = mysqli_fetch_assoc($articleID)) {
                            mysqli_query($con, "INSERT INTO statistiques (views,likes,articleID) VALUES (0,0,'{$result['articleID']}')");
                            header("location:" .  $_SERVER['PHP_SELF']);
                            exit();
                        }
                    }
                    mysqli_close($con);
                }
                ?>
                <form action="" method="POST">
                    <label>Title:</label>
                    <input type="text" id="title" name="title" required>

                    <label>Content:</label>
                    <textarea id="content" name="content" rows="5" required></textarea>

                    <button type="add-article" name="add-article">Add Article</button>
                </form>
            </section>

            <section class="manage-articles container" style="display: none;">
                <h2>Manage Articles</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include("php/config.php");

                        $articles = mysqli_query($con, "SELECT article_title , articleID FROM articles WHERE authorID = '{$_SESSION['id']}'");

                        while ($result = mysqli_fetch_assoc($articles)) {
                            echo "<tr>
                                    <td> {$result['article_title']} </td>
                                    <td> {$result['articleID']} </td>
                                    <td>
                                        <a class='edit-btn'><button class='edit-btn'>Edit</button></a>
                                        <a   href='dashboard.php?deleteArticle=" . $result['articleID'] . "'><button class='delete-btn'>DELETE</button></a>
                                    </td>
                                  </tr>";
                        }
                        ?>

                    </tbody>
                </table>
            </section> <section class="manage-comments container" style="display: none;">
                <h2>Manage Comments</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Comment Owner</th>
                            <th>Comment Content</th>
                            <th>Actions</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $comments = mysqli_query($con, "SELECT * FROM comments");

                        while ($comment = mysqli_fetch_assoc($comments)) {
                            echo "<tr>
                                    <td>{$comment['visitor_name']}</td>
                                    <td>{$comment['comment_content']}</td>
                                    
                                    <td>
                                        <a href='dashboard.php?deleteComment={$comment['commentID']}'><button class='delete-btn'>Delete</button></a>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>

           

            <section class="stats container" style="display: none;">
                <h2>Statistics</h2>
                <div class="stat-card">
                    <h3>Total Articles</h3>
                    <p>5</p>
                </div>
                <div class="stat-card">
                    <h3>Views</h3>
                    <p>120</p>
                </div>
                <div class="stat-card">
                    <h3>Comments</h3>
                    <p>25</p>
                </div>
            </section>
        </main>
    </div>
</body>
<script>
    function showPart(selector) {
        document.querySelectorAll('.container').forEach(part => {
            part.style.display = 'none';
        });
        const container = document.querySelector(selector);
        container.style.display = 'block';
    }
</script>

</html>
