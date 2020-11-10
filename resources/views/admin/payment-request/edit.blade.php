<script type="text/javascript">
    $(document).ready(function () 
    {    $.LoadingOverlay("hide");
        $( '#editEarningForm' ).on( 'submit', function(e) 
        {
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
           
            var status = $('#status').val();
            var id = $('#id').val();
         
          
           
            $.ajax({
                dataType: 'json',
            data: {id:id,status:status}, 
            type: "POST",
            url: '{{ URL::to('/admin/edit-payment-request-post') }}',
        }).done(function( data ) 
        {  error_remove (); if(data.success==false)
            {
                $.each(data.errors, function(key, value){
                    $('#'+key).closest('.form-group').addClass('has-error');
					$('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));
                });

          }else{
            search();
            //alert(data.message);
            if(data.class == 'success'){showMsg(data.message, "success");}
            $("#myModal").modal('hide');
            
	            return false;
          }
            
                         
        });

    });
});
</script>

    {!! Form::open(array('url' => '/admin/edit-content-post', 'method' => 'post','name'=>'editEarningForm','files'=>true,'novalidate' => 'novalidate','id' => 'editEarningForm')) !!}
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row">
                <div class="alert alert-danger" style="display:none"></div>
                <div class="col-12 col-sm-12 col-md-12">
                            <div class="row form-group">
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Status</label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                {!! Form::select('status',[''=>'Select Status','unpaid'=>'Unpaid','paid'=>'Paid'],$payment_request_data->status  , ['class' => 'form-control','required'=>'required','id'=>'status','onkeypress' => 'error_remove()']) !!}

                                </div>{!! Form::hidden('id',base64_encode($payment_request_data->id),['id'=>'id']) !!}

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
    <script src="{{URL::asset('resources/admin_assets/bower_components/ckeditor/ckeditor.js')}}"></script>

    <script>
  $(function () {
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('description')
    //bootstrap WYSIHTML5 - text editor
    $('.textarea').wysihtml5()
  })
</script>