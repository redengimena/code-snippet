(function($) {
/* "use strict" */

    let init = function() {
        createUsageLineChart('#lineChart_activeUsers', activeUsers);
        createUsageLineChart('#lineChart_totalPlays', totalPlays);
        createUsageLineChart('#lineChart_vizzyPlays', vizzyPlays);
    }

    let createUsageLineChart = function(selector, data) {

        if(jQuery(selector).length > 0 ){
          const lineChart = document.getElementById(selector.replace('#','')).getContext('2d');
          const values = Object.values(data);

          new Chart(lineChart, {
            type: 'line',
            data: {
              defaultFontFamily: 'Poppins',
              labels: Object.keys(data),
              datasets: [
                {
                  data: values,
                  borderColor: 'rgba(64, 24, 157, 1)',
                  borderWidth: "2",
                  backgroundColor: 'transparent',
                  pointBackgroundColor: 'rgba(64, 24, 157, 1)'
                }
              ]
            },
            options: {
              legend: false,
              scales: {
                yAxes: [{
                  ticks: {
                    beginAtZero: true,
                    max: Math.max(...values),
                    min: 0,
                    stepSize: 20,
                    padding: 10
                  }
                }],
                xAxes: [{
                  ticks: {
                    padding: 5
                  }
                }]
              }
            }
          });
        }


    }

    init();


})(jQuery)