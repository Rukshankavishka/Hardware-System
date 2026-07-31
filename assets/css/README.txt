HARDWARE SYSTEM — CSS UPGRADE
==============================

WHAT CHANGED
------------
1. variables.css (NEW)
   - Every color in the system now lives here as a CSS variable
     (--primary, --success, --danger, --warning, --text-dark, etc.)
   - Built from your palette: #0F3D8D #2563EB #F8FAFC #FFFFFF
     #1E293B #16A34A #EA580C #DC2626
   - Want to re-theme the whole system later? Change it here once.

2. components.css (NEW)
   - A shared library: .btn, .badge, .card, .data-table, .form-control,
     .modal, .page-header, .empty-state, scrollbar + focus styles.
   - Use these classes in NEW pages so every module looks consistent
     instead of every page re-inventing its own button/table style.

3. style.css, dashboard.css, login.css — FULLY REDESIGNED
   - Sidebar: left accent bar on active item, section labels support,
     smoother hover, cleaner collapse behaviour.
   - Navbar: notification dot, focus ring on search, tidier spacing.
   - Dashboard: card accent bar + soft brand-colour glow, better hierarchy.
   - Login: fully responsive now (previous version broke on mobile —
     fixed width/height, no media query). Same visual style, cleaned up.

4. All other files (accounts, billing, categories, customers, employees,
   fleet, report, settings, stock, suppliers) — every hardcoded colour
   replaced with the matching variable, so the ENTIRE system now shares
   one consistent palette. A few module-specific accent colours (chart
   colours, status dots not in your 8-colour palette) were left as-is
   on purpose — those are intentional variety, not inconsistency.

HOW TO LOAD (add variables.css FIRST, always)
----------------------------------------------
<link rel="stylesheet" href="assets/css/variables.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/dashboard.css">
<!-- + whichever module file the page needs -->

Each module file already has @import url("variables.css") at the top,
so even if you forget the <link> tag above, the colours will still
resolve as long as variables.css sits in the same folder.

NEXT STEP (optional)
---------------------
The 10 module files (stock, billing, accounts, customers, suppliers,
employees, fleet, settings, report, categories) now share the palette
but still have their OLD layout/spacing per file. If you want those
rebuilt to the same polish level as dashboard.css/style.css (consistent
card radius, button style, table style using components.css), send them
one at a time and I'll rebuild each properly — doing all ~4,500 lines
of module CSS by hand in one go would be too rushed to be reliable.
