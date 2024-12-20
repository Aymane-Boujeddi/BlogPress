<?php
session_start();
ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/homepage.css">
    <title>Home Page</title>
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
        <section class="top-article-section">
            <?php
            include("php/config.php");
            $top_article = mysqli_query($con, "SELECT ar.article_title , ar.article_content , ar.creation_date , st.views , aut.author_name
                                               FROM articles ar
                                               JOIN statistiques st on ar.articleID = st.articleID
                                               JOIN authors aut on aut.authorID = ar.authorID
                                               ORDER BY st.views limit 1
                                               ;");

            while ($top = mysqli_fetch_assoc($top_article)) {
                echo "<h2 class='section-title'>Most Viewed Article</h2>
            <div class='top-article'>
                <h3 class='top-title'>{$top['article_title']}</h3>
                <p class='top-author'>By <strong>{$top['author_name']}</strong></p>
                <p class='top-content'>{$top['article_content']}</p>
            </div>";
            }

            ?>
            <!-- <h2 class="section-title">Most Viewed Article</h2>
            <div class="top-article">
                <h3 class="top-title">The Best IT Tech</h3>
                <p class="top-author">By <strong>Helg Dopler</strong></p>
                <p class="top-content">Lorem ipsum dolor sit amet consectetur adipisicing elit...</p>
            </div> -->
        </section>

        <section class="articles-section">
            <h2 class="section-title">All Articles</h2>
            <div class="articles-container">
                <?php
                include("php/config.php");

                $articles = mysqli_query($con, "SELECT * FROM articles 
                                               JOIN authors ON articles.authorID = authors.authorID
                                            

                ");
                while ($result = mysqli_fetch_assoc($articles)) {
                    echo "<div class='article'>
                        <h3 class='article-title'>" . htmlspecialchars($result['article_title']) . "</h3>
                        <p class='article-author'>By <strong>" . htmlspecialchars($result['author_name']) . "</strong></p>
                        <p class='article-content'>" . htmlspecialchars(substr($result['article_content'], 0, 100)) . "...</p>
                        <a class='read-more' href='article.php?articleID=" . $result['articleID'] . "'>Read more</a>
                    </div>";
                }
                ?>
            </div>
        </section>
    </main>

    <div class="sign-in-container form" style="display: none;">
        <?php
        include("php/config.php");
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }
        if (isset($_POST['sign-in'])) {
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $password = mysqli_real_escape_string($con, $_POST['password']);

            $check = mysqli_query($con, "SELECT * FROM authors WHERE author_name = '$username' AND password='$password'");
            $result = mysqli_fetch_assoc($check);

            // if (is_array($result) && !empty($result) && isset($_SESSION['username'])) {
            //     $_SESSION['username'] = $result['author_name'];
            //     $_SESSION['id'] = $result['authorID'];
            //     header("location: dashboard.php");
            // }
            if (is_array($result) && !empty($result)) {
                $_SESSION['username'] = $result['author_name'];
                $_SESSION['id'] = $result['authorID'];
            }
            if (isset($_SESSION['username'])) {
                header("location: dashboard.php");
            }
        }
        ?>

        <div class="modal-content">
            <span class="close-btn" onclick="closeContainer('.sign-in-container')">&times;</span>
            <h2>Sign-in</h2>
            <form action="" method="POST">
                <label>Username:</label>
                <input type="text" name="username" required>
                <label>Password:</label>
                <input type="password" name="password" required>
                <button type="submit" name="sign-in">Log in</button>
            </form>
        </div>
    </div>

    <div class="sign-up-container form" style="display: none;">
        <?php
        include("php/config.php");

        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if (isset($_POST['sign-up'])) {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $query = "INSERT INTO authors (author_name, email, password) VALUES ('$username', '$email', '$password')";

            if (mysqli_query($con, $query)) {
                echo "Account created successfully!";
            } else {
                echo "Error: " . mysqli_error($con);
            }
        }
        ?>

        <div class="modal-content">
            <span class="close-btn" onclick="closeContainer('.sign-up-container')">&times;</span>
            <h2>Sign-up</h2>
            <form action="" method="POST">
                <label>Username :</label>
                <input type="text" name="username" required>
                <label>Email :</label>
                <input type="email" name="email" required>
                <label>Password:</label>
                <input type="password" name="password" required>
                <button name="sign-up" type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        function showContainer(selector) {
            document.querySelectorAll('.form').forEach(item => {
                item.style.display = 'none';
            });
            const container = document.querySelector(selector);
            container.style.display = 'block';
        }

        function closeContainer(selector) {
            const container = document.querySelector(selector);
            container.style.display = 'none';
        }
    </script>
</body>

</html>