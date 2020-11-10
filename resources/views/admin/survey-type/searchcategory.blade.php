<table class="table table-bordered table-hover">
                <thead>
                <tr>
                <th>SR.NO.</th>
                  <th>Name</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;?>
                  @foreach($catedata as $data)
                <tr>
                  <td>{{$i}}</td>
                  <td>{{$data->name}}</td>
                  <td>{{$data->created_at}}</td>
                  <td class="res-dropdown" style="" align="center">
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-primary" data-original-title="Edit Category" onclick="edit_record('{{base64_encode($data->id)}}')" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></a>
                  <a data-toggle="tooltip" data-placement="top" title="" href="javascript:" class="btn btn-danger" data-original-title="Remove Category" onclick="remove_record('{{base64_encode($data->id)}}')"><i class="fa fa-trash" aria-hidden="true"></i></a></a>
                
                </td>
                </tr>  
                <?php $i++;?>
                @endforeach

                @if($i<2)
                <tr>
                <td>No Category Data</td>
                </tr>
                @endif    
                </tbody> 
                <?php $per_page =  $catedata->perPage();
                $total_page =  ceil($catedata ->total()/$per_page);
                if($total_page>1)
                {?>
                    <div class="panel-primary pagging">
                    <?php $cuurent_page = $catedata ->currentPage();
                          $next_page = $catedata ->currentPage()+1;
                          $prev_page = $catedata ->currentPage()-1;
                          $pageUrl = URL::to('/admin/users')."?";
                          if($src!="") {$pageUrl = $pageUrl."q=".$src."&"; }  
                          $strt_at =  ($per_page*($cuurent_page-1))+1;
                          $end_at = ($strt_at+$catedata ->count())-1;
                          if($cuurent_page==1){ $curent_pg_url = "javascript:void();"; $cur_pg_class = "prev btn btn-success btn-xs";}else{$curent_pg_url = $pageUrl."page=".$prev_page."&rowsize=".$per_page; $cur_pg_class = "prev btn btn-info btn-xs";}
                          if($cuurent_page>=$total_page){ $nxt_pg_url = 'javascript:void();'; $nxt_pg_class = "next btn btn-success btn-xs";}else{$nxt_pg_url = $pageUrl."page=".$next_page."&rowsize=".$per_page; $nxt_pg_class = "next btn btn-info btn-xs";} 
                        $text_line = "Showing ".$strt_at." to ".$end_at;

						?>
        <div class="row page-count panel-heading">
          <div class="col-sm-4"> <?php echo $text_line; ?> of <?php echo $catedata ->total(); ?> entries </div>
          <div class="col-sm-4">
            <ul>
              <?php if($catedata ->currentPage()>1){?>
              <li><a href="<?php echo $curent_pg_url;?>" class="<?php echo $cur_pg_class;?>"><i class="fa fa-angle-double-left"></i></a> </li>
              <?php } ?>
              <?php for($i = max($catedata ->currentPage()-2, 1); $i <= min(max($catedata ->currentPage()-2, 1)+4,$catedata ->lastPage()); $i++) {?>
              <li><a <?php if($i==$catedata ->currentPage()){echo 'class="btn btn-success btn-xs"'; echo 'href="javascript:void();"'; }else{ $pageu = $pageUrl.'page='.$i."&rowsize=".$per_page; echo "href='$pageu'"; echo 'class="btn btn-info btn-xs"';}?>><?php echo $i; ?></a> </li>
              <?php } ?>
              <?php if($catedata ->currentPage()<$total_page){?>
              <li><a href="<?php echo $nxt_pg_url;?>" class="<?php echo $nxt_pg_class;?>"><i class="fa fa-angle-double-right"></i></a> </li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </div>
      <?php } ?>
    </table>