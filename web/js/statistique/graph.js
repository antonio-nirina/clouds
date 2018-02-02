var chart = new CanvasJS.Chart("chartContainer",{  
  theme: "light2",      
  axisX:{ 
    interval: 1, 
    intervalType: "hour",        
    valueFormatString: "HH:mm", 
    labelAngle: 0,
    lineColor: "#25C8FF",
    lineThickness: 2,
    tickLength: 5,
    tickThickness: 6,
    tickColor:"#25C8FF",
    minimum: new Date(2014,02,09,00,00),
    labelFormatter: function (e) {
      var d = new Date(e.value);
      var n = d.getHours();
      if( n % 2 === 0)
        return CanvasJS.formatDate(e.value, "HH:mm");
        else
        return "";
    }  
  },
  axisY2:{
    interval: 0.025,
    valueFormatString: "#0.000"
  },
  data: [
    { 
      axisYType: "secondary",
      type: "line",
      color: "#87ceeb",
      markerType: "square",
      dataPoints: [
        { x:new Date(2014,02,09,02,00), y: 0 },
        { x: new Date(2014,02,09,04,00), y: 0 },
        { x: new Date(2014,02,09,06,00), y: 0 },
        { x: new Date(2014,02,09,08,00), y: 0},
        { x: new Date(2014,02,09,10,00), y: 0 },
        { x: new Date(2014,02,09,14,00), y: 0 },
        { x: new Date(2014,02,09,16,00), y: 0 },
        { x: new Date(2014,02,09,18,00), y: 0.05634 },
        { x: new Date(2014,02,09,20,00), y: 0.0156614 }
      ]
    }         
  ]
});

chart.render();