<script type="text/javascript">
    $(document).ready(function () 
    { $.LoadingOverlay("hide");
        $( '#addUserForm' ).on( 'submit', function(e) 
        {
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
          
            var name = $('#name').val();
           
            $.ajax({
                dataType: 'json',
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
            url: '{{ URL::to('/admin/add-survey-type-post') }}',
        }).done(function( data ) 
        {  error_remove (); if(data.success==false)
            {
                $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                });

          }else{
            search();
            if(data.class == 'success'){showMsg(data.message, "success");}

            $("#myModal").modal('hide');
            
	            return false;
          }
            
                         
        });

    });
});
</script>

    {!! Form::open(array('url' => '/admin/add-survey-type-post', 'method' => 'post','name'=>'addUserForm','files'=>true,'novalidate' => 'novalidate','id' => 'addUserForm')) !!}
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row">
                <div class="alert alert-danger" style="display:none"></div>
                
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Name<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('name',null, ['class' => 'form-control','placeholder' => 'Name','required'=>'required','id'=>'name','onkeypress' => 'error_remove()' ]) !!}
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Code<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('code',null, ['class' => 'form-control','placeholder' => 'Code','required'=>'required','id'=>'code','onkeypress' => 'error_remove()' ]) !!}
                            </div>
                            
                        </div>
                    </div>
					 <!-- <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Price<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('price',null, ['class' => 'form-control','placeholder' => 'Price','required'=>'required','id'=>'price','onkeypress' => 'error_remove()' ]) !!}
                            </div>
                            
                        </div>
                    </div> -->
					<div class="col-12 col-sm-12 col-md-6">
                            <div class="row form-group">
                                <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Status<span class="red-star">*</span></label>
                                <div class="col-lg-8 col-sm-6 col-xs-12">
                                <?php $helper=new App\Helpers;?>
                                {!! Form::select('status',[''=>'Select Status','1'=>'Active','0'=>'Deactivated'],[''], ['class' => 'form-control','required'=>'required','id'=>'status','onkeypress' => 'error_remove()']) !!}
                                </div>
                                
                            </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                            <div class="row form-group">
                                <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Daily Price<span class="red-star">*</span></label>
                                <div class="col-lg-8 col-sm-6 col-xs-12">

                                {!! Form::checkbox('type','1',null, ['required'=>'required','id'=>'status','onkeypress' => 'error_remove()']) !!}
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
