<div id="river_droplets">
<?php foreach ($droplets as $droplet): ?>
	<article class="droplet cf">
		<div class="summary">
			<section class="source <?php echo $droplet['channel'] ?>">
				<a><img src="<?php echo $droplet['identity_avatar'] ?>" /></a>
				<div class="actions">
					<span class="type"></span>
					<p class="button_change score"><a onclick=""><span>0</span></a><p>
					<div class="clear"></div>
					<ul class="dropdown left">
						<li class="confirm"><a onclick="">This is useful</a></li>
						<li class="not_useful"><a onclick="">This is not useful</a></li>
					</ul>
				</div>
			</section>
			<section class="content">
				<div class="title">
					<p class="date"><?php echo $droplet['droplet_date_pub'] ?></p>
					<h1><?php echo $droplet['identity_name'] ?></h1>
				</div>
				<div class="body">
					<p><?php echo $droplet['droplet_title'] ?></p>
				</div>
			</section>
			<section class="actions">
				<p class="button_view"><a href="/droplet/detail/<?php echo $droplet['id'];?>" class="detail_view"><span></span><strong>detail</strong></a></p>
				<div class="button">
					<p class="button_change bucket"><a><span></span><strong>buckets</strong></a></p>
					<div class="clear"></div>
					<ul class="dropdown river">
						<?php foreach ($buckets as $bucket) :
							$bucket_action = Swiftriver_Droplets::bucket_action($bucket->id, $droplet['id']);?>
							<li class="bucket"><a onclick="addBucketDroplet(this, <?php echo $bucket->id.','.$droplet['id']; ?>)" title="<?php echo $bucket_action; ?>" class="<?php echo ($bucket_action == 'remove') ? 'selected' : ''; ?>" ><span class="select"></span><?php echo $bucket->bucket_name; ?></a></li>
						<?php endforeach; ?>
						<li class="create_new"><a onclick="createBucket(this, 'droplet', <?php echo $droplet['id']; ?>)"><span class="create_trigger"><em>Create new</em></span></a></li>
					</ul>
				</div>
			</section>
		</div>
		<section class="detail cf"></section>
	</article>
<?php endforeach; ?>
</div>

<?php echo(Html::script("themes/default/media/js/jquery.infinitescroll.min.js")); ?>
<script type="text/javascript">
$(document).ready(function() {
    $('article #river_droplets').infinitescroll({
    		navSelector  	: "article .page_buttons",
    		nextSelector 	: "article .page_buttons .button_view a",
    		itemSelector 	: "article #river_droplets",
    		debug		 	: true,
    		dataType	 	: 'html'
        })
});
</script>


<div class="page_buttons">
<p class="button_view"><a href="<?php echo $view_more_url ?>">View more</a></p>
</div>