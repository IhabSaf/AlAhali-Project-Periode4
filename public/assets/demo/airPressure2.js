// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';



const stationDataLow = []
for (const key in lowStp) {
    if (lowStp.hasOwnProperty(key)) {
        stationDataLow.push(lowStp[key]);

    }
}

const stationNamesLow = Object.keys(stationDataLow).map((key) => stationDataLow[key].stationName);

const stpLow = Object.keys(stationDataLow).map((key) => stationDataLow[key].stp);

const timestampObjectsLow = Object.values(stationDataLow).map((station) => station.timestamp);

const timestampsLow = Object.keys(timestampObjectsLow).map((key) => timestampObjectsLow[key].date);

const convertedDateLow = []


for(const index in timestampsLow){

    const timestamp = new Date(timestampsLow[index]);

    // format the timestamp into a string with only the time
    const formattedTimestamp = timestamp.toLocaleString("nl-NL", {
        // year: "2-digit",
        // month: "2-digit",
        // day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
    convertedDateLow.push(formattedTimestamp)
}

//concatenation of stationname and the time of the data arrival
const statAndDate = convertedDateLow.map((value, index) => value.toString() + " Stat: "+ stationNamesLow[index]);

// get the lowest number for the y of the graph to always make all the statistics visible
lowestPoint = Math.min(...stpLow) - 20;

var ctx = document.getElementById("myLowStpChart");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: convertedDateLow,
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
            data: stpLow
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
                    max: 1000,
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