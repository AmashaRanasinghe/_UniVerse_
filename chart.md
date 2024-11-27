# to add charts - opt

<!-- <?php
use \koolreport\datasources\PdoDataSource;
use \koolreport\widgets\google\ColumnChart;

$connection = array(
    "connectionString" => "mysql:host=localhost;dbname=sales",
    "username" => "root",
    "password" => "",
    "charset" => "utf8"
);

ColumnChart::create([
    "dataSource" => (new PdoDataSource($connection))->query("
        SELECT customerName, SUM(amount) as total
        FROM orders
        GROUP BY customerName
    ")
]); -->
