<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch applications for logged-in user


$success_query = "SELECT 
    COUNT(*) FILTER (WHERE LOWER(status) IN ('selected','offer received')) AS success,
    COUNT(*) AS total
    FROM applications WHERE user_id = $1";

$success_result = pg_query_params($conn, $success_query, array($user_id));
$data = pg_fetch_assoc($success_result);

$success_rate = ($data['total'] > 0)
    ? round(($data['success'] / $data['total']) * 100)
    : 0;

// search logic
$search = $_POST['search'] ?? '';
$status_filter = $_POST['status'] ?? '';

$query = "SELECT * FROM applications WHERE user_id = $1";
$params = [$user_id];
$paramIndex = 2;

// search filter
if ($search != '') {
    $query .= " AND company_name ILIKE $" . $paramIndex;
    $params[] = "%$search%";
    $paramIndex++;
}

// status filter
if ($status_filter != '' && $status_filter != 'All Statuses') {
    $query .= " AND status = $" . $paramIndex;
    $params[] = $status_filter;
    $paramIndex++;
}

$query .= " ORDER BY date_applied DESC";

$result = pg_query_params($conn, $query, $params);

$last_month_query = "SELECT 
    COUNT(*) FILTER (WHERE LOWER(status) IN ('selected','offer received')) AS success,
    COUNT(*) AS total
FROM applications 
WHERE user_id = $1 
AND date_applied >= date_trunc('month', CURRENT_DATE - INTERVAL '1 month')
AND date_applied < date_trunc('month', CURRENT_DATE)";

$last_month_result = pg_query_params($conn, $last_month_query, array($user_id));
$last_data = pg_fetch_assoc($last_month_result);

$last_rate = ($last_data['total'] > 0)
    ? round(($last_data['success'] / $last_data['total']) * 100)
    : 0;

$current_month_query = "SELECT 
    COUNT(*) FILTER (WHERE LOWER(status) IN ('selected','offer received')) AS success,
    COUNT(*) AS total
FROM applications 
WHERE user_id = $1 
AND date_applied >= date_trunc('month', CURRENT_DATE)";

$current_month_result = pg_query_params($conn, $current_month_query, array($user_id));
$current_data = pg_fetch_assoc($current_month_result);

$current_rate = ($current_data['total'] > 0)
    ? round(($current_data['success'] / $current_data['total']) * 100)
    : 0;

$change = $current_rate - $last_rate;
?>




<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Job Application Management | Placement Tracker</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
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

        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        .font-headline {
            font-family: 'Manrope', sans-serif;
        }
    </style>
</head>

