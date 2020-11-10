<script type="text/javascript">
    $(document).ready(function () 
    {   $.LoadingOverlay('hide');
        $( '#addProductInventory' ).on( 'submit', function(e) 
        {$.LoadingOverlay('show');
            e.preventDefault();
               $.ajaxSetup({
                  headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}

              });
            $.ajax({
               dataType: 'json',
               type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                 processData:false,
                  url: '{{ URL::to('/admin/import-port-post') }}',

        }).done(function( data ) 
        {  error_remove (); 
        if(data.success==false)
        {
              $.each(data.errors, function(key, value){
              $('#'+key).closest('.form-group').addClass('has-error');
					      $('<div class="jquery-validate-error help-block animated fadeInDown">'+value+'</div>').insertAfter($('#'+key));

                });

          }else{
            search();
            if(data.class == 'success')
            {showMsg(data.message, "success");}
            $("#myModal").modal('hide');
	            return false;
          }                 
        });

    });

});

</script>



    {!! Form::open(array('url' => '/admin/import-port-post', 'method' => 'post','name'=>'addProductInventory','files'=>true,'novalidate' => 'novalidate','id' => 'addProductInventory')) !!}

    <a href="{{asset('/media/sample_port_import.xlsx')}}">Download Sample File</a>
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12">
                        <div class="row form-group">
                            <label class="col-lg-12 col-sm-12 col-xs-12">Select File<span class="red-star">*</span></label>
                            <div class="col-lg-12 col-sm-12 col-xs-12" id="import_file">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="input-file-placeholder" placeholder="Upload File" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3" >
                                        <span class="import-excel" >
                                        <input type="file" name="import_file"  class="form-control input-file" onkeypress="error_remove()">
                                        <button class="btn btn-outline-success">Browse</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
    <div class="row form-btn text-center">
        <div class="col-sm-12 p-r-30">
        {!! Form::submit('Import',['class' => 'btn btn-primary btn-flat subbtn', 'type' => 'submit']) !!}
        {!! Form::submit('Cancel',['class' => 'btn btn-flat subbtn','data-dismiss'=>'modal']) !!}
        </div>
    </div>
    {!! Form::close() !!}
