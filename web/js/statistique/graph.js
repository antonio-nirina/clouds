/******Graphique*****/

    function add(H){
        H.wrap(H.Tick.prototype, 'render', function (p, i, o, op) {
        p.call(this, i, o, op);
        const axis = this.axis;
        var aq = axis.axisLine;
    if (axis.isXAxis) {
        var a = axis.ticks;
        const mark = this.mark;
        const label = this.label; 
        aq.attr({
            stroke: '#8DE3FF',
            "stroke-width":4
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
                color: 'black',
                });
                label.attr({
                  rotation: 0
                });

            }
    }
    });
};

function createChart(data){
  var deliv = [];
  var open = [];
  var click = [];
  var desab = [];
  var bloque = [];
  var spa = [];
  var erreu = [];
  var read = [];

  var delivre = [];
  var opened = [];
  var clicked = [];
  var desabo = [];
  var bloqued = [];
  var spam = [];
  var erreur = [];

var dateCurrent = data.length>0 ? data[0].LastActivityAt:data.LastActivityAt.date;
var today = new Date(dateCurrent);
var reading = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());

$.each(data, function(i, item) {
  var date = new Date(item.LastActivityAt);
  var  dateUTC = Date.UTC(date.getFullYear(),date.getMonth(),date.getDate(),date.getHours());
     deliv.push({
      'date': dateUTC,
      'delivre': item.DeliveredCount
              });
     open.push({
      'date':dateUTC,
      'open':item.OpenedCount
     });
     click.push({
      'date':dateUTC,
      'click':item.ClickedCount
     });
     desab.push({
          'date':dateUTC,
          'desabo':item.UnsubscribedCount
     });
     bloque.push({
      'date':dateUTC,
      'bloqued':item.BlockedCount
     });
     spa.push({
      'date':dateUTC,
      'spam':item.SpamComplaintCount
     });
     erreu.push({
          'date':dateUTC,
          'erreur':item.BouncedCount
     });
});

$.each(deliv, function(index, val) {
     delivre.push(Object.values(val));
     
});
$.each(open, function(index, val) {
     opened.push(Object.values(val));
     
});

$.each(click, function(index, val) {
     clicked.push(Object.values(val));
     
});

$.each(desab, function(index, val) {
     desabo.push(Object.values(val));
     
});

$.each(bloque, function(index, val) {
     bloqued.push(Object.values(val));
     
});

$.each(spa, function(index, val) {
     spam.push(Object.values(val));
     
});

$.each(erreu, function(index, val) {
     erreur.push(Object.values(val));
     
});


    var option =  {
            xAxis: {
                type: 'datetime',
                tickInterval:1*3600*1000,                    
            },
            title: {
                text: null
                },
            yAxis: {
                    minRange:0.25,
                    opposite: true,
                    className: 'highcharts-color-1',
                    opposite: true, 
                    title: {
                        text: null
                     }                        
            },
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    color: '#87ceeb',
                    line: {
                        softThreshold:false
                    }
                        }
            },
          series: [
           {
            pointStart: reading, 
            showInLegend: false, 
            marker: {
            symbol: "square",
            width: 16,
            height: 16,
            fillColor: '#87ceeb'
            },
            data: [
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),0),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),1),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),2),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),3),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),4),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),6),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),5),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),6),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),7),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),8),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),9),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),10),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),11),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),12),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),13),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),14),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),15),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),16),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),17),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),18),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),19),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),20),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),21),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),22),0],
              [Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(),23),0]
            ]
          },  

          /*{
            data: delivre,
            color:"#838383",
            showInLegend: false,
            marker: {
              symbol: "circle",
              width: 16,
              height: 16,
              fillColor: '#838383'
            }
          },
          
          {
            data: opened,
            color:"#9DE077",
            showInLegend: false,
            marker: {
              symbol: "square",
              width: 16,
              height: 16,
              fillColor: '#9DE077'
            }
          },

          {
            data: clicked,
            color:"#56A400",
            showInLegend: false,
            marker: {
              symbol: "square",
              width: 16,
              height: 16,
              fillColor: '#56A400'
            }
          },

          {
            data: desabo,
            color:"#1DC7FF",
            showInLegend: false,
            marker: {
              symbol: "circle",
              width: 16,
              height: 16,
              fillColor: 'blue'
            }
          },

          {
            data: bloqued,
            showInLegend: false,
            color:"black",
            marker: {
              symbol: "square",
              width: 16,
              height: 16,
              fillColor: 'black'
            }
          },

          {
            data: spam,
            showInLegend: false,
            color:"red",
            marker: {
              symbol: "square",
              width: 16,
              height: 16,
              fillColor: 'red'
            }
          },

          {
            data: erreur,
            color:"orange",
            showInLegend: false,
            marker: {
              symbol: "square",
              width: 16,
              height: 16,
              fillColor: 'orange'
            }
          }*/
        ]
    };

    const H = Highcharts;
    add(H);
    H.chart('container', option);
}

/*******EndGraphique***********/

