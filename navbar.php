<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Navbar </title>

    <!-- css -->
    <link rel="stylesheet" href="CSS/Common/style.css">
    <link rel="stylesheet" href="CSS/navbar.css">
</head>

<body>
    <div class="nav-container container flex-column">
        <div class="nav-div div flex-row">
            <div class="flex-row logo-div pointer" onclick="window.location.href='index.php'">
                <img src="Assests/Images/rentrover-logo-rectangle.png" alt="Website Logo" class="website-logo">
            </div>

            <div class="flex-row operation-div">
                <div class="flex-row contact-div">
                    <img src="Assests/Icons/call.png" alt="">
                    <p class="p-form"> +977 9823645014 </p>
                </div>

                <div class="flex-row pointer wishlist-div" id="saved-icon">
                    <div class="left flex-column">
                        <img src="Assests/Icons/saved.png" alt="">
                    </div>

                    <div class="flex-column right">
                        <p class="p-form">
                            <?php echo ($wishlistCount <= 9) ? $wishlistCount : "9+"; ?>
                        </p>
                    </div>
                </div>

                <div class="flex-row login-div">
                    <button onclick="window.location.href='login.php'"> Login </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>