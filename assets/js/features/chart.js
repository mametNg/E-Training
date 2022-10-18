const chartMin = (val, start, end) => {
	return {
		label: 'MIN',
		fill: false,
		data: [
		{
			x: start,
			y: val
		}, {
			x: end,
			y: val
		}
		],
		backgroundColor: 'rgba(255, 188, 31, 0.2)',
		borderColor: 'rgba(255, 188, 31, 1)',
		pointHoverRadius: 10,
		pointHitRadius: 10,
		pointBorderWidth: 5,
		lineTension: 0,
		pointRadius: 3,
		borderWidth: 2
	};
}

const chartMax = (val, start, end) => {
	return	{
		label: 'MAX',
		fill: false,
		data: [
		{
			x: start,
			y: val
		}, {
			x: end,
			y: val
		}
		],
		backgroundColor: 'rgba(255, 31, 31, 0.2)',
		borderColor: 'rgba(255, 31, 31, 1)',
		pointHoverRadius: 10,
		pointHitRadius: 10,
		pointBorderWidth: 5,
		lineTension: 0,
		pointRadius: 3,
		borderWidth: 2
	}
}

const chartMins = (arrVal) => {
	return {
		label: 'MIN',
		fill: false,
		data: arrVal,
		backgroundColor: 'rgba(255, 188, 31, 0.2)',
		borderColor: 'rgba(255, 188, 31, 1)',
		pointHoverRadius: 10,
		pointHitRadius: 10,
		pointBorderWidth: 5,
		lineTension: 0,
		pointRadius: 3,
		borderWidth: 2
	};
}

const chartMaxs = (arrVal) => {
	return	{
		label: 'MAX',
		fill: false,
		data: arrVal,
		backgroundColor: 'rgba(255, 31, 31, 0.2)',
		borderColor: 'rgba(255, 31, 31, 1)',
		pointHoverRadius: 10,
		pointHitRadius: 10,
		pointBorderWidth: 5,
		lineTension: 0,
		pointRadius: 3,
		borderWidth: 2
	}
}
const isColor = () => {
	const rand = Math.floor(Math.random() * 9);
	let arr = [
		{
			'border': 'rgba(54, 162, 235, 1)',
			'bg': 'rgba(54, 162, 235, 0.2)',
		},
		{
			'border': 'rgba(255, 188, 31, 1)',
			'bg': 'rgba(255, 188, 31, 0.2)',
		},
		{
			'border': 'rgba(255, 31, 31, 1)',
			'bg': 'rgba(255, 31, 31, 0.2)',
		},
		{
			'border': 'rgba(64, 255, 31, 1)',
			'bg': 'rgba(64, 255, 31, 0.2)',
		},
		{
			'border': 'rgba(83, 31, 255, 1)',
			'bg': 'rgba(83, 31, 255, 0.2)',
		},
		{
			'border': 'rgba(255, 109, 31, 1)',
			'bg': 'rgba(255, 109, 31, 0.2)',
		},
		{
			'border': 'rgba(255, 31, 206, 1)',
			'bg': 'rgba(255, 31, 206, 0.2)',
		},
		{
			'border': 'rgba(31, 255, 135, 1)',
			'bg': 'rgba(31, 255, 135, 0.2)',
		},
		{
			'border': 'rgba(188, 255, 31, 1)',
			'bg': 'rgba(188, 255, 31, 0.2)',
		}
	];

	return arr[rand];
}

const chartValue = (name, arrVal) => {
	const clr = isColor();

	return {
		label: name,
		fill: false,
		data: arrVal,
		// data: [80, 99, 70, 50, 120, 30],
		backgroundColor: clr.bg,
		borderColor: clr.border,
		pointHoverRadius: 5,
		pointHitRadius: 5,
		pointBorderWidth: 5,
		lineTension: 0,
		pointRadius: 1,
		borderWidth: 1
	}
}

const chartConfig = () => {
	return {
		type: 'line',
		data: {
			labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
			datasets: [
				chartValue("Start", [80, 99, 70, 50, 120, 30]),
				chartMin(30),
				chartMax(100)
			]
		},
		options: {
			// spanGaps: 86400,
			responsive: true,
			scales: {
				y: {
					beginAtZero: true
				}
			},
			plugins: {
				title: {
					display: true,
					text: 'Tempol Cure - Clean Room'
				},
				// subtitle: {
				// 	display: true,
				// 	text: 'CLEAN ROOM'
				// },
				tooltip: {
					usePointStyle: true,
					callbacks: {
						labelPointStyle: function(context) {
							return {
								pointStyle: 'rectRounded',
								rotation: 0
							};
						}
					}
				},
				legend: {
					position: 'bottom'
				}
			},
			transitions: {
				show: {
					animations: {
						x: {
							from: 0
						},
						// y: {
						// 	from: 0
						// }
					}
				},
				hide: {
					animations: {
						x: {
							to: 0
						},
						// y: {
						// 	to: 0
						// }
					}
				}
			},
			interaction: {
				intersect: false,
				// mode: 'point',
				// mode: 'index',
			},
			animation: {
				duration: 1500
			}
		}
	};
}

