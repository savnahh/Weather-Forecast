if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker
      .register('/service-worker.js') //Register the service worker
      .then(registration => {
        console.log('Service Worker registered with scope:', registration.scope);
      })
      .catch(error => {
        console.log('Service Worker registration failed:', error);
      });
  });
}

const API_KEY = '9d36297c63748b8d6f3c1e1652b35069';

function getWeather(cityName) {
  const cityInput = document.getElementById('city-input');
  const cityNameHeader = document.getElementById('city-name');
  const dayAndDate = document.getElementById('day-and-date');
  const weatherIcon = document.getElementById('weather-icon');
  const weatherCondition = document.getElementById('weather-condition');
  const temperature = document.getElementById('temperature');
  const rainfall = document.getElementById('rainfall');
  const windspeed = document.getElementById('windspeed');
  const humidity = document.getElementById('humidity');
  const weatherForecast = document.querySelector('.weather-forecast');

  const cachedWeatherData = localStorage.getItem(cityName);

  if (cachedWeatherData) {
    //use cached weather data
    const data = JSON.parse(cachedWeatherData);
    updateWeatherData(data);
  } else {
    //fetch weather data from the API
    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${cityName}&appid=${API_KEY}&units=metric`)
      .then(response => response.json())
      .then(data => {
        //cache weather data in localStorage
        localStorage.setItem(cityName, JSON.stringify(data));
        //update weather data on the page
        updateWeatherData(data);
      })
      .catch(error => {
        //display error message
        console.log(error);
        cityName.textContent = 'City not found'
      });
  }

  function updateWeatherData(data) {
      //city name
      cityNameHeader.textContent = `${data.name}, ${data.sys.country}`;
      //day and date
      const date = new Date();
      const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      const dayOfWeek = days[date.getDay()];
      const day = date.getDate();
      const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      const month = monthNames[date.getMonth()];
      const year = date.getFullYear();
      dayAndDate.innerText = `${dayOfWeek}, ${day} ${month} ${year}`;
      //weather icon
      const iconUrl = `https://openweathermap.org/img/wn/${data.weather[0].icon}.png`;
      weatherIcon.style.backgroundImage = `url(${iconUrl})`;
      //weather condition
      weatherCondition.innerText = data.weather[0].description;
      //temperature
      temperature.innerText = `${Math.round(data.main.temp)}°C`;
      //rainfall
      if (data.rain && data.rain['1h']) {
        rainfall.innerText = `Rainfall: ${data.rain['1h']} mm/h`;
      } else {
        rainfall.innerText = 'No rainfall';
      }
      //windspeed
      windspeed.innerText = `Wind Speed: ${data.wind.speed} m/s`;
      //humidity
      humidity.innerText = `Humidity: ${data.main.humidity}%`;
      //weather forecast
      weatherForecast.style.display = 'block';
    }
}
//get weather for 'New York City' on page load
getWeather('New York City');

//event listener for search button
document.getElementById("search-btn").addEventListener("click", myfunction)
function myfunction(){
  //get the city name from the input field
  let val = document.getElementById("city-input").value;
  //get weather for the entered city
  getWeather(val);
}