<div class="nav-container container">
    <div class="nav flex-row div">
        <div class="logo-div pointer" onclick="window.location.href='home.php'">
            <img src="../../Assests/Images/rentrover-logo-rectangle.png" alt="Website Logo" class="website-logo">
        </div>

        <div class="flex-row operation-div">
            <!-- wishlist -->
            <div class="flex-row wishlist-div pointer" onclick="window.location.href='wishlist.php'">
                <div class="right flex-column">
                    <div class="top">
                        <img src="../../Assests/Icons/saved.png" alt="">
                    </div>

                    <div class="bottom flex-row">
                        <p class="p-form">
                            <?php echo ($wishlistCount < 10) ? $wishlistCount : '9<sup>+</sup>'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- notification -->
            <div class="flex-row wishlist-div pointer" onclick="toggleNotificationMenu()">
                <div class="right flex-column">
                    <div class="top">
                        <img src="../../Assests/Icons/Notification-black-filled.png" alt="">
                    </div>

                    <div class="bottom flex-row">
                        <p class="p-form">
                            <?php echo $notificationCount < 9 ? $notificationCount : '9<sup>+</sup>'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- profile -->
            <div class="flex-row profile-div right pointer" onclick="toggleUserMenu()">
                <div class="left">
                    <img src="../../Assests/uploads/user/<?php echo $user->userPhoto; ?>" alt="error" id="profile-pic">
                </div>

                <div class="right flex-row">
                    <p class="p-small f-bold">
                        <?php echo returnFormattedString($user->firstName); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- user menu  -->
<div class="user-menu-container container" id="user-menu-container">
    <div class="user-menu-div div flex-row">
        <div class="menu-container flex-column" id="menu-container">
            <!-- my profile -->
            <div class="section flex-row pointer" onclick="window.location.href='account.php?task=view-profile'">
                <div class="left">
                    <img src="../../Assests/Icons/user.png" alt="">
                </div>

                <div class="right">
                    <p class="p-normal"> My Profile </p>
                </div>
            </div>

            <!-- my room -->
            <div class="section flex-row pointer" onclick="window.location.href='account.php?task=my-room'">
                <div class="left">
                    <img src="../../Assests/Icons/room.png" alt="">
                </div>

                <div class="right">
                    <p class="p-normal"> My Room </p>
                </div>
            </div>

            <div class="section flex-row pointer" onclick="window.location.href='wishlist.php'">
                <div class="left">
                    <img src="../../Assests/Icons/Wishlist_out.png" alt="">
                </div>

                <div class="right">
                    <p class="p-normal"> Wishlist </p>
                </div>
            </div>

            <div class="section flex-row pointer" onclick="window.location.href='system-announcement.php'">
                <div class="left">
                    <img src="../../Assests/Icons/announcement.png" alt="">
                </div>

                <div class="right">
                    <p class="p-normal"> System Announcement </p>
                </div>
            </div>

            <hr>

            <div class="section flex-row pointer" onclick="window.location.href='logout.php'">
                <div class="left">
                    <img src="../../Assests/Icons/logout.svg" alt="">
                </div>

                <div class="right">
                    <p class="p-normal"> Log Out </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- notification -->
