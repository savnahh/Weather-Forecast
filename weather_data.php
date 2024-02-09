<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather&family=Montserrat&family=Sacramento&display=swap" rel="stylesheet">
    <title>Weather History of New York</title>

    <style>
    body{
        background-image: url("pexels-magda-ehlers-2114014.png");

    }
    
    table {
        font-family: Arial, sans-serif;
        border: solid black;
        width: 100%;
        margin: 20px 0;
        box-shadow: 10px;
    }

    th, td {
        border: solid black;
        text-align: center;
        padding: 12px;
    }

    th {
        background: linear-gradient(#66347F, #9E4784);
        font-weight: bold;
        color: black;
    }

    tr {
        background: #D27685;
    }

    h1 {
        font-family: 'Sacramento', cursive;
        font-size: 5rem;
        text-align: center;
        margin: 30px 0;
        color: black;
    }
    </style>

</head>
<body>
<h1>Weather History</h1>
<table id="weather-history-table">
    <tr>
        <th>City</th>
        <th>Date/Time</th>
        <th>Weather Icon</th>
        <th>Weather Condition</th>
        <th>Temperature (°C)</th>
        <th>Rainfall</th>
        <th>Wind Speed (m/s)</th>
        <th>Humidity (%)</th>
        <th>Pressure (hPa)</th> 
    </tr>
    
    <?php
    //connect to database
    $mysqli = new mysqli("sql100.epizy.com", "epiz_34247396", "oyMNfRWVqbSaQ", "epiz_34247396_2330777_weather");
    //URL for openweathermap API call
    $url = 'https://api.openweathermap.org/data/2.5/weather?q=new%20york&appid=9d36297c63748b8d6f3c1e1652b35069';
    //get data from openweathermap and store in JSON object
    $data = file_get_contents($url);
    $json = json_decode($data, true);

    //fetch required fields from JSON object
    $city_name = $json['name'];
    $dt = $json['dt'];
    $weather_icon = $json['weather'][0]['icon'];
    $weather_condition = $json['weather'][0]['main'];
    $temperature = $json['main']['temp'] - 273.15; // Convert Kelvin to Celsius
    $rainfall = isset($json['rain']['1h']) ? $json['rain']['1h'] : 0;
    $wind_speed = $json['wind']['speed'];
    $humidity = $json['main']['humidity'];
    $pressure = $json['main']['pressure'];
    
    //insert data into weather table
    mysqli_query($mysqli, "insert into weather_data(city_name, dt, weather_icon, weather_condition, temperature, rainfall, wind_speed, humidity, pressure) values('$city_name', $dt, '$weather_icon', '$weather_condition', $temperature, $rainfall, $wind_speed, $humidity, $pressure)");

    //build SQL query to retrieve weather of past week
    $sql = "SELECT * FROM weather_data WHERE dt >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 WEEK))";

    //execute SQL query and retrieve results
    $result = mysqli_query($mysqli, $sql);

    //create an array to store the weather history data
    $weatherHistory = [];

    //loop through results and store data in the array
    while ($row = mysqli_fetch_assoc($result)) {
        $weatherHistory[] = [
            'city_name' => $row['city_name'],
            'dt' => date('Y-m-d H:i:s', $row['dt']),
            'weather_icon' => $row['weather_icon'],
            'weather_condition' => $row['weather_condition'],
            'temperature' => round($row['temperature'], 2),
            'rainfall' => $row['rainfall'],
            'wind_speed' => $row['wind_speed'],
            'humidity' => $row['humidity'],
            'pressure' => $row['pressure'] 
        ];

        //output the table row with weather data
        echo "<tr>";
        echo "<td>" . $row['city_name'] . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $row['dt']) . "</td>";
        echo "<td>" . $row['weather_icon'] . "</td>";
        echo "<td>" . $row['weather_condition'] . "</td>";
        echo "<td>" . round($row['temperature'], 2) . "</td>"; // Round temperature to 2 decimal places
        echo "<td>" . $row['rainfall'] . "</td>";
        echo "<td>" . $row['wind_speed'] . "</td>";
        echo "<td>" . $row['humidity'] . "</td>";
        echo "<td>" . $row['pressure'] . "</td>";
        echo "</tr>";
    }

    //convert the weather history array to JSON string
    $weatherHistoryJson = json_encode($weatherHistory, JSON_NUMERIC_CHECK);

    //generate JavaScript code to store the weather history data in localStorage
    echo "<script>";
    echo "const weatherHistoryData = " . $weatherHistoryJson . ";";
    echo "localStorage.setItem('weatherHistory', JSON.stringify(weatherHistoryData));";
    echo "</script>";

    //close database connection
    mysqli_close($mysqli);
    ?>
    </table>
</body>
</html>