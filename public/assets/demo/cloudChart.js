// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';
// Bar Chart Example

// create const for table data button
const cloudinessButton = document.getElementById('button-cloudiness');

// create const for the table that is hidden at the start
const table = document.getElementById('table-data-cloudiness');


const stationData = []
for (const key in filteredResult) {
    if (filteredResult.hasOwnProperty(key)) {
        stationData.push(filteredResult[key]);

    }
}

const stationNames = Object.keys(stationData).map((key) => stationData[key].stationName);

const cldc = Object.keys(stationData).map((key) => stationData[key].cldc);

const timestampObjects = Object.values(stationData).map((station) => station.timestamp);

const timestamps = Object.keys(timestampObjects).map((key) => timestampObjects[key].date);

const convertedDate = []


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
lowestPoint = Math.min(...cldc) - 5;



var ctx = document.getElementById("myCloudChart");
var myLineChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: stationNames,
        datasets: [
            {
                label: "% of cloudiness",
                backgroundColor: "rgba(18,174,204)", // of deze rgba(11, 91, 107)?
                borderColor: "rgba(2,117,216,1)",
                data: cldc,
            },
        ],
    },

    options: {
            scales: {
                xAxes: [{
                    time: {
                        unit: 'month'
                    },
                    gridLines: {
                        display: true
                    },

                }],
                yAxes: [{
                    ticks: {
                        min: lowestPoint,
                        max: 100,
                        maxTicksLimit: 20
                    },
                    gridLines: {
                        display: true
                    }
                }],
            },
            legend: {
                display: false
            },
        },

});


let isVisible = false;

cloudinessButton.addEventListener('click', function() {
    if (isVisible) {
        table.style.display = 'none';
        isVisible = false;
    } else {
        table.style.display = 'block';
        isVisible = true;
    }
});