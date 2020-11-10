<script type="text/javascript">
    $(document).ready(function () 
    { $.LoadingOverlay("hide");
        $( '#editContentForm' ).on( 'submit', function(e) 
        {
            e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
           
            var title = $('#title').val();
            var id = $('#id').val();
            var user_type = $('#user_type').val();

            var description = CKEDITOR.instances['description'].getData();
            $.ajax({
                dataType: 'json',
            data: {title:title,id:id,description:description,user_type:user_type}, 
            type: "POST",
            url: '{{ URL::to('/admin/edit-content-post') }}',
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
            if(data.class == 'danger'){showMsg(data.message, "danger");}

            $("#myModal").modal('hide');
            
	            return false;
          }
            
                         
        });

    });
});
</script>

    {!! Form::open(array('url' => '/admin/edit-content-post', 'method' => 'post','name'=>'editContentForm','files'=>true,'novalidate' => 'novalidate','id' => 'editContentForm')) !!}
    <div class="col-sm-12 p-r-30">
        <div class="panel form-horizontal panel-transparent">
            <div class="panel-body">
                <div class="row">
                <div class="alert alert-danger" style="display:none"></div>
                <div class="col-12 col-sm-12 col-md-12">
                            <div class="row form-group">
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Visible User<span class="red-star">*</span></label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                {!! Form::select('user_type',["all"=>'All','operator'=>'Operator','surveyor'=>'Surveyor'],$content_data->user, ['class' => 'form-control','required'=>'required','id'=>'user_type','onkeypress' => 'error_remove()' ]) !!}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="row form-group">
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Title<span class="red-star">*</span></label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                {!! Form::hidden('id',base64_encode($content_data->id),['id'=>'id']) !!}
                                {!! Form::text('title',$content_data->title, ['class' => 'form-control','placeholder' => 'Title','required'=>'required','id'=>'title','onkeypress' => 'error_remove()' ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="row form-group">
                                <label class="col-lg-2 col-sm-6 col-xs-12 control-label">Description<span class="red-star">*</span></label>
                                <div class="col-lg-10 col-sm-6 col-xs-12">
                                    <textarea id="description" name="description" rows="10" cols="80">{{$content_data->description}}</textarea>
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
    <script src="{{asset('/public/admin_assets/bower_components/ckeditor/ckeditor.js')}}"></script>

    <script>
  $(function () {
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('description')
    //bootstrap WYSIHTML5 - text editor
    $('.textarea').wysihtml5()
  })
</script>