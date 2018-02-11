
$(document).ready(function(){
	/*
		<script src="code/highcharts.js"></script>
		function add(H){
        //const H = Highcharts;
        H.wrap(H.Tick.prototype, 'render', function (p, i, o, op) {
        p.call(this, i, o, op);
        const axis = this.axis;
        var aq = axis.axisLine;
    if (axis.isXAxis) {
        var a = axis.ticks;
        const mark = this.mark;
        const label = this.label; 
        
        aq.attr({
            stroke: 'red',
            "stroke-width":3
        });
        var ad = label.textStr;
            if(ad.split(':')[0]%2 !== 0){
                label.css({
                display: 'none'
            });
            } else {
                label.css({
                fontSize: '10px',
                fontWeigth: 'bold',
                color: 'black'
                });
            }
            console.log(mark);
        const d = mark.d.split(' ').map(v => {
          var n = Number(v);
          return H.isNumber(n) ? n : v;
        });
        d[2] -= 3;
        d[5] += 2;    
        mark.attr({
          'stroke-width': 1,
          stroke: 'green',
          d: d.join(' ')
        });
    }
    });
};

const H = Highcharts;
add(H);
H.chart('container', {
    xAxis: {
            type: 'datetime',
            tickInterval:1*3600*1000,
            
    },
    yAxis: {
            minRange:0.1,
            opposite: true,
            className: 'highcharts-color-1',
            opposite: true,
            title: {
                text: 'Secondary axis'
            }   
                  //tickInterval: 1
    },
    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            line: {
                softThreshold:false
            }
                }
    },
  series: [{
    pointStart: Date.UTC(2018,02,06,0,0),
    marker: {
        symbol: "square",
        width: 16,
        height: 16,
        fillColor: 'red'
        },
        data: [
            //[dataPA[0].x,dataPA[0].y],
        [Date.UTC(2018,02,06,4,34),null],
        [Date.UTC(2018,02,06,5,04),null],
        [Date.UTC(2018,02,06,6,30),null],
        [Date.UTC(2018,02,06,8,14),null],
        [Date.UTC(2018,02,06,9,34),null],
        [Date.UTC(2018,02,06,10,34),1],
        [Date.UTC(2018,02,06,12,34),0],
        [Date.UTC(2018,02,06,13,34),0],
        [Date.UTC(2018,02,06,16,34),0],
        [Date.UTC(2018,02,06,18,34),0],
        [Date.UTC(2018,02,06,19,34),0],
        [Date.UTC(2018,02,06,21,34),0],
        [Date.UTC(2018,02,06,23,34),0]

        ]
  }]
});

*/
   
var url = $('input[name=filterPeriode]').val();
    var data={
    bloque: 0.5,
    cliquer: 1,
    //delivre: 0,
    //desabo: 0,
    //erreur: 0,
    last :{
    "date": "2018-02-07 14:13:36.581923",
    "timezone_type": 3,
    "timezone": "Africa/Khartoum"
  }
    //last: { date: "2018-02-07T10:40:21.852173Z"}
    //tsy last :new Date("2018-02-07T08:53:09.704Z")
  };
  var dataPoints1 = [];
    var dataPoints2 = [];
    var today= new Date();
    var min=new Date(today.getFullYear(), today.getMonth(), today.getDate(),0,0,0);
    var d = new Date(data.last.date);
    dataPoints1.push({
             x: d,
             y: data.cliquer
            });
   dataPoints2.push({
            x: d,
            y: data.bloque
          });
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
      minimum: min,
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
      interval: 0.25,

      valueFormatString: "#0.00"
    },
    data: [
      { 
        axisYType: "secondary",
        type: "line",
        color: "#87ceeb",
        markerType: "square",
        dataPoints: dataPoints1
      },
      { 
        axisYType: "secondary",
        type: "line",
        color: "red",
        markerType: "square",
        dataPoints: dataPoints2
      }          
    ]
  });
  chart.render();

  $(document).on('click','.clearable .dropdown-itemNo', function(e){
        e.preventDefault();
        var a=$(this).parents('.dropdown').find('button').addClass('active').html($(this).html());
        setTimeout(sendChoice($(this)), 0);
    });

    $(document).on('mouseleave', '.dropdown-menu', function() {
        $(document).click();
    });

    function sendChoice($periode=null){
       var filter = $('.dropdown.filtres').find('button').hasClass('active');
        var  data = {};
        if (filter) {
            data.status = $('.dropdown.filtres').find('button').find("span").html().trim();
        }
        if (data.status=="Today") {
          window.location.reload();
        } else if(data.status=="Yesterday"){

          function convertDate(inputFormat) {
                  function pad(s) { return (s < 10) ? '0' + s : s; }
                  var d = new Date(inputFormat);
                  return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/');
            }

          $.ajax({
            url:url,
            method:'POST',
            data:{'filter': data.status},
            dataType: 'json',
            success:function(dataY){
              console.log(dataY);
              if (dataY!={}){
                $('.valTot').css('display','none');
                $('.valTot2').css('display','block');
                $('.valDel2').css('display','block');
                $('.valDel').css('display','none');
                $('.valOuv2').css('display','block');
                $('.valOuv').css('display','none');
                $('.valClik2').css('display','block');
                $('.valClik').css('display','none');
                $('#blbl2').css('display','block');
                $('.blbl2').css('display','block');
                $('.blbl').css('display','none');
                $('#blErr2').css('display','block');
                $('.blErr2').css('display','block');
                $('.blErr').css('display','none');
                $('#blDesa2').css('display','block');
                $('.blDesa2').css('display','block');
                $('.blDesa').css('display','none');
                $('#blSp2').css('display','block');
                $('.blSp2').css('display','block');
                $('.blSp').css('display','none');
                $('#nbrMail').css('display','none');
                
                $('.valTot2').append(dataY.info.total);
                $('.valDel2').append(dataY.info.delivre);
                $('.valOuv2').append(dataY.info.ouvert);
                $('.valClik2').append(dataY.info.cliquer);
                $('#blbl2').append(dataY.info.bloque);
                $('#blErr2').append(dataY.info.erreur);
                $('#blDesa2').append(dataY.info.desabo);
                $('#blSp2').append(dataY.info.spam);
                $('#nbrMail2').append(dataY.info.total);

                $('.tableDetail').css('display','none');
                $('.tableDetail2').css('display','block');
                $.each(dataY.fromTo,function(i, value) {
                $("<tr></tr>").appendTo('.table tbody')
                .append("<td>"+ value.sujet+"</td><td>"+ value.sender +"</td><td>"+value.to+"</td><td>"+convertDate(value.date)+"</td>");
        
                 });
              }else{
                $('.headSujet').css('width','23%');
              }
            }
        });
      }
        
    }

});