<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $company = $_POST['company_name'];
    $role = $_POST['job_role'];
    $date = $_POST['date_applied'];
    $status = ucfirst($_POST['status']);


    $query = "INSERT INTO applications 
    (user_id, company_name, job_role, date_applied, status) 
    VALUES ($1,$2,$3,$4,$5)";

    $result = pg_query_params($conn, $query, [
        $user_id,
        $company,
        $role,
        $date,
        $status
    ]);

    if (!$result) {
        echo "Error: " . pg_last_error($conn);
        exit();
    } else {
        header("Location: application.php");
        exit();
    }
}

// Success Probability Calculation
$prob_query = "SELECT 
    COUNT(*) AS total,
    COUNT(*) FILTER (WHERE status = 'Selected') AS selected,
    COUNT(*) FILTER (WHERE status = 'Rejected') AS rejected,
    COUNT(*) FILTER (WHERE status = 'Interview') AS interview
FROM applications
WHERE user_id = $1";

$prob_result = pg_query_params($conn, $prob_query, [$user_id]);
$prob_data = pg_fetch_assoc($prob_result);

$total = $prob_data['total'];
$selected = $prob_data['selected'];
$rejected = $prob_data['rejected'];
$interview = $prob_data['interview'];

$success_percent = 0;

if ($total > 0) {
    $success_percent = round(($selected / $total) * 100);
}
$circumference = 364.4;
$offset = $circumference - ($success_percent / 100) * $circumference;



?>




