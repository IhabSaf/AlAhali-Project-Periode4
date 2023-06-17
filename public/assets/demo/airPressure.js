// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';


// create const for table data button
const lowStpButton = document.getElementById('button-pressure-low');
const highStpButton = document.getElementById('button-pressure-high');

// create const for the table that is hidden at the start
const tableLow = document.getElementById('table-data-highStp');
const tableHigh = document.getElementById('table-data-lowStp');


const stationData = []
for (const key in highStp) {
    if (highStp.hasOwnProperty(key)) {
        stationData.push(highStp[key]);

    }
}

const stationNames = Object.keys(stationData).map((key) => stationData[key].stationName);

const stpHigh = Object.keys(stationData).map((key) => stationData[key].stp);

const timestampObjects = Object.values(stationData).map((station) => station.timestamp);

const timestamps = Object.keys(timestampObjects).map((key) => timestampObjects[key].date);

const convertedDate = []

console.log(stpHigh)

for(const index in timestamps){

    const timestamp = new Date(timestamps[index]);

    // format the timestamp into a string with only the time
    const formattedTimestamp = timestamp.toLocaleString("nl-NL", {
        // year: "2-digit",
        // month: "2-digit",
        // day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
    convertedDate.push(formattedTimestamp)
}

//concatenation of stationname and the time of the data arrival
const statAndDate = convertedDate.map((value, index) => value.toString() + " Stat: "+ stationNames[index]);

// get the lowest number for the y of the graph to always make all the statistics visible
lowestPoint = Math.min(...stpHigh) - 20;


var ctx = document.getElementById("myHighStpChart");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: convertedDate,
        datasets: [{
            label: "mBar",
            lineTension: 0.3,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 50,
            pointBorderWidth: 2,
            data: stpHigh
        }],
    },
    options: {
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    min: lowestPoint,
                    max: 1100,
                    maxTicksLimit: 5
                },
                gridLines: {
                    color: "rgba(0, 0, 0, .125)",
                }
            }],
        },
        legend: {
            display: true
        }
    }
});


let isVisibleLow = false;
lowStpButton.addEventListener('click', function() {
    console.log("clicked")
    if (isVisibleLow) {
        tableLow.style.display = 'none';
        isVisibleLow = false;
    } else {
        tableLow.style.display = 'block';
        isVisibleLow = true;
    }
});

let isVisibleHigh = false;
highStpButton.addEventListener('click', function() {
    console.log("clicked")
    if (isVisibleHigh) {
        tableHigh.style.display = 'none';
        isVisibleHigh = false;
    } else {
        tableHigh.style.display = 'block';
        isVisibleHigh = true;
    }
});