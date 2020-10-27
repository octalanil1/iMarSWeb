<style>
.all-select {
    margin: 0px 0px 7px;
    display: block;
}
.all-select #ckbCheckAll {
    margin-left: 21px;
}
</style>
<div class="row">
    <div class="col-md-12 col-lg-12 col-xl-12">
        <div class="surveyors">
            <div id="port_id">
                <div id="price">
                <ul class="listing">
                <?php // echo count($userportdetail);?>
                <?php if(count($port_data)!=0 ) {?>
                    <span class="all-select"><input type="checkbox" id="ckbCheckAll" onclick='selectAll()'/> Sellect All</span>
                    @foreach($port_data as $key => $data)
                        <li>
                            <input type="checkbox" name="port_id[{{$key}}]" value="{{$data['id']}}" class="checkBoxClass">
                            <span class="user-info">
                                <h4>{{$data['port']}}</h4>
                            </span>
                            <input type="text" name="price[{{$key}}]"  placeholder="Transportation Cost ($USD)" value="" class="form-control">
                        </li>
                    @endforeach
                <?php }else{ ?>

                <li>
                    <span class="ship-info">
                    <h3>No Port Available</h3>
                    </span>
                </li>
                <?php  } ?>

                </ul>
                </div>
            </div>
        </div>
    </div>
</div>
