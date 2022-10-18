const ctx = document.getElementById('myBarChart').getContext('2d');
const DATA_COUNT = 7;
const NUMBER_CFG = {count: DATA_COUNT, min: -100, max: 100};
const data = {
  labels: {count: DATA_COUNT},
  datasets: [
    {
      label: 'Triangles',
      data: [4215, 5312, 6251, 7841, 9821, 14984],
      fill: false,
      borderColor: "#f41023",
      backgroundColor: "#f41023",
      pointStyle: 'triangle',
      pointRadius: 6,
    },
    {
      label: 'Circles',
      data: [4215, 5312, 6251, 7841, 9821, 14984],
      fill: false,
      borderColor: "#1060f4",
      backgroundColor: "#1060f4",
      pointStyle: 'circle',
      pointRadius: 6,
    },
    {
      label: 'Stars',
      data: [4215, 5312, 6251, 7841, 9821, 14984],
      fill: false,
      borderColor: "#10f48a",
      backgroundColor: "#10f48a",
      pointStyle: 'star',
      pointRadius: 6,
    }
  ]
};
const actions = [
  {
    name: 'Toggle Tooltip Point Style',
    handler(chart) {
      chart.options.plugins.tooltip.usePointStyle = !chart.options.plugins.tooltip.usePointStyle;
      chart.update();
    }
  },
];
const config = {
  type: 'line',
  data: data,
  options: {
    interaction: {
      mode: 'index',
    },
    plugins: {
      title: {
        display: true,
        text: (ctx) => 'Tooltip point style: ' + ctx.chart.options.plugins.tooltip.usePointStyle,
      },
      tooltip: {
        usePointStyle: true,
      }
    }
  }
};
const myChart = new Chart(ctx, config);


