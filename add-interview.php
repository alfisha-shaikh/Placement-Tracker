<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// INSERT DATA
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_name = $_POST['company_name'];
    $interview_date = $_POST['interview_date'];
    $round_name = $_POST['round_name'];
    $mode = $_POST['mode'];

    $query = "INSERT INTO interviews 
    (user_id, company_name, interview_date, round_name, mode)
    VALUES ($1, $2, $3, $4, $5)";

    $result = pg_query_params($conn, $query, array(
        $user_id,
        $company_name,
        $interview_date,
        $round_name,
        $mode
    ));

    if ($result) {
        header("Location: interview.php");
        exit();
    } else {
        echo "Error inserting interview";
    }
}
?>



<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Add New Interview | Placement Tracker</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-fixed-dim": "#b6c4ff",
                        "surface-container-high": "#e6e8ea",
                        "primary-fixed": "#dce1ff",
                        "error": "#ba1a1a",
                        "on-tertiary-fixed-variant": "#004b74",
                        "on-error-container": "#93000a",
                        "on-primary-fixed-variant": "#264191",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-fixed": "#00164e",
                        "outline": "#757682",
                        "on-background": "#191c1e",
                        "background": "#f7f9fb",
                        "surface-tint": "#4059aa",
                        "outline-variant": "#c5c5d3",
                        "on-surface-variant": "#444651",
                        "inverse-surface": "#2d3133",
                        "tertiary": "#002d49",
                        "surface-container": "#eceef0",
                        "surface-bright": "#f7f9fb",
                        "primary": "#00236f",
                        "tertiary-fixed": "#cde5ff",
                        "surface": "#f7f9fb",
                        "secondary-fixed": "#89f5e7",
                        "secondary-fixed-dim": "#6bd8cb",
                        "on-surface": "#191c1e",
                        "secondary-container": "#86f2e4",
                        "on-tertiary-fixed": "#001d32",
                        "on-tertiary-container": "#6ab3ef",
                        "on-primary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "on-primary-container": "#90a8ff",
                        "inverse-on-surface": "#eff1f3",
                        "inverse-primary": "#b6c4ff",
                        "tertiary-fixed-dim": "#94ccff",
                        "surface-container-low": "#f2f4f6",
                        "primary-container": "#1e3a8a",
                        "on-secondary-fixed": "#00201d",
                        "on-secondary-fixed-variant": "#005049",
                        "on-secondary-container": "#006f66",
                        "surface-dim": "#d8dadc",
                        "on-error": "#ffffff",
                        "on-secondary": "#ffffff",
                        "error-container": "#ffdad6",
                        "tertiary-container": "#00446b",
                        "secondary": "#006a61",
                        "surface-variant": "#e0e3e5",
                        "on-tertiary": "#ffffff"
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
        .font-headline {
            font-family: 'Manrope', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .cta-gradient {
            background: linear-gradient(135deg, #00236f 0%, #1e3a8a 100%);
        }
    </style>
</head>

<body class="bg-surface font-body text-on-surface">
    <!-- SideNavBar (Shared Component aligned with SCREEN_7) -->
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
            <button class="w-full cta-gradient text-white py-3 rounded-xl font-semibold flex items-center justify-center gap-2 active:scale-95 transition-transform shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-sm">add</span>
                New Interview
            </button>
        </div>
    </aside>
    <!-- TopNavBar (Shared Component aligned with SCREEN_7) -->
    <header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 flex justify-between items-center h-16 px-8 ml-64 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-800/50 shadow-sm dark:shadow-none font-manrope text-sm">
        <div class="flex items-center bg-surface-container-low px-4 py-2 rounded-full w-96 group focus-within:ring-2 ring-primary/20 transition-all">
            <span class="material-symbols-outlined text-outline mr-2 text-lg">search</span>
            <input class="bg-transparent border-none focus:ring-0 text-sm w-full p-0 placeholder:text-outline" placeholder="Search placements..." type="text" />
        </div>

        <div class=" flex items-center gap-6">
            <div class="flex items-center gap-4">
                <button class="p-2 text-slate-400 hover:text-blue-700 transition-all cursor-pointer active:scale-95">
                    <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                </button>
                <button class="p-2 text-slate-400 hover:text-blue-700 transition-all cursor-pointer active:scale-95">
                    <span class="material-symbols-outlined" data-icon="settings">settings</span>
                </button>
            </div>
            <div class="h-8 w-px bg-slate-100"></div>
            <div class="flex items-center gap-3 group cursor-pointer">
                <a href="logout.php" class="text-sm font-medium text-blue-900">Logout</a>
            </div>
        </div>

    </header>
    <!-- Main Content Area -->
    <main class="ml-64 pt-24 min-h-screen">
        <div class="p-8 max-w-7xl mx-auto">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 mb-8 text-xs font-label font-medium tracking-wide">

                <span class="text-on-surface-variant uppercase">Interviews</span>
                <span class="material-symbols-outlined text-[10px] text-outline">chevron_right</span>
                <span class="text-primary font-bold uppercase">Add New</span>
            </nav>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Main Form Section -->
                <div class="lg:col-span-8">
                    <div class="mb-10">
                        <h2 class="font-headline text-3xl font-extrabold text-on-surface tracking-tight mb-2">Schedule New Interview</h2>
                        <p class="text-on-surface-variant text-sm max-w-md">Log the details for your upcoming discussion and keep your recruitment funnel organized.</p>
                    </div>
                    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                        <form method="POST" class="space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Company Selection -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Company / Application</label>
                                    <div class="relative">
                                        <input name="company_name" class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface" type="text" placeholder="Enter company name" />
                                    </div>
                                </div>
                                <!-- Interview Round -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Interview Round</label>
                                    <div class="relative">
                                        <select name="round_name" class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface appearance-none">
                                            <option>Technical Round</option>
                                            <option>HR Round</option>
                                            <option>Managerial Round</option>
                                            <option>Aptitude Test</option>
                                        </select>
                                        <span class="absolute right-0 top-1/2 -translate-y-1/2 material-symbols-outlined pointer-events-none text-on-surface-variant text-lg">expand_more</span>
                                    </div>
                                </div>
                                <!-- Date & Time -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Date &amp; Time</label>
                                    <div class="relative">
                                        <input class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface" type="datetime-local" name="interview_date" />
                                    </div>
                                </div>
                                <!-- Interview Mode -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Interview Mode</label>
                                    <div class="flex gap-2">
                                        <label class="flex-1 cursor-pointer">
                                            <input checked="" class="peer sr-only" name="mode" type="radio" value="Online" checked />
                                            <div class="text-center py-2.5 px-4 rounded-lg bg-surface-container-high peer-checked:bg-secondary-container peer-checked:text-on-secondary-container transition-all font-label text-xs font-semibold">Online</div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input class="peer sr-only" name="mode" type="radio" value="Offline" />
                                            <div class="text-center py-2.5 px-4 rounded-lg bg-surface-container-high peer-checked:bg-secondary-container peer-checked:text-on-secondary-container transition-all font-label text-xs font-semibold">On-site</div>
                                        </label>
                                    </div>
                                </div>
                                <!-- Meeting Link -->
                                <div class="col-span-full space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Meeting Link / Physical Location</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute right-0 top-1/2 -translate-y-1/2 text-outline/60 text-lg">link</span>
                                        <input class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50" placeholder="https://zoom.us/j/..." type="text" />
                                    </div>
                                </div>
                                <!-- Interviewer Name -->
                                <div class="col-span-full space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Interviewer Name <span class="lowercase font-normal opacity-60">(Optional)</span></label>
                                    <input class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50" placeholder="e.g. Sarah Jenkins" type="text" />
                                </div>
                                <!-- Preparation Notes -->
                                <div class="col-span-full space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Preparation Notes</label>
                                    <textarea class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50 resize-none" placeholder="Focus on system design patterns and previous leadership experience..." rows="4"></textarea>
                                </div>
                            </div>
                            <!-- Form Actions -->
                            <div class="flex items-center justify-end gap-6 pt-6">
                                <button class="text-on-primary-fixed-variant font-headline font-bold text-sm tracking-tight hover:opacity-70 transition-opacity" type="button">Cancel</button>
                                <button class="bg-primary text-white px-10 py-4 rounded-full font-headline font-bold text-sm shadow-xl shadow-primary/10 hover:shadow-primary/20 transition-all active:scale-95" type="submit">Schedule Interview</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Sidebar / Widget Section -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Preparation Tips (Restyled consistent with SCREEN_7) -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/15">
                        <h4 class="font-headline font-bold text-sm text-on-surface flex items-center gap-2 mb-6">
                            <span class="material-symbols-outlined text-secondary">tips_and_updates</span>
                            Interview Preparation
                        </h4>
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="bg-secondary/10 p-2 h-fit rounded-lg">
                                    <span class="material-symbols-outlined text-secondary text-lg">search</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-xs text-on-surface uppercase tracking-wider">Research Company</h5>
                                    <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">Understand their mission and culture to align your answers.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="bg-secondary/10 p-2 h-fit rounded-lg">
                                    <span class="material-symbols-outlined text-secondary text-lg">terminal</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-xs text-on-surface uppercase tracking-wider">Review Fundamentals</h5>
                                    <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">Ensure a firm grasp of core concepts for this specific round.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="bg-secondary/10 p-2 h-fit rounded-lg">
                                    <span class="material-symbols-outlined text-secondary text-lg">chat</span>
                                </div>
                                <div>
                                    <h5 class="font-bold text-xs text-on-surface uppercase tracking-wider">STAR Method</h5>
                                    <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">Prepare behavioral answers using Situation, Task, Action, and Result.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 pt-4 border-t border-outline-variant/15">
                            <a class="text-primary text-[10px] font-bold uppercase flex items-center gap-2 hover:translate-x-1 transition-transform" href="#">
                                View full prep guide
                                <span class="material-symbols-outlined text-xs">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                    <!-- Pipeline Status Widget -->
                    <div class="bg-surface-container-low rounded-xl p-6 relative overflow-hidden group">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-headline font-bold text-on-surface">Active Pipeline</h3>
                                <span class="text-2xl font-extrabold text-primary font-headline">12</span>
                            </div>
                            <div class="space-y-3">
                                <div class="bg-surface-container-lowest p-3 rounded-lg flex items-center justify-between border border-outline-variant/5">
                                    <span class="text-xs font-semibold text-on-surface-variant">Phone Screens</span>
                                    <span class="bg-secondary-container text-on-secondary-container text-[10px] px-2 py-0.5 rounded-full font-bold">04</span>
                                </div>
                                <div class="bg-surface-container-lowest p-3 rounded-lg flex items-center justify-between border border-outline-variant/5">
                                    <span class="text-xs font-semibold text-on-surface-variant">On-sites Scheduled</span>
                                    <span class="bg-secondary-container text-on-secondary-container text-[10px] px-2 py-0.5 rounded-full font-bold">02</span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-secondary/5 rounded-full blur-3xl"></div>
                    </div>
                    <!-- Company Context Decorator -->
                    <div class="h-40 rounded-xl overflow-hidden relative group shadow-sm">
                        <img alt="Corporate office building" class="w-full h-full object-cover grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDEu_B_4Y4SakezFaGAuGOt6yh-J77NgJBZkU_AXNbYVTEwugfIDhCsuVc-GXLs5vs4SnF7AFmSTE3f0AXWoxAynb-98wz1xb6z_47rlZVBc8sfKagY2tam63kZQVSkwyPO13v-9uHFfgpjobIZGeetWBPLDaphHfNGpFJcsjlFYOQxusHX5_75ca2gMcFOf6tokeqQdXWxM6iFikd9xAz5EWOWNy0_KlR4IKjJNd4iauZL-7Cpo9T8AF5gg5bOuI9QhIqaUupGy2I" />
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent flex items-end p-4">
                            <span class="text-white text-[10px] font-bold uppercase tracking-[0.2em]">Curated Professionalism</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</body>

</html>