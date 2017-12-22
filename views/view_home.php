
<style>
    /*ul.home-grid{margin-left: 10%}*/
    ul.home-grid li{display: inline-block ; list-style: none; margin:10px 5px}
</style>
<div style="background-color: #efeae3; margin-top: -5px">
    <div class="container" style="margin-top: 4%">
        <div class="row clearfix">
		<div class="col-md-12 column">
			<div class="row">
                            <ul class="home-grid">
                                <?php foreach ($query->result() as $row): ?>
                                <li>
                                    <a  href="<?php echo base_url() ?>report/<?=$row->item ?>/<?=$row->id ?>" class="btn btn-lg btn-warning view-report"><span class="glyphicon glyphicon-list-alt"></span> <br/>
                                    <?=$row->item ?><br/>
                                     <small>Click here for see report</small>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                    </div>
            </div>
        </div>
    </div>
</div>

<div style="text-align: center; position: relative; color: orange; margin-top:0px; background-color: #ddd; padding: 10px; min-height: 300px">
    <h2><i class="glyphicon glyphicon-th-list"></i>&nbsp;&nbsp;Please select a product to see stock report</h2>
</div>

       <?php // echo $this->pagination->create_links();  ?>
