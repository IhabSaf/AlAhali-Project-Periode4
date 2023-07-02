// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// create const for table data button
const lowStpButton = document.getElementById('button-pressure-low');

// create const for the table that is hidden at the start
const tableLow = document.getElementById('table-data-highStp');
const tableHigh = document.getElementById('table-data-lowStp');

// Start:  this is all for the HIGH pressure
const stationData = [];

for (const key in Stp) {
    if (Stp.hasOwnProperty(key)) {
        stationData.push(Stp[key]);
    }
}

const threshold = 1029

const stationNames = Object.keys(stationData).map((key) => stationData[key].stationName);
const stp = Object.keys(stationData).map((key) => stationData[key].stp);
const timestampObjects = Object.values(stationData).map((station) => station.timestamp);
const timestamps = Object.keys(timestampObjects).map((key) => timestampObjects[key].date);

const convertedDate = [];
for (const index in timestamps) {
    const timestamp = new Date(timestamps[index]);
    const formattedTimestamp = timestamp.toLocaleString("nl-NL", {
        year: "2-digit",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
    convertedDate.push(formattedTimestamp);
}

//concatenation of stationname and the time of the data arrival
const statAndDate = convertedDate.map((value, index) => value.toString() + " Stat: " + stationNames[index]);

// get the lowest & highest number for the y of the graph to always make all the statistics visible
const lowestPoint = Math.floor(Math.min(...stp) - 50);
const highestPoint = Math.floor(Math.max(...stp) + 50);
const colors = stp.map(value => (value < threshold ? 'rgb(18,174,204)': 'rgb(75, 35, 67)'));

var ctx = document.getElementById("myStpChart");

var myLineChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: stationNames,
        datasets: [{
            label: "mBar",
            lineTension: 0.3,
            backgroundColor: colors,
            borderColor: "rgba(18,174,204)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 50,
            pointBorderWidth: 2,
            data: stp
        }]
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
                    maxTicksLimit: 20
                }
            }],
            yAxes: [{
                ticks: {
                    min: lowestPoint,
                    max: highestPoint,
                    maxTicksLimit: 20
                },
                gridLines: {
                    color: "rgba(0, 0, 0, .125)",
                }
            }],
        },
        legend: {
            display: true
        },
        tooltips: {
            callbacks: {
                title: function(tooltipItem) {
                    return stationNames[tooltipItem[0].index];
                },
                label: function(tooltipItem, data) {
                    var index = tooltipItem.index;
                    var value = data.datasets[tooltipItem.datasetIndex].data[index];
                    return 'mBar: ' + value + ' - ' + convertedDate[index];                }
            }
        }
    }
});

let isVisibleLow = false;
lowStpButton.addEventListener('click', function() {
    if (isVisibleLow) {
        tableLow.style.display = 'none';
        isVisibleLow = false;
    } else {
        tableLow.style.display = 'block';
        isVisibleLow = true;
    }
});

