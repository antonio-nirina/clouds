$(document).ready(function(){

  var date=new Date().toLocaleDateString();
  var dataPoints=[];
   var url = $('input[name=filterPeriode]').val();
  $.ajax({
      url:url,
      method:'POST',
      data:{'filter':date},
      dataType: 'json',
      success:function(data){
          console.log(data);
      }
    });

    $(document).on('click','.clearable .dropdown-itemNo', function(e){
        e.preventDefault();
        var a=$(this).parents('.dropdown').find('button').addClass('active').html($(this).html());
        setTimeout(sendChoice($(this)), 0);
    });

    $(document).on('mouseleave', '.dropdown-menu', function() {
        $(document).click();
    });

    function sendChoice($periode=null){
       var filter = $('.dropdown.filtres').find('button').hasClass('active'),
            data = {};
        if (filter) {
            data.status = $('.dropdown.filtres').find('button').find("span").html().trim();
        }
    }

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
    minimum: new Date(2018,01,06,00,00),
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
        { x:new Date(2018,01,06,02,00), y: 0 },
        { x: new Date(2018,01,06,04,00), y: 0 },
        { x: new Date(2018,01,06,06,00), y: 0 },
        { x: new Date(2018,01,06,08,00), y: 0},
        { x: new Date(2018,01,06,10,00), y: 0 },
        { x: new Date(2018,01,06,14,00), y: 0 },
        { x: new Date(2018,01,06,16,00), y: 0 },
        { x: new Date(2018,01,06,18,00), y: 0.05634 },
        { x: new Date(2018,01,06,20,00), y: 0.0156614 }
      ]
    }         
  ]
});

chart.render();

});