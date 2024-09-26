<!DOCTYPE html> <!-- HTML5 document type declaration -->
<html lang="nl"> <!-- Begin HTML document with Dutch language setting -->
<head> <!-- Begin head section -->
    <meta charset="UTF-8"> <!-- Character encoding set to UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design meta tag -->
    <title>Studie Dashboard</title> <!-- Title of the webpage -->
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS file -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> <!-- jQuery library -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> <!-- Google Charts library -->
    <script type="text/javascript">
        // Laad de Google Charts packages
        google.charts.load('current', {'packages':['corechart', 'line']}); // Load Google Charts packages
        google.charts.setOnLoadCallback(drawCharts); // Set callback to draw charts when loaded

        function drawCharts() { // Function to draw charts
            // AJAX-aanroep voor formatieve toetsen
            $.ajax({
                url: 'formatief.php', // URL to fetch data for formative tests
                dataType: 'json', // Expected data type is JSON
                success: function(data) { // On successful AJAX call
                    var formatieveData = google.visualization.arrayToDataTable([ // Convert data to Google Charts format
                        ['Task', 'Aantal'], // Column headers
                        ['Behaald', parseFloat(data.behaald)], // Data row for achieved tasks
                        ['Nog te doen', parseFloat(data.nog_te_doen)] // Data row for tasks to be done
                    ]);
                    var options1 = { // Options for the chart
                        title: 'Formatieve Toetsen', // Chart title
                        pieHole: 0.4, // Donut chart hole size
                        slices: {
                            0: { offset: 0.1, color: '#4CAF50' }, // Styling for the first slice
                            1: { color: '#FFC107' } // Styling for the second slice
                        }
                    };
                    var chart1 = new google.visualization.PieChart(document.getElementById('donutchart1')); // Create new PieChart
                    chart1.draw(formatieveData, options1); // Draw the chart with data and options
                },
                error: function(jqXHR, textStatus, errorThrown) { // On AJAX call error
                    console.log("Error in formatieve toetsen AJAX call:", textStatus, errorThrown); // Log error
                }
            });

            // AJAX-aanroep voor summatieve toetsen (donutgrafiek)
            $.ajax({
                url: 'summatief.php', // URL to fetch data for summative tests
                dataType: 'json', // Expected data type is JSON
                success: function(data) { // On successful AJAX call
                    var summatieveData = google.visualization.arrayToDataTable([ // Convert data to Google Charts format
                        ['Task', 'Aantal'], // Column headers
                        ['Behaald', parseFloat(data.behaald)], // Data row for achieved tasks
                        ['Nog te doen', parseFloat(data.nog_te_doen)] // Data row for tasks to be done
                    ]);
                    var options2 = { // Options for the chart
                        title: 'Summatieve Toetsen', // Chart title
                        pieHole: 0.4, // Donut chart hole size
                        slices: {
                            0: { offset: 0.1, color: '#2196F3' }, // Styling for the first slice
                            1: { color: '#FF5722' } // Styling for the second slice
                        }
                    };
                    var chart2 = new google.visualization.PieChart(document.getElementById('donutchart2')); // Create new PieChart
                    chart2.draw(summatieveData, options2); // Draw the chart with data and options
                },
                error: function(jqXHR, textStatus, errorThrown) { // On AJAX call error
                    console.log("Error in summatieve toetsen AJAX call:", textStatus, errorThrown); // Log error
                }
            });

            // AJAX-aanroep voor summatieve cijfers
            $.ajax({
                url: 'summatiefcijfer.php', // URL to fetch data for summative scores
                dataType: 'json', // Expected data type is JSON
                success: function(data) { // On successful AJAX call
                    var summatieveCijfersText = '<h3>Je cijfers:</h3><div class="cards-container">'; // Initialize HTML for scores
                    data.forEach(function(row) { // Loop through each row of data
                        summatieveCijfersText += `
                            <div class="card">
                                <p>${row.datum}</p>  <!-- Datum toegevoegd -->
                                <p>${row.vakken}</p>  <!-- Vakken toegevoegd -->
                                <p>${row.score}</p>
                            </div>`; // Append each score to the HTML
                    });
                    summatieveCijfersText += '</div>'; // Close the cards container
                    $('#summatieve-cijfers-lijst').html(summatieveCijfersText); // Insert HTML into the DOM
                },
                error: function(jqXHR, textStatus, errorThrown) { // On AJAX call error
                    console.log("Error in summatieve cijfers AJAX call:", textStatus, errorThrown); // Log error
                }
            });

            // AJAX-aanroep voor de voortgangsgrafiek
            $.ajax({
                url: 'data.php', // URL to fetch data for progress chart
                dataType: 'json', // Expected data type is JSON
                success: function(data) { // On successful AJAX call
                    var voortgangData = new google.visualization.DataTable(); // Create new DataTable
                    voortgangData.addColumn('string', 'Maand'); // Add column for month
                    voortgangData.addColumn('number', 'Aantal behaalde toetsen en examens'); // Add column for number of tests and exams
                    data.forEach(function(row) { // Loop through each row of data
                        voortgangData.addRow([row.maand, parseInt(row.aantal)]); // Add row to DataTable
                    });
                    var options3 = { // Options for the chart
                        title: 'Voortgang 2023-2024', // Chart title
                        curveType: 'function', // Curve type for the line chart
                        legend: { position: 'bottom' }, // Legend position
                        hAxis: {
                            title: 'Maand', // Horizontal axis title
                        },
                        vAxis: {
                            title: 'Aantal toetsen en examens', // Vertical axis title
                            minValue: 0 // Minimum value for vertical axis
                        },
                    };
                    var chart3 = new google.charts.Line(document.getElementById('curve_chart')); // Create new Line chart
                    chart3.draw(voortgangData, google.charts.Line.convertOptions(options3)); // Draw the chart with data and options
                },
                error: function(jqXHR, textStatus, errorThrown) { // On AJAX call error
                    console.log("Error in voortgang AJAX call:", textStatus, errorThrown); // Log error
                }
            });
        }
    </script> <!-- End of script section -->
</head> <!-- End of head section -->
<body> <!-- Begin body section -->
    <h1 id="welkom_text">Welkom Achraf</h1> <!-- Welcome message -->
    <h2 id="cursus">Cursus: Software Development</h2> <!-- Course title -->

    <div class="chart-container"> <!-- Container for charts -->
        <div id="donutchart1" class="chart"></div> <!-- Placeholder for first donut chart -->
        <div id="donutchart2" class="chart"></div> <!-- Placeholder for second donut chart -->
    </div>
    <div id="curve_chart" class="chart"></div> <!-- Placeholder for line chart -->

    <div id="summatieve-cijfers-lijst" class="cards-container"></div> <!-- Placeholder for summative scores list -->
</body> <!-- End body section -->
</html> <!-- End HTML document -->
