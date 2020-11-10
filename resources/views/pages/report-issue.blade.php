<script type="text/javascript">
    $(document).ready(function () 
    {
        $( '#ReporIssueForm' ).on( 'submit', function(e) 
        {
			$.LoadingOverlay("show");
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
            url: '{{ URL::to('/report-issue-post') }}',
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
                
                  if(data.class == 'success'){showMsg(data.message, "success");}
                  if(data.class == 'danger'){showMsg(data.message, "danger");}

                  showpage('{{URL::asset('/report-issue')}}');
            }
           $.LoadingOverlay("hide");     
        });

    });
});


</script>
<style>
	.sieldest-list {
		display: inline-block;
		width: 100%;
		border: 1px solid #d2e9f3;
		border-radius: 5px;
	}
	.sieldest-list li {
		list-style: none;
		padding: 15px;
		// border-bottom: 1px solid #d2e9f3;
	}
	.upload-name {
		border: 0px;
		background: transparent;
		position: relative;
		top: 10px;
		padding-left: 15px;
	}
	</style> 
 <section class="page">
		<div class="row">
		    <div class="col-md-12 col-lg-12 col-xl-12">
		    	<div class="surveyors">
             <div class="right-flex-box"> <h4>Report an Issue</h4> </div>
                {!! Form::open(array('url' => 'report-issue-post', 'method' => 'post','name'=>'ReporIssueForm','files'=>true,'novalidate' => 'novalidate','id' => 'ReporIssueForm')) !!}
                <div class="form-group">
                    {!! Form::select('survey_id', $survey_data_box,null, ['class' => 'form-control','required'=>'required','id'=>'survey_id']) !!}

              </div>
              <div class="form-group">
                    {!! Form::textarea('comment', null, ['class' => 'form-control','placeholder' => 'Comment','required'=>'required','id'=>'comment']) !!}

              </div>
              <ul class="sieldest-list">
				<li>
                    <div class="row">
                        <label class="col-md-4 control-label Upload-Wire">File:</label>
                        <div class="col-md-2">
                            <div class="upload-file" id="file">
                            <input type="file" name="file" id="myfile" class="form-control input-file" onkeypress="error_remove()">
                                <button class="btn-success">Upload</button>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                        <input type="text" class="upload-name" id="input-file-placeholder" placeholder="File name" disabled>
                        </div>
                        </div>
                </li>
               
                </ul>
              <div class="login-inner">
              <button type="submit" class="btn btn-primary">Submit<img src="{{ URL::asset('/public/media') }}/arrow.png" alt="#"></button>
                  {!! Form::close() !!}
              </div>

		    				
		  	</div>
		</div>
		</div>
</section>
      
  