(function ($, Drupal, drupalSettings) {
  'use strict';
  jQuery.noConflict(true);
// Drupal.ajax = Drupal.ajax || {};

// Drupal.settings.urlIsAjaxTrusted = Drupal.settings.urlIsAjaxTrusted || {};

/**
 * Move a block in the blocks table from one region to another via select list.
 *
 * This behavior is dependent on the tableDrag behavior, since it uses the
 * objects initialized in that behavior to update the row.
 */
Drupal.behaviors.csat = {
  attach: function (context, settings) {
    var top       = config_top || '';
    var height    = config_height || '';
    var width     = config_width || '';
    var path      = config_path || '';
    var root      = config_rootpath || '';
    var basepath  = config_basepath || '';
    var minutes   = config_minutes || '';
    var open      = config_open || '';
    var enabled   = config_enabled || '';
    var allow     = config_allow || '';
    var deny      = config_deny || '';
    // var top       = $('input[name=config_top]').val() || '';
    // var height    = $('input[name=config_height]').val() || '';
    // var width     = $('input[name=config_width]').val() || '';
    // var path      = $('input[name=config_path]').val() || '';
    // var root      = $('input[name=config_rootpath]').val() || '';
    // var basepath  = $('input[name=config_basepath]').val() || '';
    // var minutes   = $('input[name=config_minutes]').val() || '';
    // var open      = $('input[name=config_open]').val() || '';
    // var enabled   = $('input[name=config_enabled]').val() || '';
    // var allow     = $('input[name=config_allow]').val() || '';
    // var deny      = $('input[name=config_deny]').val() || '';
    var curr_url  = window.location.href || '';
    var allow_arr = [];
    var deny_arr  = [];
    
    // if(allow      !== "") allow_arr = allow.split(/\r?\n/);
    // if(deny       !== "") deny_arr  = deny.split(/\r?\n/);
    // var isallow   = match_string(curr_url, allow_arr);
    // var isdeny    = match_string(curr_url, deny_arr);
    var isallow   = allow;
    var isdeny    = deny;
    


    let wrap = document.createElement('div');
    wrap.setAttribute('class', 'text-muted');
    // wrap.innerHTML = '<input type="hidden" name="feel" value="" /><button onclick="reply(\'terrible\')" type="button" value="terrible" class="btn feel"><img src="'+path+'/img/default/survey/1 - terrible.svg" alt="Terrible" data-imgid="1" /></button><button onclick="reply(\'bad\')" type="button" value="bad" class="btn feel"><img src="'+path+'/img/default/survey/2 - bad.svg" alt="Bad" data-imgid="2" /></button><button onclick="reply(\'okay\')" type="button" value="okay" class="btn feel"><img src="'+path+'/img/default/survey/3 - okay.svg" alt="Okay" data-imgid="3" /></button><button onclick="reply(\'good\')" type="button" value="good" class="btn feel"><img src="'+path+'/img/default/survey/4 - good.svg" alt="Good" data-imgid="4"/></button><button onclick="reply(\'excellent\')" type="button" value="excellent" class="btn feel"><img src="'+path+'/img/default/survey/5 - excellent.svg" alt="Good" data-imgid="5" /></button><div class="mt-3 text-left"><label>Feedback <span class="text-muted">- (Optional)</span></label><textarea name="feedback" class="form-control" placeholder="Please give your feedback"></textarea></div>'
    wrap.innerHTML = '<input type="hidden" name="feel" value="" /><button type="button" value="terrible" class="btn feel"><img src="'+path+'/img/default/survey/1 - terrible.svg" alt="Terrible" data-imgid="1" /></button><button type="button" value="bad" class="btn feel"><img src="'+path+'/img/default/survey/2 - bad.svg" alt="Bad" data-imgid="2" /></button><button type="button" value="okay" class="btn feel"><img src="'+path+'/img/default/survey/3 - okay.svg" alt="Okay" data-imgid="3" /></button><button type="button" value="good" class="btn feel"><img src="'+path+'/img/default/survey/4 - good.svg" alt="Good" data-imgid="4"/></button><button type="button" value="excellent" class="btn feel"><img src="'+path+'/img/default/survey/5 - excellent.svg" alt="Good" data-imgid="5" /></button><div class="mt-3 text-left"><label>Feedback <span class="text-muted">- (Optional)</span></label><textarea name="feedback" class="form-control" placeholder="Please give your feedback" maxlength="200" rows="3"></textarea></div>';

    //change question
    var question = config_question || '';
    // var question = $('input[name=config_question]').val() || '';
    if(question == '') question = 'How was your overall experience while using the application?';

    //timer for open csat survey
    enabled = enabled.trim();
    open    = open.trim();
    if(enabled == true || enabled == 1 || enabled == '1')
    {
      if(!(isdeny > 0 && isallow < 1))
      {

        if(open == true || open == 1 || open == '1')  
        {
          swal_open(question, wrap);
        } 
        else
        {
          if(!isNaN(minutes))
          {
            minutes   = parseInt(minutes);
            if(minutes > 0)
            {        
              var timer = 0;
              timer     = (minutes * 60) * 1000
              setTimeout(function(){ swal_open(question, wrap); }, timer);
            }
          } 
        }  
      }
    }

    //csat icon click
    if(top !== '') $('.sweet-csat').css('top',top); 
      else $('.sweet-csat').css('top','75%');
    if(height !== '') $('.sweet-csat').css('height',height+'px');
      else $('.sweet-csat').css('height','50px');
    if(width !== '') $('.sweet-csat').css('width',width+'px'); 
      else $('.sweet-csat').css('width','50px');
    $('.sweet-csat').on('click', function(e){
      e.preventDefault(); 
      swal_open(question, wrap); 
      reset_csat_form();          
      
    });

    //on type comment
    // $('textarea[name=comment]').on('keyup keypress',function() 
    $(document).on('keyup', 'textarea[name="feedback"]', function()
    {
      //console.log('here');
      var maxlen = 200;
      var length = $(this).val().length;
      if(length > (maxlen-10) )
      {
        if($('.error-msg').length < 1)
          $(this).after('<label class="error-msg">Max length '+maxlen+' characters only! Length is '+length+'.</label>');
        else
          $('.error-msg').html('Max length '+maxlen+' characters only! Length is '+length+'.');
      }
      else $('.error-msg').remove();
    });

    //choosing rating
    $(document).on('click', ".feel", function(){
      var feel = $(this).attr('value');
      var feel_val = 0;
      switch(feel){
        case 'terrible':
            feel_val = 1;
            break;
        case 'bad':
            feel_val = 2;
            break;
        case 'okay':
            feel_val = 3;
            break;
        case 'good':
            feel_val = 4;
            break;
        case 'excellent':
            feel_val = 5;
            break;
      }

      for(var i = 1; i<=5; i++ ){
        var src = $('img[data-imgid="'+i+'"]').attr('src');
        var b= '-gry'; 
        if(i != feel_val){
          var position = -4;
          var output = [src.slice(0, position), b, src.slice(position)].join(''); 
          if(src.search(b) < 0){
            $('img[data-imgid="'+i+'"]').attr('src', output);
          }
        }else{ 
          if(src.search(b) > 0){  
            var output = src.replace(b, "");
            $('img[data-imgid="'+i+'"]').attr('src', output);
          }
        }
      }

      if(feel_val){
        $('input[name="feel"]').val(feel_val);
      }

      $('.swal-button.swal-button--confirm').removeAttr('disabled');
    
    });

    //match string in array
    function match_string(str1, list)
    {
      var list2   = '';
      var ismatch  = 0;

      if(list.length > 0)
      {
        for (var i = 0; i < list.length; i++) 
        {
          var all = list[i].indexOf("*");
          if(all >= 0) //with wildcard
          {
            list2 = list[i].replace("*", " ");
            list2 = list2.trim();
            if (str1.search(list2.trim()) >= 0) ismatch = 1;
          }
          else
          {
            list2 = list[i].trim();
            if (str1.search(list2.trim()) >= 0) ismatch = 1;
          }
        }

      }
      // else ismatch = 1;

      return ismatch;
    }

    //open csat survey
    function swal_open(question, wrap)
    {
      swal({
          title: question,
          // text: "",
          // icon: "info",
          className: 'swal-logout',
          closeOnClickOutside: false,
          content: {
            element: wrap
          },
          buttons: {
            cancel: {
              text: "Close",
              value: 'close',
              visible: true,
              className: "btn btn-secondary",
              closeModal: true,
            },
            confirm: {
              text: "Submit",
              value: 'submit_exp',
              visible: true, 
              className: "btn btn-primary CsatSubmitButton", 
              closeModal: true,
            },
          }
      }).then(function (value) {
        
          if (value === 'close') {
            // window.location.reload();
            // swal("You are now logged out. Thank you for using the app!", {
            //   icon: "success",
            // });
            // setTimeout(function() { window.location.href = app_url_logout; }, 2000);
          }else if(value === 'submit_exp'){
            // alert($('input[name=feel]').val());
            if($('input[name=feel]').val()){
                // console.log('submit')
                submit_exp(); 
            }else{ 
                $('.swal-content').append('<p class="col-12 mt-4 text-danger small">Please select from the smileys.</p>');
            }
          }  
      });

      //add id in submit
      $('.CsatSubmitButton').attr('id', 'CsatSubmitButton');

      //change csat survey bgcolor
      var bgcolor = config_bgcolor || '';
      // var bgcolor = $('input[name=config_bgcolor]').val() || '';
      if(bgcolor !== '') $('.swal-modal').css('background', bgcolor);

      //position csat survey
      var align = config_align || '';
      // var align = $('input[name=config_align]').val() || '';
      if(align !== '') $('.swal-modal').css(align, 0);
      
      $('.swal-button.swal-button--confirm').attr('disabled', 'disabled');
    }

    //submit csat survey
    function submit_exp(){
      var feel_val = $('input[name="feel"]').val();
      var feedback_val = $('textarea[name="feedback"]').val();
      if(feel_val)
        { 
          // var csrf_val = $('meta[name="csrf-token"]').attr('content');
          $.ajax({ 
            // headers: {
            //     'X-CSRF-TOKEN': csrf_val},
             url : basepath+'csat/submit', 
             type : "POST",  
             data : {'rate':feel_val, 'feedback':feedback_val },
             dataType: "json",
             success:function(data)
             { 
                var message = config_message || '';
                if(message !== '') swal(message, '', "success");
                else swal('Thanks for your rating!', '', "success");
                
                // setTimeout(function() { window.location.reload(); }, 1000);
                // setTimeout(function() { reset_csat_form(); }, 1000);

             }, 
          });
       }
      
    }

    function reset_csat_form()
    {
      $('input[name="feel"]').val('');
      $('textarea[name="feedback"]').val('');
      for(var i = 1; i<=5; i++ ){
        var src = $('img[data-imgid="'+i+'"]').attr('src');
        var b= '-gry'; 
        var position = -4;
        if(src.search(b) > 0){  
          var output = src.replace(b, "");
          $('img[data-imgid="'+i+'"]').attr('src', output);
        }
        
      }
    }

    //reports datatable
    if($('#reports_table').length > 0)
    {
      if ( ! $.fn.DataTable.isDataTable( '#reports_table' ) ) 
      {
        $('#reports_table').DataTable().destroy();
        $('#reports_table').DataTable({
          responsive:  {
            details: {
                type: 'column'
            }
          },
          columns: [
              {
                  responsivePriority  : 5,
                  className           : 'text-center',
                  width               : "10%"
              },
              {
                  responsivePriority  : 1,
                  className           : 'text-center',
                  width               : "20%"
              },
              {
                  responsivePriority  : 3,
                  className           : 'text-center',
                  width               : "20%"
              },
              {
                  responsivePriority  : 2,
                  width               : "30%"
              },
              {
                  responsivePriority  : 4,
                  width               : "20%"
              }
          ],
          columnDefs: [
            {
              className: 'control',
              orderable: false,
              targets:   0,

            },
            {
              className: 'wrap-text text-center dt-center',
              targets:   2,

            },
            {
              className: 'text-center dt-center',
              targets:   [0,1],

            }
          ],
          autoWidth: true,  
          dom: '<"dt-buttons"B>lfrtip',
          buttons: [
            {
                extend: 'excelHtml5',
                title : 'Customer Satisfaction Report'
            },
            {
                extend: 'csvHtml5',
                title : 'Customer Satisfaction Report'
            },
            {
                extend: 'pdfHtml5',
                // orientation: 'landscape',
                // pageSize: 'LEGAL',
                title : 'Customer Satisfaction Report',
                customize: function (doc) {
                  // doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
                  doc.content[1].table.widths = 
                      Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                  var rowCount = doc.content[1].table.body.length;
                  for (i = 1; i < rowCount; i++) {
                    doc.content[1].table.body[i][0].alignment = 'center';
                    doc.content[1].table.body[i][1].alignment = 'center';
                    doc.content[1].table.body[i][2].alignment = 'center';
                  };
                }
            }   
          ]
        });
      }
    }
    //reports datatable
    
  }
};
// 


})(jQuery, Drupal, drupalSettings);
