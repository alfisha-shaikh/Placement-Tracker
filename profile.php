<?php
session_start();
require_once 'connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = $1";
$result = pg_query_params($conn, $query, array($user_id));

$selected_count = pg_fetch_result(pg_query_params(
    $conn,
    "SELECT COUNT(*) FROM applications WHERE user_id = $1 AND status='Selected'",
    array($user_id)
), 0, 0);

// Applications
$app_count = pg_fetch_result(pg_query_params(
    $conn,
    "SELECT COUNT(*) FROM applications WHERE user_id = $1",
    array($user_id)
), 0, 0);

$user = pg_fetch_assoc($result);

$success_rate = $app_count > 0 ? round(($selected_count / $app_count) * 100) : 0;

$circle_length = 364.4;
$offset = $circle_length - ($circle_length * $success_rate / 100);



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // PROFILE UPDATE
    if ($_POST['action'] == 'update_profile') {

        $name = $_POST['name'];
        $email = $_POST['email'];
        $university = $_POST['university'];

        $update_query = "UPDATE users 
                         SET name=$1, email=$2, university=$3 
                         WHERE id=$4";

        $update_result = pg_query_params($conn, $update_query, array(
            $name,
            $email,
            $university,
            $user_id
        ));

        if ($update_result) {
            header("Location: profile.php?updated=1");
            exit();
        } else {
            echo "Update failed";
        }
    }

    // PASSWORD UPDATE
    if ($_POST['action'] == 'change_password') {

        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($current_password !== $user['password']) {
            echo "<p style='color:red;'>Wrong current password</p>";
        } elseif ($new_password !== $confirm_password) {
            echo "<p style='color:red;'>Passwords do not match</p>";
        } else {

            $update_pass_query = "UPDATE users SET password=$1 WHERE id=$2";

            $result = pg_query_params($conn, $update_pass_query, array(
                $new_password,
                $user_id
            ));

            if ($result) {
                header("Location: profile.php?password_updated=1");
                exit();
            } else {
                echo "<p style='color:red;'>Password update failed</p>";
            }
        }
    }
}

?>


<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Profile Update - Placement Tracker</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "error": "#ba1a1a",
                        "on-tertiary-fixed": "#001d32",
                        "on-error": "#ffffff",
                        "tertiary-container": "#00446b",
                        "on-secondary-container": "#006f66",
                        "on-tertiary": "#ffffff",
                        "surface-container-high": "#e6e8ea",
                        "secondary": "#006a61",
                        "primary": "#00236f",
                        "secondary-fixed-dim": "#6bd8cb",
                        "tertiary-fixed": "#cde5ff",
                        "on-error-container": "#93000a",
                        "surface-bright": "#f7f9fb",
                        "inverse-on-surface": "#eff1f3",
                        "on-primary": "#ffffff",
                        "secondary-fixed": "#89f5e7",
                        "on-surface": "#191c1e",
                        "surface-container-low": "#f2f4f6",
                        "primary-fixed-dim": "#b6c4ff",
                        "tertiary-fixed-dim": "#94ccff",
                        "background": "#f7f9fb",
                        "on-primary-container": "#90a8ff",
                        "inverse-primary": "#b6c4ff",
                        "surface-container": "#eceef0",
                        "surface-container-highest": "#e0e3e5",
                        "outline": "#757682",
                        "on-secondary": "#ffffff",
                        "surface": "#f7f9fb",
                        "error-container": "#ffdad6",
                        "primary-container": "#1e3a8a",
                        "surface-dim": "#d8dadc",
                        "on-tertiary-container": "#6ab3ef",
                        "tertiary": "#002d49",
                        "secondary-container": "#86f2e4",
                        "outline-variant": "#c5c5d3",
                        "primary-fixed": "#dce1ff",
                        "on-secondary-fixed": "#00201d",
                        "surface-variant": "#e0e3e5",
                        "on-primary-fixed": "#00164e",
                        "on-background": "#191c1e",
                        "surface-tint": "#4059aa",
                        "surface-container-lowest": "#ffffff",
                        "on-secondary-fixed-variant": "#005049",
                        "on-tertiary-fixed-variant": "#004b74",
                        "on-primary-fixed-variant": "#264191",
                        "inverse-surface": "#2d3133",
                        "on-surface-variant": "#444651"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        .headline {
            font-family: 'Manrope', sans-serif;
        }
    </style>
</head>

