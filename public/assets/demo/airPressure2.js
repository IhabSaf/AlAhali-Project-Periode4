// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';
// Bar Chart Example
const stationData2 = []
for (const key in lowStp) {
    if (lowStp.hasOwnProperty(key)) {
        stationData2.push(lowStp[key]);

    }
}

const stationNames2 = Object.keys(stationData2).map((key) => stationData2[key].stationName);

const stpLow = Object.keys(stationData2).map((key) => stationData2[key].stp);

const timestampObjects2 = Object.values(stationData2).map((station) => station.timestamp);

const timestamps2 = Object.keys(timestampObjects2).map((key) => timestampObjects2[key].date);

const convertedDate2 = []

console.log(stpLow)

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

var ctx = document.getElementById("myLowStpChart");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: convertedDate2,
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
                    min: lowStpButton,
                    max: 1100,
                    maxTicksLimit: 5
                },
                gridLines: {
                    color: "rgba(0, 0, 0, .125)",
                }
            }],
        },
        legend: {
            display: false
        }
    }
});