$(document).ready(function(){
  $('#ui-datepicker-div').css('display','none');
        $('#qst2').css('display','none');
        $('#qst4').css('display','none');
        /*selection date par defaut*/
        $('#calendar').datepicker({
             dateFormat: 'dd/mm/yy'
        });
        var a=$('#calendar').datepicker('setDate',new Date()).val();
        var data=a.split('/');
        $('#d1').val(data[0]);
        $('#m1').val(data[1]);
        $('#y1').val(data[2]);
        $('#d2').val(data[0]);
        $('#m2').val(data[1]);
        $('#y2').val(data[2]);
        $('#ui-datepicker-div').css('display','none');
        /**fin date default**/

        $('#qst').on('click',function(){
            $('#question').css('display','block');  
            $('#qst').hide();   
            setTimeout(function(){
                $('#qst2').show();
            },1);
            $.datepicker.setDefaults($.datepicker.regional[ "fr" ]);
            $( ".with" ).datepicker({
                minDate: new Date(),
                altField: ".periode",
                altFormat: "dd/MM/yy",
                beforeShow: function(input, inst) {
                var c=$('.ui-datepicker-calendar tbody tr .ui-datepicker-today').addClass('ui-datepicker-current-day');  
            console.log(c);
                    $('#ui-datepicker-div').removeClass(function() {
                        return $('.with3').get(0).id;  
                });
                //
                $('#ui-datepicker-div').addClass(this.id);
              },
                onSelect:  function(data,inst){ 
                    if(data!=='undefined'){
                        let a=data.split('/');                     
                        $('#qst2').hide();
                        $('#qst').show();
                        $('#d2').val(a[0]);
                        $('#m2').val(a[1]);
                        $('#y2').val(a[2]);
                        $('#qst2').hide();
                        setTimeout(function(){
                            $('#qst').show();
                        },2);
                    }
                },
                onClose: function(data,inst){
                   if(data==""||data==this.value){
                        $('#qst').show();
                        $('#qst2').hide();                                  
                    }
                    
                }

            });
              //$('#ui-datepicker-div').css('display','none');
        }); 

        $('#qst3').on('click',function(){
            $('#question3').css('display','block');  
            $('#qst3').hide();   
            setTimeout(function(){
                $('#qst4').show();
            },1);
            $.datepicker.setDefaults($.datepicker.regional[ "fr" ]);
            $( ".with3" ).datepicker({
                minDate: new Date(),
                altField: ".periode",
                altFormat: "d MM yy",
                beforeShow: function(input, inst) {
                    $('#ui-datepicker-div').removeClass(function() {
                        return $('.with').get(0).id; 
                });
                $('#ui-datepicker-div').addClass(this.id);
              },
                onSelect:  function(data,inst){                  
                    if(data!=='undefined'){
                        let a=data.split('/');
                        $('#qst4').hide();
                        $('#qst3').show();
                        $('#d1').val(a[0]);
                        $('#m1').val(a[1]);
                        $('#y1').val(a[2]);
                        $('#qst4').hide();
                        setTimeout(function(){
                            $('#qst3').show();
                        },2);
                    }
                },
                onClose: function(data,inst){
                    console.log(data);
                   if(data==""||data==this.value){
                        $('#qst3').show();
                        $('#qst4').hide(); 
                                 
                    }else{

                    }
                    
                }

            });
              //$('#ui-datepicker-div').css('display','none');
        }); 
    $('#vueStat').on('click',function(){
       $('.ensemble').css('display','block');
       $('#vueStat').css('display','none');
    });
      
});