<body class="bg-surface text-on-surface antialiased">
    <!-- SideNavBar -->
    <aside class="h-screen w-64 border-r-0 fixed left-0 top-0 bg-slate-50 flex flex-col py-6 px-4 z-50">
        <div class="mb-10 px-2">
            <h1 class="text-xl font-bold text-blue-900 tracking-tighter">Placement Tracker</h1>
            <p class="text-xs text-slate-500 font-medium">The Curated Architect</p>
        </div>
        <nav class="flex-1 space-y-2">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 hover:text-blue-900 hover:bg-slate-200/50 transition-colors duration-200" href="dashboard.php">
                <span class="material-symbols-outlined text-lg" data-icon="dashboard">dashboard</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Dashboard</span>
            </a>
            <!-- Active State: Applications -->
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-blue-700 font-bold border-r-4 border-blue-700 bg-white scale-98 transition-all" href="application.php">
                <span class="material-symbols-outlined text-lg" data-icon="work">work</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Applications</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 hover:text-blue-900 hover:bg-slate-200/50 transition-colors duration-200" href="interview.php">
                <span class="material-symbols-outlined text-lg" data-icon="event">event</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Interviews</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 hover:text-blue-900 hover:bg-slate-200/50 transition-colors duration-200" href="profile.php">
                <span class="material-symbols-outlined text-lg" data-icon="person">person</span>
                <span class="font-manrope text-sm font-medium tracking-tight">Profile</span>
            </a>
        </nav>
        <div class="mt-auto px-2">
            <button onclick="window.location.href='add-application.php'" class="w-full bg-gradient-to-br from-primary to-primary-container text-white py-3 rounded-full font-headline text-sm font-semibold shadow-lg shadow-primary/10 flex items-center justify-center gap-2 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
                Add Application
            </button>
        </div>
    </aside>
    <!-- TopNavBar -->
    <header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 flex justify-between items-center h-16 px-8 ml-64">
        <div class="flex items-center flex-1 max-w-xl">
            <div class="relative w-full group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                <input class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Search applications, companies..." type="text" />
            </div>
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
    <!-- Main Content Canvas -->
    <main class="ml-64 pt-24 pb-12 px-12 min-h-screen">
        <?php if (isset($_GET['msg'])): ?>
            <div class="text-green-600 text-sm mb-4">
                <?= $_GET['msg'] == 'deleted' ? 'Application deleted successfully!' : '' ?>
            </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <h2 class="text-3xl font-extrabold text-on-surface tracking-tight font-headline">Application Pipeline</h2>
                <p class="text-on-surface-variant mt-1 font-body">Manage and track your active career pursuits.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex bg-surface-container-low p-1 rounded-xl">
                    <button class="px-4 py-2 text-xs font-semibold rounded-lg bg-white shadow-sm text-primary">All Applications</button>
                    <button class="px-4 py-2 text-xs font-medium text-on-surface-variant hover:text-primary transition-colors">Archive</button>
                </div>
                <button class="bg-surface-container-high text-on-primary-fixed-variant px-5 py-2.5 rounded-full text-sm font-semibold flex items-center gap-2 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span>
                    Filter
                </button>
            </div>
        </div>
        <!-- Filters & Stats Bento Row -->
        <div class="grid grid-cols-12 gap-6 mb-8">
            <div class="col-span-12 lg:col-span-8 bg-surface-container-lowest p-6 rounded-xl flex items-center gap-8">
                <form method="POST" class="col-span-12 lg:col-span-8 bg-surface-container-lowest p-6 rounded-xl flex items-center gap-8">
                    <div class="flex-1 space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-outline">Search by Company</label>
                        <div class="flex items-center gap-2 border-b-2 border-surface-container-highest focus-within:border-secondary transition-all py-1">
                            <span class="material-symbols-outlined text-slate-400 text-lg">business</span>
                            <input name="search" value="<?= htmlspecialchars($search) ?>" class="w-full bg-transparent border-none p-0 focus:ring-0 text-sm placeholder:text-slate-300" placeholder="e.g. Google, Airbnb" type="text" />
                        </div>
                    </div>
                    <div class="w-px h-10 bg-slate-100"></div>
                    <div class="flex-1 space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-outline">Filter by Status</label>
                        <select name="status" class="w-full bg-transparent border-none p-0 focus:ring-0 text-sm font-medium appearance-none cursor-pointer">
                            <option <?= $status_filter == 'All Statuses' ? 'selected' : '' ?>>All Statuses</option>
                            <option <?= $status_filter == 'Applied' ? 'selected' : '' ?>>Applied</option>
                            <option <?= $status_filter == 'Offer Received' ? 'selected' : '' ?>>Offer Received</option>
                            <option <?= $status_filter == 'Selected' ? 'selected' : '' ?>>Selected</option>
                            <option <?= $status_filter == 'Interviewing' ? 'selected' : '' ?>>Interviewing</option>
                            <option <?= $status_filter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>

                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="bg-gradient-to-br from-primary to-primary-container text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-lg shadow-primary/20 flex items-center gap-2 active:scale-95 transition-all hover:opacity-90">

                            <span class="material-symbols-outlined text-sm">search</span>
                            Apply Filters
                        </button>
                    </div>

                </form>

            </div>
            <div class="col-span-12 lg:col-span-4 bg-primary text-white p-6 rounded-xl relative overflow-hidden flex flex-col justify-between">
                <div class="relative z-10">
                    <p class="text-xs font-medium text-white/70 uppercase tracking-widest">Success Probability</p>
                    <h3 class="text-4xl font-extrabold font-headline mt-1">
                        <?= $success_rate ?>%
                    </h3>
                </div>
                <div class="relative z-10 flex items-center gap-2 text-xs font-semibold bg-white/10 w-fit px-3 py-1 rounded-full backdrop-blur-md">
                    <span class="material-symbols-outlined text-sm">
                        <?= $change >= 0 ? 'trending_up' : 'trending_down' ?>

                    </span>

                    <?= ($change >= 0 ? '+' : '') . $change ?>% from last month
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <span class="material-symbols-outlined text-9xl">analytics</span>
                </div>
            </div>
        </div>
        <!-- Applications Table Section -->
        <div class="bg-surface-container-lowest rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50">
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-outline">Company</th>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-outline">Role</th>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-outline text-center">Date Applied</th>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-outline text-center">Status</th>
                            <th class="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-outline text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">

                        <?php if (pg_num_rows($result) == 0): ?>

                            <tr>
                                <td colspan="5" class="text-center py-10 text-gray-400">
                                    No applications found 🚀
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php while ($row = pg_fetch_assoc($result)): ?>

                                <tr class="hover:bg-slate-50/50 transition-colors group">

                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary font-bold">
                                                <?= strtoupper(substr($row['company_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold"><?= htmlspecialchars($row['company_name']) ?></p>

                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-5">
                                        <p class="text-sm font-medium"><?= htmlspecialchars($row['job_role']) ?></p>
                                    </td>

                                    <td class="px-8 py-5 text-center">
                                        <span class="text-xs">
                                            <?= date("d M Y", strtotime($row['date_applied'])) ?>
                                        </span>
                                    </td>

                                    <td class="px-8 py-5 text-center">

                                        <?php
                                        $status = strtolower($row['status']);

                                        if ($status == 'selected' || $status == 'offer received') {
                                            $color = 'bg-green-500';
                                        } elseif ($status == 'rejected') {
                                            $color = 'bg-red-500';
                                        } elseif ($status == 'interviewing') {
                                            $color = 'bg-yellow-500';
                                        } else {
                                            $color = 'bg-blue-500';
                                        }
                                        ?>

                                        <span class="px-3 py-1 rounded-full text-xs text-white <?= $color ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>

                                    </td>

                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2">

                                            <a href="edit_application.php?id=<?= $row['id'] ?>"
                                                class="p-2 text-slate-400 hover:text-primary">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </a>

                                            <a href="delete_application.php?id=<?= $row['id'] ?>"
                                                class="p-2 text-slate-400 hover:text-red-500"
                                                onclick="return confirm('Delete this application?')">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </a>


                                        </div>
                                    </td>

                                </tr>

                            <?php endwhile; ?>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
            <!-- Pagination-esque footer -->
            <div class="px-8 py-4 bg-slate-50/30 flex items-center justify-between">

                <?php
                $count = pg_num_rows($result);
                ?>
                <span class="text-xs text-gray-500">
                    Showing <?= $count ?> application<?= $count != 1 ? 's' : '' ?>
                </span>

                <div class="flex gap-2">
                    <button class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 hover:bg-white text-slate-400 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 bg-white text-primary font-bold text-xs">1</button>
                    <button class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 hover:bg-white text-on-surface-variant font-medium text-xs">2</button>
                    <button class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 hover:bg-white text-on-surface-variant font-medium text-xs">3</button>
                    <button class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-100 hover:bg-white text-slate-400 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal Backdrop (Hidden by default, shown for reference/visual design) -->
    <!-- Remove 'hidden' to see the modal design -->
    <div class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-primary/20 backdrop-blur-sm">
        <div class="bg-surface-container-lowest w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-surface-container-low/30">
                <div>
                    <h3 class="text-xl font-bold font-headline text-on-surface">New Application</h3>
                    <p class="text-xs text-on-surface-variant">Capture the details of your latest career move.</p>
                </div>
                <button class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form class="p-8 grid grid-cols-2 gap-6">
                <div class="col-span-1 space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-outline pl-1">Company Name</label>
                    <input class="w-full bg-surface-container-low border-none border-b-2 border-transparent focus:border-secondary transition-all rounded-lg px-4 py-3 text-sm focus:ring-0" placeholder="e.g. Apple Inc." type="text" />
                </div>
                <div class="col-span-1 space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-outline pl-1">Role Title</label>
                    <input class="w-full bg-surface-container-low border-none border-b-2 border-transparent focus:border-secondary transition-all rounded-lg px-4 py-3 text-sm focus:ring-0" placeholder="e.g. Senior Architect" type="text" />
                </div>
                <div class="col-span-1 space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-outline pl-1">Location</label>
                    <input class="w-full bg-surface-container-low border-none border-b-2 border-transparent focus:border-secondary transition-all rounded-lg px-4 py-3 text-sm focus:ring-0" placeholder="City, State or Remote" type="text" />
                </div>
                <div class="col-span-1 space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-outline pl-1">Package Range</label>
                    <input class="w-full bg-surface-container-low border-none border-b-2 border-transparent focus:border-secondary transition-all rounded-lg px-4 py-3 text-sm focus:ring-0" placeholder="e.g. $140k - $160k" type="text" />
                </div>
                <div class="col-span-1 space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-outline pl-1">Date Applied</label>
                    <input class="w-full bg-surface-container-low border-none border-b-2 border-transparent focus:border-secondary transition-all rounded-lg px-4 py-3 text-sm focus:ring-0" type="date" />
                </div>
                <div class="col-span-1 space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-outline pl-1">Current Status</label>
                    <select class="w-full bg-surface-container-low border-none border-b-2 border-transparent focus:border-secondary transition-all rounded-lg px-4 py-3 text-sm focus:ring-0">
                        <option>Applied</option>
                        <option>Screening</option>
                        <option>Technical Interview</option>
                        <option>Final Round</option>
                        <option>Offer</option>
                    </select>
                </div>
                <div class="col-span-2 space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-outline pl-1">Resume / Portfolio Link</label>
                    <input class="w-full bg-surface-container-low border-none border-b-2 border-transparent focus:border-secondary transition-all rounded-lg px-4 py-3 text-sm focus:ring-0" placeholder="https://..." type="url" />
                </div>
                <div class="col-span-2 space-y-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-outline pl-1">Personal Notes</label>
                    <textarea class="w-full bg-surface-container-low border-none border-b-2 border-transparent focus:border-secondary transition-all rounded-lg px-4 py-3 text-sm focus:ring-0" placeholder="Key details, referral name, etc..." rows="3"></textarea>
                </div>
                <div class="col-span-2 flex items-center justify-end gap-4 mt-4">
                    <button class="px-6 py-2.5 rounded-full text-sm font-semibold text-on-surface-variant hover:bg-slate-100 transition-colors" type="button">Cancel</button>
                    <button class="px-8 py-2.5 rounded-full text-sm font-semibold bg-gradient-to-br from-primary to-primary-container text-white shadow-lg shadow-primary/20 active:scale-95 transition-all" type="submit">Save Application</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>