<script type="text/javascript">
    $(document).ready(function () 
    {  $.LoadingOverlay("hide");
        $( '#addCountryForm' ).on( 'submit', function(e) 
        {
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
            url: '{{ URL::to('/admin/add-country-post') }}',
        }).done(function( data ) 
        {  error_remove (); if(data.success==false)
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

    {!! Form::open(array('url' => '/admin/add-country-post', 'method' => 'post','name'=>'addCountryForm','files'=>true,'novalidate' => 'novalidate','id' => 'addCountryForm')) !!}
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row">
                <div class="alert alert-danger" style="display:none"></div>
                
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Country Name<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('name',null, ['class' => 'form-control','placeholder' => 'Country Name','required'=>'required','id'=>'name','onkeypress' => 'error_remove()' ]) !!}
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Country Code<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('sortname',null, ['class' => 'form-control','placeholder' => 'Country Code','required'=>'required','id'=>'sortname','onkeypress' => 'error_remove()' ]) !!}
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6">
                        <div class="row form-group">
                            <label class="col-lg-4 col-sm-6 col-xs-12 control-label">Dialing Code<span class="red-star">*</span></label>
                            <div class="col-lg-8 col-sm-6 col-xs-12">
                            {!! Form::text('phonecode',null, ['class' => 'form-control','placeholder' => 'Dialing Code','required'=>'required','id'=>'phonecode','onkeypress' => 'error_remove()' ]) !!}
                            </div>
                            
                        </div>
                    </div>
                <div class="col-12 col-sm-12 col-md-12">
                    <div class="row form-group">  
                        <label class="col-md-2 control-label"></label>
                        <label class="checkbox-inline-country">REGISTRATION
                        {!! Form::checkbox('registration','1',null, ['id'=>'registration'] )!!} 
                        <span class="checkmark"></span>
                        </label>
                        <label class="checkbox-inline-country">COUNTRIES WITH PORTS
                        {!! Form::checkbox('country_with_ports','1',null, ['id'=>'country_with_ports'] )!!} 
                        <span class="checkmark"></span></label>
                        <label class="checkbox-inline-country">CHASE WIRE COUNTRIES
                        {!! Form::checkbox('chase_wire_country','1',null, ['id'=>'chase_wire_country'] )!!} 
                        <span class="checkmark"></span>

                    </label>
                        <label class="checkbox-inline-country">PAYPAL COUNTRIES
                        {!! Form::checkbox('paypal_country','1',null, ['id'=>'paypal_country'] )!!} 
                        <span class="checkmark"></span>

                    </label>
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
