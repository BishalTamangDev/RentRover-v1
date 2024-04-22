<?php
// session
if (!session_start())
    session_start();

if (!isset($_SESSION['adminId']))
    header("Location: login.php");

// including external files
include_once '../../class/user_class.php';
include_once '../../class/admin_class.php';
include_once '../../class/functions.php';

// creating objects
$admin = new Admin();
$user = new User();
$userObj = new User();

$admin->adminId = $_SESSION['adminId'];

$search = false;

if (isset($_GET['content'])) {
    $search = true;
    $content = $_GET['content'];
} else
    $search = false;

// on search
if (isset($_GET['$search-btn'])) {
    $content = $_GET['content'];
    header("Location: users.php?content='$content'");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> Users </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- linking external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="flex-row body-container">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="f-bold negative"> Users </p>
            </div>

            <!-- card -->
            <div class="card-container content-container flex-row">
                <!-- all users card -->
                <div class="flex-column pointer card" id="role-all-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $user->countUsers("all", "all"); ?>
                    </p>
                    <p class="p-form"> Total Users </p>
                </div>

                <!-- landlord card -->
                <div class="flex-column pointer card" id="role-landlord-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $user->countUsers("landlord", "all"); ?>
                    </p>
                    <p class="p-form"> Landlord </p>
                </div>

                <!-- tenant card -->
                <div class="flex-column pointer card" id="role-tenant-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $user->countUsers("tenant", "all"); ?>
                    </p>
                    <p class="p-form"> Tenant </p>
                </div>

                <!-- verified users card -->
                <div class="flex-column pointer card" id="account-state-verified-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $user->countUsers("all", "verified"); ?>
                    </p>
                    <p class="p-form"> Verified </p>
                </div>

                <!-- unverified users card -->
                <div class="flex-column pointer card" id="account-state-unverified-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $user->countUsers("all", "unverified"); ?>
                    </p>
                    <p class="p-form"> Unverified </p>
                </div>
            </div>

            <!-- filter & search -->
            <div class="container content-container flex-row filter-search-container">
                <div class="flex-row filter-div">
                    <div class="flex-row filter-icon-div ">
                        <img src="../../Assests/Icons/filter.png" alt="">
                    </div>

                    <!-- role select -->
                    <div class="flex-row filter-select-div user-role-div">
                        <label for="user-role"> Role </label>
                        <select name="user-role" id="user-role-select">
                            <option value="0"> All </option>
                            <option value="1"> Landlord </option>
                            <option value="2"> Tenant </option>
                        </select>
                    </div>

                    <div class="flex-row filter-select-div user-account-state-div">
                        <label for="user-role"> Account State </label>
                        <select name="account-state" id="account-state-select">
                            <option value="0"> All </option>
                            <option value="1"> Unverified </option>
                            <option value="2"> Verified </option>
                            <option value="3"> Suspended </option>
                        </select>
                    </div>

                    <div class="flex-row pointer clear-filter-div" id="clear-sort">
                        <p class="p-form"> Clear Sort </p>
                        <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                    </div>
                </div>

                <div class="flex-row search-div">
                    <?php
                    if ($search) {
                        ?>
                        <div class="clear-search-div flex-row pointer" onclick="window.location.href='users.php'">
                            <p class="p-form"> Clear Search </p>
                            <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                        </div>
                        <?php
                    }
                    ?>

                    <form action="" method="GET" class="flex-row search-form">
                        <input type="text" name="content" id="" placeholder="Search here..." value="<?php if ($search)
                            echo $content; ?>" required>
                        <button class="search-button" type="submit" name="search-btn"> <img
                                src="../../Assests/Icons/search-normal.svg" alt=""> </button>
                    </form>
                </div>
            </div>

            <!-- table section -->
            <div class="users-table-container table-container content-container flex-column">
                <!-- top section : heading, sor & search -->
                <div class="table-top-section flex-row">
                    <!-- sorting -->
                    <div class="table-name-clear-sort-div flex-row">
                        <!-- hidden -->
                        <div class="table-name flex-row">
                            <p class="p-normal f-bold" id="user-table-heading">
                                <?php echo (!$search) ? 'All Users' : 'Search Results '; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php $sets = ($search) ? $userObj->searchUser($_GET['content']) : $sets = $userObj->fetchAllUsers(); ?>
                <table class="users-table table-class" id="users-table">
                    <thead>
                        <th class="t-id first-td"> ID </th>
                        <th class="t-username first-td"> Name </th>
                        <th class="t-location"> Address </th>
                        <th class="t-email"> Email Address </th>
                        <th class="t-contact"> Contact </th>
                        <th class="t-role"> Role </th>
                        <th class="t-account-state"> Account State </th>
                    </thead>

                    <tbody>
                        <?php
                        if (sizeof($sets) > 0) {
                            foreach ($sets as $set) {
                                ?>
                                <tr onclick="window.location.href='user-detail.php?userId=<?php echo $set['user_id'] ?>'" class="element <?php echo ($set['role'] == 'Landlord') ? 'landlord-element' : 'tenant-element'; ?> <?php if ($set['account_state'] == 0)
                                                 echo 'unverified-element';
                                             else if ($set['account_state'] == 1)
                                                 echo 'verified-element';
                                             else
                                                 echo 'suspended-element';
                                             ?>">

                                    <td class="t-id first-td">
                                        <?php echo $set['user_id']; ?>
                                    </td>
                                    <td class="t-username first-td">
                                        <?php echo returnFormattedName($set['first_name'], $set['middle_name'], $set['last_name']); ?>
                                    </td>

                                    <td class="t-location">
                                        <?php echo returnFormattedAddress($set['province'], $set['district'], $set['area_name'], $set['ward']); ?>
                                    </td>

                                    <td class="t-email">
                                        <?php echo $set['email']; ?>
                                    </td>

                                    <td class="t-contact">
                                        <?php echo $set['contact']; ?>
                                    </td>

                                    <td class="t-role">
                                        <?php echo ucfirst($set['role']); ?>
                                    </td>

                                    <td class="t-account-state">
                                        <?php
                                        if ($set['account_state'] == 0)
                                            echo "Unverified";
                                        else if ($set['account_state'] == 1)
                                            echo "Verified";
                                        else
                                            echo "Suspended";
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="flex-column empty-data-div" id="empty-data-div">
                <img src="../../Assests/Icons/empty.png" alt="">
                <p class="p-normal negative"> Found none! </p>
            </div>
        </article>
    </div>

    <!-- script section -->
    <script>
        const activeMenu = $('#user-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>

    <!-- custom script section -->
    <script>
        $(document).ready(function () {
            const elements = $('.element');
            const landlordElements = $('.landlord-element');
            const tenantElements = $('.tenant-element');

            const verifiedElements = $('.verified-element');
            const unverifiedElements = $('.unverified-element');
            const suspendedElements = $('.suspended-element');

            const clearSortDiv = $('#clear-sort');

            // values
            role = 0;
            account = 0;

            clearSortDiv.hide();

            // individual sorting task
            $('#role-all-filter-card').click(function () {
                role = 0;
                $('#user-role-select')[0].value = role;
                filterElement();
            });

            $('#role-landlord-filter-card').click(function () {
                role = 1;
                $('#user-role-select')[0].value = role;
                filterElement();
            });

            $('#role-tenant-filter-card').click(function () {
                role = 2;
                $('#user-role-select')[0].value = role;
                filterElement();
            });

            // account state
            $('#account-state-unverified-filter-card').click(function () {
                account = 1;
                $('#account-state-select')[0].value = account;
                filterElement();
            });

            $('#account-state-verified-filter-card').click(function () {
                account = 2;
                $('#account-state-select')[0].value = account;
                filterElement();
            });


            // role select
            $('#user-role-select').change(function () {
                role = $('#user-role-select')[0].value;
                filterElement();
            });

            // account state select
            $('#account-state-select').change(function () {
                account = $('#account-state-select')[0].value
                filterElement();
            });

            filterElement = () => {
                elements.hide();

                var elementCount = 0;

                // role
                if (role == 0) {
                    elements.show();
                } else if (role == 1) {
                    landlordElements.show()
                } else if (role == 2) {
                    tenantElements.show();
                }

                // account state
                if (account == 1) {
                    verifiedElements.hide();
                    suspendedElements.hide();
                } else if (account == 2) {
                    unverifiedElements.hide();
                    suspendedElements.hide();
                } else if (account == 3) {
                    verifiedElements.hide();
                    unverifiedElements.hide();
                }

                elementCount = countVisibleRows();
                if (elementCount == 0) {
                    $("#empty-data-div").show();
                } else {
                    $("#empty-data-div").hide();
                }

                $('#clear-sort').show();

                if (role == 0 && account == 0)
                    $('#clear-sort').hide();
            }

            filterElement();

            function countVisibleRows() {
                var visibleRows = $("#users-table tbody tr:visible");
                var visibleRowCount = visibleRows.length;
                return visibleRowCount;
            }

            $('#clear-sort').click(function () {
                role = 0;
                account = 0;
                filterElement();
                $('#user-role-select')[0].value = role;
                $('#account-state-select')[0].value = account;
                $('#clear-sort').hide();
            });
        });
    </script>
</body>

</html