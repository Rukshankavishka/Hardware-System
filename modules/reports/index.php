<?php

$page = "reports";

require_once "../../config/database.php";

$database = new Database();
$pdo = $database->connect();

$month = $_GET['month'] ?? date('m');
$year  = $_GET['year'] ?? date('Y');

$stmt = $pdo->prepare("
    SELECT 
        SUM(total_amount) AS sales,
        COUNT(invoice_id) AS bills
    FROM invoices
    WHERE MONTH(created_at) = ?
      AND YEAR(created_at) = ?
      AND status = 'completed'
");

$stmt->execute([$month, $year]);

$report = $stmt->fetch(PDO::FETCH_ASSOC);

$totalSales = $report['sales'] ?? 0;
$totalBills = $report['bills'] ?? 0;

// Total Income

$stmt = $pdo->prepare("

SELECT SUM(amount) AS income

FROM transactions

WHERE type='IN'

AND MONTH(transaction_date)=?

AND YEAR(transaction_date)=?

");

$stmt->execute([

$month,

$year

]);

$income = $stmt->fetch(PDO::FETCH_ASSOC);

$totalIncome = $income['income'] ?? 0;

// Total Profit

$stmt = $pdo->prepare("
SELECT SUM(profit) AS total_profit
FROM invoice_items
WHERE MONTH(created_at)=?
AND YEAR(created_at)=?
");

$stmt->execute([
    $month,
    $year
]);

$profit = $stmt->fetch(PDO::FETCH_ASSOC);

$totalProfit = $profit['total_profit'] ?? 0;


// Total Products

$stmt = $pdo->query("
SELECT COUNT(product_id) AS total_products
FROM products
WHERE status='active'
");

$product = $stmt->fetch(PDO::FETCH_ASSOC);

$totalProducts = $product['total_products'] ?? 0;


// Total Stock Value

$stmt = $pdo->query("
SELECT SUM(buying_price * stock_qty) AS stock_value
FROM products
");

$stock = $stmt->fetch(PDO::FETCH_ASSOC);

$totalStockValue = $stock['stock_value'] ?? 0;


$stmt = $pdo->prepare("
SELECT
DAY(created_at) AS day,
SUM(total_amount) AS sales
FROM invoices
WHERE MONTH(created_at)=?
AND YEAR(created_at)=?
AND status='completed'
GROUP BY DAY(created_at)
ORDER BY DAY(created_at)
");

$stmt->execute([$month,$year]);

$labels = [];
$data = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $labels[] = $row['day'];
    $data[]   = $row['sales'];
}

// Income & Expense

$stmt = $pdo->prepare("
SELECT
type,
SUM(amount) AS total
FROM transactions
WHERE MONTH(transaction_date)=?
AND YEAR(transaction_date)=?
GROUP BY type
");

$stmt->execute([$month,$year]);

$totalIncomeChart = 0;
$totalExpenseChart = 0;

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    if($row['type'] == 'IN'){
        $totalIncomeChart = $row['total'];
    }

    if($row['type'] == 'OUT'){
        $totalExpenseChart = $row['total'];
    }

}

// Stock Status

$inStock = $pdo->query("
SELECT COUNT(*) FROM products
WHERE stock_qty > 10
")->fetchColumn();

$lowStock = $pdo->query("
SELECT COUNT(*) FROM products
WHERE stock_qty > 0
AND stock_qty <= 10
")->fetchColumn();

$outStock = $pdo->query("
SELECT COUNT(*) FROM products
WHERE stock_qty = 0
")->fetchColumn();


require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../../assets/css/report.css">

<div class="report-container">

    <div class="report-header">

        <h2>
            <i class="fa-solid fa-chart-line"></i>
            Business Reports
        </h2>

        <form method="GET" class="month-filter">

            <select name="month">
                <?php
                $currentMonth = $_GET['month'] ?? date('m');

                for ($i = 1; $i <= 12; $i++) {
                    $selected = ($currentMonth == $i) ? "selected" : "";
                    echo "<option value='$i' $selected>" . date("F", mktime(0, 0, 0, $i, 1)) . "</option>";
                }
                ?>
            </select>

            <select name="year">
                <?php
                $currentYear = $_GET['year'] ?? date('Y');

                for ($y = 2025; $y <= 2030; $y++) {
                    $selected = ($currentYear == $y) ? "selected" : "";
                    echo "<option value='$y' $selected>$y</option>";
                }
                ?>
            </select>

              <button type="submit">
                <i class="fa-solid fa-chart-column"></i>
                View Report
            </button>

        </form>

    </div><!-- /.report-header (this was missing before, which broke your whole layout) -->

    <!-- Cards -->
    <div class="report-cards">

        <div class="report-card sales-card">
            <i class="fa-solid fa-money-bill-trend-up"></i>
            <h5>Total Sales</h5>
            <h3>Rs. <?= number_format($totalSales, 2); ?></h3>
        </div>

        <div class="report-card income-card">
            <i class="fa-solid fa-wallet"></i>
            <h5>Total Income</h5>
            <h3><?= number_format($totalIncome,2); ?></h3>
        </div>

        <div class="report-card profit-card">
            <i class="fa-solid fa-chart-pie"></i>
            <h5>Total Profit</h5>
            <h3> Rs. <?= number_format($totalProfit,2); ?></h3>
        </div>

        <div class="report-card bill-card">
            <i class="fa-solid fa-file-invoice"></i>
            <h5>Total Bills</h5>
            <h3><?= $totalBills; ?></h3>
        </div>

        <div class="report-card product-card">
            <i class="fa-solid fa-box"></i>
            <h5>Total Products</h5>
            <h3><?= $totalProducts; ?></h3>
        </div>

        <div class="report-card stock-card">
            <i class="fa-solid fa-warehouse"></i>
            <h5>Stock Value</h5>
            <h3>Rs. <?= number_format($totalStockValue,2); ?></h3>
        </div>

    </div>

    <!-- Chart Area -->
    <div class="chart-box">
        <h4>Monthly Sales Overview</h4>
        <canvas id="salesChart" class="sales-chart"></canvas>
    </div>

    <div class="chart-row">

        <div class="chart-box">
            <h4>Income vs Expense</h4>
            <canvas id="incomeChart"></canvas>
        </div>

        <div class="chart-box">
            <h4>Stock Status</h4>
            <canvas id="stockChart"></canvas>
        </div>

    </div>

</div>

<script>

const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels); ?>,

        datasets:[{
            label:'Daily Sales',
            data: <?= json_encode($data); ?>,
            borderWidth:3,
            tension:0.4,
            fill:false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Income vs Expense Bar Chart
const incomeCtx = document.getElementById('incomeChart');

new Chart(incomeCtx,{

    type:'bar',

    data:{

        labels:['Income','Expense'],

        datasets:[{

            label:'Amount',

            data:[
                <?= $totalIncomeChart; ?>,
                <?= $totalExpenseChart; ?>
            ],

            borderWidth:1

        }]

    },

    options:{

        responsive:true,

        maintainAspectRatio:false

    }

});

// Stock Status Pie Chart
const stockCtx = document.getElementById('stockChart');

new Chart(stockCtx,{

    type:'doughnut',

    data:{

        labels:[
            'In Stock',
            'Low Stock',
            'Out Of Stock'
        ],

        datasets:[{

            data:[
                <?= $inStock; ?>,
                <?= $lowStock; ?>,
                <?= $outStock; ?>
            ],

            backgroundColor:[
                '#22c55e',
                '#f59e0b',
                '#ef4444'
            ],

            borderWidth:2

        }]

    },

    options:{

        responsive:true,

        maintainAspectRatio:false,

        plugins:{
            legend:{
                position:'bottom'
            }
        }

    }

});

</script>

<?php
require_once "../../includes/footer.php";
?>