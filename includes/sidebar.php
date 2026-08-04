<div class="sidebar">

    <!-- Logo -->
    <div class="logo-section">
            <img src="../../assets/images/logo.png" alt="Logo" class="logo-section img"> 
            <div class="brand-name">
                <span class="main-title">THISARU</span>
                <span class="sub-title">HARDWARE SHOP</span>
            </div>
        </div>

    <!-- Menu -->
    <ul class="menu">

        <li class="<?= ($page == 'dashboard') ? 'active' : ''; ?>">
            <a href="/hardware_System/modules/dashboard/index.php">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>
        </li>

       <li class="<?= ($page == 'products') ? 'active' : ''; ?>">
            <a href="/hardware_System/modules/products/index.php">
                <i class="fa-solid fa-box"></i>
                <span>Products</span>
            </a>
        </li>

        <li class="<?= ($page == 'categories') ? 'active' : ''; ?>">
            <a href="/hardware_System/modules/categories/index.php">
                <i class="fa-solid fa-layer-group"></i>
                <span>Categories</span>
            </a>
        </li>

        
        <li class="<?= ($page == 'billing') ? 'active' : ''; ?>">
            <a href="/hardware_System/modules/billing/index.php">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Billing</span>
            </a>
        </li>

        <li class="<?= ($page == 'invoices') ? 'active' : ''; ?>">
            <a href="../../modules/invoices/index.php" >
                <i class="fas fa-file-invoice"></i>
                <span>Invoices</span>
            </a>
        </li>

        <li class="<?= ($page == 'customers') ? 'active' : ''; ?>">
            <a href="/hardware_system/modules/customers/index.php">
                <i class="fa-solid fa-users"></i>
                <span>Customers</span>
            </a>
        </li>

        <li  class="<?= ($page == 'suppliers') ? 'active' : ''; ?>">
            <a href="/hardware_system/modules/suppliers/index.php">
                <i class="fa-solid fa-truck"></i>
                <span>Suppliers</span>
            </a>
        </li>

        <li class="<?= ($page == 'fleet') ? 'active' : ''; ?>">
            <a href="/hardware_system/modules/fleet/index.php">
                <i class="fa-solid fa-truck-fast"></i>
                <span>Fleet Management</span>
            </a>
        </li>

        <li class="<?= ($page == 'employees') ? 'active' : ''; ?>">
            <a href="/hardware_system/modules/employees/index.php">
                <i class="fa-solid fa-users"></i>
                <span>Employees</span>
            </a>
        </li>

        <li class="<?= ($page == 'stock') ? 'active' : ''; ?>">
            <a href="/hardware_system/modules/stock/index.php">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Stock</span>
            </a>
        </li>

        <li class="<?= ($page == 'accounts') ? 'active' : ''; ?>" >
            <a href="/hardware_system/modules/accounts/index.php">
                <i class="fa-solid fa-sack-dollar"></i>
                <span>Accounts</span>
            </a>
        </li>

        <li  class="<?= ($page == 'reports') ? 'active' : ''; ?>">
            <a href="/hardware_system/modules/reports/index.php">
                <i class="fa-solid fa-chart-line"></i>
                <span>Reports</span>
            </a>
        </li>

         <li  class="<?= ($page == 'settings') ? 'active' : ''; ?>">
            <a href="/hardware_system/modules/settings/index.php">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
        </li>

    </ul>
    <!-- Logout -->
    <div class="logout">
        <a href="/hardware_system/logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>

</div>