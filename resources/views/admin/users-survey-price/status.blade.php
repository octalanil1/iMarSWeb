<script type="text/javascript">
    $(document).ready(function () 
    { 
        $( '#addUserForm' ).on( 'submit', function(e) 
        {
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
            var status = $('#status').val();
            var survey_id = $('#survey_id').val();
            var status_id = $('#status_id').val();
            
            $.ajax({
                dataType: 'json',
            data: { status:status,survey_id:survey_id,status_id:status_id}, 
            type: "POST",
            url: '{{ URL::to('/admin/change-surveryor-status-post') }}',
        }).done(function( data ) 
        {  error_remove (); if(data.success==false)
            {
                $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                });

          }else{
            search();
            $("#msg-data").html(data.message);
            $("#myModal").modal('hide');
            
	            return false;
          }
            
                         
        });

    });
});
</script>

    {!! Form::open(array('url' => '/admin/change-surveryor-status-post', 'method' => 'post','name'=>'addUserForm','files'=>true,'novalidate' => 'novalidate','id' => 'addUserForm')) !!}
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row">
                <div class="alert alert-danger" style="display:none"></div>
                <div class="col-12 col-sm-12 col-md-12">
                        <div class="row form-group">
                            <label class="col-lg-6 col-sm-6 col-xs-12 control-label">Select Surveryor Status<span class="red-star">*</span></label>
							  {!! Form::hidden('survey_id',$survey_id,['id'=>'survey_id']) !!}
							  {!! Form::hidden('status_id',$status_id,['id'=>'status_id']) !!}
                            <div class="col-lg-6 col-sm-6 col-xs-12">
							@if($status_id == 4)
								{!! Form::select('status', [""=>"Select Surveryor Status","5"=>"Invoice Paid"],null, ['class' => 'form-control','required'=>'required','id'=>'status','onkeypress' => 'error_remove()']) !!}
							@else 
								{!! Form::select('status', [""=>"Select Surveryor Status","6"=>"Surveyor Paid"],null, ['class' => 'form-control','required'=>'required','id'=>'status','onkeypress' => 'error_remove()']) !!}
                            @endif 								
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row form-btn text-center">
        <div class="col-sm-12 p-r-30">
        <div class="col-md-12"> 
        {!! Form::submit('Save',['class' => 'btn btn-primary btn-flat subbtn', 'type' => 'submit']) !!}
        {!! Form::submit('Cancel',['class' => 'btn btn-flat subbtn','data-dismiss'=>'modal']) !!}
        </div>
    </div>
    {!! Form::close() !!}