$(document).ready(function(){
var url = $('input[name=filterPeriode]').val();
var jsonNow = $('input[name=dataNow]').val();
   createChart(JSON.parse(jsonNow));

//console.log(H);
/**filtre sur periode aujord'hui hier last7 days...**/
  $(document).on('click','.clearable .dropdown-itemNo', function(e){
        e.preventDefault();
        var a=$(this).parents('.dropdown').find('button').addClass('active').html($(this).html());
        setTimeout(sendChoice($(this)), 0);// on remplace par change pour ne pas se reload ou une requete ajax se lance
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
        console.log(data.status);
        switch (data.status) {
              case "Today":
                window.location.reload();
              break;
              case "Yesterday":
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
                          //createChart(jsonNow);
                            if (dataY.info!= {}){                             
                            $('.valTot3').css('display','none');
                            $('.valDel3').css('display','none');
                            $('.valOuv3').css('display','none');
                            $('.valClik3').css('display','none');
                            $('#blbl3').css('display','none');
                            $('.blbl3').css('display','none');
                            $('#blErr3').css('display','none');
                            $('.blErr3').css('display','none');
                            $('#blDesa3').css('display','none');
                            $('.blDesa3').css('display','none');
                            $('#blSp3').css('display','none');
                            $('.blSp3').css('display','none');
                            $('#nbrMail3').css('display','none');
                            $('.tableDetail3').css('display','none');
                            $('.tableDetail3').removeClass('table');
                            $('.tableDetail').removeClass('table');
                            $('#tableBody1').children('tr').remove();
                            $('#tableBody3').children('tr').remove();

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
                            $('#nbrMail2').css('display','block');

                            $('.valTot2').html(dataY.info.res.total);
                            $('.valDel2').html(dataY.info.res.delivre);
                            $('.valOuv2').html(dataY.info.res.ouvert);
                            $('.valClik2').html(dataY.info.res.cliquer);
                            $('#blbl2').html(dataY.info.res.bloque);
                            $('#blErr2').html(dataY.info.res.erreur);
                            $('#blDesa2').html(dataY.info.res.desabo);
                            $('#blSp2').html(dataY.info.res.spam);
                            $('#nbrMail2').html(dataY.info.res.total);

                            $('.tableDetail').css('display','none');
                            $('.tableDetail2').css('display','block');
                              
                            $.each(dataY.fromTo,function(i, value) {
                              $("<tr></tr>").appendTo('.table #tableBody2')
                              .append("<td>"+ value.sujet+"</td><td>"+ value.sender +"</td><td>"+value.emailTo+"</td><td>"+convertDate(value.date)+"</td>");
                      
                               });
                            } else {
                              $('.headSujet').css('width','23%');
                            }
                      }
                 });

              break;
              case "last7days":
                $.ajax({
                    url:url,
                    method:'POST',
                    data:{'filter': data.status},
                    dataType: 'json',
                    success: function(dataLast7){
                      console.log(dataLast7);
                      //createChart(jsonNow);
                      if (dataLast7!={}  && dataLast7.fromTo!= {}){
                            $('.valTot').css('display','none');
                            $('.valTot2').css('display','none');
                            $('.valTot3').css('display','block');

                            $('.valDel2').css('display','none');
                            $('.valDel3').css('display','block');
                            $('.valDel').css('display','none');

                            $('.valOuv2').css('display','none');
                            $('.valOuv3').css('display','block');
                            $('.valOuv').css('display','none');

                            $('.valClik2').css('display','none');
                            $('.valClik3').css('display','block');
                            $('.valClik').css('display','none');

                            $('#blbl2').css('display','none');
                            $('.blbl2').css('display','none');
                            $('.blbl').css('display','none');

                            $('#blbl3').css('display','block');
                            $('.blbl3').css('display','block');

                            $('#blErr2').css('display','none');
                            $('.blErr2').css('display','none');
                            $('.blErr').css('display','none');

                            $('#blErr3').css('display','block');
                            $('.blErr3').css('display','block');

                            $('#blDesa2').css('display','none');
                            $('.blDesa2').css('display','none');
                            $('.blDesa').css('display','none');

                            $('#blDesa3').css('display','block');
                            $('.blDesa3').css('display','block');

                            $('#blSp2').css('display','none');
                            $('.blSp2').css('display','none');

                            $('#blSp3').css('display','block');
                            $('.blSp3').css('display','block');

                            $('.blSp').css('display','none');
                            $('#nbrMail').css('display','none');
                            $('#nbrMail2').css('display','none');

                            $('.tableDetail').css('display','none');
                            $('.tableDetail2').css('display','none');
                              
                            $('#tableBody1').children('tr').remove();
                            $('#tableBody2').children('tr').remove();

                            $('.valTot3').html(dataLast7.info.res.total);
                            $('.valDel3').html(dataLast7.info.res.delivre);
                            $('.valOuv3').html(dataLast7.info.res.ouvert);
                            $('.valClik3').html(dataLast7.info.res.cliquer);
                            $('#blbl3').html(dataLast7.info.res.bloque);
                            $('#blErr3').html(dataLast7.info.res.erreur);
                            $('#blDesa3').html(dataLast7.info.res.desabo);
                            $('#blSp3').html(dataLast7.info.res.spam);
                            $('#nbrMail3').html(dataLast7.info.res.total);
                            $('.tableDetail3').css('display','block');
                              $.each(dataLast7.fromTo,function(i, value) {
                            $("<tr></tr>").appendTo('.table #tableBody3')
                              .append("<td>"+ value.sujet+"</td><td>"+ value.sender +"</td><td>"+value.emailTo+"</td><td>"+convertDate(value.date)+"</td>");
                      
                               });
                            } else {
                              $('.headSujet').css('width','23%');
                            }

                    }
                });
                break;

        }

        
    }

 /*****************end filtre periode**********/

    /*$(document).on('click','.clearable .dropdown-itemNo', function(e){
        e.preventDefault();
        var a=$(this).parents('.dropdown').find('button').addClass('active').html($(this).html());
        setTimeout(sendChoice($(this)), 0);
    });*/

});