<script type="text/javascript">
    $(document).ready(function () 
    { 
         $.LoadingOverlay("hide");
    });
</script>

<style>
ul.listing li span.user-img img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    object-position: top;
}


ul.listing li span.user-img {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100px;
    height: 100px;
    border: 1px solid #ccc;
    border-radius: 50%;
    float: left;
    overflow: hidden;
}
    .fas.fa-star.selected{
        color:#ec8b5d;
    }
    #surveyors_id #sort {
        margin-bottom: 0px;
        height: 45px;
    }
    
    #surveyor-msg {
        color: red;
        padding: 10px;
        margin-bottom: 0px;
        display: block;
    }
/* Custom Checkbox Box */
    
    .user-info .custom_check {
        display: inline-block;
        cursor: pointer;
        line-height: 22px;
        position: relative;
        padding-left: 30px;
        margin: 5px 0px;
        position: absolute;
        right: 0px;
        top: 67px;
        width: 30px;
    }
    .label-formbox {
    position: absolute;
    right: 40px;
    top: 49%;
    transform: translateY(-50%);
    background: 
    #2f3c7f;
    padding: 2px 8px 3px;
    font-size: 13px;
    border-radius: 55px;
    color:
        #fff;
        width: 88px;
        text-align: center;
    }
    
    .user-info .custom_check input {
        width: 100%;
        height: 100%;
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        cursor: pointer;
        margin: 0px 0px;
        z-index: 2;
    }
    
    .user-info .custom_check .check_indicator {
        height: 22px;
        width: 22px;
        position: absolute;
        top: 0px;
        left: 0px;
        background: #ffffff;
        border: 1px solid #cccccc;
        border-radius: 0px;
    }
    
    .user-info .custom_check input:checked+input+.check_indicator:before,
    .user-info .custom_check input:checked+input+input+.check_indicator:before,
    .user-info .custom_check input:checked+input+input+input+.check_indicator:before,
    .user-info .custom_check input:checked+input+input+input+input+.check_indicator:before,
    .user-info .custom_check input:checked+input+input+input+input+input+.check_indicator:before,
    .user-info .custom_check input:checked+input+input+input+input+input+input+.check_indicator:before,
    .user-info .custom_check input:checked+input+input+input+input+input+input+input+.check_indicator:before,
    .user-info .custom_check input:checked+input+input+input+input+input+input+input+input+.check_indicator:before {
        width: 7px;
        height: 12px;
        position: absolute;
        left: 6px;
        top: 2px;
        border: solid #00cc00;
        border-width: 0px 2px 2px 0px;
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
        content: "";
    }
</style>
    <div class="row">
        <div class="col-md-6 col-lg-6 col-xl-6" >
            {!! Form::select('sort',['rating'=>'Rating','low_high'=>'Price Low to High','high_low'=>'Price High to Low','job_ac'=>'Job Acceptance % High to Low'],$sort, ['class' => 'form-control', 'id' => 'sort','required'=>'required','onkeypress' => 'error_remove()','onchange'=>'Sort();']) !!}
        </div>
    </div>
<div class="row">
<div class="col-md-12 col-lg-12 col-xl-12">
    @if($survey_type_id=='31') <span style="color:red;padding:10px;">Select Up to 5 surveyors to receive bids. </span>  
    @else <span style="color:red;padding:10px;" id="surveyor-msg">Select Primary Surveyor</span>  
    @endif
<div class="surveyors">							
    <ul class="listing">
        <?php if(count($surveyor_user_data)!=0 ) {?>
        @foreach($surveyor_user_data as $data)
        <li>
            <a href="javascript::void(0);" onclick="view_record('{{$data['id']}}')">
            <span class="user-img" ><img src="@if($data['image']!="") {{$data['image']}} @endif" alt="#"></span>
        </a>
            <span class="user-info">
                <h4>{{$data['first_name']}}  {{$data['last_name']}}   @if($data['company']!="") - {{$data['company']}}@endif </h4>
                <p>  
                    @if($survey_type_id=='31')
                        ${{$data['port_price']}} transportation cost 
                    @else
                            @if($data['port_price']=='0') 
                                ${{$data['pricing']}}  
                                @if($data['price_type']=='daily') 
                                    / Day 
                                @endif
                        @else
                            ${{$data['pricing']}}    
                            @if($data['price_type']=='daily') / Day @endif + ${{$data['port_price']}} transportation cost 
                        @endif
                    @endif
                    <span>
                        <?php
                        // for($i=1;$i<=5;$i++) 
                        // {
                        //     $selected = "";
                        
                        //     if(!empty($data['rating']) && $i<=$data['rating']) {
                        //     $selected = "selected";
                        //     }
                        //     ?>
                        
                    <?php  //}  ?>

                    <?php
                    
                   
                        for($i=1;$i<=5;$i++) 
                        { 
                            if($data['rating'] < $i ) {
                                if(is_float((float)$data['rating']) && (round((float)$data['rating']) == $i)){
                                    
                                    echo "<img src=\"https://72.octallabs.com/imars/public/media/star-half.png\">";
                                }else{
                                    echo "<img src=\"https://72.octallabs.com/imars/public/media/star-empty.png\">";
                                }
                             }else {
                                echo "<img src=\"https://72.octallabs.com/imars/public/media/star.png\">";
                             } 
                             } ?>
                </span>
            </p>
            <p>Job Acceptance: @if(!empty($data['percentage_job_acceptance']))  {{$data['percentage_job_acceptance']}} % @else 0% @endif</p>
            <p>Avg.Response: {{$data['average_response_time']}}</p>
            <span class="custom_check">&nbsp; <input type="checkbox" name="surveyors_id[]" class="mycheckbox" value="{{$data['id']}}"><span class="check_indicator">&nbsp;</span></span>

        </span>
    </li>
    @endforeach
<?php }else{ ?>

<li>
<span class="ship-info">
<h3>No Surveyor Available</h3>
</span>
</li>
<?php  } ?>

</ul>
</div>
</div>
</div>