<body class="bg-surface text-on-surface">
    <!-- SideNavBar -->
    <aside class="flex flex-col h-full py-6 px-4 h-screen w-64 border-r-0 fixed left-0 top-0 bg-slate-50 dark:bg-slate-900 z-50">
        <div class="mb-10 px-2">
            <h1 class="text-xl font-bold text-blue-900 tracking-tighter">Placement Tracker</h1>
            <p class="text-xs text-slate-500 font-medium">The Curated Architect</p>
        </div>
        <nav class="flex-1 space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200 group" href="dashboard.php">
                <span class="material-symbols-outlined text-lg" data-icon="dashboard">dashboard</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200 group" href="application.php">
                <span class="material-symbols-outlined text-lg" data-icon="work">work</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Applications</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200 group" href="interview.php">
                <span class="material-symbols-outlined text-lg" data-icon="event">event</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Interviews</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-blue-700 dark:text-teal-400 font-bold border-r-4 border-blue-700 dark:border-teal-400 bg-white dark:bg-slate-800 transition-all" href="profile.php">
                <span class="material-symbols-outlined text-lg" data-icon="person">person</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Profile</span>
            </a>
        </nav>
        <div class="mt-auto pt-6 border-t border-slate-200 dark:border-slate-800">
            <button onclick="window.location.href='add-application.php'" class="w-full bg-gradient-to-br from-primary to-primary-container text-white py-3 rounded-full font-headline text-sm font-semibold shadow-lg shadow-primary/10 flex items-center justify-center gap-2 hover:opacity-90 transition-all active:scale-95">
                <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
                Add Application
            </button>
        </div>
    </aside>
    <!-- Main Content Area -->
    <div class="ml-64 flex flex-col min-h-screen">
        <!-- TopNavBar -->
        <header class="w-full sticky top-0 z-40 bg-[#f7f9fb] flex justify-between items-center px-8 h-16 transition-colors">
            <div class="flex items-center space-x-4">
                <h1 class="text-xl font-bold text-[#00236f] font-['Manrope'] tracking-tight">Placement Tracker</h1>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-slate-400 hover:text-blue-700 cursor-pointer transition-all">notifications</span>
                    <span class="material-symbols-outlined text-slate-400 hover:text-blue-700 cursor-pointer transition-all">settings</span>
                </div>
                <div class="h-6 w-px bg-slate-100"></div>
                <button onclick="window.location.href='logout.php'" class="text-sm font-manrope font-semibold text-blue-900 hover:text-blue-700 transition-all cursor-pointer active:scale-95">Logout</button>

            </div>
        </header>
        <!-- Page Content -->
        <main class="p-8 max-w-7xl mx-auto w-full">

            <?php if (isset($_GET['updated'])): ?>
                <p class="text-green-600 font-semibold mb-4">
                    Profile updated successfully!
                </p>
            <?php endif; ?>
            <?php if (isset($_GET['password_updated'])): ?>
                <p class="text-green-600 font-semibold mb-4">
                    Password updated successfully!
                </p>
            <?php endif; ?>

            <div class="grid grid-cols-12 gap-8">
                <!-- Central Form Column -->
                <div class="col-span-12 lg:col-span-8 space-y-8">
                    <!-- Profile Information Card -->
                    <section class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-primary font-headline tracking-tight">Profile Information</h2>
                            <p class="text-on-surface-variant text-sm mt-1">Update your personal details and academic credentials.</p>
                        </div>
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider block">Full Name</label>
                                    <input class="w-full bg-surface-container-highest border-none border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all rounded-t-lg px-4 py-3 font-medium text-on-surface" placeholder="Enter your full name" name="name" type="text" value="<?= $user['name'] ?? '' ?>" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider block">Email Address</label>
                                    <input class="w-full bg-surface-container-highest border-none border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all rounded-t-lg px-4 py-3 font-medium text-on-surface" placeholder="email@example.com" name="email" type="email" value="<?= $user['email'] ?>" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider block">University/College Name</label>
                                <input class="w-full bg-surface-container-highest border-none border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all rounded-t-lg px-4 py-3 font-medium text-on-surface" placeholder="Your institution name" name="university" type="text" value="<?= $user['university'] ?>" />
                            </div>
                            <div class="pt-6 flex flex-wrap gap-4">
                                <button class="px-8 py-3 bg-gradient-to-br from-primary to-primary-container text-white font-semibold rounded-full shadow-lg hover:shadow-primary/20 active:scale-95 transition-all duration-200 text-sm" type="submit">
                                    Update Profile
                                </button>
                                <button class="px-8 py-3 bg-surface-container-high text-on-primary-fixed-variant font-semibold rounded-full hover:bg-surface-container-highest active:scale-95 transition-all duration-200 text-sm" type="button">
                                    Reset Changes
                                </button>
                            </div>
                        </form>
                    </section>
                    <!-- Security Section Card -->
                    <section class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                        <div class="flex items-center space-x-3 mb-8">
                            <span class="material-symbols-outlined text-primary" data-icon="lock">lock</span>
                            <h2 class="text-2xl font-bold text-primary font-headline tracking-tight">Security</h2>
                        </div>
                        <form class="space-y-6" method="POST">
                            <input type="hidden" name="action" value="change_password">
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider block">Current Password</label>
                                <input name="current_password" class="w-full bg-surface-container-highest border-none border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all rounded-t-lg px-4 py-3 font-medium text-on-surface" placeholder="••••••••••••" type="password" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider block">New Password</label>
                                    <input name="new_password" class="w-full bg-surface-container-highest border-none border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all rounded-t-lg px-4 py-3 font-medium text-on-surface" placeholder="Enter new password" type="password" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider block">Confirm Password</label>
                                    <input name="confirm_password" class="w-full bg-surface-container-highest border-none border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all rounded-t-lg px-4 py-3 font-medium text-on-surface" placeholder="Repeat new password" type="password" />
                                </div>
                            </div>
                            <div class="pt-4">
                                <button class="text-primary font-bold hover:text-primary-container underline-offset-4 hover:underline transition-all text-sm" type="submit">
                                    Change Password
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
                <!-- Sidebar Content Column -->
                <div class="col-span-12 lg:col-span-4 space-y-8">
                    <!-- Profile Strength Sidebar Card -->
                    <aside class="bg-surface-container-low rounded-xl p-6 space-y-6">
                        <h3 class="text-lg font-bold text-primary font-headline tracking-tight">Profile Strength</h3>
                        <!-- Progress Ring Implementation -->
                        <div class="flex flex-col items-center justify-center py-4">
                            <div class="relative w-32 h-32">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle class="text-surface-container-highest" cx="64" cy="64" fill="transparent" r="58" stroke="currentColor" stroke-width="8"></circle>
                                    <circle class="text-secondary" cx="64" cy="64" fill="transparent" r="58" stroke="currentColor" stroke-dasharray="364.4" stroke-dashoffset="<?= $offset ?>" stroke-linecap="round" stroke-width="8"></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-2xl font-extrabold text-on-surface font-headline"><?= $success_rate ?>%</span>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-secondary mt-4">Almost Complete</p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-surface-container-lowest rounded-lg shadow-sm">
                                <div class="flex items-center space-x-3">
                                    <span class="material-symbols-outlined text-green-600 text-sm" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span class="text-xs font-medium">Academic Info</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant opacity-50">Verified</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-surface-container-lowest rounded-lg shadow-sm">
                                <div class="flex items-center space-x-3">
                                    <span class="material-symbols-outlined text-green-600 text-sm" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span class="text-xs font-medium">Personal Details</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-on-surface-variant opacity-50">Verified</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-surface-container-lowest rounded-lg shadow-sm border-2 border-secondary/20">
                                <div class="flex items-center space-x-3">
                                    <span class="material-symbols-outlined text-outline text-sm" data-icon="pending">pending</span>
                                    <span class="text-xs font-medium">Security Scan</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-secondary">In Action</span>
                            </div>
                        </div>
                    </aside>
                    <!-- Account Overview Sidebar Card -->
                    <section class="bg-surface-container-lowest rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-primary font-headline tracking-tight mb-4">Account Overview</h3>
                        <div class="space-y-4">
                            <div class="flex flex-col">
                                <span class="text-xs text-on-surface-variant uppercase tracking-wider font-semibold">User Role</span>
                                <span class="text-sm font-bold text-on-surface">Senior Placement Director</span>
                            </div>

                            <div class="flex flex-col">
                                <span class="text-xs text-on-surface-variant uppercase tracking-wider font-semibold">Security Level</span>
                                <div class="flex items-center mt-1 space-x-2">
                                    <div class="flex space-x-1">
                                        <div class="h-1.5 w-6 rounded-full bg-secondary"></div>
                                        <div class="h-1.5 w-6 rounded-full bg-secondary"></div>
                                        <div class="h-1.5 w-6 rounded-full bg-secondary"></div>
                                        <div class="h-1.5 w-6 rounded-full bg-surface-container-highest"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-on-secondary-container">Level 3</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 pt-6 border-t border-outline-variant/10">
                            <button class="w-full py-2.5 text-xs font-bold text-error bg-error-container/20 rounded-lg hover:bg-error-container/40 transition-colors">
                                Deactivate Account
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>

</html>