<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/home.css">
    <title>Home Page</title>
</head>

<body>
    <header>
        <nav class="navbar">
            <button class="sign-in-btn" onclick="showContainer('.sign-in-container')">Sign-in</button>
            <button class="sign-up-btn" onclick="showContainer('.sign-up-container')">Sign-up</button>
        </nav>
    </header>

    <main>
        <div class="top_article">
            <?php
            
            $top_article = mysqli_query($con, "SELECT ar.article_title , ar.article_content , ar.creation_date , st.views
                                               FROM articles ar
                                               JOIN statistiques st on ar.articleID = st.articleID
                                               ORDER BY st.views limit 1
                                               ;");

            while($top = mysqli_fetch_assoc($top_article)){
                echo " <h2>Most viewed article</h2>
            <div class='top-title'>{$top['article_title']}</div>
            <div class='top-author'>{$top['author_name']}</div>
            <div class='top-content'>
                <p>{$top['article_content']}</p>
            </div>";
            }           

            ?>
        </div>
        <div class="articles">
            <?php
            include("php/config.php");

            $articles = mysqli_query($con, "SELECT * FROM articles join authors on articles.authorID = authors.authorID");


            while ($result = mysqli_fetch_assoc($articles)) {
                echo "<div class='article'>
                <div class='poster'></div>
                <div class='title'> TITLE :{$result['article_title']}</div>
                <div class='author'> AUTHOR : {$result['author_name']}</div>
                <div class='content'> CONTENT : {$result['article_content']}</div>
                <a href='article.php?articleID={$result['articleID']}'>Read more</a>
            </div>";
            }

            ?>

            <!-- <div class="article">
                <div class="poster"></div>
                <div class="title"></div>
                <div class="author"></div>
                <div class="content"></div>
            </div> -->

        </div>
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

            if (is_array($result) && !empty($result) && isset($_SESSION['username'])) {
                $_SESSION['username'] = $result['author_name'];
                $_SESSION['id'] = $result['authorID'];
                header("location: dashboard.php");
            }
            // if (isset($_SESSION['username'])) {
            //     header("location: dashboard.php");
            // }
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

        <div class="modal-content">



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



            <span class="close-btn" onclick="closeContainer('.sign-up-container')">&times;</span>
            <h2>Sign-up</h2>
            <form action="" method="POST">
                <label>Username :</label>
                <input type="text" name="username" required>
                <label>Email :</label>
                <input type="email" name="email" required>
                <label>Password:</label>
                <input type="password" name="password" required>
                <button name="sign-up" type="submit">submit</button>
            </form>
        </div>
    </div>

</body>
<script>
    function showContainer(selector) {
        document.querySelectorAll('.form').forEach(item => {
            item.style.display = 'none'
        })
        const container = document.querySelector(selector);
        container.style.display = 'block';
    }

    function closeContainer(selector) {
        const container = document.querySelector(selector);
        container.style.display = 'none';
    }
</script>

</html>