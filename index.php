<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studie Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Laad de Google Charts packages
        google.charts.load('current', {'packages':['corechart', 'line']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            // AJAX-aanroep voor formatieve toetsen
            $.ajax({
                url: 'formatief.php',
                dataType: 'json',
                success: function(data) {
                    var formatieveData = google.visualization.arrayToDataTable([
                        ['Task', 'Aantal'], 
                        ['Behaald', parseFloat(data.behaald)], 
                        ['Nog te doen', parseFloat(data.nog_te_doen)]
                    ]);
                    var options1 = {
                        title: 'Formatieve Toetsen',
                        pieHole: 0.4,
                        slices: {
                            0: { offset: 0.1, color: '#4CAF50' },
                            1: { color: '#FFC107' }
                        }
                    };
                    var chart1 = new google.visualization.PieChart(document.getElementById('donutchart1'));
                    chart1.draw(formatieveData, options1);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("Error in formatieve toetsen AJAX call:", textStatus, errorThrown);
                }
            });

            // AJAX-aanroep voor summatieve toetsen (donutgrafiek)
            $.ajax({
                url: 'summatief.php',
                dataType: 'json',
                success: function(data) {
                    var summatieveData = google.visualization.arrayToDataTable([
                        ['Task', 'Aantal'],
                        ['Behaald', parseFloat(data.behaald)],
                        ['Nog te doen', parseFloat(data.nog_te_doen)]
                    ]);
                    var options2 = {
                        title: 'Summatieve Toetsen',
                        pieHole: 0.4,
                        slices: {
                            0: { offset: 0.1, color: '#2196F3' },
                            1: { color: '#FF5722' }
                        }
                    };
                    var chart2 = new google.visualization.PieChart(document.getElementById('donutchart2'));
                    chart2.draw(summatieveData, options2);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("Error in summatieve toetsen AJAX call:", textStatus, errorThrown);
                }
            });

            $.ajax({
    url: 'summatiefcijfer.php',
    dataType: 'json',
    success: function(data) {
        var summatieveCijfersText = '<h3>Je cijfers:</h3><div class="cards-container">';
        data.forEach(function(row) {
            summatieveCijfersText += `
                <div class="card">
                    <p>${row.datum}</p>  <!-- Datum toegevoegd -->
                    <p>${row.score}</p>
                </div>`;
        });
        summatieveCijfersText += '</div>'; // Sluit de kaarten container
        $('#summatieve-cijfers-lijst').html(summatieveCijfersText);
    },
    error: function(jqXHR, textStatus, errorThrown) {
        console.log("Error in summatieve cijfers AJAX call:", textStatus, errorThrown);
    }
});

            // AJAX-aanroep voor de voortgangsgrafiek
            $.ajax({
                url: 'data.php',
                dataType: 'json',
                success: function(data) {
                    var voortgangData = new google.visualization.DataTable();
                    voortgangData.addColumn('string', 'Maand');
                    voortgangData.addColumn('number', 'Aantal behaalde toetsen en examens');
                    data.forEach(function(row) {
                        voortgangData.addRow([row.maand, parseInt(row.aantal)]);
                    });
                    var options3 = {
                        title: 'Voortgang 2023-2024',
                        curveType: 'function',
                        legend: { position: 'bottom' },
                        hAxis: {
                            title: 'Maand',
                        },
                        vAxis: {
                            title: 'Aantal toetsen en examens',
                            minValue: 0
                        },
                        width: 1550,
                        height: 500
                    };
                    var chart3 = new google.charts.Line(document.getElementById('curve_chart'));
                    chart3.draw(voortgangData, google.charts.Line.convertOptions(options3));
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("Error in voortgang AJAX call:", textStatus, errorThrown);
                }
            });
        }
    </script>
</head>
<body>
    <h1 id="welkom_text">Welkom Achraf</h1>
    <h2 id="cursus">Cursus: Software Development</h2>

    <div class="chart-container">
        <div id="donutchart1" class="chart"></div>
        <div id="donutchart2" class="chart"></div>
    </div>
    <div id="curve_chart" class="chart"></div>

    <div id="summatieve-cijfers-lijst" class="cards-container"></div>

</body>
</html>
