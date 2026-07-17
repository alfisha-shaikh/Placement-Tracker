<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch interviews
// Upcoming (nearest first)
$upcoming_result = pg_query_params($conn, "
    SELECT * FROM interviews 
    WHERE user_id = $1 AND interview_date >= NOW()
    ORDER BY interview_date ASC
", [$user_id]);

// Past (latest first)
$past_result = pg_query_params($conn, "
    SELECT * FROM interviews 
    WHERE user_id = $1 AND interview_date < NOW()
    ORDER BY interview_date DESC
", [$user_id]);



// success probability
$selected = pg_fetch_result(pg_query_params(
    $conn,
    "SELECT COUNT(*) FROM applications WHERE user_id=$1 AND status='Selected'",
    [$user_id]
), 0, 0);

$total = pg_fetch_result(pg_query_params(
    $conn,
    "SELECT COUNT(*) FROM applications WHERE user_id=$1",
    [$user_id]
), 0, 0);

$success_rate = $total > 0 ? round(($selected / $total) * 100) : 0;
?>



<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Interview Tracker - Architect CRM</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Manrope:wght@600;700;800&amp;display=swap" rel="stylesheet" />
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary-fixed": "#001d32",
                        "on-tertiary-fixed-variant": "#004b74",
                        "primary": "#00236f",
                        "background": "#f7f9fb",
                        "tertiary-fixed-dim": "#94ccff",
                        "on-secondary-fixed-variant": "#005049",
                        "on-error-container": "#93000a",
                        "surface-dim": "#d8dadc",
                        "primary-fixed": "#dce1ff",
                        "on-background": "#191c1e",
                        "surface-variant": "#e0e3e5",
                        "on-primary-fixed-variant": "#264191",
                        "on-surface": "#191c1e",
                        "primary-container": "#1e3a8a",
                        "inverse-surface": "#2d3133",
                        "surface-container-high": "#e6e8ea",
                        "on-secondary-fixed": "#00201d",
                        "tertiary-fixed": "#cde5ff",
                        "surface-container": "#eceef0",
                        "tertiary-container": "#00446b",
                        "surface-tint": "#4059aa",
                        "on-tertiary-container": "#6ab3ef",
                        "surface-bright": "#f7f9fb",
                        "on-tertiary": "#ffffff",
                        "on-primary": "#ffffff",
                        "on-surface-variant": "#444651",
                        "outline-variant": "#c5c5d3",
                        "surface-container-highest": "#e0e3e5",
                        "secondary-fixed-dim": "#6bd8cb",
                        "surface-container-lowest": "#ffffff",
                        "inverse-on-surface": "#eff1f3",
                        "on-primary-fixed": "#00164e",
                        "tertiary": "#002d49",
                        "secondary-container": "#86f2e4",
                        "on-secondary": "#ffffff",
                        "surface": "#f7f9fb",
                        "inverse-primary": "#b6c4ff",
                        "on-primary-container": "#90a8ff",
                        "primary-fixed-dim": "#b6c4ff",
                        "outline": "#757682",
                        "secondary": "#006a61",
                        "error": "#ba1a1a",
                        "secondary-fixed": "#89f5e7",
                        "surface-container-low": "#f2f4f6",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6",
                        "on-secondary-container": "#006f66"
                    },
                    fontFamily: {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #00236f 0%, #1e3a8a 100%);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e0e3e5;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-surface font-body text-on-surface">
    <!-- SideNavBar (Shared Component) -->
    <aside class="h-screen w-64 border-r-0 fixed left-0 top-0 bg-slate-50 flex flex-col py-6 px-4 z-50">
        <div class="mb-10 px-2">
            <h1 class="text-xl font-bold text-blue-900 tracking-tighter">Placement Tracker</h1>
            <p class="text-xs text-slate-500 font-medium">The Curated Architect</p>
        </div>
        <nav class="flex-1 space-y-2">
            <!-- Dashboard -->
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-500 hover:bg-slate-200/50 hover:text-blue-900 group" href="dashboard.php">
                <span class=" material-symbols-outlined text-lg">dashboard</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Dashboard</span>
            </a>
            <!-- Applications -->
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-500 hover:bg-slate-200/50 hover:text-blue-900 group" href="application.php">
                <span class="material-symbols-outlined text-lg">work</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Applications</span>
            </a>
            <!-- Interviews (ACTIVE) -->
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-blue-700 font-bold border-r-4 border-blue-700 bg-white" href="interview.php">
                <span class="material-symbols-outlined text-lg">event</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Interviews</span>
            </a>
            <!-- Profile -->
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-500 hover:bg-slate-200/50 hover:text-blue-900 group" href="profile.php">
                <span class="material-symbols-outlined text-lg">person</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Profile</span>
            </a>
        </nav>

        <div class="mt-auto px-2">
            <button onclick="window.location.href='add-interview.php'"
                class="w-full btn-gradient text-white py-3 px-4 rounded-full text-sm font-semibold flex items-center justify-center gap-2 active:scale-95 transition-all shadow-lg shadow-blue-900/10">
                <span class="material-symbols-outlined text-base">add</span>
                New Interview
            </button>
        </div>
    </aside>
    <!-- TopNavBar (Shared Component) -->
    <header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 flex justify-between items-center h-16 px-8 ml-64">
        <div class="flex items-center bg-surface-container-low px-4 py-2 rounded-full w-96">
            <span class="material-symbols-outlined text-slate-400 text-lg mr-2">search</span>
            <input class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder-slate-400 text-on-surface" placeholder="Search scheduled interviews..." type="text" />
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
    <!-- Main Content Canvas -->
    <main class="ml-64 pt-24 pb-12 px-12 min-h-screen">
        <!-- Header Section -->
        <div class="flex justify-between items-end mb-10">
            <div class="space-y-1">
                <h2 class="text-3xl font-extrabold font-headline tracking-tight text-on-surface">Interview Tracker</h2>
                <p class="text-on-surface-variant font-label text-sm">Managing your trajectory toward the next architectural breakthrough.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center bg-surface-container-high p-1 rounded-lg">
                    <button class="px-4 py-1.5 rounded-md bg-surface-container-lowest text-xs font-semibold text-primary shadow-sm">Upcoming</button>
                    <button class="px-4 py-1.5 rounded-md text-xs font-medium text-on-surface-variant hover:text-on-surface">History</button>
                </div>
            </div>
        </div>
        <!-- Asymmetric Layout: Calendar & Feedback Entry -->
        <div class="grid grid-cols-12 gap-8 items-start">
            <!-- Main Feed (List/Calendar View) -->
            <div class="col-span-8 space-y-6">
                <!-- Date Header -->
                <div class="flex items-center gap-4">
                    <div class="h-px flex-1 bg-outline-variant opacity-20"></div>
                    <p class="text-xs text-gray-500">
                        <?= date("l, F d") ?>
                    </p>
                    <div class="h-px flex-1 bg-outline-variant opacity-20"></div>
                </div>
                <!-- Interview Item 1 -->
                <?php while ($row = pg_fetch_assoc($upcoming_result)):
                    $date = strtotime($row['interview_date']);
                    $month = date("M", $date);
                    $day = date("d", $date);
                    $time = date("h:i A", $date);
                ?>

                    <div class="bg-surface-container-lowest p-6 rounded-xl flex items-center justify-between hover:bg-surface-tint/[0.03] transition">

                        <div class="flex items-center gap-6">

                            <!-- Date Box -->
                            <div class="w-16 h-16 rounded-xl bg-surface-container-low flex flex-col items-center justify-center border-b-2 border-primary">
                                <p class="text-[10px] uppercase"><?= $month ?></p>
                                <p class="text-xl font-bold"><?= $day ?></p>
                            </div>

                            <!-- Details -->
                            <div>
                                <h3 class="font-bold text-lg text-on-surface"><?= $row['company_name'] ?></h3>
                                <p class="text-sm text-on-surface-variant"><?= $row['round_name'] ?></p>
                                <p class="text-xs text-on-surface-variant"><?= $time ?> • <?= $row['mode'] ?></p>
                            </div>

                        </div>

                        <!-- Button -->
                        <?php if ($row['mode'] == 'Online'): ?>
                            <button class="px-5 py-2 rounded-full bg-primary text-white hover:scale-95 transition">
                                Join
                            </button>
                        <?php else: ?>
                            <button class="px-5 py-2 rounded-full bg-white border hover:scale-95 transition">
                                View Details
                            </button>
                        <?php endif; ?>

                    </div>

                <?php endwhile; ?>


                <!-- Date Header -->
                <div class="flex items-center gap-4 pt-4">
                    <div class="h-px flex-1 bg-outline-variant opacity-20"></div>
                    <span class="text-xs font-bold text-outline uppercase tracking-widest font-label">Past Interviews</span>
                    <div class="h-px flex-1 bg-outline-variant opacity-20"></div>
                </div>
                <!-- Interview Item 2 (Past) -->
                <?php while ($row = pg_fetch_assoc($past_result)):
                    $date = strtotime($row['interview_date']);
                    $month = date("M", $date);
                    $day = date("d", $date);
                    $time = date("h:i A", $date);
                ?>

                    <div class="bg-surface-container-low/50 p-6 rounded-xl flex items-center justify-between opacity-80">

                        <div class="flex items-center gap-6">

                            <div class="w-16 h-16 rounded-xl bg-surface-container flex flex-col items-center justify-center">
                                <p class="text-[10px] uppercase"><?= $month ?></p>
                                <p class="text-xl font-bold"><?= $day ?></p>
                            </div>

                            <div>
                                <h3 class="font-bold text-lg"><?= $row['company_name'] ?></h3>
                                <p class="text-sm"><?= $row['round_name'] ?></p>
                                <p class="text-xs text-green-600">Completed • <?= $time ?></p>
                            </div>

                        </div>

                        <button class="px-5 py-2 rounded-full bg-white border">
                            View Feedback
                        </button>

                    </div>

                <?php endwhile; ?>
            </div>
            <!-- Side Panel: Feedback Logger -->
            <div class="col-span-4 sticky top-24">
                <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm border-b-4 border-secondary overflow-hidden relative">
                    <!-- Subtle BG Accent -->
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-secondary/5 rounded-full blur-3xl"></div>
                    <h3 class="text-xl font-bold font-headline mb-2 text-on-surface">Log Interview Feedback</h3>
                    <p class="text-sm text-on-surface-variant mb-6 leading-relaxed">Capture insights while they're fresh. Your curated reflection drives improvement.</p>
                    <form class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-outline">Select Interview</label>
                            <div class="relative">
                                <select class="w-full bg-surface-container-low border-0 border-b border-outline-variant focus:border-secondary focus:ring-0 text-sm py-3 rounded-t-lg transition-all appearance-none">

                                    <option value="">Select a recent company...</option>

                                    <?php
                                    $dropdown_result = pg_query_params($conn, "
        SELECT id, company_name, round_name 
        FROM interviews 
        WHERE user_id = $1 
        ORDER BY interview_date DESC
    ", [$user_id]);

                                    while ($row = pg_fetch_assoc($dropdown_result)):
                                    ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['company_name'] ?> - <?= $row['round_name'] ?>
                                        </option>
                                    <?php endwhile; ?>


                                    <select class="w-full bg-surface-container-low border-0 border-b border-outline-variant focus:border-secondary focus:ring-0 text-sm py-3 rounded-t-lg transition-all appearance-none">

                                    </select>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-outline">expand_more</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-outline">What went well?</label>
                            <textarea class="w-full bg-surface-container-low border-0 border-b border-outline-variant focus:border-secondary focus:ring-0 text-sm py-3 rounded-t-lg transition-all resize-none" placeholder="e.g. Articulated portfolio vision clearly..." rows="3"></textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-outline">Areas for improvement</label>
                            <textarea class="w-full bg-surface-container-low border-0 border-b border-outline-variant focus:border-secondary focus:ring-0 text-sm py-3 rounded-t-lg transition-all resize-none" placeholder="e.g. Need to improve salary negotiation points..." rows="3"></textarea>
                        </div>
                        <div class="flex items-center gap-4 pt-2">
                            <button class="flex-1 btn-gradient text-white font-bold py-3.5 rounded-full text-sm shadow-md active:scale-95 transition-all" type="button">Submit Feedback</button>
                        </div>
                    </form>
                </div>
                <!-- Stats/Brief Mini Card -->
                <div class="mt-6 bg-primary p-6 rounded-xl text-white flex items-center gap-6">
                    <div class="p-3 bg-white/10 rounded-full">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">trending_up</span>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-blue-200 tracking-widest leading-none mb-1">Success Rate</p>
                        <h4 class="text-2xl font-extrabold font-headline leading-none tracking-tight"><?= $success_rate ?>%</h4>
                    </div>
                    <div class="ml-auto">
                        <span class="text-[10px] font-medium bg-secondary px-2 py-1 rounded text-white"><?php $growth = rand(1, 10); ?>
                            <span>+<?= $growth ?>% this week</span></span>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>

</html>