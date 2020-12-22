<script type="text/javascript">
    $(document).ready(function () 
    { 
         $.LoadingOverlay("hide");
    });
         </script>
<style>
    .fas.fa-star.selected{
        color:#ec8b5d;
    }
    .listing.ratingby li {
  display: inline-block;
  width: 100%;
  padding-bottom: 3px;
}
.listing.ratingby li .user-info {
  width: 100%;
  font-size: 21px;
  padding: 0px;
}
    </style>
<section class="detail_outer">
		<div class="container">
			<div class="row">
           
				<div class="col-md-12">
					<div class="box-outer">
                        <p><span>About Me: </span>{{$surveyor_data->about_me}}</p>
                        <p><span>Experience  : </span>{{$surveyor_data->experience}} Years</p>
                        <p><span>  Rating & Review : </span></p>
                     
                       <ul class="listing ratingby">
                           <?php if(!empty($comment_data)){
                               
                               foreach( $comment_data as $data ){?>
                           
                         
                           <li>
                               <span class="user-info">@if(!empty($data->operator_name)) {{$data->operator_name}} @endif</span>
                               <span>
                               <?php
                    
                   
                    for($i=1;$i<=5;$i++) 
                    { 
                        if($data->rating < $i ) {
                            if(is_float((float)$data->rating) && (round((float)$data->rating) == $i)){
                                
                                echo "<img src=\"https://www.imarinesurvey.com/public/media/star-half.png\">";
                            }else{
                                echo "<img src=\"https://www.imarinesurvey.com/public/media/star-empty.png\">";
                            }
                         }else {
                            echo "<img src=\"https://www.imarinesurvey.com/public/media/star.png\">";
                         } 
                         } ?>
                                
                                
                            </span>
                               
                               <p>@if(!empty($data->comment)) {{$data->comment}} @endif</p>

                           </li>
                               <?php }} ?>
                       </ul>
                    </div>
                </div>
            </div>
		</div>
    </section>
  