console.log("print this");

const stationDataHigh = []
for (const key in highStps) {
    if (highStps.hasOwnProperty(key)) {
        stationDataHigh.push(highStps[key]);

    }
}
const stpHigh = Object.keys(stationDataHigh).map((key) => stationDataHigh[key].stp);
for(let i = 0; i < stpHigh.length; i++){stpHigh[i] = (stpHigh[i] === null ? 0 : stpHigh[i]);}
console.log(stpHigh)

const stationDataLow = []
for (const key in lowStps) {
    if (lowStps.hasOwnProperty(key)) {
        stationDataLow.push(lowStps[key]);
        
    }
}
const stpLow = Object.keys(stationDataLow).map((key) => stationDataLow[key].stp);
for(let i = 0; i < stpLow.length; i++){stpLow[i] = (stpLow[i] === null ? 0 : stpLow[i]);}
console.log(stpLow);

// SETUP BLOCK
// const labels = [];
const data = {
    labels: labels,
    datasets: [{
        label: 'below 990mBar',
        pointBackgroundColor: 'rgb(18, 174, 204)',
        pointBorderColor: 'rgb(18, 174, 204)',
        borderColor: 'rgb(18, 174, 204)',
        data: stpLow
    },
    {
        label: 'above 1030mBar',
        pointBackgroundColor: 'rgb(11, 91, 107)',
        pointBorderColor: 'rgb(11, 91, 107)',
        borderColor: 'rgb(11, 91, 107)',
        data: stpHigh
    }]
};

// CONFIG BLOCK
const config = {
    type: 'line',
    data,
    options: {}
};

// RENDER BLOCK
var myChart = new Chart(
    document.getElementById('myChart'),
    config
);