<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Add New Application | PlacementTracker</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "inverse-on-surface": "#eff1f3",
                        "secondary-container": "#86f2e4",
                        "surface-bright": "#f7f9fb",
                        "on-surface": "#191c1e",
                        "on-secondary-fixed-variant": "#005049",
                        "on-secondary-fixed": "#00201d",
                        "on-error-container": "#93000a",
                        "error": "#ba1a1a",
                        "surface-tint": "#4059aa",
                        "tertiary-fixed-dim": "#94ccff",
                        "on-primary-fixed": "#00164e",
                        "surface-container-high": "#e6e8ea",
                        "on-tertiary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-container": "#6ab3ef",
                        "on-primary-container": "#90a8ff",
                        "secondary-fixed": "#89f5e7",
                        "on-surface-variant": "#444651",
                        "tertiary-fixed": "#cde5ff",
                        "surface-variant": "#e0e3e5",
                        "primary-container": "#1e3a8a",
                        "on-secondary-container": "#006f66",
                        "secondary": "#006a61",
                        "on-primary": "#ffffff",
                        "tertiary-container": "#00446b",
                        "on-error": "#ffffff",
                        "secondary-fixed-dim": "#6bd8cb",
                        "on-secondary": "#ffffff",
                        "surface-dim": "#d8dadc",
                        "on-tertiary-fixed": "#001d32",
                        "tertiary": "#002d49",
                        "on-background": "#191c1e",
                        "background": "#f7f9fb",
                        "surface": "#f7f9fb",
                        "inverse-surface": "#2d3133",
                        "on-primary-fixed-variant": "#264191",
                        "inverse-primary": "#b6c4ff",
                        "surface-container-highest": "#e0e3e5",
                        "outline-variant": "#c5c5d3",
                        "primary-fixed-dim": "#b6c4ff",
                        "primary-fixed": "#dce1ff",
                        "on-tertiary-fixed-variant": "#004b74",
                        "surface-container-low": "#f2f4f6",
                        "error-container": "#ffdad6",
                        "primary": "#00236f",
                        "outline": "#757682",
                        "surface-container": "#eceef0"
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

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .cta-gradient {
            background: linear-gradient(135deg, #00236f 0%, #1e3a8a 100%);
        }
    </style>
</head>

<body class="bg-surface font-body text-on-surface">
    <!-- SideNavBar (Shared Component) -->
    <aside class="h-screen w-64 border-r-0 fixed left-0 top-0 flex flex-col py-6 px-4 bg-slate-50 dark:bg-slate-900 font-manrope text-sm font-medium tracking-tight z-50">


        <div class="mb-10 px-2">
            <h1 class="text-xl font-bold text-blue-900 tracking-tighter">Placement Tracker</h1>
            <p class="text-xs text-slate-500 font-medium">The Curated Architect</p>
        </div>
        <nav class="flex-1 space-y-2">
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-blue-900 dark:hover:text-slate-100 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200" href="dashboard.php">
                <span class="material-symbols-outlined text-lg">dashboard</span>
                <span>Dashboard</span>
            </a>
            <!-- Active: Applications -->
            <a class="flex items-center gap-3 px-4 py-3 text-blue-700 dark:text-teal-400 font-bold border-r-4 border-blue-700 dark:border-teal-400 bg-white dark:bg-slate-800 transition-all" href="application.php">
                <span class="material-symbols-outlined text-lg">work</span>
                <span>Applications</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-blue-900 dark:hover:text-slate-100 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200" href="interview.php">
                <span class="material-symbols-outlined text-lg">event</span>
                <span>Interviews</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-blue-900 dark:hover:text-slate-100 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200" href="profile.php">
                <span class="material-symbols-outlined text-lg">person</span>
                <span>Profile</span>
            </a>
        </nav>
        <div class="mt-auto px-2">
            <button class="w-full cta-gradient text-white py-3 rounded-xl font-semibold flex items-center justify-center gap-2 active:scale-95 transition-transform shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-sm">add</span>
                Add Application
            </button>
        </div>
    </aside>
    <!-- TopNavBar (Shared Component) -->
    <header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 flex justify-between items-center h-16 px-8 ml-64 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-800/50 shadow-sm dark:shadow-none font-manrope text-sm">
        <div class="flex items-center bg-surface-container-low px-4 py-2 rounded-full w-96 group focus-within:ring-2 ring-primary/20 transition-all">
            <span class="material-symbols-outlined text-outline mr-2 text-lg">search</span>
            <input class="bg-transparent border-none focus:ring-0 text-sm w-full p-0 placeholder:text-outline" placeholder="Search applications..." type="text" />
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
                <span class="text-on-surface-variant uppercase">Applications</span>
                <span class="material-symbols-outlined text-[10px] text-outline">chevron_right</span>
                <span class="text-primary font-bold uppercase">Add New</span>
            </nav>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Main Form Section -->
                <div class="lg:col-span-8">
                    <div class="mb-10">
                        <h2 class="font-headline text-3xl font-extrabold text-on-surface tracking-tight mb-2">Add Application</h2>
                        <p class="text-on-surface-variant text-sm max-w-md">Document your professional journey. Each entry adds to your editorial narrative of career success.</p>
                    </div>
                    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm">
                        <form class="space-y-8" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Company Name -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Company Name</label>
                                    <input class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50" placeholder="e.g. Goldman Sachs" type="text" name="company_name" required />
                                </div>
                                <!-- Role -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Position Role</label>
                                    <input class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50" placeholder="e.g. Software Engineer Intern" type="text" name="job_role" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <!-- Location -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Location</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute right-0 top-1/2 -translate-y-1/2 text-outline/60 text-lg">location_on</span>
                                        <input class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50" placeholder="Bengaluru, KA" type="text" name="location" />
                                    </div>
                                </div>
                                <!-- Salary -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Salary Package</label>
                                    <input name="package" class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50" placeholder="e.g. 12 LPA" type="text" />
                                </div>
                                <!-- Date Applied -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Date Applied</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute right-0 top-1/2 -translate-y-1/2 text-outline/60 text-lg">calendar_today</span>
                                        <input class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface" type="date" name="date_applied" required />
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Status -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Application Status</label>
                                    <select name="status" class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface">
                                        <option value="Applied">Applied</option>
                                        <option value="Interview">Offer Received</option>
                                        <option value="Selected">Selected</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                </div>
                                <!-- Resume Link -->
                                <div class="space-y-1.5">
                                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Resume Link</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute right-0 top-1/2 -translate-y-1/2 text-outline/60 text-lg">link</span>
                                        <input name="resume_link" class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50" placeholder="https://drive.google.com/..." type="url" />
                                    </div>
                                </div>
                            </div>
                            <!-- Notes -->
                            <div class="space-y-1.5">
                                <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant">Notes &amp; Strategy</label>
                                <textarea name="notes" class="w-full bg-surface-container-highest border-0 border-b-2 border-transparent focus:border-secondary focus:ring-0 transition-all px-0 py-3 text-on-surface placeholder:text-outline/50 resize-none" placeholder="Mention key referral details, interview focus points, or company research..." rows="4"></textarea>
                            </div>
                            <!-- Form Actions -->
                            <div class="flex items-center justify-end gap-6 pt-6">
                                <button onclick="window.location.href='application.php'" class="text-on-primary-fixed-variant font-headline font-bold text-sm tracking-tight hover:opacity-70 transition-opacity" type="button">Cancel</button>
                                <button class="bg-primary text-white px-10 py-4 rounded-full font-headline font-bold text-sm shadow-xl shadow-primary/10 hover:shadow-primary/20 transition-all active:scale-95" type="submit">Add Application</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Sidebar / Widget Section -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Success Probability Widget -->
                    <div class="bg-surface-container-low rounded-xl p-6 relative overflow-hidden group">
                        <div class="relative z-10 text-center">
                            <h3 class="font-headline font-bold text-on-surface mb-1">Success Probability</h3>
                            <p class="text-[10px] text-on-surface-variant mb-6 uppercase tracking-widest">Based on your recent match data</p>
                            <div class="flex flex-col items-center py-4">
                                <!-- Progress Ring -->
                                <div class="relative w-32 h-32 mb-4">
                                    <svg class="w-full h-full transform -rotate-90">
                                        <circle class="text-outline-variant/20" cx="64" cy="64" fill="transparent" r="58" stroke="currentColor" stroke-width="8"></circle>
                                        <circle class="text-secondary" cx="64" cy="64" fill="transparent" r="58" stroke="currentColor" stroke-dasharray="364.4" stroke-dashoffset="<?= $offset ?>" stroke-linecap="round" stroke-width="8"></circle>
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center flex-col">
                                        <span class="text-3xl font-extrabold text-primary">
                                            <?= $success_percent ?>%
                                        </span>

                                        <span class="text-[9px] font-bold text-secondary uppercase tracking-tighter">
                                            <?php
                                            if ($success_percent >= 70) echo "High Match";
                                            elseif ($success_percent >= 40) echo "Medium Match";
                                            else echo "Low Match";
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <p class="text-center text-xs text-on-surface-variant px-4 leading-relaxed">
                                    You applied to <b><?= $total ?></b> jobs, got
                                    <b><?= $selected ?></b> selected,
                                    <b><?= $interview ?></b> interviews, and
                                    <b><?= $rejected ?></b> rejections.
                                </p>
                            </div>
                        </div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-secondary/5 rounded-full blur-3xl"></div>
                    </div>
                    <!-- Helpful Tips Card -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/15">
                        <h4 class="font-headline font-bold text-sm text-on-surface flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-secondary">tips_and_updates</span>
                            Architect's Tip
                        </h4>
                        <ul class="space-y-4">
                            <li class="flex gap-3">
                                <span class="h-1.5 w-1.5 rounded-full bg-secondary mt-1.5 shrink-0"></span>
                                <p class="text-xs text-on-surface-variant leading-relaxed">Always attach a link to the specific resume version used for this application.</p>
                            </li>
                            <li class="flex gap-3">
                                <span class="h-1.5 w-1.5 rounded-full bg-secondary mt-1.5 shrink-0"></span>
                                <p class="text-xs text-on-surface-variant leading-relaxed">Add referral names in the notes to track communication paths.</p>
                            </li>
                        </ul>
                    </div>
                    <!-- Company Context Decorator -->
                    <div class="h-40 rounded-xl overflow-hidden relative group">
                        <img alt="Corporate office building" class="w-full h-full object-cover grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700" data-alt="modern glass and steel skyscraper architecture against a deep blue dusk sky with glowing interior lights" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDEu_B_4Y4SakezFaGAuGOt6yh-J77NgJBZkU_AXNbYVTEwugfIDhCsuVc-GXLs5vs4SnF7AFmSTE3f0AXWoxAynb-98wz1xb6z_47rlZVBc8sfKagY2tam63kZQVSkwyPO13v-9uHFfgpjobIZGeetWBPLDaphHfNGpFJcsjlFYOQxusHX5_75ca2gMcFOf6tokeqQdXWxM6iFikd9xAz5EWOWNy0_KlR4IKjJNd4iauZL-7Cpo9T8AF5gg5bOuI9QhIqaUupGy2I" />
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