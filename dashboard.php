<?php
session_start();
require_once 'connect.php';


if (!isset($_SESSION['name'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// fetch applications
$query = "SELECT * FROM applications WHERE user_id = $1 ORDER BY date_applied DESC LIMIT 5";
$result = pg_query_params($conn, $query, array($user_id));

// Applications
$app_count = pg_fetch_result(pg_query_params(
  $conn,
  "SELECT COUNT(*) FROM applications WHERE user_id = $1",
  array($user_id)
), 0, 0);

// Interviews
$interview_count = pg_fetch_result(pg_query_params(
  $conn,
  "SELECT COUNT(*) FROM interviews WHERE user_id = $1",
  array($user_id)
), 0, 0);

// Selected
$selected_count = pg_fetch_result(pg_query_params(
  $conn,
  "SELECT COUNT(*) FROM applications WHERE user_id = $1 AND status='Selected'",
  array($user_id)
), 0, 0);

// Rejected
$rejected_count = pg_fetch_result(pg_query_params(
  $conn,
  "SELECT COUNT(*) FROM applications WHERE user_id = $1 AND status='Rejected'",
  array($user_id)
), 0, 0);

$success_rate = $app_count > 0 ? round(($selected_count / $app_count) * 100) : 0;

$circle_length = 364.4;
$offset = $circle_length - ($circle_length * $success_rate / 100);

// Fetch next upcoming interview
$next_interview = pg_query_params(
  $conn,
  "SELECT * FROM interviews 
   WHERE user_id = $1 AND interview_date >= NOW() 
   ORDER BY interview_date ASC 
   LIMIT 1",
  array($user_id)
);

$interview_data = pg_fetch_assoc($next_interview);

$time_left = "";
$round_name = "";
$interview_date_display = "";

if ($interview_data) {
  $round_name = $interview_data['round_name']; // e.g. HR, Technical
  $interview_datetime = strtotime($interview_data['interview_date']);

  $current_time = time();
  $diff = $interview_datetime - $current_time;

  if ($diff > 0) {
    $hours = floor($diff / 3600);
    $minutes = floor(($diff % 3600) / 60);

    $time_left = "Next interview in {$hours}h {$minutes}m";
  } else {
    $time_left = "Interview time passed";
  }

  $interview_date_display = date("D, d M • h:i A", $interview_datetime);
  $month = $interview_data ? date("M", $interview_datetime) : "";
  $day = $interview_data ? date("d", $interview_datetime) : "";
}



?>



<!doctype html>
<html class="light" lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "on-tertiary-fixed": "#001d32",
            "on-tertiary-fixed-variant": "#004b74",
            primary: "#00236f",
            background: "#f7f9fb",
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
            tertiary: "#002d49",
            "secondary-container": "#86f2e4",
            "on-secondary": "#ffffff",
            surface: "#f7f9fb",
            "inverse-primary": "#b6c4ff",
            "on-primary-container": "#90a8ff",
            "primary-fixed-dim": "#b6c4ff",
            outline: "#757682",
            secondary: "#006a61",
            error: "#ba1a1a",
            "secondary-fixed": "#89f5e7",
            "surface-container-low": "#f2f4f6",
            "on-error": "#ffffff",
            "error-container": "#ffdad6",
            "on-secondary-container": "#006f66",
          },
          fontFamily: {
            headline: ["Manrope"],
            body: ["Inter"],
            label: ["Inter"],
          },
          borderRadius: {
            DEFAULT: "0.25rem",
            lg: "0.5rem",
            xl: "0.75rem",
            full: "9999px",
          },
        },
      },
    };
  </script>
  <style>
    html {
      scroll-behavior: smooth;
    }

    .material-symbols-outlined {
      font-variation-settings:
        "FILL" 0,
        "wght" 400,
        "GRAD" 0,
        "opsz" 24;
    }

    .glass-panel {
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(12px);
    }

    .cta-gradient {
      background: linear-gradient(135deg, #00236f 0%, #1e3a8a 100%);
    }
  </style>
</head>

<body class="bg-surface font-body text-on-surface">


  <?php if (isset($_SESSION['success'])): ?>
    <script>
      alert("<?= $_SESSION['success'] ?>");
    </script>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>



  <!-- SideNavBar -->
  <aside
    class="h-screen w-64 border-r-0 fixed left-0 top-0 flex flex-col py-6 px-4 bg-slate-50 dark:bg-slate-900 font-manrope text-sm font-medium tracking-tight">
    <div class="mb-10 px-2">
      <h1 class="text-xl font-bold text-blue-900 tracking-tighter">Placement Tracker</h1>
      <p class="text-xs text-slate-500 font-medium">The Curated Architect</p>
    </div>
    <nav class="flex-1 space-y-2">
      <!-- Active: Dashboard -->
      <a
        class="flex items-center gap-3 px-4 py-3 text-blue-700 dark:text-teal-400 font-bold border-r-4 border-blue-700 dark:border-teal-400 bg-white dark:bg-slate-800 transition-all"
        href="dashboard.php">
        <span class="material-symbols-outlined text-lg" data-icon="dashboard">dashboard</span>
        <span>Dashboard</span>
      </a>
      <a
        class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-blue-900 dark:hover:text-slate-100 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200"
        href="application.php">
        <span class="material-symbols-outlined text-lg" data-icon="work">work</span>
        <span>Applications</span>
      </a>
      <a
        class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-blue-900 dark:hover:text-slate-100 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200"
        href="interview.php">
        <span class="material-symbols-outlined text-lg" data-icon="event">event</span>
        <span>Interviews</span>
      </a>
      <a
        class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-blue-900 dark:hover:text-slate-100 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-colors duration-200"
        href="profile.php">
        <span class="material-symbols-outlined text-lg" data-icon="person">person</span>
        <span>Profile</span>
      </a>
    </nav>
    <div class="mt-auto px-2">
      <button
        onclick="window.location.href='add-application.php'"
        class="w-full cta-gradient text-white py-3 rounded-xl font-semibold flex items-center justify-center gap-2 active:scale-95 transition-transform shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-sm">add</span>
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
  <!-- Main Content -->
  <main class="ml-64 pt-24 px-12 pb-12 min-h-screen">
    <!-- Header Section -->
    <header class="mb-10">
      <h2
        class="font-headline text-3xl font-extrabold text-on-surface tracking-tight mb-1">
        Portfolio Dashboard
      </h2>

      <p class="text-on-surface-variant font-label">
        Welcome <?= $_SESSION['name']; ?>! Your placement momentum is increasing.
      </p>
    </header>


    <!-- Stats Summary Row -->
    <section class="grid grid-cols-4 gap-8 mb-12">
      <!-- Total Applications -->
      <div
        class="bg-surface-container-lowest p-6 rounded-xl border-b-2 border-primary/5 hover:bg-surface-container-low transition-colors group">
        <div class="flex justify-between items-start mb-4">
          <div
            class="w-12 h-12 rounded-lg bg-primary/5 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
            <span class="material-symbols-outlined">folder_open</span>
          </div>

        </div>
        <p
          class="text-on-surface-variant text-xs font-bold uppercase tracking-widest mb-1">
          Total Applications
        </p>
        <p
          class="font-headline text-4xl font-extrabold text-primary tracking-tighter">

          <?= $app_count ?>

        </p>
      </div>
      <!-- Interviews Scheduled -->
      <div
        class="bg-surface-container-lowest p-6 rounded-xl border-b-2 border-primary/5 hover:bg-surface-container-low transition-colors group">
        <div class="flex justify-between items-start mb-4">
          <div
            class="w-12 h-12 rounded-lg bg-secondary-container/20 flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-white transition-all">
            <span class="material-symbols-outlined">schedule</span>
          </div>
          <div class="flex -space-x-2">
            <img
              alt="Team member"
              class="w-6 h-6 rounded-full border-2 border-white"
              data-alt="avatar of a team member"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuA3z2Gd_ah_tUgAVNrxsk2CeHsNybNge1FaZV2G00V8OdeA-2gKkn_ShTslD_dlS0rBTqPrWQAOAlvGiEM41qkM3fK746d0wYmkBhaNnZySaSJMUxwzf1b-whhZeUM5lnfqPGmgZrvNEie7dHDSV25hezO8r7w_eDqU152OuEKEnAd4RDXfPQXRcBgV4EdQF90lt31H9QrYVPdNtQvMb_f3aAJX9O2DnaPwrD2QGJ68NnNd0xoIlZmTZHPF133t4kQHS2yr_v71AtY" />
            <img
              alt="Hiring Manager"
              class="w-6 h-6 rounded-full border-2 border-white"
              data-alt="avatar of a hiring manager"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuDQCgjAxt_yjjFlph7n3rmoeUfWpdfLKSOEu7_QP_itV04iy-n4yjZGa3GEXYl2t7OUj2REGe_yy6edkHl3qrRasl9I39yZsyZ2k0pK9-Zr-U2cdhowV7BLpY2yYwsyvrCG2TGlwFc27MEu6VBhgfRSJFA19Ugzjg52M-S7wLaFZEqLQxak2FYkh1VeNDisPiJiEVDpP7sF9WupZh6pMLH_r46Qt4emHVtMig2MmrWwIyFBmtP7YxMBtBLqd-S15bFSV-JgIweXGpw" />
          </div>
        </div>
        <p
          class="text-on-surface-variant text-xs font-bold uppercase tracking-widest mb-1">
          Interviews
        </p>
        <p
          class="font-headline text-4xl font-extrabold text-primary tracking-tighter">

          <?= $interview_count  ?>

        </p>
      </div>
      <!-- Selected -->
      <div
        class="bg-surface-container-lowest p-6 rounded-xl border-b-2 border-secondary/20 hover:bg-surface-container-low transition-colors group relative overflow-hidden">
        <div
          class="absolute top-0 right-0 w-16 h-16 bg-secondary/5 rounded-bl-full pointer-events-none"></div>
        <div class="flex justify-between items-start mb-4">
          <div
            class="w-12 h-12 rounded-lg bg-secondary flex items-center justify-center text-white">
            <span
              class="material-symbols-outlined"
              style="font-variation-settings: 1">stars</span>
          </div>
        </div>
        <p
          class="text-on-surface-variant text-xs font-bold uppercase tracking-widest mb-1">
          Selected
        </p>
        <p
          class="font-headline text-4xl font-extrabold text-secondary tracking-tighter">
          <?= $selected_count  ?>
        </p>
      </div>
      <!-- Rejected -->
      <div
        class="bg-surface-container-lowest p-6 rounded-xl border-b-2 border-primary/5 hover:bg-surface-container-low transition-colors group">
        <div class="flex justify-between items-start mb-4">
          <div
            class="w-12 h-12 rounded-lg bg-error-container/30 flex items-center justify-center text-error group-hover:bg-error group-hover:text-white transition-all">
            <span class="material-symbols-outlined">cancel</span>
          </div>
        </div>
        <p
          class="text-on-surface-variant text-xs font-bold uppercase tracking-widest mb-1">
          Rejected
        </p>
        <p
          class="font-headline text-4xl font-extrabold text-primary tracking-tighter">
          <?= $rejected_count  ?>
        </p>
      </div>

    </section>
    <!-- Middle Row: Alerts & Progress -->
    <div class="grid grid-cols-12 gap-8 mb-12">
      <!-- Upcoming Interview Alert -->
      <section class="col-span-8">
        <div
          class="bg-primary p-8 rounded-xl text-white relative overflow-hidden shadow-xl shadow-primary/20">
          <!-- Decorative background -->
          <div
            class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
          <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-6">
              <div
                class="w-20 h-20 rounded-full glass-panel flex flex-col items-center justify-center text-primary">
                <span class="text-xs font-bold uppercase">
                  <?= isset($month) ? $month : ' ' ?>
                </span>

                <span class="text-2xl font-black">
                  <?= isset($day) ? $day : ' ' ?>
                </span>
              </div>
              <div>
                <?php if ($interview_data): ?>

                  <div class="flex items-center gap-2 mb-2">
                    <span class="bg-secondary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                      <?= $round_name ?>
                    </span>

                    <span class="text-white/70 text-sm font-label italic">
                      <?= $time_left ?>
                    </span>
                  </div>

                  <h3 class="text-2xl font-headline font-extrabold mb-1">
                    <?= $interview_data['round_name'] ?> at <?= $interview_data['company_name'] ?>
                  </h3>

                  <p class="text-white/80 font-label flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">schedule</span>
                    <?= $interview_date_display ?> • <?= $interview_data['mode'] ?>
                  </p>

                <?php else: ?>

                  <h3 class="text-xl font-bold">No upcoming interviews</h3>

                <?php endif; ?>
              </div>
              <button
                class="bg-white text-primary px-8 py-4 rounded-full font-bold text-sm hover:bg-secondary-fixed transition-all active:scale-95">
                Prep Materials
              </button>
            </div>
          </div>
        </div>
      </section>
      <!-- Success Rate Card -->
      <section class="col-span-4">
        <div
          class="bg-surface-container-lowest p-8 rounded-xl h-full flex flex-col items-center justify-center text-center">
          <p
            class="text-xs font-bold text-outline-variant uppercase tracking-widest mb-6">
            Success Probability
          </p>
          <div class="relative w-32 h-32 mb-4">
            <!-- SVG Progress Ring -->
            <svg class="w-full h-full transform -rotate-90">
              <circle
                class="text-surface-container-high"
                cx="64"
                cy="64"
                fill="transparent"
                r="58"
                stroke="currentColor"
                stroke-width="8"></circle>
              <circle
                class="text-secondary"
                cx="64"
                cy="64"
                fill="transparent"
                r="58"
                stroke="currentColor"
                stroke-dasharray="364.4"
                stroke-dashoffset="<?= $offset ?>"
                stroke-linecap="round"
                stroke-width="8">
              </circle>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <span class="text-3xl font-headline font-black text-primary"><?= $success_rate ?>%</span>
            </div>
          </div>
          <p class="text-sm font-medium text-on-surface-variant">
            Top 5% of candidate pool
          </p>
        </div>
      </section>
    </div>

    <?php

    if (pg_num_rows($result) == 0):
    ?>

      <div class="text-center py-12">
        <h3 class="text-xl font-bold text-primary mb-2">
          🚀 Welcome to Placement Tracker!
        </h3>
        <p class="text-on-surface-variant mb-4">
          You haven’t added any applications yet.
        </p>

        <button onclick="window.location.href='add-application.php'"
          class="bg-primary text-white px-6 py-3 rounded-full font-semibold">
          + Add Your First Application
        </button>
      </div>

    <?php else: ?>

      <!-- Recent Applications Table -->
      <section class="bg-surface-container-lowest rounded-xl p-8">
        <div class="flex justify-between items-center mb-8">
          <div>
            <h3 class="font-headline text-xl font-extrabold text-primary">
              Recent Applications
            </h3>
            <p class="text-sm text-outline-variant font-medium">
              Tracking your latest 5 submissions
            </p>
          </div>
          <button
            class="text-primary font-bold text-sm flex items-center gap-2 hover:gap-3 transition-all">
            View All History
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </button>
        </div>

        <?php while ($row = pg_fetch_assoc($result)): ?>

          <div class="grid grid-cols-12 items-center p-4 hover:bg-surface-container-low rounded-lg transition-all">

            <div class="col-span-4">
              <p class="font-bold"><?= $row['company_name'] ?></p>
              <p class="text-xs"><?= $row['job_role'] ?></p>
            </div>

            <div class="col-span-3 text-sm">
              <?= $row['date_applied'] ?>
            </div>

            <div class="col-span-3">
              <span class="bg-secondary px-4 py-1 rounded-full text-white text-xs">
                <?= $row['status'] ?>
              </span>
            </div>

          </div>

        <?php endwhile; ?>
      <?php endif; ?>
      </section>
  </main>

</body>

</html>