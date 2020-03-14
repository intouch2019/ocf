$(document).ready(function() {
	// Graph
	var opt1 = { 
	    style:'vertical',
	    vertBarWidth: "12",
	    vertBarHeight: 'line',
	    labelVisibility: 'hidden',
	    backColor:'#3b3b3b',
	    milestones: {
	      1: {
	        mlLabelVis: 'hidden',   
	        mlLineWidth: 0  
	      }
	    },
	    foreColor:'#00afd9'
	};
	var opt2 = { 
	    style:'vertical',
	    vertBarWidth: "12",
	    vertBarHeight: 'line',
	    labelVisibility: 'hidden',
	    backColor:'#3b3b3b',
	    milestones: {
	      1: {
	        mlLabelVis: 'hidden',   
	        mlLineWidth: 0  
	      }
	    },
	    foreColor:'#00be94'
	}; 
	var opt3 = { 
	    style:'vertical',
	    vertBarWidth: "12",
	    vertBarHeight: 'line',
	    labelVisibility: 'hidden',
	    backColor:'#3b3b3b',
	    milestones: {
	      1: {
	        mlLabelVis: 'hidden',   
	        mlLineWidth: 0  
	      }
	    },
	    foreColor:'#00be67'
	}; 
	var opt4 = { 
	    style:'vertical',
	    vertBarWidth: "12",
	    vertBarHeight: 'line',
	    labelVisibility: 'hidden',
	    backColor:'#3b3b3b',
	    milestones: {
	      1: {
	        mlLabelVis: 'hidden',   
	        mlLineWidth: 0  
	      }
	    },
	    foreColor:'#adda41'
	}; 
	$('#ag-1').barIndicator(opt1);
	$('#ag-2').barIndicator(opt2);
	$('#ag-3').barIndicator(opt3);
	$('#ag-4').barIndicator(opt4);

	//Cicle
	$('#circle').circleProgress({
	  	value: 0.72,
	  	fill: {gradient: [['#00b0d9', .1], ['#83e3ff', 1]], gradientAngle: Math.PI / 4}
	}).on('circle-animation-progress', function(event, progress, stepValue) {
	  	$(this).find('strong').html(parseInt(100 * (String(stepValue.toFixed(2)).substr(1))) + '<i>% </i><small>complete</small>');
	});
});