<div class="notification-menu-container" id="notification-container">
    <div class="notification-menu-div flex-row">
        <div class="notification-container flex-column shadow" id="notification-container">
            <div class="flex-column notification-top-section">
                <div class="top flex-row">
                    <p class="p-large f-bold"> Notification </p>
                    <p class="p-normal pointer info" onclick="window.location.href='notification.php'">
                        See all</p>
                </div>

                <div class="flex-row bottom">
                    <div class="pointer card" id="all-notification-trigger">
                        <p class="p-form"> All </p>
                    </div>

                    <div class="pointer card" id="unseen-notification-trigger">
                        <p class="p-form"> Unseen </p>
                    </div>

                    <div class="pointer card" id="seen-notification-trigger">
                        <p class="p-form"> Seen </p>
                    </div>
                </div>
            </div>

            <hr style="background-color:lightgray;">

            <?php
            $notificationSets = $notification->fetchNotification("tenant", $_SESSION['tenantUserId']);

            foreach ($notificationSets as $notificationSet) {
                if ($notificationSet['type'] == 'user-registration') { // user registration
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='account.php?task=view-profile'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/notification_icon_user_registration.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> You joined RentRover. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'user-verify') { // user verify
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='account.php?task=view-profile'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/verified.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> Your accound has been verified. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'user-suspend') { // user suspend
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='account.php?task=view-profile'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/report.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> You accound has been suspended. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'custom-room-notification') { // custom room
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    $link = "room-details.php?roomId=$roomId";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/setting.svg" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> A room you custom searched has been found. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'room-application-accept') { // room application accept
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    $link = "room-details.php?roomId=$roomId";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/application-accepted.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> Your application for the room has been accepted. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                    // room application reject
                } elseif ($notificationSet['type'] == 'room-application-reject') { // room aplication reject
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    $link = "room-details.php?roomId=$roomId";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/application-rejected.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> Your application for the room has been rejected. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'room-application-make-tenant') { // room application > make tenant
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    // modify -> my account.php
                    $link = "account.php?task=my-room";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/make-tenant.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> You have been registered to the a new room. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'room-leave-application-accept') {  // room leave applciation accept 
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    // modify -> my account.php
                    $link = "account.php?task=my-room";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/make-tenant.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> The room leaving application has been accepted. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'tenancy-end') { // tenancy end
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    // modify -> my account.php
                    $link = "room-details.php?roomId=$roomId";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/leave-room.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> You have been unregistered from the room. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'tenant-voice-solved') { // tenant voice solved
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    // modify -> my account.php
                    $link = "account.php?task=my-voice";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/solved.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> Your issue has been solved. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'tenant-voice-response') {  // tenant voice response
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    // modify -> my account.php
                    $link = "account.php?task=my-voice";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/speaking.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> The landlord replied to your issue. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } elseif ($notificationSet['type'] == 'announcement') { // announcement
                    ?>
                    <?php
                    $roomId = $notificationSet['room_id'];
                    // modify -> my account.php
                    $link = "account.php?task=announcement";
                    ?>
                    <div class="pointer flex-row section read-notification notification-element <?php echo ($notificationSet['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <div class="left flex-row">
                            <div class="dot seen-dot unseen-dot"> </div>
                            <div class="icon-box">
                                <img src="../../Assests/Icons/announcement.png" alt="icon">
                            </div>
                        </div>

                        <div class="right flex-column">
                            <p class="p-normal"> The landlord has an announcement. </p>
                            <p class="p-small n-light">
                                <?php echo $notificationSet['date_time']; ?>
                            </p>
                        </div>
                    </div>
                    <?php
                } else { // other
                    echo $notificationSet['type'] . "<br>";
                }
            }
            ?>

            <div class="flex-column empty-data-div <?php if (sizeof($notificationSets) > 0)
                echo "hidden"; ?>" id="empty-notification-div">
                <img src="../../Assests/Icons/empty.png" alt="">
                <p class="p-normal negative" id="empty-notification-msg"> Notification is empty! </p>
            </div>

            <!-- dummy notification -->
            <div class="pointer flex-row section read-notification hidden" onclick="window.location.href=''">
                <div class="left flex-row">
                    <div class="dot seen-dot unseen-dot"> </div>
                    <div class="icon-box">
                        <img src="../../Assests/Icons/blank.jpg" alt="icon">
                    </div>
                </div>

                <div class="right flex-column">
                    <p class="p-normal"> Notification detail 1</p>
                    <p class="p-small n-light"> Date </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jquery -->
<script src="../../Js/jquery-3.7.1.min.js"> </script>

<!-- js section -->
<script>
    logout = () => {
        $('#logout-dialog-container').show();
    }
</script>

<script>
    var notificationTrigger = 1;

    $('#all-notification-trigger').css('background-color', 'lightgray');

    $('#all-notification-trigger').click(function () {
        $('.seen-notification').show();
        $('.unseen-notification').show();
        notificationTrigger = 1;
        toggleNotification();
    });

    $('#unseen-notification-trigger').click(function () {
        $('.seen-notification').hide();
        $('.unseen-notification').show();
        notificationTrigger = 2;
        toggleNotification();
    });

    $('#seen-notification-trigger').click(function () {
        $('.seen-notification').show();
        $('.unseen-notification').hide();
        notificationTrigger = 3;
        toggleNotification();
    });

    toggleNotification = () => {
        var notifictionElements = $('.notification-element:visible');
        if (notifictionElements.length == 0)
            $('#empty-notification-div').show();
        else
            $('#empty-notification-div').hide();

        if (notificationTrigger == 1) {
            $('#all-notification-trigger').css('background-color', 'lightgray');
            $('#unseen-notification-trigger').css('background-color', 'unset');
            $('#seen-notification-trigger').css('background-color', 'unset');
            $('#empty-notification-msg')[0].innerHTML = "Notification is empty";
        } else if (notificationTrigger == 2) {
            $('#all-notification-trigger').css('background-color', 'unset');
            $('#unseen-notification-trigger').css('background-color', 'lightgray');
            $('#seen-notification-trigger').css('background-color', 'unset');
            $('#empty-notification-msg')[0].innerHTML = "No unseen notification!";
        } else {
            $('#all-notification-trigger').css('background-color', 'unset');
            $('#unseen-notification-trigger').css('background-color', 'unset');
            $('#seen-notification-trigger').css('background-color', 'lightgray');
            $('#empty-notification-msg')[0].innerHTML = "No seen notification!";
        }
    }
</script>