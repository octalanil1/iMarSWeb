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
           
            var received_from_operator = $('#received_from_operator').val();
            var id = $('#id').val();
           var invoice_status = $('#invoice_status').val();
          //alert(invoice_status);
           
            $.ajax({
                dataType: 'json',
            data: {received_from_operator:received_from_operator,invoice_status:invoice_status,id:id}, 
            type: "POST",
            url: '{{ URL::to('/admin/edit-earning-post') }}',
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
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Received From Operator</label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                {!! Form::text('received_from_operator ',$earning_data->received_from_operator , ['class' => 'form-control','placeholder' =>'Received From Operator ','required'=>'required','id'=>'received_from_operator','onkeypress' => 'error_remove()' ]) !!}
                                </div>{!! Form::hidden('id',base64_encode($earning_data->id),['id'=>'id']) !!}

                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="row form-group">
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Invoice Paid Status</label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                {!! Form::select('invoice_status',[''=>'Select Status','unpaid'=>'Invoice Unpaid','paid'=>'Invoice Paid'],$earning_data->invoice_status , ['class' => 'form-control','required'=>'required','id'=>'invoice_status','onkeypress' => 'error_remove()']) !!}
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

    