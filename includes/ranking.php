<?php
session_start();
include 'mysql.php';

if (strtotime($_COOKIE["StartEvent"])) {

    if (!isset($_SESSION["LastID"])) {
        $_SESSION["LastID"] = 27961486;
    }

    $logs = array();
    $stmt = $dbll->prepare('SELECT * FROM logs WHERE id > ?  AND `insert` > ? AND TYPE = "killed"');
    $stmt->bind_param("is", $_SESSION["LastID"], $_COOKIE["StartEvent"]);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

        $logs .= $row["message"];
    }
}


$re = '/(wurde von .* (?<STEAMID64>[^\/][0-9]{8,}))/';
preg_match_all($re, $logs, $matches, PREG_SET_ORDER, 0);

$values = array();
foreach ($matches as $item => $val) {
    $values[] = trim($val[0]);
}
$ArrayCount = array_count_values($values);
print_r($ArrayCount);

?>

<div class="pricing-table princig-table-flat row no-gutters mt-3 mb-3" style="margin-left: 20%;">
    <div class="col-lg-3 col-sm-6">
        <div class="plan">
            <h3>Enterprise<span>$59</span></h3>
            <ul>
                <li><strong>10GB</strong> Disk Space</li>
                <li><strong>100GB</strong> Monthly Bandwidth</li>
                <li><strong>20</strong> Email Accounts</li>
                <li><strong>Unlimited</strong> subdomains</li>
                <li><a class="btn btn-primary" href="#">Get Started Now!</a></li>
            </ul>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 text-center">
        <div class="plan most-popular">
            <h3 style="background-color: #9c1f00 !IMPORTANT; color:#fff !IMPORTANT;">Professional<em class="desc">Most Popular</em><span>$29</span></h3>
            <ul>
                <li><strong>5GB</strong> Disk Space</li>
                <li><strong>50GB</strong> Monthly Bandwidth</li>
                <li><strong>10</strong> Email Accounts</li>
                <li><strong>Unlimited</strong> subdomains</li>
                <li><a class="btn btn-primary" href="#">Get Started Now!</a></li>
            </ul>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="plan">
            <h3>Standard<span>$17</span></h3>
            <ul>
                <li><strong>3GB</strong> Disk Space</li>
                <li><strong>25GB</strong> Monthly Bandwidth</li>
                <li><strong>5</strong> Email Accounts</li>
                <li><strong>Unlimited</strong> subdomains</li>
                <li><a class="btn btn-primary" href="#">Get Started Now!</a></li>
            </ul>
        </div>
    </div>
</div>

<?php
$dbll->close();
?>