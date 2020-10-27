
<script type="text/javascript">
    $(document).ready(function () 
    {
        $.LoadingOverlay("hide");
        $( '#MyOperatorForm' ).on( 'submit', function(e) 
        {
			    //$.LoadingOverlay("show");
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
            
            $.ajax({
                dataType: 'json',
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
            url: '{{ URL::to('/add-rating-post') }}',
        }).done(function( data ) 
        {  error_remove (); 
			if(data.success==false)
            {
                    $.each(data.errors, function(key, value){
                        $('#'+key).closest('.form-group').addClass('has-error');
                        $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                    });
                

          }else
		  {
          
            if(data.class == 'success')
            {
                showMsg(data.message, "success");
                $("#ratingModal").modal('hide');
                $("#myModal").modal('hide');
                $('.modal-backdrop').remove();     

                showpage('{{URL::asset('/mysurvey')}}');
            }
            else{
                showMsg(data.message, "danger");
            }
            
          }
          $.LoadingOverlay("hide");     
        });

    });
});

</script> 
<script type="text/javascript">
       
  
       function cancelform(take_user_id,give_user_id)
    {
    $('#take_user_id').val(take_user_id);
    $('#give_user_id').val(give_user_id);
    $('#sho-form').show();
    }
      
       function closeform()
    {
    $('#rating_1,#rating_2,#rating_3,#rating_4,#rating_5').val("");
    $('#sho-form,#error_rating').hide();
    $('.rate-main-li li').each(function(index) {
      $(this).removeClass('selected');
    });
    }
       
   function formvl(){
    var err = "";      
  $('.rating-hd').each(function(){
  if($(this).val()==""){
  err = "1";  
  }             
   });       
    
  if(err!=""){
  $("#error_rating").show(); 
    return false; 
  }else{
  $("#error_rating").hide();   
  return true;
  }
}
   
function highlightStar(obj,id) {
  removeHighlight(id);    
  $('#tutorial-'+id+' li').each(function(index) {
    $(this).addClass('highlight');
    if(index == $('#tutorial-'+id+' li').index(obj)) {
      return false; 
    }
  });
}

function removeHighlight(id) {
  $('#tutorial-'+id+' li').removeClass('selected');
  $('#tutorial-'+id+' li').removeClass('highlight');
}

function addRating(obj,id) {
  $('#tutorial-'+id+' li').each(function(index) {
    $(this).addClass('selected');
    $('#rating_'+id).val((index+1));
    if(index == $('#tutorial-'+id+' li').index(obj)) {
      return false; 
    }
  });
  /*$.ajax({
  url: "add_rating.php",
  data:'id='+id+'&rating='+$('#tutorial-'+id+' #rating').val(),
  type: "POST"
  });*/
}

function resetRating(id) {
  if($('#rating_'+id).val() != 0) {
    $('#tutorial-'+id+' li').each(function(index) {
      $(this).addClass('selected');
      if((index+1) == $('#rating_'+id).val()) {
        return false; 
      }
    });
  }
}   
 $(document).ready(function(){
  closeform();
                
   });     
</script> 
  <div class="login-inner">
{!! Form::open(array('url' => 'add-rating-post', 'method' => 'post','name'=>'MyOperatorForm','files'=>true,'novalidate' => 'novalidate','id' => 'MyOperatorForm')) !!}


<div class="row">
                    <div class="col-sm-12"> <span id="error_rating" style = "color:red;display: none;"> <span> Please select all type of rates .</span> </span>
                      <div id="tutorial-1" class="rate-main-li survey-group">
                      <!--  <h4>Punctuality</h4>-->
                        <ul onmouseout="resetRating(1);" class="add-rating survey-star">
                          <li class="" onmouseover="highlightStar(this,1);" onmouseout="removeHighlight(1);" onclick="addRating(this,1);">★</li>
                          <li class="" onmouseover="highlightStar(this,1);" onmouseout="removeHighlight(1);" onclick="addRating(this,1);">★</li>
                          <li class="" onmouseover="highlightStar(this,1);" onmouseout="removeHighlight(1);" onclick="addRating(this,1);">★</li>
                          <li class="" onmouseover="highlightStar(this,1);" onmouseout="removeHighlight(1);" onclick="addRating(this,1);">★</li>
                          <li class="" onmouseover="highlightStar(this,1);" onmouseout="removeHighlight(1);" onclick="addRating(this,1);">★</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
{!! Form::hidden('rating_1', null, ['id' => 'rating_1','class' => 'rating-hd']) !!}

</div>
{!! Form::hidden('survey_id',base64_encode($survey_id),['id'=>'survey_id']) !!}

{!! Form::hidden('operator_id',base64_encode($operator_id),['id'=>'operator_id']) !!}
{!! Form::hidden('surveyor_id',base64_encode($surveyor_id),['id'=>'surveyor_id']) !!}

<div class="form-group">
{!! Form::textarea('comment', null, ['class' => 'form-control','placeholder' => 'Comment','required'=>'required','id'=>'comment','style'=>"height: 87px;"]) !!}
</div>
<button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
{!! Form::close() !!}
  </div>