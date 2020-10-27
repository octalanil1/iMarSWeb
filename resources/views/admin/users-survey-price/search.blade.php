
<table class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>SR.NO.</th>
                  <th>User</th>
                  <th>Survey Type</th>
                  <th>Survey Price</th>
                  <th>Created</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($surveydata as $key => $data)
                <tr>
                <td>{{$surveydata->firstItem() + $key}}</td>
                  <td>{{$data->user_email}}</td>
                  <td>{{$data->survey_type_name}}</td>
                  <td>{{$data->survey_price}}</td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                
		
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td class="text-center" colspan="5">No Survey Price Data</td>

                </tr>
                @endif    
                </tbody> 
                
    </table>
    <?php 
          $per_page =  $surveydata ->perPage();
          $cuurent_page = $surveydata ->currentPage();
          $strt_at =  ($per_page*($cuurent_page-1))+1;
					$end_at = ($strt_at+$surveydata ->count())-1;
          $text_line = "Showing ".$strt_at." to ".$end_at; 
    ?>
  <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $surveydata ->total(); ?> rows </div>

    {!! $surveydata->links() !!}