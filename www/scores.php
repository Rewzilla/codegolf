<?php
if(!defined("IN_MAIN"))
	error("Invalid access");

$res = $db->query("SELECT username FROM submissions GROUP BY username ORDER BY username");
$usernames = $res->fetch_all();
?>

<div style="width:75%;margin: auto">
    <canvas id="canvas"></canvas>
</div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script>
    function randInt(max_val) {
        return Math.floor(Math.random() * max_val);
    }
    var config = {
        type: 'line',
        data: {
            datasets: [
            <?php foreach ($usernames as $username) {
                $username = $username[0];
            ?>
            {
                label: '<?php echo $username; ?>',
                <?php
                    $red = rand(0, 255);
                    $green = rand(0, 255);
                    $blue = rand(0, 255);
                    echo "backgroundColor: 'rgb(" . $red . ", " . $green . ", " . $blue . ")',\n";
                    echo "borderColor: 'rgb(" . $red . ", " . $green . ", " . $blue . ")',\n";
                ?>
                data: [
                    <?php
                        $scores = $db->prepare("SELECT time, score FROM submissions WHERE username=?");
                        $scores->bind_param("s", $username);
                        $scores->execute();
                        $scores->bind_result($time, $score);
                        while ($scores->fetch()) {
                    ?>
                    { x:'<?php echo $time; ?>', y:<?php echo $score; ?> },
                    <?php
                        }
                        $scores->close();
                    ?>
                ],
                fill: false,
            },
            <?php } ?>
            ]
        },
        options: {
            responsive: true,
            layout: {
                padding: {
                    top:0,
                }
            },
            title: {
                display: true,
                text: 'Score Trends',
                fontSize: 25,
            },
            legend: {
                display: true,
                position: 'right'
            },
            tooltips: {
                enabled: true,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            elements: {
                line: {
                    tension: 0
                }
            },
            scales: {
                xAxes: [{
                    display: true,
                    type: 'time',
                    scaleLabel: {
                        display: false,
                        labelString: ''
                    },
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Bytes'
                    },
                    ticks: {
                        maxTicksLimit:20,
                        min:0,
                    }
                }]
            }
        }
    };

    window.onload = function() {
        var ctx = document.getElementById('canvas').getContext('2d');
        window.myLine = new Chart(ctx, config);
    };
</script>
