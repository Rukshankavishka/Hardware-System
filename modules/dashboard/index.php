<?php

$page = "dashboard";

require_once "../../includes/auth.php";
require_once "../../config/database.php";


$database = new Database();
$pdo = $database->connect();

// Dashboard Filter

$view = $_GET['view'] ?? 'today';


if($view == 'month'){

    $dateCondition = "
    MONTH(created_at)=MONTH(CURDATE())
    AND YEAR(created_at)=YEAR(CURDATE())
    ";

}else{

    $dateCondition = "
    DATE(created_at)=CURDATE()
    ";

}


// =======================
// TODAY SALES
// =======================

$stmt = $pdo->query("
    SELECT SUM(total_amount) AS sales
    FROM invoices
    WHERE $dateCondition
    AND status='completed'
");

$todaySales = $stmt->fetch(PDO::FETCH_ASSOC)['sales'] ?? 0;



// =======================
// TODAY BILLS
// =======================

$stmt = $pdo->query("
    SELECT COUNT(invoice_id) AS bills
    FROM invoices
    WHERE $dateCondition
    AND status='completed'
");

$todayBills = $stmt->fetch(PDO::FETCH_ASSOC)['bills'] ?? 0;



// =======================
// TOTAL PRODUCTS
// =======================

$stmt = $pdo->query("
    SELECT COUNT(product_id) AS total
    FROM products
    WHERE status='active'
");

$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;



// =======================
// STOCK VALUE
// =======================

$stmt = $pdo->query("
    SELECT SUM(buying_price * stock_qty) AS value
    FROM products
");

$stockValue = $stmt->fetch(PDO::FETCH_ASSOC)['value'] ?? 0;



// =======================
// LOW STOCK
// stock qty <=10
// =======================

$stmt = $pdo->query("
    SELECT COUNT(product_id) AS low
    FROM products
    WHERE stock_qty <=10
    AND stock_qty >0
");

$lowStock = $stmt->fetch(PDO::FETCH_ASSOC)['low'] ?? 0;



// =======================
// OUT OF STOCK
// =======================

$stmt = $pdo->query("
    SELECT COUNT(product_id) AS outstock
    FROM products
    WHERE stock_qty=0
");

$outStock = $stmt->fetch(PDO::FETCH_ASSOC)['outstock'] ?? 0;


// =======================
// SALES CHART DATA
// =======================

$chartLabels = [];
$chartData = [];


if($view == 'today'){

    $stmt = $pdo->query("
        SELECT 
        HOUR(created_at) AS hour,
        SUM(total_amount) AS total

        FROM invoices

        WHERE DATE(created_at)=CURDATE()
        AND status='completed'

        GROUP BY HOUR(created_at)

        ORDER BY hour
    ");


    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        $chartLabels[] = $row['hour']." :00";
        $chartData[] = $row['total'];

    }


}else{


    $stmt = $pdo->query("
        SELECT 
        DAY(created_at) AS day,
        SUM(total_amount) AS total

        FROM invoices

        WHERE MONTH(created_at)=MONTH(CURDATE())
        AND YEAR(created_at)=YEAR(CURDATE())
        AND status='completed'

        GROUP BY DAY(created_at)

        ORDER BY day
    ");



    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        $chartLabels[] = "Day ".$row['day'];
        $chartData[] = $row['total'];

    }

}

// =======================
// SALES VS PROFIT
// =======================

$stmt = $pdo->query("

SELECT

SUM(quantity * selling_price) AS sales,

SUM(profit) AS profit

FROM invoice_items

WHERE MONTH(created_at)=MONTH(CURDATE())

AND YEAR(created_at)=YEAR(CURDATE())

");


$profitData = $stmt->fetch(PDO::FETCH_ASSOC);


$totalChartSales = $profitData['sales'] ?? 0;

$totalChartProfit = $profitData['profit'] ?? 0;


// =======================
// IN STOCK
// =======================

$stmt = $pdo->query("
    SELECT COUNT(product_id) AS instock
    FROM products
    WHERE stock_qty >10
");

$inStock = $stmt->fetch(PDO::FETCH_ASSOC)['instock'] ?? 0;



require_once "../../includes/header.php";
require_once "../../includes/sidebar.php";
require_once "../../includes/navbar.php";

?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">

<div class="dashboard-container">


<!-- Header -->

<div class="dashboard-header">

<h2>
<i class="fa-solid fa-chart-line"></i>
Business Dashboard
</h2>

</div>

<form method="GET" class="dashboard-filter">

    <select name="view">

        <option value="today"
        <?=($view=='today')?'selected':'';?>>
        Today
        </option>

        <option value="month"
        <?=($view=='month')?'selected':'';?>>
        This Month
        </option>

    </select>

    <button type="submit">
    View
    </button>

</form>

<br>
<!-- Cards -->

<div class="dashboard-cards">

    <div class="dash-card blue">

        <i class="fa-solid fa-money-bill-trend-up"></i>

        <div>

            <h5>Today's Sales</h5>

            <h2>
            Rs. <?=number_format($todaySales,2);?>
            </h2>

        </div>

    </div>


    <div class="dash-card green">

        <i class="fa-solid fa-file-invoice"></i>

        <div>

            <h5>Today's Bills</h5>

            <h2>
            <?=$todayBills;?>
            </h2>

        </div>

    </div>


    <div class="dash-card purple">

        <i class="fa-solid fa-box"></i>

        <div>

            <h5>Total Products</h5>

            <h2>
            <?=$totalProducts;?>
            </h2>

        </div>

    </div>


    <div class="dash-card orange">

        <i class="fa-solid fa-warehouse"></i>

        <div>

            <h5>Stock Value</h5>

            <h2>
            Rs. <?=number_format($stockValue,2);?>
            </h2>

        </div>

    </div>


    <div class="dash-card red">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <div>
            <h5>Low Stock</h5>

            <h2>
            <?=$lowStock;?>
            </h2>

        </div>

    </div>


    <div class="dash-card dark">

        <i class="fa-solid fa-ban"></i>

        <div>

            <h5>Out Of Stock</h5>

            <h2>
            <?=$outStock;?>
            </h2>

        </div>

    </div>

</div>



<!-- Stock Overview -->


        <div class="stock-overview">


            <h3>
            <i class="fa-solid fa-boxes-stacked"></i>
            Stock Overview
            </h3>



            <div class="stock-status">

                <div>
                    <h2><?=$inStock;?></h2>
                    <p>In Stock</p>
                </div>


                <div>
                    <h2><?=$lowStock;?></h2>
                    <p>Low Stock</p>
                </div>



                <div>
                    <h2><?=$outStock;?></h2>
                    <p>Out Of Stock</p>
                </div>

            </div>

        </div>

</div>

    <div class="chart-row">

    <!-- Sales Chart -->

        <div class="chart-card">
            <h3>
            <i class="fa-solid fa-chart-line"></i>
            Sales Overview
            </h3>

            <canvas id="salesChart"></canvas>
        </div>

    <!-- Sales Profit Chart -->

        <div class="chart-card">

            <h3>
            <i class="fa-solid fa-chart-column"></i>
            Sales vs Profit
            </h3>
            <canvas id="profitChart"></canvas>

        </div>

    </div>


<script>

    const ctx = document.getElementById('salesChart');
        new Chart(ctx, {
        type:'line',
        data:{
        labels: <?=json_encode($chartLabels);?>,
        datasets:[{
        label:'Sales',
        data: <?=json_encode($chartData);?>,
        borderWidth:3,
        tension:0.4

        }]

        },


            options:{
            responsive:true,
            plugins:{
            legend:{
            display:true
            }
            }
            }
            });

    const profitCtx = document.getElementById('profitChart');

        new Chart(profitCtx, {

        type:'bar',

        data:{

        labels:[
        'Sales',
        'Profit'
        ],

        datasets:[{

        label:'Amount',

        data:[

        <?= $totalChartSales ?>,

        <?= $totalChartProfit ?>

        ],

        borderWidth:1

        }]

        },
        options:{
        responsive:true,
        maintainAspectRatio:false
        }
        });


</script>

<?php

require_once "../../includes/footer.php";

?>